<?php

namespace App\Models\Settings;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\OfficerPayable;
use Illuminate\Database\Eloquent\Model;
use DB;
use CommonService;

class CurrencyExchange extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'currency_exchange';
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $guarded = [];
    public $timestamps = true;

    // Used only when no USD -> KHR rate has been entered yet
    public const DEFAULT_USD_KHR = 4000;

    private static array $rates = [];

    /**
     * Latest rate to turn one $from into $to (by issue date), e.g. CurrencyExchange::rate() = riel per dollar.
     * A rate entered the other way round is inverted. Without any rate: DEFAULT_USD_KHR (or its inverse), else 1.
     */
    public static function rate(string $from = Currency::USD, string $to = Currency::KHR): float
    {
        $from = strtoupper($from);
        $to = strtoupper($to);
        if ($from === $to) return 1.0;
        return self::$rates["$from>$to"] ??= (function () use ($from, $to) {
            $fromId = Currency::idOf($from);
            $toId = Currency::idOf($to);
            $latest = fn ($a, $b) => (float) self::query()->whereNotDeleted()->where('from_currency', $a)->where('to_currency', $b)
                ->where('exchange_rate', '>', 0)->orderByDesc('issue_date')->orderByDesc('id')->value('exchange_rate');
            if ($rate = $latest($fromId, $toId)) return $rate;
            if ($rate = $latest($toId, $fromId)) return 1 / $rate;
            if ($from === Currency::USD && $to === Currency::KHR) return (float) self::DEFAULT_USD_KHR;
            if ($from === Currency::KHR && $to === Currency::USD) return 1 / self::DEFAULT_USD_KHR;
            return 1.0;
        })();
    }

    public function paymentType()
    {
        return $this->hasMany(PaymentType::class);
    }

    public function officerPayable()
    {
        return $this->hasMany(OfficerPayable::class);
    }

    public static function dataFields(){
        // Loop Data Field From Table to put in array for mapping data field to search data table
        $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new CurrencyExchange)->getTable());
        $str = "";
        for($i=0; $i < count($data["tables"]); $i++){
            if($data["tables"][$i]->Null == "NO" && $data["tables"][$i]->Field != 'id'){
                $str .= $data["tables"][$i]->Field.",";
            }
        }
        $newArr = explode(",", $str);
        array_pop($newArr);
        $dataFields = $newArr;
        return $dataFields;
    }

    public static function formAttributes(){
        $formAttributes = array();
        $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new CurrencyExchange)->getTable());
        for($i=0; $i < count($data["tables"]); $i++){
            if($data["tables"][$i]->Field != "id" && $data["tables"][$i]->Field != "created_by" && $data["tables"][$i]->Field != "modified_by" && $data["tables"][$i]->Field != "created_at" && $data["tables"][$i]->Field != "updated_at"){
                $formAttributes[] = array(
                    "name" => $data["tables"][$i]->Field,
                    "type" => CommonService::convertDataType($data["tables"][$i]),
                    "required" => $data["tables"][$i]->Null=="NO"?true:false,
                );
            }
        }
        return $formAttributes;
    }

    public static function getSingleOpt(){
        $query = CurrencyExchange::whereNotDeleted()->get();
        $data = array();
        foreach($query as $row){
            $data[] = array(
                "label" => $row->name_kh,
                "value" => $row->id,
            );
        }
        return $data;
    }

    public static function checkQuery($filter){
        $query = CurrencyExchange::orderByFilter($filter);
        $whereClause = $query;
        $whereClause->orderByFilter($filter);
        $d = "";
        if($filter["search_field"]){
            $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
            $dataFields = $arraySingle;
            if(isset($filter["search_field"])){
                for($i=0 ; $i < count($filter["search_field"]); $i++){
                    $field = array_key_first($filter["search_field"][$i]);
                    if (array_key_exists($field, $dataFields)) {
                        if(isset($filter["search_field"][$i][$field]["value"])){
                            $whereClause->Where($field, 'Like', '%'.$filter["search_field"][$i][$field]["value"].'%');
                        }else{
                            $whereClause->Where($field, 'Like', '%'.$filter["search_field"][$i][$field].'%');
                        }
                    }
                }
            }
        }

        $whereClause->whereNotDeleted();
        return $whereClause;
    }

    public static function getData($filter){
        $whereClause = CurrencyExchange::checkQuery($filter);
        $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
        $whereClause->limit($filter["limit"]);
        $data = collect($whereClause->get());
        return $data;
    }

    public static function getTotal($filter){
        $whereClause = CurrencyExchange::checkQuery($filter);
        $total = $whereClause->count();
        return $total;
    }
}
