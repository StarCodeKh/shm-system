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
    $data["tables"] = DB::select('show columns from '.env("DB_PREFIX").(new Status)->getTable());
    $str = "";
    for($i=0; $i < count($data["tables"]); $i++){
      if($data["tables"][$i]->Null == "NO" && $data["tables"][$i]->Field != 'id'){
      // if($data["tables"][$i]->Field != 'id'){
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
    $data["tables"] = DB::select('show columns from '.env("DB_PREFIX").(new Status)->getTable());
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
    $query = Status::orderBy("order_level");
    $whereClause = $query;
    if(is_array($groupStatus)){
      $whereClause->whereIn("group", $groupStatus);
    }else{
      $whereClause->where("group", $groupStatus);
    }
    $whereClause->whereNotIn("status", [4])->orWhereNull("status");
    $queries = collect($whereClause->get());
    $data = array();
    foreach($queries as $row){
      $data[] = array(
        "label" => $row->title_kh.' ('.$row->title_en.')',
        "value" => $row->id,
      );
    }
    return $data;
  }

  public static function checkQuery($filter){
    $whereClause = "";
    $query = Status::orderBy($filter["sort"], $filter["order"]);
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
    
    $whereClause->whereNotIn("status", [4]);
    $whereClause->orWhereNull("status");
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
