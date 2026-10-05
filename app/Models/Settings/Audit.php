<?php

namespace App\Models\Settings;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
  protected $table = 'audits';
  protected $primaryKey = 'id';
  protected $fillable = [
    'user_type',
    'user_id',
    'event',
    'auditable_type',
    'auditable_id',
    'old_values',
    'new_values',
    'url',
    'ip_address',
    'user_agent',
    'tags',
    'created_at',
    'updated_at',
  ];
  public $timestamps = false;

  public function user(){
    return $this->belongsTo('App\User','user_id');
  }

  public static function dataFields()
  {
    // Loop Data Field From Table to put in array for mapping data field to search data table
    $data["tables"] = DB::select('show columns from ' . env("DB_PREFIX") . (new Audit)->getTable());
    $str = "";
    for ($i = 0; $i < count($data["tables"]); $i++) {
      $str .= $data["tables"][$i]->Field . ",";
    }
    $newArr = explode(",", $str);
    array_pop($newArr);
    $dataFields = $newArr;
    return $dataFields;
  }

  public static function getSingleOpt()
  {
    $query = Audit::orderBy("updated_at")->get();
    $data = array();
    foreach ($query as $row) {
      $data[] = array(
        "label" => $row->name_kh,
        "value" => $row->name_kh,
      );
    }
    return $data;
  }

  public static function checkQuery($filter)
  {
    $query = Audit::with("user")->orderBy($filter["sort"], $filter["order"]);
    $whereClause = $query;
    if ($filter["search_field"]) {
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if (array_key_exists("name_kh", $dataFields) && $dataFields["name_kh"] != null) {
        $whereClause->Where("name_kh", $dataFields["name_kh"]);
      }
      if (array_key_exists("name_en", $dataFields) && $dataFields["name_en"] != null) {
        $whereClause->Where("name_en", $dataFields["name_en"]);
      }
    }
    return $whereClause;
  }

  public static function getData($filter)
  {
    $whereClause = Audit::checkQuery($filter);
    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
    $whereClause->limit($filter["limit"]);
    $data = collect($whereClause->get());
    return $data;
  }

  public static function getTotal($filter)
  {
    $whereClause = Audit::checkQuery($filter);
    $total = $whereClause->count();
    return $total;
  }
}
