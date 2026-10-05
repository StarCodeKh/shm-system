<?php

namespace App\Models;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\CompositionRequest;
use App\Models\Officer;
use App\Models\Settings\Status;
use App\Models\Settings\Currency;
use App\Models\Settings\Entity;
use App\Models\Settings\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use DB;
use CommonService;
class OfficerDocument extends Model implements Auditable
{
  use \OwenIt\Auditing\Auditable;
  protected $table = 'officer_document';
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

  public static function dataFields(){
    // Loop Data Field From Table to put in array for mapping data field to search data table
    $data["tables"] = DB::select('show columns from '.env("DB_PREFIX").(new OfficerDocument)->getTable());
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
    $data["tables"] = DB::select('show columns from '.env("DB_PREFIX").(new OfficerDocument)->getTable());
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

  public static function checkQuery($filter){
    $query = OfficerDocument::with("officer")->with("status");
    $whereClause = $query;
    if(isset($filter["data"]["officer_id"])){
      $whereClause->where("officer_id", $filter["data"]["officer_id"]);
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
              $whereClause->Where("".$field, 'Like', '%'.$filter["search_field"][$i][$field]["value"].'%');
            }else{
              $whereClause->Where("".$field, 'Like', '%'.$filter["search_field"][$i][$field].'%');
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
    $whereClause = OfficerDocument::checkQuery($filter);
    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
    $whereClause->limit($filter["limit"]);
    $data = collect($whereClause->get());
    return $data;
  }

  public static function getTotal($filter){
    $whereClause = OfficerDocument::checkQuery($filter);
    $total = $whereClause->count();
    return $total;
  }
}