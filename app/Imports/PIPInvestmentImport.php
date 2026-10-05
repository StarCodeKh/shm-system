<?php

namespace App\Imports;

use App\Models\Modules\BudgetArrangement\PIPInvestment;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Auth;
class PIPInvestmentImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        if(isset($row["id"]) && $row["id"]!=''){
            $queryCheck = PIPInvestment::where("id", (int)$row["id"])->first();
            if(!$queryCheck){
                PIPInvestment::insert([
                    "id"=> $row["id"],
                    "planning_id"=> config_planning_year,
                    "entity_id"=> $row["entity_id"],
                    "sub_program_id"=>$row["sub_program_id"],
                    "cluster_activity_id"=>isset($row["cluster_activity_id"])?$row["cluster_activity_id"]:0,
                    "pip_no"=>$row["pip_no"],
                    "project_status_id"=>$row["project_status"],
                    "project_type_id"=>$row["project_type"],
                    "project_name_en"=>$row["project_name_en"],
                    "project_name_kh"=>$row["project_name_kh"],
                    "currency_exchange_id"=>1,
                    "exchange_rate"=>4000,
                    "column_type"=>null,
                    "status"=>1,
                    "est_costing_year_0"=>$row["est_costing_year_0"],
                    "est_costing_year_1"=>$row["est_costing_year_1"],
                    "est_costing_year_2"=>$row["est_costing_year_2"],
                    "s_fin_year_0"=>$row["s_fin_year_0"],
                    "s_fin_year_1"=>$row["s_fin_year_1"],
                    "s_fin_year_2"=>$row["s_fin_year_2"],
                    "add_req_fin_year_0"=>$row["add_req_fin_year_0"],
                    "add_req_fin_year_1"=>$row["add_req_fin_year_1"],
                    "add_req_fin_year_2"=>$row["add_req_fin_year_2"],
                    "grading_no" => $row["grading_no"],
                    "finance_resource_id"=>$row["finance_resource_id"],
                    "created_by"=>Auth::user()->id
                ]);
            }
        }
    }
}
