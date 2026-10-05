<?php

namespace App\Models\Modules\BudgetArrangement;

use Illuminate\Database\Eloquent\Model;

class CeilingDataEntity extends Model
{
    protected $table = 'mef_ceiling_data';
    protected $fillable = [
                            'ceiling_entity_id',
                            'ceiling_exp_group',
                            'ceiling_exp_type',
                            'ceiling_rule',
                            'ceiling_rule',
                            'reg_expense',
                            'non_reg_expense',
                            'ceiling_tran_year_0',
                            'ceiling_tran_year_1',
                            'ceiling_tran_year_2',
                            'ceiling_tran_year_3',
                            'increase_rate_year_0',
                            'increase_rate_year_1',
                            'increase_rate_year_2',
                            'increase_rate_year_3',
                            'remark',
                            'created_by',
                            'modified_by'
                        ];
    public $timestamps = true;
}
