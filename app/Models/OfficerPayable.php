<?php

namespace App\Models;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\Settings\Account;
use App\Models\Settings\Entity;
use App\Models\Settings\Department;
use App\Models\Settings\Status;
use App\Models\Settings\Currency;
use App\Models\Settings\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use DB;
use CommonService;
class OfficerPayable extends Model implements Auditable
{
  use \OwenIt\Auditing\Auditable;
  protected $table = 'officer_payable';
  protected $primaryKey = 'id';
  protected $fillable = [];
  protected $guarded = [];
  public $timestamps = true;
  protected $casts = [
    'due_date' => 'datetime:'.("Y-m-d"),
    'payment_date' => 'datetime:'.("Y-m-d"),
  ];
  public function officer(){
    return $this->belongsTo(Officer::class, 'officer_id');
  }

  public function department(){
    return $this->belongsTo(Department::class, 'department_id');
  }

  public function entity(){
    return $this->belongsTo(Entity::class, 'entity_id');
  }

  public function status()
  {
    return $this->belongsTo(Status::class, "status");
  }

  public function currency()
  {
    return $this->belongsTo(Currency::class, "currency_id");
  }

  public function account()
  {
    return $this->belongsTo(Account::class, "account_id");
  }

  public function paymentMethod()
  {
    return $this->belongsTo(PaymentMethod::class, "payment_method_id");
  }
  
  public function paymentHistory()
  {
    return $this->hasMany(OfficerPayment::class);
  }

  public static function dataFields(){
    // Loop Data Field From Table to put in array for mapping data field to search data table
    $data["tables"] = DB::select('show columns from '.env("DB_PREFIX").(new OfficerPayable)->getTable());
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

  // public static function formAttributes(){
  //   $formAttributes = array();

  //   $prefix = env('DB_PREFIX');
  //   $database = env('DB_DATABASE'); // your current database

  //   $tables = [
  //     $prefix . (new \App\Models\OfficerPayable)->getTable(),
  //     $prefix . (new \App\Models\Officer)->getTable()
  //   ];

  //   $tables_list = "'" . implode("','", $tables) . "'";
  //   $columns_list = "'code','officer_id', 'entity_id', 'department_id'"; // only these columns

  //   $columns = DB::select("
  //     SELECT *
  //     FROM INFORMATION_SCHEMA.COLUMNS
  //     WHERE TABLE_SCHEMA = '{$database}'
  //     AND TABLE_NAME IN ({$tables_list})
  //     AND COLUMN_NAME IN ({$columns_list})
  //   ");

  //   foreach ($columns as $col) {
  //     $formAttributes[] = array(
  //       "name" => $col->COLUMN_NAME,
  //       "type" => CommonService::convertDataTypeFromComlumnBinding($col->COLUMN_NAME, $col->DATA_TYPE),
  //       "required" => $col->IS_NULLABLE=="NO"?true:false,
  //     );
  //   }
  //   return $formAttributes;
  // }

  public static function formAttributes(){
    $formAttributes = array();
    $data["tables"] = DB::select('show columns from '.env("DB_PREFIX").(new OfficerPayable)->getTable());
    for($i=0; $i < count($data["tables"]); $i++){
      if($data["tables"][$i]->Field != "id" && $data["tables"][$i]->Field != "created_by" && $data["tables"][$i]->Field != "modified_by" && $data["tables"][$i]->Field != "created_at" && $data["tables"][$i]->Field != "updated_at"){
        $formAttributes[] = array(
          "name" => $data["tables"][$i]->Field,
          "type" => CommonService::convertDataTypeWay1($data["tables"][$i]),
          "required" => $data["tables"][$i]->Null=="NO"?true:false,
        );
      }
    }
    return $formAttributes;
  }

  public static function getSingleOpt(){
    $query = OfficerPayable::orderBy("order_level")->whereNotIn("status", [4])->orWhereNull("status")->get();
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
    $query = OfficerPayable::with("currency")->with("account")->with("officer")->with("status")->with("paymentMethod");
    $whereClause = $query;
    if(isset($filter["data"]["officer_id"])){
      $whereClause->where("officer_id", $filter["data"]["officer_id"]);
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
              $whereClause->Where($field, 'Like', '%'.$filter["search_field"][$i][$field]["value"].'%');
            }else{
              $whereClause->Where($field, 'Like', '%'.$filter["search_field"][$i][$field].'%');
            }
          }
        }
      }
    }
    $whereClause->whereIn("status", [15,19]);
    //$whereClause->WhereNull("status");
    $whereClause = $query;
    return $whereClause;
  }

  public static function getData($filter){
    $whereClause = OfficerPayable::checkQuery($filter);
    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
    $whereClause->limit($filter["limit"]);
    $data = collect($whereClause->get());
    return $data;
  }

  public static function getTotal($filter){
    $whereClause = OfficerPayable::checkQuery($filter);
    $total = $whereClause->count();
    return $total;
  }

  public static function getTotalAmount($filter, $status){
    $whereClause = OfficerPayable::where("status", $status);
    if($filter["search_field"]){
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if(isset($filter["search_field"])){
        for($i=0 ; $i < count($filter["search_field"]); $i++){
          $field = array_key_first($filter["search_field"][$i]);
          if (array_key_exists($field, $dataFields)) {
            if(isset($filter["search_field"][$i][$field]["value"])){
              // $whereClause->Where($field, 'Like', '%'.$filter["search_field"][$i][$field]["value"].'%');
              $whereClause->Where($field, $filter["search_field"][$i][$field]["value"]);
            }else{
              $whereClause->Where($field, 'Like', '%'.$filter["search_field"][$i][$field].'%');
            }
          }
        }
      }
    }
    $total = $whereClause->sum("amount");
    return $total;
  }

  public static function getSummary($filter){

    $summary = ["currency" => "រៀល",
                "paid" => "២២២២២",
                "unpaid" => "២២២"
              ];
    return $summary;
  } 

  public static function getOfficerPayables($filter){
    $query = DB::table("officer_payable as of")
              ->select("of.*", DB::raw("CONCAT(first_name, ' ', last_name, '-', identity_member_no) AS officer_id"), "c.name_kh as currency_id", "ms.title_kh as status" ,"pm.name_kh as payment_method_id")
              ->LeftJoin("officer as f", "f.id", "=", "of.officer_id")
              ->LeftJoin("payment_method as pm", "pm.id", "=", "of.payment_method_id")
              ->LeftJoin("mef_status as ms", "ms.id", "=", "of.status")
              ->LeftJoin("currency as c", "c.id", "=", "of.currency_id")
              ->where("of.status", 15);
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

    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
    $whereClause->limit($filter["limit"]);
    $data = collect($whereClause->get());
    return $data;
  }

  public static function getPayableInfoByOfficer($request){
    $officerId = $request["value"];
    $findOfficer = Officer::with("department")->with("entity")->find($officerId);
    $paymentHistories = array();
    if($findOfficer){
      $whereClause = OfficerPayable::where("officer_id", $officerId)->whereNotIn("status", [15])->orderby("due_date", "desc");
      $paymentHistories = collect($whereClause->get());
      // Get Payable 
      $payables = OfficerPayable::with("currency")->where("officer_id", $officerId)->where("status", 15)->orderby("due_date", "desc")->get();
      $payableArr = array();
      foreach($payables as $payable){
        $payableArr[] = array(
          "label" => "បង់ប្រាក់ក្នុងឆ្នាំ-".date("Y", strtotime($payable->due_date))."- ចំនួន ".$payable->amount."(".$payable->currency->name_kh.")",
          "value" => $payable->amount,
          "id" => $payable->id
        );
      }
    }
    $data = array(
      "officerDetail" => $findOfficer,
      "payables" => $payableArr,
      "paymentHistories" => $paymentHistories
    );
    return $data;
  }
}