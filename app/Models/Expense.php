<?php

namespace App\Models;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\Expense;
use App\Models\Settings\Entity;
use Illuminate\Database\Eloquent\Model;
use App\Models\Settings\Status;
use App\Models\Settings\Account;
use App\Models\Settings\Currency;
use App\Models\Settings\PaymentMethod;
use DB;
use CommonService;
class Expense extends Model implements Auditable
{
  use \OwenIt\Auditing\Auditable;
  protected $table = 'expense';
  protected $primaryKey = 'id';
  protected $fillable = [];
  protected $guarded = [];
  public $timestamps = true;
  protected $casts = [
    'issue_date' => 'datetime:'.("Y-m-d"),
  ];

   public function status()
  {
    return $this->belongsTo(Status::class, "status");
  }

  public function currency()
  {
    return $this->belongsTo(Currency::class, "currency_id");
  }

  public function account()
  {
    return $this->belongsTo(Account::class, "account_code");
  }

  public function paymentMethod()
  {
    return $this->belongsTo(PaymentMethod::class, "payment_method_id");
  }
  public function entity()
  {
    return $this->belongsTo(Entity::class, "entity_id");
  }

  public static function dataFields(){
    // Loop Data Field From Table to put in array for mapping data field to search data table
    $data["tables"] = DB::select('show columns from '.env("DB_PREFIX").(new Expense)->getTable());
    $str = "";
    // dd($data["tables"]);
    for($i=0; $i < count($data["tables"]); $i++){
      if($data["tables"][$i]->Null == "NO" && $data["tables"][$i]->Field != 'id'){
        $str .= $data["tables"][$i]->Field.",";
      }
    }
    $newArr = explode(",", $str);
    array_pop($newArr);
    $dataFields = $newArr;
    return $dataFields;
  }

  public static function formAttributes(){
    $formAttributes = array();
    $data["tables"] = DB::select('show columns from '.env("DB_PREFIX").(new Expense)->getTable());
    for($i=0; $i < count($data["tables"]); $i++){
      if($data["tables"][$i]->Field != "id" && $data["tables"][$i]->Field != "created_by" && $data["tables"][$i]->Field != "modified_by" && $data["tables"][$i]->Field != "created_at" && $data["tables"][$i]->Field != "updated_at"){
        $formAttributes[] = array(
          "name" => $data["tables"][$i]->Field,
          "type" => CommonService::convertDataTypeWay1($data["tables"][$i]),
          "required" => $data["tables"][$i]->Null=="NO"?true:false,
        );
      }
    }
    return $formAttributes;
  }

  public static function getSingleOpt(){
    $query = Expense::orderBy($filter["sort"], $filter["order"])->whereNotIn("status", [4])->orWhereNull("status")->get();
    $data = array();
    foreach($query as $row){
      $data[] = array(
        "label" => $row->name_kh,
        "value" => $row->id,
      );
    }
    return $data;
  }

  public static function checkQuery($filter){
    $query = Expense::with("currency")->with("account")->with("status")->with("PaymentMethod")->with("entity");
    $whereClause = $query;
    $whereClause->orderBy($filter["sort"], $filter["order"]);
    $d = "";
    if($filter["search_field"]){
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if(isset($filter["search_field"])){
        for($i=0 ; $i < count($filter["search_field"]); $i++){
          $field = array_key_first($filter["search_field"][$i]);
          if (array_key_exists($field, $dataFields)) {
            if(isset($filter["search_field"][$i][$field]["value"])){
              $whereClause->Where($field, 'Like', '%'.$filter["search_field"][$i][$field]["value"].'%');
            }else{
              $whereClause->Where($field, 'Like', '%'.$filter["search_field"][$i][$field].'%');
            }
          }
        }
      }
    }

    $whereClause->whereNotIn("status", [4]);
    $whereClause->orWhereNull("status");
    return $whereClause;
  }

  public static function getData($filter){
    $whereClause = Expense::checkQuery($filter);
    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
    $whereClause->limit($filter["limit"]);
    $data = collect($whereClause->get());
    return $data;
  }

  public static function getTotal($filter){
    $whereClause = Expense::checkQuery($filter);
    $total = $whereClause->count();
    return $total;
  }

  public static function getMonthlyExpense(){
    $results = DB::table('expense')
              ->select('issue_date', DB::raw('SUM(amount) as total_amount'))
              ->whereYear('issue_date', date("Y"))
              ->groupBy(
                  DB::raw('YEAR(issue_date)'),
                  DB::raw('MONTH(issue_date)')
                )
                ->orderBy('issue_date')
                ->orderBy('issue_date')
              ->get();
    $data = array();
    foreach($results as $row){
      $data[] = $row->total_amount;
    }
    return $data;
  }
  public static function insertExpense($data)
  {
    $sql = "
        INSERT INTO tbl_expense
        (
            expense_title,
            account,
            payment_method_id,
            issue_date,
            category,
            amount,
            reference,
            description,
            entity_id,
            exchange_rate,
            currency_id,
            status,
            created_by,
            created_at
        )
        VALUES
        (
            :expense_title,
            :account,
            :payment_method_id,
            NOW(),
            :category,
            :amount,
            :reference,
            :description,
            :entity_id,
            :exchange_rate,
            :currency_id,
            :status,
            :created_by,
            NOW()
        )
    ";

    return DB::insert($sql, [
        'expense_title' => $data['expense_title'],
        'account'       => $data['account'],
        'payment_method_id'=> $data['payment_method_id'],
        'category'      => $data['category'],
        'amount'        => $data['amount'],
        'description'   => $data['description'] ?? null,
        'reference'     => $data['reference'] ?? null,
        'entity_id'     => $data['entity_id'],
        'exchange_rate' => $data['exchange_rate'],
        'currency_id'   => $data['currency_id'],
        'status'        => $data['status'],
        'created_by'    => $data['created_by']
    ]);
 }

}