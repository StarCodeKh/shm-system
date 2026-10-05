<?php

namespace App\Models\Modules\BudgetArrangement;

use Illuminate\Database\Eloquent\Model;

class PIPInvDetail extends Model
{
    protected $table = 'mef_pip_inv_detail';
    protected $fillable = [
                            'pip_inv_id',
                            'attribute_en',
                            'attribute_kh',
                            'est_costing_year_0',
                            'est_costing_year_1',
                            'est_costing_year_2',
                            's_fin_year_0',
                            's_fin_year_1',
                            's_fin_year_2',
                            'add_req_fin_year_0',
                            'add_req_fin_year_1',
                            'add_req_fin_year_2',
                        ];
    public $timestamps = true;
}
