<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class AccountTypeGroup extends Model
{
  protected $table = 'account_type_group';
  protected  $primaryKey = 'id';
  protected $fillable = [
    "name_en",
    "name_kh",
    'order_level',
    'status',
    'created_by',
    'modified_by'
  ];
  public $timestamps = false;

  public static function getAccountTypeGroupOptions($filter)
  {
    $data = array();
    $query = AccountTypeGroup::orderBy($filter["sort"], $filter["order"]);
    $whereClause = $query;
    $whereClause->whereNotIn("status", [4])->orWhere("status", null);
    $results = collect($whereClause->get());
    foreach ($results as $row) {
      $data[] = array(
        "label" => $row->name_kh,
        "value" => $row->id,
      );
    }
    return $data;
  }

  public static function getAccountTypeGroups($filter)
  {
    $data = array();
    $query = AccountTypeGroup::orderBy($filter["sort"], $filter["order"]);
    $whereClause = $query;
    $whereClause->whereNotIn("status", [4])->orWhere("status", null);
    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
    $whereClause->limit($filter["limit"]);

    if ($filter["search_field"]) {
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if (isset($input["search_field"])) {
        for ($i = 0; $i < count($input["search_field"]); $i++) {
          $field = array_key_first($input["search_field"][$i]); //array('key1', 'key2', 'key3');
          if (in_array($field, $dataFields)) {
            $whereClause->orWhere($field, "Like", "%" . $input["search_field"][$i][$field] . "%");
          }
        }
      }
    }

    $results = collect($whereClause->get());
    foreach ($results as $row) {
      $data[] = array(
        'id' => $row->id,
        'account_type_group' => isset($row->AccountTypeGroup) ? $row->AccountTypeGroup->name_kh : "",
        'account_type_group_id' => array(
          "label" => $row->AccountTypeGroup ? $row->AccountTypeGroup->name_kh : "",
          "value" => $row->AccountTypeGroup ? $row->AccountTypeGroup->id : "",
        ),
        "name_en" => $row->name_en ? $row->name_en : "",
        "name_kh" => $row->name_kh ? $row->name_kh : "",
        'description' => $row->description,
        'order_level'
      );
    }
    return $data;
  }

  public static function getCount($filter)
  {
    $data = array();
    $query = AccountTypeGroup::orderBy($filter["sort"], $filter["order"]);
    $whereClause = $query;
    $whereClause->whereNotIn("status", [4])->orWhere("status", null);
    if ($filter["search_field"]) {
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if (isset($input["search_field"])) {
        for ($i = 0; $i < count($input["search_field"]); $i++) {
          $field = array_key_first($input["search_field"][$i]); //array('key1', 'key2', 'key3');
          if (in_array($field, $dataFields)) {
            $whereClause->orWhere($field, "Like", "%" . $input["search_field"][$i][$field] . "%");
          }
        }
      }
    }
    $total = $whereClause->count();
    return $total;
  }
}
