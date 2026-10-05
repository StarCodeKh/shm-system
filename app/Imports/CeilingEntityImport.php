<?php

namespace App\Imports;

use App\Models\Modules\BudgetArrangement\CeilingEntity;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use Auth;

class CeilingEntityImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        // dd($row);
        $queryCheck = CeilingEntity::where("id", (int)$row["id"])->first();
        if(!$queryCheck){
            if($row["entity_id"]!=''){
                CeilingEntity::insert([
                    "planing_id"=>config_planning_year,
                    "ceiling_exp_group"=>$row["ceiling_exp_group"],
                    // "ceiling_exp_type"=>$row["ceiling_exp_type"],
                    "sub_program_id"=>$row["sub_program_id"],
                    "entity_id"=>$row["entity_id"],
                    "entity_parent_id"=>$row["entity_parent_id"],
                    "ceiling_rule"=>$row["ceiling_rule"],
                    "reg_expense"=>$row["reg_expense"],
                    "non_reg_expense"=>$row["non_reg_expense"],
                    "ceiling_tran_year_0"=>$row["ceiling_tran_year_0"],
                    "ceiling_tran_year_1"=>$row["ceiling_tran_year_1"],
                    "ceiling_tran_year_2"=>$row["ceiling_tran_year_2"],
                    // "ceiling_tran_year_3"=>$row["ceiling_tran_year_3"],
                    "increase_rate_year_0"=>$row["increase_rate_year_0"],
                    "increase_rate_year_1"=>$row["increase_rate_year_1"],
                    "increase_rate_year_2"=>$row["increase_rate_year_2"],
                    // "increase_rate_year_3"=>$row["increase_rate_year_3"],    
                    "remark"=>$row["remark"],
                    "created_by"=>Auth::user()->id
                ]);
            }
        }
    }
}
