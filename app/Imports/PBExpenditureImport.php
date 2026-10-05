<?php

namespace App\Imports;

use App\Models\Modules\BudgetArrangement\PBExpenditure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use Auth;
class PBExpenditureImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        $queryCheck = PBExpenditure::where("id", (int)$row["id"])->first();
        // if($row["chapter_id"]!="" && $row["account_id"]!="" && $row["sub_account_id"]!=""){
        PBExpenditure::insert([
            'id' => $row["id"],
            'planning_id' => config_planning_year,
            'entity_id' => $row["entity_id"],
            'account_group_id' => $row["account_group_id"],
            'account_id' => $row["account_id"],
            'sub_account_id' => $row["sub_account_id"],
            'imple_income_budget' => $row["imple_income_budget"],
            'annual_credit' => $row["annual_credit"],
            'review_credit' => $row["review_credit"],
            'new_credit' => $row["new_credit"],
            'imple_budget_semester_1' => $row["imple_budget_semester_1"],
            'imple_estimate_budget_semester_2' => $row["imple_estimate_budget_semester_2"],
            'total_estimate_budget' => $row["total_estimate_budget"],
            'percent_credit_budget_rule' => (int)$row["percent_credit_budget_rule"],
            'new_credit_budget' => $row["new_credit_budget"],
            'total_budget_plan' => $row["total_budget_plan"],
            'percentage_credit_budget_plan' => (int)$row["percentage_credit_budget_plan"],
            'percentage_new_credit_rule_plan' => (int)$row["percentage_new_credit_rule_plan"],
            'total_compare_year' => $row["total_compare_year"],
            'percentage_compare_credit_rule' => (int)$row["percentage_compare_credit_rule"],
            'non_regular_expense' => $row["non_regular_expense"],
            'description_inc_dec' => $row["description_inc_dec"],
            'created_by' => Auth::user()->id,
            'is_delete' => 0,
        ]);
        // }
    }
}