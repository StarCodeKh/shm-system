<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class ProjectStatus extends Model
{
    protected $table = 'mef_project_status';
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

    public static function getProjectStatusOpt(){
      $query = ProjectStatus::orderByLevel()->get();
      $data = array();
      foreach($query as $row){
        $data[] = array(
          "label" => $row->name_kh,
          "value" => $row->id,
        );
      }
      return $data;
    }


    public static function getProjectStatus($filter){
      $data = array();
      $query = ProjectStatus::orderByFilter($filter);
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
          "remark" => $row->remark,
          "status" => $row->status,
        );
      }
      return $data;
    }
  
    public static function getCount($filter){
      $query = ProjectStatus::orderByFilter($filter);
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
