<?php

namespace App\Models\Modules\BudgetArrangement;

use Illuminate\Database\Eloquent\Model;

class CeilingRule extends Model
{
    protected $table = 'mef_ceiling_rule';
    protected $fillable = [
                            'planning_id',
                            "name_en",
                            // 'ceiling_exp_type',
                            "name_kh",
                            'issue_date',
                            'authorize_by',
                            'approved_by',
                            'status',
                            'remark',
                            'created_by',
                            'modified_by'
                        ];
    public $timestamps = true;
}
