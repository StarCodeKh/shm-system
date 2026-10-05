<?php

namespace App\Models\Settings;

use App\Models\Settings\AccountTypeGroup;
use Illuminate\Database\Eloquent\Model;

class AccountGroup extends Model
{
  protected $table = 'account_group';
  protected  $primaryKey = 'id';
  protected $fillable = [
    'account_type_id',
    'code',
    "name_en",
    "name_kh",
    'description',
    'order_level',
    'status',
    'created_by',
    'modified_by'
  ];
  public $timestamps = false;

  public function AccountTypeGroup()
  {
    return $this->belongsTo(AccountTypeGroup::class, 'group');
  }
  public static function getAccGroupOpts($filter)
  {
    $query = AccountGroup::orderBy("order_level")->get();
    $data = array();
    foreach ($query as $row) {
      $data[] = array(
        "label" => $row->code . "-" . (config_language == "en" ? $row->name_en : $row->name_kh),
        "value" => $row->id,
      );
    }
    return $data;
  }
  
  public static function getSingleOpt(){
    $query = AccountGroup::whereNotIn("status", [4])->orWhereNull("status")->get();
    $data = array();
    foreach($query as $row){
      $data[] = array(
        "label" => $row->code.':'.$row->name_kh,
        "value" => $row->code,
      );
    }
    return $data;
  }

  public static function getAccountGroups($filter)
  {
    $data = array();
    $query = AccountGroup::orderBy($filter["sort"], $filter["order"]);
    $whereClause = $query;
    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
    $whereClause->limit($filter["limit"]);

    if ($filter["search_field"]) {
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if (array_key_exists('account_type_id', $dataFields)) {
        $whereClause->Where("id", $dataFields["account_type_id"]["value"]);
      }
      if (array_key_exists('code', $dataFields)) {
        $whereClause->Where("code", $dataFields["code"]["value"]);
      }
      if (array_key_exists("name_kh", $dataFields)) {
        $whereClause->Where("name_kh", $dataFields["name_kh"]);
      }
      if (array_key_exists("name_en", $dataFields)) {
        $whereClause->Where("name_en", $dataFields["name_en"]);
      }
    }
    $results = collect($whereClause->get());
    foreach ($results as $row) {
      $data[] = array(
        'id' => $row->id,
        'account_type_group' => $row->AccountTypeGroup->name_kh,
        'account_type_group_id' => array(
          "label" => $row->AccountTypeGroup->name_kh,
          "value" => $row->AccountTypeGroup->id
        ),
        'code' => $row->code,
        "name_en" => $row->name_en,
        "name_kh" => $row->name_kh,
        'description' => $row->description,
        'order_level' => $row->order_level,
      );
    }
    return $data;
  }
  public static function getCount($filter)
  {
    $query = AccountGroup::orderBy($filter["sort"], $filter["order"]);
    $whereClause = $query;
    $whereClause->whereNotIn("status", [4])->orWhere("status", null);
    if ($filter["search_field"]) {
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if (array_key_exists('account_type_id', $dataFields)) {
        $whereClause->Where("account_type_id", $dataFields["account_type_id"]["value"]);
      }
    }
    $total = $whereClause->count();
    return $total;
  }
}
