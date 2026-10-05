<?php

namespace App\Models\Settings;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use App\User;
use DB;
use Auth;
use CommonService;

class LetterType extends Model implements Auditable
{
  use \OwenIt\Auditing\Auditable,HasRoles;
  protected $table = 'letter_type';
  protected $primaryKey = 'id';
  protected $fillable = [];
  protected $guarded = [];
  public $timestamps = true;

  // public static function
  public static function dataFields(){
    // Loop Data Field From Table to put in array for mapping data field to search data table
    $data["tables"] = DB::select('show columns from '.env("DB_PREFIX").(new LetterType)->getTable());
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
    $data["tables"] = DB::select('show columns from '.env("DB_PREFIX").(new LetterType)->getTable());
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
    $query = LetterType::orderBy("order_level")->whereNotIn("status", [4])->orWhereNull("status")->get();
    $data = array();
    foreach($query as $row){
      $data[] = array(
        "label" => $row->name_kh,
        "value" => $row->name_kh,
      );
    }
    return $data;
  }

  public static function checkQuery($filter){
    $whereClause = "";
    
    
    $query = LetterType::orderBy($filter["sort"], $filter["order"]);
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
    $whereClause = LetterType::checkQuery($filter);
    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
    $whereClause->limit($filter["limit"]);
    $data = collect($whereClause->get());
    return $data;
  }

  public static function getTotal($filter){
    $total = 0;
    $whereClause = LetterType::checkQuery($filter);
    $total = $whereClause->count();
    return $total;
  }
}
