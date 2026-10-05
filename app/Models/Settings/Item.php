<?php

namespace App\Models\Settings;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
  protected $table = 'mef_item';
  protected $fillable = [
                          "account_id",
                          "sub_account_id",
                          "category_id",
                          "sub_category_id",
                          "code",                      
                          "name_en",
                          "name_kh",
                          "default_unit_id",
                          'order_level',
                          "is_active",
                          "status",
                          "created_by",
                          "modified_by",
                        ];
  public $timestamps = true;

  public function ItemCategory(){
    return $this->belongsTo(ItemCategory::class, 'category_id');
  }
  public function Account(){
    return $this->belongsTo(Account::class, 'account_id');
  }
  public function SubAccount(){
    return $this->belongsTo(Account::class, 'sub_account_id');
  }

  public static function getItems(){
    $data = array();
    $query = Item::orderBy("order_level");
    $whereClause = $query;
    $whereClause->whereNotIn("status", [4])->orWhere("status", null);
    $queryResult = collect($whereClause->get());
    foreach($queryResult as $row){
      $data[] = array(
        "label" => (config_language=="en")?$row->name_en:$row->name_kh,
        "value" => $row->id,
      );
    }
    
    return $data;
  }

  public static function getItem($filter){
    $data = array();
    $queryObj = Item::orderBy($filter["sort"], $filter["order"]);
    $whereClause = $queryObj;
    $whereClause->whereIn("status", [13])->orWhere("status", null);
    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);       
    $whereClause->limit($filter["limit"]);
    if($filter["search_field"]){
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if (array_key_exists('account_id', $dataFields)) {
        $whereClause->Where("id", $dataFields["account_id"]["value"]);
      }
      if (array_key_exists('sub_account_id', $dataFields)) {
        $whereClause->Where("sub_account_id", $dataFields["sub_account_id"]["value"]);
      }
      if (array_key_exists('category_id', $dataFields)) {
        $whereClause->Where("category_id", $dataFields["category_id"]["value"]);
      }
    }
    $query = collect($whereClause->get());

    foreach($query as $row){ 
      $data[] = array(
        "id" => $row->id,
        "account_id" => array(
          "label" => $row->Account?$row->Account->code."-".$row->Account->name_kh:"",
          "value" => $row->account_id
        ),
        "sub_account_id" => array(
          "label" => $row->SubAccount?$row->SubAccount->code."-".$row->SubAccount->name_kh:"",
          "value" => $row->sub_account_id
        ),
        'category_id' => array(
            "label" => $row->ItemCategory?$row->ItemCategory->name_kh:"",
            "value" => $row->category_id
          ),
        "category" => $row->ItemCategory?$row->ItemCategory->name_kh:"",
        "sub_category_id" => $row->sub_category_id,
        "code" => $row->code,                      
        "name_en" => $row->name_en,
        "name_kh" => $row->name_kh,
        "default_unit_id" => $row->default_unit_id,
        'order_level',
        "is_active" => $row->is_active,
        "status" => $row->status,
        "created_by" => $row->created_by,
        "modified_by" => $row->modified_by,
      );
    }
    return $data;
  }

  public static function getCount($filter){
    $queryObj = Item::orderBy($filter["sort"], $filter["order"]);
    $whereClause = $queryObj;
    $whereClause->whereIn("status", [13])->orWhere("status", null);
    if($filter["search_field"]){
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if (array_key_exists('account_id', $dataFields)) {
        $whereClause->Where("id", $dataFields["account_id"]["value"]);
      }
      if (array_key_exists('sub_account_id', $dataFields)) {
        $whereClause->Where("sub_account_id", $dataFields["sub_account_id"]["value"]);
      }
      if (array_key_exists('category_id', $dataFields)) {
        $whereClause->Where("category_id", $dataFields["category_id"]["value"]);
      }
    }

    $total = $whereClause->count();
    return $total;
  }
}