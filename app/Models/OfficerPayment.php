<?php

namespace App\Models;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\Settings\Status;
use App\Models\Settings\Currency;
use App\Models\Settings\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use DB;
use CommonService;
class OfficerPayment extends Model implements Auditable
{
  use \OwenIt\Auditing\Auditable;
  protected $table = 'officer_payment';
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

  public function payable()
  {
    return $this->belongsTo(OfficerPayable::class);
  }

  public static function dataFields(){
    // Loop Data Field From Table to put in array for mapping data field to search data table
    $data["tables"] = DB::select('show columns from '.env("DB_PREFIX").(new OfficerPayment)->getTable());
    $str = "";
    // dd($data["tables"]);
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
    $data["tables"] = DB::select('show columns from '.env("DB_PREFIX").(new OfficerPayment)->getTable());
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
    $query = OfficerPayment::orderBy("order_level")->whereNotIn("status", [4])->orWhereNull("status")->get();
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
    // $query = DB::TABLE("officer_payment")
    //         ->Join("officer","officer.id", "=", "officer_payment.officer_id")
    //         ->select('officer_payment.*', DB::raw("CONCAT(tbl_officer.first_name, ' ', tbl_officer.last_name) AS officer_id"));
    $query = OfficerPayment::with("officer")->with("status")->with("currency")->with("paymentMethod");

            $whereClause = $query;
    if(isset($filter["data"]["officer_id"])){
      $whereClause->where("officer_payment.officer_id", $filter["data"]["officer_id"]);
    }
    $whereClause->orderBy($filter["sort"], $filter["order"]);
    $d = "";
    if($filter["search_field"]){
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if(isset($filter["search_field"])){
        for($i=0 ; $i < count($filter["search_field"]); $i++){
          $field = array_key_first($filter["search_field"][$i]);
          if (array_key_exists($field, $dataFields)) {
            if(isset($filter["search_field"][$i][$field]["value"])){
              $whereClause->Where("officer_payment.".$field, 'Like', '%'.$filter["search_field"][$i][$field]["value"].'%');
            }else{
              $whereClause->Where("officer_payment.".$field, 'Like', '%'.$filter["search_field"][$i][$field].'%');
            }
          }
        }
      }
    }

    $whereClause->whereNotIn("officer_payment.status", [4]);
    $whereClause->orWhereNull("officer_payment.status");
    return $whereClause;
  }

  public static function checkQueryResult($filter,$status){
    $query = OfficerPayment::orderBy($filter["sort"], $filter["order"]);
    $whereClause = $query;
    if(isset($filter["data"]["officer_id"])){
      $whereClause->where("officer_payment.officer_id", $filter["data"]["officer_id"]);
    }
    if(isset($status)){
      $whereClause->where("status", $status);
    }
    $whereClause->orderBy($filter["sort"], $filter["order"]);
    if($filter["search_field"]){
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if(isset($filter["search_field"])){
        for($i=0 ; $i < count($filter["search_field"]); $i++){
          $field = array_key_first($filter["search_field"][$i]);
          if (array_key_exists($field, $dataFields)) {
            if(isset($filter["search_field"][$i][$field]["value"])){
              $whereClause->Where("officer_payment.".$field, 'Like', '%'.$filter["search_field"][$i][$field]["value"].'%');
            }else{
              $whereClause->Where("officer_payment.".$field, 'Like', '%'.$filter["search_field"][$i][$field].'%');
            }
          }
        }
      }
    }

    $whereClause->whereIn("officer_payment.status", [19]);
    $whereClause->orWhereNull("officer_payment.status");
    return $whereClause;
  }


  public static function getData($filter){
    $whereClause = OfficerPayment::checkQuery($filter);
    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
    $whereClause->limit($filter["limit"]);
    $data = collect($whereClause->get());
    return $data;
  }

  public static function getResultData($filter, $status){
    $whereClause = OfficerPayment::checkQueryResult($filter,$status);
    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
    $whereClause->limit($filter["limit"]);
    $data = collect($whereClause->get());
    return $data;
  }


  public static function getTotal($filter){
    $whereClause = OfficerPayment::checkQuery($filter);
    $total = $whereClause->count();
    return $total;
  }

  // Report rows before paging: shared by the paged rows and the exact count
  public static function getOfficerPaymentReportsQuery($filter){
    $query = DB::table("officer_payment as op")
              ->select("op.*", "ms.title_kh as status", "c.name_kh as currency_id", DB::raw("CONCAT(first_name, ' ', last_name, '-', identity_member_no) AS officer_id"), "pm.name_kh as payment_method_id")
              ->Join("officer as f", "op.officer_id", "=", "f.id")
              ->Join("payment_method as pm", "pm.id", "=", "op.payment_method_id")
              ->Join("mef_status as ms", "ms.id", "=", "op.status")
              ->Join("currency as c", "c.id", "=", "op.currency_id")
              ->where("op.status", 19);
    $whereClause = $query;
    if(isset($filter["data"]["officer_id"])){
      // $whereClause->where("officer_id", (int)$filter["data"]["officer_id"]);
    }
    $whereClause->orderBy($filter["sort"], $filter["order"]);
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

    return $whereClause;
  }

  public static function getOfficerPaymentReports($filter){
    return collect(self::getOfficerPaymentReportsQuery($filter)
      ->offset(($filter["page_number"] - 1) * $filter["limit"])
      ->limit($filter["limit"])
      ->get());
  }

  public static function countOfficerPaymentReports($filter){
    return self::getOfficerPaymentReportsQuery($filter)->count();
  }

}