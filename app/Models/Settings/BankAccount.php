<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
  protected $table = 'bank_account';
  protected  $primaryKey = 'id';
  protected $fillable = [
    "name_en",
    "name_kh",
    'account_group',
    'account_name',
    'account_type',
    'account_number',
    'open_balance',
    'currency',
    'code',
    'address',
    'remark',
    'created_by',
    'modified_by',
    'is_default',
    'issue_date'
  ];
  public $timestamps = true;

  public static function dataFields()
  {
    // Loop Data Field From Table to put in array for mapping data field to search data table
    $data["tables"] = DB::select('show columns from ' . env("DB_PREFIX") . (new BankAccount)->getTable());
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
    $query = BankAccount::orderBy("order_level")->whereNotIn("status", [4])->orWhereNull("status")->get();
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
    $query = BankAccount::orderBy($filter["sort"], $filter["order"]);
    $whereClause = $query;
    $whereClause->whereNotIn("status", [4])->orWhereNull("status");
    if (isset($filter["search_field"])) {
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
    $whereClause = BankAccount::checkQuery($filter);
    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
    $whereClause->limit($filter["limit"]);
    $data = collect($whereClause->get());
    return $data;
  }

  public static function getTotal($filter)
  {
    $whereClause = BankAccount::checkQuery($filter);
    $total = $whereClause->count();
    return $total;
  }
}
