<?php

namespace App\Models\Modules\BudgetArrangement\Costing;

use Illuminate\Database\Eloquent\Model;

class Costing extends Model
{
  protected $table = 'mef_costing';
  protected $fillable = [
                          'code_program',
                          'account_group',
                          'sequence_no',
                          'code_costing_item',
                          'code_cluster_act',
                          'account_id',
                          'account_code',
                          'sub_account_id',
                          'code_sub_acc',
                          'item_group',
                          'item_name_kh',
                          'costing_type',
                          'unit_id',
                          'qty',
                          'currency',
                          'unit_price',
                          'total_amount',
                          'time_per_year',
                          'total_annual_amount',
                          'month',
                          'expense_type',
                          'remark'
                        ];
                        
  public $timestamps = false;
}
