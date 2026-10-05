<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use DB;
use Auth;

class BSPAssignProgramImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row): \Illuminate\Database\Eloquent\Model|array|null
    {
        // // TRUNCATE TABLE `tbl_mef_subprogram_to_entity`
        // // TRUNCATE TABLE `tbl_mef_cluster_activity_to_entity`
        // // TRUNCATE TABLE `tbl_mef_activity_to_entity`
        // // TRUNCATE TABLE `tbl_mef_kpi_cluster_activity`
        // // TRUNCATE TABLE `tbl_mef_kpi_subprogram`
        // // TRUNCATE TABLE `tbl_mef_kpi_activity`

        // // *** Assign Sub Program, Cluster Activity, Activity
        // if($row["sub_program_id"]!='' && $row["cluster_activity_id"] == ''){
        //     // dd($row);
        //     BSPSubProgramToEntity::insert([
        //         'id'=>$row["id"],
        //         'sub_program_id'=>$row['sub_program_id'],
        //         'planning_id'=>$row['planning_id'],
        //         'entity_id'=>$row['entity_id'],
        //         'entity_member_id'=>$row['entity_member_id'],
        //         'created_by'=>Auth::user()->id,
        //     ]);
        // }

        // if($row["sub_program_id"]!='' && $row["cluster_activity_id"] != ''){
        //     BSPClusterActToEntity::insert([
        //         'id'=>$row["id"],
        //         'sub_prog_to_entity_id'=>$row['sub_prog_to_entity_id'],
        //         'planning_id'=>$row['planning_id'],
        //         'sub_program_id'=>$row['sub_program_id'],
        //         'cluster_activity_id'=>$row['cluster_activity_id'],
        //         'entity_id'=>$row['entity_id'],
        //         'entity_member_id'=>$row['entity_member_id'],
        //         'created_by'=>Auth::user()->id,
        //     ]);
        // }

        // if($row["cluster_activity_id"]!='' && $row["activity_id"] != ''){
        //     BSPActToEntity::insert([
        //         'id' => $row["id"],
        //         'activity_id'=>$row['activity_id'],
        //         // 'cluster_activity_id'=>$row['cluster_activity_id'],
        //         'planning_id'=>$row['planning_id'],
        //         'entity_id'=>$row['entity_id'],
        //         'entity_member_id'=>$row['entity_member_id'],
        //         'created_by'=>Auth::user()->id,
        //     ]);
        // }

        // **** Assign Indicator to Subprogram, Cluster Act, Activity
        if($row["subprog_to_entity_id"]!='' && $row["cluster_act_to_entity_id"] == ''){
            DB::table("mef_kpi_subprogram")->insert([
                "kpi_program_id" => null,
                "subprog_to_entity_id" => $row["subprog_to_entity_id"],
                "planning_id" => config_planning_year,
                "code" => null,
                "kpi_name_en" => $row["kpi_name_kh"],
                "kpi_name_kh" => $row["kpi_name_kh"],
                "order_level" => null,
                "attachment" => null,
                "year" => null,
                "progress" => null,
                "acheivement" => null,
                "reference_kpi_id" => null,
                "status" => 1,
                "created_by" => Auth::user()->id
            ]);
        }

        if($row["cluster_act_to_entity_id"]!='' && $row["id"] != ''){
            DB::table("mef_kpi_cluster_activity")->insert([
                "cluster_act_to_entity_id" => $row["cluster_act_to_entity_id"],
                "planning_id" => config_planning_year,
                "kpi_sub_program_id" => null,
                "code" => null,
                "kpi_name_en" => $row["kpi_name_kh"],
                "kpi_name_kh" => $row["kpi_name_kh"],
                "order_level" => null,
                "attachment" => null,
                "year" => null,
                "progress" => null,
                "acheivement" => null,
                "reference_kpi_id" => null,
                "status" => 1,
                "created_by" => Auth::user()->id
            ]);
        }
    }
}
