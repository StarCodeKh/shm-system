<?php

namespace App\Imports;

use App\Models\Modules\BudgetMonitoring\RevProgram;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use Auth;
class BMRevProgramImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        // dd($row["id"]);
        $id = $row["id"];
        $queryCheck = RevProgram::where("id", $id)->first();
        if(!$queryCheck){
            if($row["entity_id"] != ""){
                DB::table("mef_revenue_program")->insert([
                    "id"=>$row["id"],
                    "planning_id" => config_planning_year,
                    "entity_id" => $row["entity_id"],
                    // 'currency' => $row["currency"],
                    // 'currency_id' => $row["currency_id"],
                    // 'exchange_rate' => $row["exchange_rate"],
                    "created_by" => Auth::user()->id,
                ]);
            }
        }

        $queryDetailCheck = DB::table("mef_revenue_program_detail")
                            ->where("id", (int)$row["id"])
                            ->first();
        if(!$queryDetailCheck){
            if($row["rev_pro_id"] !="")
                DB::table("mef_revenue_program_detail")->insert([
                    "id"=>$row["id"],
                    "rev_pro_id"=>$row["rev_pro_id"],
                    "account_group_id"=>$row["account_group_id"],
                    "account_id"=>$row["account_id"],
                    "sub_account_id"=>$row["sub_account_id"],
                    "finance_law"=>$row["finance_law"],
                    "annual_program_plan"=>$row["annual_program_plan"],
                    "annual_percentage_law"=>$row["annual_percentage_law"],
                    "s1_total_bp"=>$row["s1_total_bp"],
                    "s1_bp_jan"=>$row["s1_bp_jan"],
                    "s1_bp_feb"=>$row["s1_bp_feb"],
                    "s1_bp_mar"=>$row["s1_bp_mar"],
                    "s1_total_est_imp"=>$row["s1_total_est_imp"],
                    "s1_est_imp_jan"=>$row["s1_est_imp_jan"],
                    "s1_est_imp_feb"=>$row["s1_est_imp_feb"],
                    "s1_est_imp_mar"=>$row["s1_est_imp_mar"],
                    "s2_total_bp"=>$row["s2_total_bp"],
                    "s2_bp_apr"=>$row["s2_bp_apr"],
                    "s2_bp_may"=>$row["s2_bp_may"],
                    "s2_bp_jun"=>$row["s2_bp_jun"],
                    "s2_total_adj"=>$row["s2_total_adj"],
                    "s2_adj_apr"=>$row["s2_adj_apr"],
                    "s2_adj_may"=>$row["s2_adj_may"],
                    "s2_adj_jun"=>$row["s2_adj_jun"],
                    "s3_total_pb"=>$row["s3_total_pb"],
                    "s3_bp_jul"=>$row["s3_bp_jul"],
                    "s3_bp_aug"=>$row["s3_bp_aug"],
                    "s3_bp_sep"=>$row["s3_bp_sep"],
                    "s3_total_est_imp"=>$row["s3_total_est_imp"],
                    "s3_est_imp_jul"=>$row["s3_est_imp_jul"],
                    "s3_est_imp_aug"=>$row["s3_est_imp_aug"],
                    "s3_est_imp_sep"=>$row["s3_est_imp_sep"],
                    "s4_total_bp"=>$row["s4_total_bp"],
                    "s4_bp_oct"=>$row["s4_bp_oct"],
                    "s4_bp_nov"=>$row["s4_bp_nov"],
                    "s4_bp_dec"=>$row["s4_bp_dec"],
                    "s4_total_adj"=>$row["s4_total_adj"],
                    "s4_adj_oct"=>$row["s4_adj_oct"],
                    "s4_adj_nov"=>$row["s4_adj_nov"],
                    "s4_adj_dec"=>$row["s4_adj_dec"],
                    "remark"=>$row["remark"]
                ]);
        }
    }
}