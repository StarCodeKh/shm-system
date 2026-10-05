<?php

namespace App\Imports;

use App\Models\Modules\BudgetArrangement\CeilingDataEntity;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use Auth;

class CeilingDataEntityImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        // dd($row);
        if($row["ceiling_exp_type"]!=''){
            CeilingDataEntity::insert([
                "ceiling_entity_id"=>$row["ceiling_entity_id"],
                "ceiling_exp_group"=>$row["ceiling_exp_group"],
                "ceiling_exp_type"=>$row["ceiling_exp_type"],
                "ceiling_rule"=>$row["ceiling_rule"],
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
