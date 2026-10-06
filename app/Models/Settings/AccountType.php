<?php

namespace App\Models\Settings;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;
use DB;
use Spatie\Permission\Traits\HasRoles;
use CommonService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class AccountType extends Model implements Auditable, FromCollection, WithHeadings
{
  use \OwenIt\Auditing\Auditable, HasRoles;
  protected $table = 'account_type';
  protected $guarded = [];
  protected $primaryKey = 'id';
  protected $fillable = [];
  public $timestamps = false;

  public function AccountTypeGroup()
  {
    return $this->belongsTo(AccountTypeGroup::class, 'account_type_group_id');
  }

  // public static function
  public static function dataFields()
  {
    $data["tables"] = DB::select('show columns from ' . DB::getTablePrefix() . (new AccountType)->getTable());
    $str = "";
    for ($i = 0; $i < count($data["tables"]); $i++) {
      if ($data["tables"][$i]->Null == "NO" && $data["tables"][$i]->Field != 'id') {
        $str .= $data["tables"][$i]->Field . ",";
      }
    }
    $newArr = explode(",", $str);
    array_pop($newArr);
    $dataFields = $newArr;
    return $dataFields;
  }

  public static function getSingleOpt(){
    $query = AccountType::whereNotDeleted()->get();
    $data = array();
    foreach($query as $row){
      $data[] = array(
        "label" => $row->name_en,
        "value" => $row->type_code
      );
    }
    return $data;
  }

  public static function formAttributes(){
    $formAttributes = array();
    $data["tables"] = DB::select('show columns from ' . DB::getTablePrefix() . (new AccountType)->getTable());
    for ($i = 0; $i < count($data["tables"]); $i++) {
      if ($data["tables"][$i]->Field != "id" && $data["tables"][$i]->Field != "created_by" && $data["tables"][$i]->Field != "modified_by" && $data["tables"][$i]->Field != "created_at" && $data["tables"][$i]->Field != "updated_at") {
        $formAttributes[] = array(
          "name" => $data["tables"][$i]->Field,
          "type" => CommonService::convertDataType($data["tables"][$i]),
          "required" => $data["tables"][$i]->Null == "NO" ? true : false,
        );
      }
    }
    return $formAttributes;
  }

  public static function getAccTypes()
  {
    $query = AccountType::orderByLevel()->whereNotDeleted()->get();
    $data = array();
    foreach ($query as $row) {
      $data[] = array(
        "label" => $row->name_kh,
        "value" => $row->id,
      );
    }
    return $data;
  }

  public static function getAccTypeCode(){
    $query = AccountType::orderByLevel()->whereNotDeleted()->get();
    $data = array();
    foreach($query as $row){
      $data[] = array(
        "label" => $row->type_code.': '.$row->name_en,
        "value" => $row->type_code,
      );
    }
    return $data;
  }

  public static function checkQuery($filter){
    $whereClause = "";
    $query = AccountType::orderByFilter($filter);
    $whereClause = $query;
    if ($filter["search_field"]) {
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if (isset($filter["search_field"])) {
        for ($i = 0; $i < count($filter["search_field"]); $i++) {
          $field = array_key_first($filter["search_field"][$i]); //array('key1', 'key2', 'key3');
          if (array_key_exists($field, $dataFields)) {
            if (isset($filter["search_field"][$i][$field]["value"])) {
              $whereClause->Where($field, 'Like', '%' . $filter["search_field"][$i][$field]["value"] . '%');
            } else {
              $whereClause->Where($field, 'Like', '%' . $filter["search_field"][$i][$field] . '%');
            }
          }
        }
      }
    }

    $whereClause->whereNotDeleted();
    return $whereClause;
  }

  public static function getData($filter)
  {
    $data = [];
    $whereClause = AccountType::checkQuery($filter);
    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
    $whereClause->limit($filter["limit"]);
    $results = collect($whereClause->get());
    foreach ($results as $row) {
      $data[] = array(
        'id' => $row->id,
        'type_code' => $row->type_code,
        "name_en" => $row->name_en?$row->name_en:"",
        "name_kh" => $row->name_kh?$row->name_kh:"",
        'description' => $row->description,
        'order_level' => $row->order_level,
      );
    }
    return $data;
  }

  public static function getTotal($filter)
  {
    $total = 0;
    $whereClause = AccountType::checkQuery($filter);
    $total = $whereClause->count();
    return $total;
  }

  public function collection(): \Illuminate\Support\Enumerable
  {
    $filter = array(
      "sort" => "id",
      "order" => "asc",
      "page_number" => 1,
      "limit" => 100,
      "search_field" => array()
    );
    $data = [];
    $whereClause = AccountType::checkQuery($filter);
    $whereClause->select("id", "type_code", "name_en", "name_kh", "description", "order_level");
    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
    $whereClause->limit($filter["limit"]);
    $results = collect($whereClause->get());
    return $results;
  }

    public function headings(): array
    {
        return [
            'id',
            'type_code',
            'name_en',
            'name_kh',
            'description',
            'order_level'
        ];
    }
}
