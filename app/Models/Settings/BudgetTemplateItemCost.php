<?php

namespace App\Models\Settings;
use Illuminate\Database\Eloquent\Model;

class BudgetTemplateItemCost extends Model
{
  protected  $primaryKey = 'id';
  
  protected $table = 'mef_budget_template_item_cost';
  protected $fillable = [
                          "budget_template_id",
                          "account_group_id",
                          "account_id",
                          "sub_account_id",
                          "code",
                          "item_id",                
                          "name_en",
                          "name_kh",
                          "unit_id",
                          "cost",
                          "qty",
                          "unit_price",
                          "total_amount",
                          "time_annual",
                          "total_annual_amount",
                          "month",
                          "is_reg_exp",
                          "expense_type",
                          'order_level',
                          "is_active",
                          "status",
                          "created_by",
                          "modified_by",
                          "created_at",
                          "updated_at",
                        ];
  public $timestamps = true;
  
  public function Unit(){
    return $this->belongsTo(Unit::class, 'unit_id');
  }
  public function AccountGroup(){
    return $this->belongsTo(AccountGroup::class, 'account_group_id');
  }
  public function Account(){
    return $this->belongsTo(Account::class, 'account_id');
  }
  public function SubAccount(){
    return $this->belongsTo(Account::class, 'sub_account_id');
  }

  public static function getBudgetTemplateItemCosts($filter){
    $data = array();
    $query = BudgetTemplateItemCost::orderBy($filter["sort"], $filter["order"]);
    $whereClause = $query;
    $whereClause->whereNotIn("status", [4])->orWhereNull("status");
    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);       
    $whereClause->limit($filter["limit"]);
    if(isset($filter["search_field"])){
      for($i=0 ; $i < count($filter["search_field"]); $i++){
        $field = array_key_first($filter["search_field"][$i]);
        if (in_array($field, $dataFields)) {
          $whereClause->orWhere($field, "Like","%".$filter["search_field"][$i][$field]."%");
        }
      }
    }
    if(isset($filter["data"]["data_info"])){
      $whereClause->where("budget_template_id", $filter["data"]["data_info"]["id"]);
    }
    $results = collect($whereClause->get());
    foreach($results as $row){
      $data[] = array(
        "id" => $row->id,
        "budget_template_id" => $row->budget_template_id,
        "account_group" => $row->AccountGroup->code."-".$row->AccountGroup->name_kh,
        "account_group_id" => array(["label"=>$row->AccountGroup->code."-".$row->AccountGroup->name_kh, "value"=> $row->AccountGroup->id]),
        "account" => $row->Account->code."-".$row->Account->name_kh,
        "account_id" => array(["label"=>$row->Account->code."-".$row->Account->name_kh, "value"=> $row->Account->id]),
        "sub_account" => $row->SubAccount->code."-".$row->SubAccount->name_kh,
        "sub_account_id" => array(["label"=>$row->SubAccount->code."-".$row->SubAccount->name_kh, "value"=> $row->SubAccount->id]),
        //"sub_account_id" => $row->sub_account_id,
        "code" => $row->code,
        "item_id" => $row->item_id,
        "name_en" => $row->name_en,
        "name_kh" => $row->name_kh,
        "unit" => $row->Unit->name_kh,
        "unit_id" => array(["label"=>$row->Unit->name_kh, "value"=> $row->Unit->unit_id]),
        "cost" => $row->cost,
        "qty" => $row->qty,
        "unit_price" => $row->unit_price,
        "total_amount" => $row->total_amount,
        "time_annual" => $row->time_annual,
        "total_annual_amount" => $row->total_annual_amount,
        "month" => $row->month,
        "is_reg_exp" => $row->is_reg_exp,
        "expense_type" => $row->expense_type,
        'order_level',
        "is_active" => $row->is_active,
        "status" => $row->status,
        "created_by" => $row->created_by,
        "modified_by" => $row->modified_by,
        "created_at" => $row->created_at,
        "updated_at" => $row->updated_at,
      );
    }
    return $data;
  }

  public static function getCount($filter){
    $data = array();
    $query = BudgetTemplateItemCost::orderBy($filter["sort"], $filter["order"]);
    $whereClause = $query;
    $whereClause->whereNotIn("status", [4])->orWhereNull("status");
    if(isset($filter["search_field"])){
      for($i=0 ; $i < count($filter["search_field"]); $i++){
        $field = array_key_first($filter["search_field"][$i]);
        if (in_array($field, $dataFields)) {
          $whereClause->orWhere($field, "Like","%".$filter["search_field"][$i][$field]."%");
        }
      }
    }
    if(isset($filter["data"]["data_info"])){
      $whereClause->where("budget_template_id", $filter["data"]["data_info"]["id"]);
    }
    $total = $whereClause->count();
    return $total;
  }
}