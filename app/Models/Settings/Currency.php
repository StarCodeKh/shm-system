<?php

namespace App\Models\Settings;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\OfficerPayable;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Model;
use DB;
use CommonService;

class Currency extends Model implements Auditable
{
  use \OwenIt\Auditing\Auditable;
  protected $table = 'currency';
  protected $primaryKey = 'id';
  protected $fillable = [];
  protected $guarded = [];
  public $timestamps = true;

  public function paymentType()
  {
    return $this->hasMany(PaymentType::class);
  }
  
  public function officerPayable()
  {
    return $this->hasMany(OfficerPayable::class);
  }

  public function expense()
  {
    return $this->hasMany(Expense::class);
  }

  public static function dataFields(){
    // Loop Data Field From Table to put in array for mapping data field to search data table
    $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new Currency)->getTable());
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
    $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new Currency)->getTable());
    for($i=0; $i < count($data["tables"]); $i++){
      if($data["tables"][$i]->Field != "id" && $data["tables"][$i]->Field != "created_by" && $data["tables"][$i]->Field != "modified_by" && $data["tables"][$i]->Field != "created_at" && $data["tables"][$i]->Field != "updated_at"){
        $formAttributes[] = array(
          "name" => $data["tables"][$i]->Field,
          "type" => CommonService::convertDataType($data["tables"][$i]),
          "required" => $data["tables"][$i]->Null=="NO"?true:false,
        );
      }
    }
    return $formAttributes;
  }

  public static function getSingleOpt(){
    // $query = Currency::orderByFilter($filter)->whereNotDeleted()->get();
    $query = Currency::whereNotDeleted()->get();
    $data = array();
    foreach($query as $row){
      $data[] = array(
        "label" => $row->label(),
        "value" => $row->id,
      );
    }
    return $data;
  }

  /** "៛ រៀល" / "$ Dollar": symbol plus the name in the user's language (X-Locale, see SetLocale) */
  public function label(): string
  {
    $name = app()->getLocale() === 'kh' ? ($this->name_kh ?: $this->name_en) : ($this->name_en ?: $this->name_kh);
    $symbol = $this->symbol_left ?: $this->symbol_right;
    return trim(($symbol ? $symbol.' ' : '').$name);
  }

  public static function checkQuery($filter){
    $query = Currency::orderByFilter($filter);
    $whereClause = $query;
    $whereClause->orderByFilter($filter);
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

    $whereClause->whereNotDeleted();
    return $whereClause;
  }

  public static function getData($filter){
    $whereClause = Currency::checkQuery($filter);
    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
    $whereClause->limit($filter["limit"]);
    $data = collect($whereClause->get());
    return $data;
  }

  public static function getTotal($filter){
    $whereClause = Currency::checkQuery($filter);
    $total = $whereClause->count();
    return $total;
  }
}