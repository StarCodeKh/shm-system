<?php

namespace App\Models\Modules\BudgetMonitoring;
use App\Models\Settings\Account;
use App\Models\Settings\AccountGroup;
use Illuminate\Database\Eloquent\Model;

class RevProgramDetail extends Model
{
  protected $table = 'mef_revenue_program_detail';
  protected $fillable = [
    "rev_pro_id",
    "account_group_id",
    "account_id",
    "sub_account_id",
    "finance_law",
    "annual_program_plan",
    "annual_percentage_law",
    "s1_total_bp",
    "s1_bp_jan",
    "s1_bp_feb",
    "s1_bp_mar",
    "s1_total_est_imp",
    "s1_est_imp_jan",
    "s1_est_imp_feb",
    "s1_est_imp_mar",
    "s2_total_bp",
    "s2_bp_apr",
    "s2_bp_may",
    "s2_bp_jun",
    "s2_total_adj",
    "s2_adj_apr",
    "s2_adj_may",
    "s2_adj_jun",
    "s2_total_est_imp",
    "s2_est_imp_apr",
    "s2_est_imp_may",
    "s1_est_imp_jun",
    "s3_total_pb",
    "s3_bp_jul",
    "s3_bp_aug",
    "s3_bp_sep",
    "s3_total_est_imp",
    "s3_est_imp_jul",
    "s3_est_imp_aug",
    "s3_est_imp_sep",
    "s4_total_bp",
    "s4_bp_oct",
    "s4_bp_nov",
    "s4_bp_dec",
    "s4_total_adj",
    "s4_adj_oct",
    "s4_adj_nov",
    "s4_adj_dec",
    "s4_total_est_imp",
    "s4_est_imp_oct",
    "s4_est_imp_nov",
    "s4_est_imp_dec",
    "status",
    "remark",
    "created_at",
    "updated_at",
  ];
            
  public $timestamps = true;

  public function AccountGroup(){
    return $this->belongsTo(AccountGroup::class,'account_group_id');
  }
  public function Account(){
    return $this->belongsTo(Account::class,'account_id');
  }
  public function SubAccount(){
    return $this->belongsTo(Account::class,'sub_account_id');
  }
}