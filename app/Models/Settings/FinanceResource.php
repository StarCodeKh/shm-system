<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class FinanceResource extends Model
{
  protected $table = 'mef_finance_resource';
  protected  $primaryKey = 'id';
  protected $fillable = [
                          "name_en",
                          "name_kh",
                          "order_level",
                          "status",
                          "created_by",
                          "modified_by"
                        ];
  public $timestamps = false;

  public static function getFinanceResourceOpts(){
    $query = FinanceResource::orderByLevel();
    $whereClause = $query;
    $whereClause->whereNotDeleted();
    $queryResult = collect($whereClause->get());
    foreach($queryResult as $row){
      $data[] = array(
        "label" => (config_language=="en")?$row->name_en:$row->name_kh,
        "value" => $row->id,
      );
    }
    return $data;
  }

  public static function getFinanceResources($filter){
    $data = array();
    $query = FinanceResource::orderByFilter($filter);
    $whereClause = $query;
    $whereClause->whereNotDeleted();
    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);       
    $whereClause->limit($filter["limit"]);

    if($filter["search_field"]){
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if (array_key_exists("name_kh", $dataFields)) {
        $whereClause->Where("name_kh", $dataFields["name_kh"]);
      }
      if (array_key_exists("name_en", $dataFields)) {
        $whereClause->Where("name_en", $dataFields["name_en"]);
      }
    }
    $results = collect($whereClause->get());
    foreach($results as $row){
      $data[] = array(
        "id" => $row->id,
        "name_en" => $row->name_en,
        "name_kh" => $row->name_kh,
        "order_level" => $row->order_level,
        "status" => $row->status,
      );
    }
    return $data;
  }

  public static function getCount($filter){
    $query = FinanceResource::orderByFilter($filter);
    $whereClause = $query;
    $whereClause->whereNotDeleted();
    if($filter["search_field"]){
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if (array_key_exists("name_kh", $dataFields)) {
        $whereClause->Where("name_kh", $dataFields["name_kh"]);
      }
      if (array_key_exists("name_en", $dataFields)) {
        $whereClause->Where("name_en", $dataFields["name_en"]);
      }
    }
    $total = $whereClause->count();
    return $total;
  }
}