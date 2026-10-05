<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class FinanceRule extends Model
{
  protected $table = 'mef_finance_rule';
  protected  $primaryKey = 'id';
  protected $fillable = [
                          "name_en",
                          "name_kh",
                          'order_level',
                          'is_active',
                          'status',
                            'created_by',
                            'modified_by'
                        ];
  public $timestamps = false;

  public static function getFinanceRule(){
    $query = FinanceRule::whereNotIn("status", [4])->orWhereNull("status")->orderBy("order_level")->get();
    $data = array();
    foreach($query as $row){
      $data[] = array(
        "label" => $row->name_kh,
        "value" => $row->id,
      );
    }
    return $data;
  }
}