<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;
use CommonService;
class Unit extends Model
{
  protected $auditStrict = true;
  protected  $primaryKey = 'id';
  
  protected $table = 'unit';
  protected $fillable = [
                          "name_en",
                          "name_kh",
                          "order_level",
                          "remark",
                          "status",
                          "created_by",
                          "modified_by"
                        ];
  public $timestamps = false;

  public static function getUnits($filter){
    // $data = array();
    // $whereClause = CommonService::queryTable(Unit::class, $filter, $flag = 1); // Flag == 1 mean if query result limit by qty
    // $results = collect($whereClause->get());
    $data = array();
    $query = Unit::orderBy($filter["sort"], $filter["order"]);
    $whereClause = $query;
    $whereClause->whereNotIn("status", [4])->orWhereNull("status");
    if(!empty($filter["search_field"])){
      $whereClause->offset((1 - 1) * $filter["limit"]);       
    }else{
      $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
    }
    $whereClause->limit($filter["limit"]);
    if($filter["search_field"]){
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if (array_key_exists('name_kh', $dataFields)) {
        $whereClause->Where("name_kh", 'Like', '%'.$dataFields["name_kh"].'%');
      }
      if (array_key_exists('name_en', $dataFields)) {
        $whereClause->Where("name_en", 'Like', '%'.$dataFields["name_en"].'%');
      }
    }
    $results = collect($whereClause->get());
    foreach($results as $row){
      $data[] = array(
        "id" => $row->id,
        "name_en" => $row->name_en,
        "name_kh" => $row->name_kh,
        "order_level" => $row->order_level,
        "remark" => $row->remark,
        "status" => $row->status,
      );
    }
    return $data;
  }

  public static function getCount($filter){
    $whereClause = CommonService::queryTable(Unit::class, $filter, $flag = 1); // Flag == 1 mean if query result limit by qty
    $total = $whereClause->count();
    return $total;
  }

}
