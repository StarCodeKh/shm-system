<?php

namespace App\Models;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\Settings\Status;
use App\Models\Settings\Currency;
use App\Models\Settings\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use DB;
use CommonService;
class OfficerRequest extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'officer_request';
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $guarded = [];
    public $timestamps = true;

    public function officer(){
        return $this->belongsTo(Officer::class, 'officer_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, "status");
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, "currency_id");
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, "payment_method_id");
    }

    public static function dataFields(){
        // Loop Data Field From Table to put in array for mapping data field to search data table
        $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new OfficerRequest)->getTable());
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
        $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new OfficerRequest)->getTable());
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
        $query = OfficerRequest::orderByLevel()->whereNotDeleted()->get();
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
        $query = OfficerRequest::with("officer")->with("status")->with("currency")->with("paymentMethod");
        $whereClause = $query;
        if(isset($filter["data"]["officer_id"])){
            $whereClause->where("officer_request.officer_id", $filter["data"]["officer_id"]);
        }
        $whereClause->orderByFilter($filter);
        if($filter["search_field"]){
            $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
            $dataFields = $arraySingle;
            if(isset($filter["search_field"])){
                for($i=0 ; $i < count($filter["search_field"]); $i++){
                    $field = array_key_first($filter["search_field"][$i]);
                    if (array_key_exists($field, $dataFields)) {
                        if(isset($filter["search_field"][$i][$field]["value"])){
                            $whereClause->Where("officer_request.".$field, 'Like', '%'.$filter["search_field"][$i][$field]["value"].'%');
                        }else{
                            $whereClause->Where("officer_request.".$field, 'Like', '%'.$filter["search_field"][$i][$field].'%');
                        }
                    }
                }
            }
        }

        $whereClause->whereNotDeleted("officer_request.status");
        return $whereClause;
    }

    public static function getData($filter){
        $whereClause = OfficerRequest::checkQuery($filter);
        $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
        $whereClause->limit($filter["limit"]);
        $data = collect($whereClause->get());
        return $data;
    }

    public static function getTotal($filter){
        $whereClause = OfficerRequest::checkQuery($filter);
        $total = $whereClause->count();
        return $total;
    }

    // Report rows before paging: shared by the paged rows and the exact count
    public static function getOfficerRequestReportsQuery($filter){
        $query = DB::table("officer as f")
            ->select("or.*", "ms.title_kh as status", "c.name_kh as currency_id", DB::raw("CONCAT(first_name, ' ', last_name, '-', identity_member_no) AS officer_id"), "pm.name_kh as payment_method_id")
            ->Join("officer_request as or", "or.officer_id", "=", "f.id")
            ->Join("payment_method as pm", "pm.id", "=", "or.payment_method_id")
            ->Join("mef_status as ms", "ms.id", "=", "or.status")
            ->Join("currency as c", "c.id", "=", "or.currency_id");
        $whereClause = $query;
        if(isset($filter["data"]["officer_id"])){
        }
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

        $whereClause->whereNotIn("f.status", [4]);
        return $whereClause;
    }

    public static function getOfficerRequestReports($filter){
        return collect(self::getOfficerRequestReportsQuery($filter)
            ->offset(($filter["page_number"] - 1) * $filter["limit"])
            ->limit($filter["limit"])
            ->get());
    }

    public static function countOfficerRequestReports($filter){
        return self::getOfficerRequestReportsQuery($filter)->count();
    }

}
