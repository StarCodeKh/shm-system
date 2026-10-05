<?php

namespace App\Imports;

use App\Models\Modules\BudgetArrangement\PBCosting;
use App\Models\Modules\BudgetArrangement\PBCostingDetail;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Auth;
class PBCostingImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        $queryCheck = PBCosting::where("id", (int)$row["id"])->first();
        if(!$queryCheck){
            if($row["costing_id"] == 0){
                PBCosting::insert([
                    'id' => $row["id"],
                    'planning_id' => config_planning_year,
                    'entity_id' => $row["entity_id"],
                    'sub_program_id' => $row["sub_program_id"],
                    'cluster_activity_id' => $row["cluster_activity_id"],
                    'activity_id' => $row["activity_id"],
                    'name_en' => $row["title_kh"],
                    'name_kh' => $row["title_kh"],
                    'order_level' => $row["order_level"],
                    'is_active' => 1,
                    'is_delete' => 0,
                    'created_by' => Auth::user()->id,
                ]);
            }
        }

        $qPBCostingDetail = PBCostingDetail::where("id", (int)$row["id"])->first();
        if(!$qPBCostingDetail){
            if($row["costing_id"] != 0 && $row["item_id"] !="" && $row["code_sub_acc"] !=""){
                PBCostingDetail::insert([
                    'id' => $row["id"],
                    'costing_id'=>$row["costing_id"],
                    'account_code'=>$row["account_code"],
                    'sub_account_code'=>$row["code_sub_acc"],
                    'line_no'=>$row["line_no"],
                    'item_id'=>$row["item_id"],
                    'unit_id'=>$row["unit_id"],
                    'cost'=>$row["unit_price"],
                    'unit_price'=>$row["unit_price"],
                    'total_amount'=>$row["total_amount"],
                    'time_annual'=>$row["time_annual"],
                    'total_annual_amount'=>$row["total_annual_amount"],
                    'name_en' => $row["title_kh"],
                    'name_kh' => $row["title_kh"],
                    'flag_type' => $row["costing_type"],
                    'month'=>$row["month"],
                    'expense_type'=>$row["expense_type"],
                    'currency_id'=>1,
                    'currency'=>$row["currency"],
                    'exchange_rate'=>0, //$row["exchange_rate"],
                    'remark'=>$row["remark"],
                    'is_delete'=>0,
                    'created_by'=> Auth::user()->id,
                ]);
            }
        }
    }
}