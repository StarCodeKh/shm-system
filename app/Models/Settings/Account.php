<?php

namespace App\Models\Settings;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
  protected $table = 'account';
  protected $primaryKey = 'id';
  protected $fillable = [
                          "account_type",
                          "parent_code",
                          "code",
                          "name_en",
                          "name_kh",
                          "description",
                          "order_level",
                          "status",
                          "created_by",
                          "modified_by"
                        ];
  public $timestamps = false;

  public static function getAcc($param)
  {
    $data = array();
    $query = Account::whereNotIn("status", [4])->orWhere("status", null);
    $whereClause = $query;
    $whereClause->orderBy("order_level");
    if(!empty($param["param"]["value"])){
      $whereClause->where("account_type", $param["param"]["value"]);
    }
    $result = collect($whereClause->get());
    foreach ($result as $row) {
      $data[] = array(
        "label" => $row->code . "-" . (config_language == "en" ? $row->en : $row->name_kh),
        "value" => $row->id,
      );
    }
    return $data;
  }

  public static function getSingleOpt(){
    $query = AccountType::whereNotIn("status", [4])->orWhereNull("status")->get();
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
    $whereClause = "";
    
    
    $query = Account::orderBy($filter["sort"], $filter["order"]);
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

  public static function getChartAccountParentCode(){
    $query = Account::WhereNull("parent_code")->whereNotIn("status", [4])->orWhereNull("status")->get();
    $data = array();
    foreach($query as $row){
      $data[] = array(
        "label" => $row->code.':'.$row->name_en,
        "value" => $row->code,
      );
    }
    return $data;
  }

  public static function getAccountCodeList(){
    $query = Account::whereNotIn("status", [4])->orWhereNull("status")->get();
    $data = array();
    foreach($query as $row){
      $data[] = array(
        "label" => $row->code.':'.$row->name_en,
        "value" => $row->code,
      );
    }
    return $data;
  }

  // public static function getAccountByGroup($param){
  //   $data = array();
  //   $query = Account::whereNotIn("status", [4])->orWhere("status", null);
  //   $whereClause = $query;
  //   $whereClause->orderBy("order_level");
  //   if(!empty($param["param"]["value"])){
  //     $whereClause->where("account_group_id", $param["param"]["value"]);
  //   }
  //   $result = collect($whereClause->get());
  //   foreach($result as $row){
  //     $data[] = array(
  //       "label" => $row->code."-".(config_language=="en"?$row->en:$row->name_kh),
  //       "value" => $row->id,
  //     );
  //   }
  //   return $data;
  // }

  public static function getSubAccount($param){
    $data = array();
    $query = Account::whereNotIn("status", [4])->orWhere("status", null);
    $whereClause = $query;
    $whereClause->orderBy("order_level");
    if (!empty($param["param"]["value"])) {
      $whereClause->where("parent_id", $param["param"]["value"]);
    }
    $result = collect($whereClause->get());
    foreach ($result as $row) {
      $data[] = array(
        "label" => $row->code . "-" . (config_language == "en" ? $row->en : $row->name_kh),
        "value" => $row->id,
      );
    }
    return $data;
  }

  public static function getAccounts($filter)
  {
    $data = array();
    $query = Account::orderBy($filter["sort"], $filter["order"]);
    $whereClause = $query;
    $whereClause->whereNotIn("status", [4])->orWhereNull("status");
    if (!empty($filter["search_field"])) {
      $whereClause->offset((1 - 1) * $filter["limit"]);
    } else {
      $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
    }
    $whereClause->limit($filter["limit"]);
    if ($filter["search_field"]) {
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if (array_key_exists('account_type', $dataFields)) {
        $whereClause->Where("account_type", 'Like', '%'.$dataFields["account_type"]["value"].'%');
      }
      if (array_key_exists('parent_code', $dataFields)) {
        $whereClause->Where("parent_code", 'Like', '%'.$dataFields["parent_code"]["value"].'%');
      }
      if (array_key_exists('code', $dataFields)) {
        $whereClause->Where("code", 'Like', '%' . $dataFields["code"] . '%');
      }
      if (array_key_exists('name_kh', $dataFields)) {
        $whereClause->Where("name_kh", 'Like', '%' . $dataFields["name_kh"] . '%');
      }
      if (array_key_exists('name_en', $dataFields)) {
        $whereClause->Where("name_en", 'Like', '%' . $dataFields["name_en"] . '%');
      }
    }
    $results = collect($whereClause->get());
    foreach ($results as $row) {
      $data[] = array(
        "id" => $row->id,
        "account_type" => $row->account_type,
        "parent_code" => $row->parent_code,
        "code" => $row->code,
        "name_en" => $row->name_en,
        "name_kh" => $row->name_kh,
        "description" => $row->description,
        "order_level" => $row->order_level,
        "status" => $row->status,
        "created_by" => $row->created_by,
        "modified_by" => $row->modified_by,
      );
    }
    return $data;
  }

  public static function getCount($filter)
  {
    $data = array();
    $query = Account::orderBy($filter["sort"], $filter["order"]);
    $whereClause = $query;
    if ($filter["search_field"]) {
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if (array_key_exists('account_type', $dataFields)) {
        $whereClause->Where("account_type", 'Like', '%'.$dataFields["account_type"]["value"].'%');
      }
      if (array_key_exists('parent_code', $dataFields)) {
        $whereClause->Where("parent_code", 'Like', '%'.$dataFields["parent_code"]["value"].'%');
      }
      if (array_key_exists('code', $dataFields)) {
        $whereClause->Where("code", 'Like', '%' . $dataFields["code"] . '%');
      }
      if (array_key_exists('name_kh', $dataFields)) {
        $whereClause->Where("name_kh", 'Like', '%' . $dataFields["name_kh"] . '%');
      }
      if (array_key_exists('name_en', $dataFields)) {
        $whereClause->Where("name_en", 'Like', '%' . $dataFields["name_en"] . '%');
      }
    }
    $total = $whereClause->count();
    return $total;
  }

   public static function getAccountCode($accountType){
    $query = Account::orderBy("order_level")->whereIn("account_type", $accountType)->whereNotIn("status", [4])->orWhere("status", null)->get();
    $data = array();
    foreach($query as $row){
      $data[] = array(
        "label" => $row->code.': '.$row->name_en,
        "value" => $row->code,
      );
    }
    return $data;
  }
  
}
