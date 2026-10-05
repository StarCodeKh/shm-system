<?php

namespace App\Imports;

use App\Models\Modules\BudgetArrangement\PBRevenue;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Auth;
class PBRevenueImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        // dd(count($row));
        $queryCheck = PBRevenue::where("id", (int)$row["id"])->first();
        if(!$queryCheck){
            PBRevenue::insert([
                'id' => $row["id"],
                'planning_id' => config_planning_year,
                'entity_id' => $row["entity_id"],
                'account_id' => $row["account_id"],
                'sub_account_id' => (int)$row["sub_account_id"],
                'imple_rev_budget' => $row["imple_rev_budget"],
                'rule_basic_rev_plan' => $row["rule_basic_rev_plan"],
                'rule_qty_rev_plan' => (int)$row["rule_qty_rev_plan"],
                'rule_budget_credit' => $row["rule_budget_credit"],
                'imple_budget_semester_1' => $row["imple_budget_semester_1"],
                'imple_budget_semester_2' => $row["imple_budget_semester_2"],
                'annual_estimate' => $row["annual_estimate"],
                'percentage_rule' => $row["percentage_rule"],
                'basic_rev_plan' => $row["basic_rev_plan"],
                'qty_rev_plan' => (int)$row["qty_rev_plan"],
                'total_rev_plan' => $row["total_rev_plan"],
                'percentage_rev_plan' => $row["percentage_rev_plan"],
                'is_delete' => 0,
                'created_by' => Auth::user()->id,
            ]);
        }
    }
}