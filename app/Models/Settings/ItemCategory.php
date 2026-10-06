<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    protected $table = 'mef_item_category';
    protected $fillable = [
                            "parent_id",
                            "name_en",
                            "name_kh",
                            "order_level",
                            "remark",
                            "status",
                            "created_by",
                            "modified_by"
                          ];
    public $timestamps = false;

    public static function getItemCategories($filter){
      $data = array();
      $query = ItemCategory::orderByFilter($filter);
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
          "parent_id" => $row->parent_id,
          "name_en" => $row->name_en,
          "name_kh" => $row->name_kh,
          "order_level" => $row->order_level,
          "remark" => $row->remark,
          "status" => $row->status,
          "created_by" => $row->created_by,
          "modified_by" => $row->modified_by
        );
      }
      return $data;
    }
  
    public static function getCount($filter){
      $query = ItemCategory::orderByFilter($filter);
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
