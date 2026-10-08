<?php

namespace App\Models\Settings;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\OfficerPayable;
use App\Models\OfficerRequest;
use App\Models\OfficerPaymentRecurring;
use App\Models\Notification;
use DB;
use CommonService;

class Status extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable,HasRoles;
    protected $table = 'mef_status';

    // Status ids the code relies on (rows of the status table). Use these names, never the bare numbers.
    const DELETED = 4;   // soft delete: hidden from every list (see the whereNotDeleted() query macro)
    const ACTIVE = 13;
    const INACTIVE = 14;
    const UNPAID = 15;   // officer payable not yet paid
    const PAID = 19;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $guarded = [];
    public $timestamps = true;

    public function paymentType()
    {
        return $this->hasMany(PaymentType::class);
    }

    public function officerPayable()
    {
        return $this->hasMany(OfficerPayable::class);
    }

    public function officerRequest()
    {
        return $this->hasMany(OfficerRequest::class);
    }

    public function officerPaymentRecurring()
    {
        return $this->hasMany(OfficerPaymentRecurring::class);
    }

    public function notification()
    {
        return $this->hasMany(Notification::class);
    }

    // public static function
    public static function dataFields(){
        // Loop Data Field From Table to put in array for mapping data field to search data table
        $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new Status)->getTable());
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
        $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new Status)->getTable());
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

    public static function getSingleOpt($groupStatus){
        $whereClause = "";
        $query = Status::orderByLevel();
        $whereClause = $query;
        if(is_array($groupStatus)){
            $whereClause->whereIn("group", $groupStatus);
        }else{
            $whereClause->where("group", $groupStatus);
        }
        // Grouped so deleted rows are excluded without dropping the group filter
        $whereClause->where(function ($q) {
            $q->whereNotDeleted();
        });
        $queries = collect($whereClause->get());
        $data = array();
        foreach($queries as $row){
            $data[] = array(
                "label" => CommonService::localName($row, 'title'),
                "value" => $row->id,
            );
        }
        return $data;
    }

    // Options for fields that store a text value (e.g. gender "male") instead of the status id.
    // The stored value comes from `criteria`, falling back to `title_kh`.
    public static function getValueOpt($group){
        $queries = Status::where("group", $group)
            ->where(function ($q) {
                $q->whereNotDeleted();
            })
            ->orderByLevel()
            ->get();
        $data = array();
        foreach($queries as $row){
            $data[] = array(
                "label" => CommonService::localName($row, 'title'),
                "label_en" => $row->title_en,
                "value" => $row->criteria ?: $row->title_kh,
            );
        }
        return $data;
    }
    public static function checkQuery($filter){
        $whereClause = "";
        $query = Status::orderByFilter($filter);
        $whereClause = $query;
        if($filter["search_field"]){
            $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
            $dataFields = $arraySingle;
            if(isset($filter["search_field"])){
                for($i=0 ; $i < count($filter["search_field"]); $i++){
                    $field = array_key_first($filter["search_field"][$i]); //array('key1', 'key2', 'key3');
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
        $data = [];
        $whereClause = Status::checkQuery($filter);
        $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
        $whereClause->limit($filter["limit"]);
        $data = collect($whereClause->get());
        return $data;
    }

    public static function getTotal($filter){
        $total = 0;
        $whereClause = Status::checkQuery($filter);
        $total = $whereClause->count();
        return $total;
    }
}
