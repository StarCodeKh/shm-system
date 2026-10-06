<?php

namespace App\Models\Modules\BudgetArrangement;
use App\Models\Modules\ProgramManagement\Program;
use App\Models\Modules\ProgramManagement\SubProgram;
use App\Models\Modules\ProgramManagement\ClusterActivity;
use App\Models\Modules\ProgramManagement\Activity;
use Illuminate\Database\Eloquent\Model;

class PBCosting extends Model
{
  protected $table = 'mef_costing_to_entity';
  protected $fillable = [
                          'planning_id',
                          'entity_id',
                          'sub_program_id',
                          'cluster_activity_id',
                          'activity_id',
                          "name_en",
                          "name_kh",
                          'order_level',
                          'is_active',
                          'status',
                          'created_by',
                          'modified_by',
                          'created_at',
                          'updated_at'
                        ];
                        
  public $timestamps = true;

  public static function getPBCostingByEntities($filter){
    $data = array();
    $queryPro = Program::orderByFilter($filter);
    $whereProClause = $queryPro;
    $whereProClause->offset(($filter["page_number"] - 1) * $filter["limit"]);       
    $whereProClause->limit($filter["limit"]);
    if($filter["search_field"]){
        $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
        $dataFields = $arraySingle;
        if (array_key_exists('program_id', $dataFields)) {
            $whereProClause->Where("id", $dataFields["program_id"]["value"]);
        }
        if (array_key_exists('entity_id', $dataFields)) {
            $whereProClause->Where("id", $dataFields["entity_id"]["value"]);
        }
    }
    $resultPro = collect($whereProClause->get());
    foreach($resultPro as $pro){
        $proId = $pro->id;
        $querySubPro = SubProgram::orderByLevel();
        $whereSubPro = $querySubPro;
        $whereSubPro->where("program_id", $proId);
        if($filter["search_field"]){
            $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
            $dataFields = $arraySingle;
            if (array_key_exists('sub_program_id', $dataFields)) {
                $whereProClause->Where("id", $dataFields["sub_program_id"]["value"]);
            }
            if (array_key_exists('entity_id', $dataFields)) {
                $whereProClause->Where("id", $dataFields["entity_id"]["value"]);
            }
        }
        $resultSubPro = collect($whereSubPro->get());
        $subProArr = array();
        foreach($resultSubPro as $subPro){
            $subProId = $subPro->id;
            $queryClusAct = ClusterActivity::orderByLevel();
            $whereClusAct = $queryClusAct;
            $whereClusAct->where("sub_program_id", $subProId);
            // $whereClusAct->offset(($filter["page_number"] - 1) * $filter["limit"]);
            // $whereClusAct->limit($filter["limit"]);
            if($filter["search_field"]){
                $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
                $dataFields = $arraySingle;
                if (array_key_exists('cluster_activity_id', $dataFields)) {
                    $whereProClause->Where("id", $dataFields["cluster_activity_id"]["value"]);
                }
                if (array_key_exists('entity_id', $dataFields)) {
                    $whereProClause->Where("id", $dataFields["entity_id"]["value"]);
                }
            }
            $resultClusAct = collect($whereProClause->get());
            $clusArr = array();
            foreach($resultClusAct as $clusAct){
                $clusActId = $clusAct->id;
                $queryAct = Activity::orderByLevel();
                $whereAct = $queryAct;
                $whereAct->where("cluster_activity_id", $clusActId);
                // $whereAct->offset(($filter["page_number"] - 1) * $filter["limit"]);       
                // $whereAct->limit($filter["limit"]);
                if($filter["search_field"]){
                    $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
                    $dataFields = $arraySingle;
                    if (array_key_exists('activity_id', $dataFields)) {
                        $whereProClause->Where("id", $dataFields["activity_id"]["value"]);
                    }
                }
                $resultAct = Collect($whereAct->get());
                $actArr = array();
                foreach($resultAct as $act){
                    $actId = $act->id;
                    // $queryCosting = PBCosting::orderByFilter($filter);
                    // $whereCostingClause = $queryCosting;
                    // // $whereCostingClause->where("activity_id");
                    // $whereCostingClause->where("is_active", 1);
                    // $whereCostingClause->whereNotIn("status", [1]);
                    // // $whereCostingClause->offset(($filter["page_number"] - 1) * $filter["limit"]);       
                    // // $whereCostingClause->limit($filter["limit"]);
                    // if($filter["search_field"]){
                    //     $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
                    //     $dataFields = $arraySingle;
                    //     if (array_key_exists("name_en", $dataFields)) {
                    //         $whereCostingClause->Where("name_en", "Like" , "%".$dataFields["name_en"]."%");
                    //     }
                    //     if (array_key_exists("name_kh", $dataFields)) {
                    //         $whereCostingClause->Where("name_kh", "Like" , "%".$dataFields["name_kh"]."%");
                    //     }
                    //     if (array_key_exists('entity_id', $dataFields)) {
                    //         $whereCostingClause->Where("id", $dataFields["entity_id"]["value"]);
                    //     }
                    // }
                    // $resultCosting = collect($whereCostingClause->get());
                    // foreach($resultCosting as $costing){
                        
                    // }
                    // Return Activity Data
                    $actArr[] = array(
                        "desc" => $act->code."-".$act->name_kh, // program name
                        "chapter" => "",
                        "account" => "",
                        "sub_account" => "",
                        "no" => "",
                        "line" => "",
                        "code_prog_act" => "10101",
                        "code_cluster_activity" => "",
                        "code_activity" => "",
                        "expense_type" => "",
                        "unit" => "",
                        "quantity" => "",
                        "unit_price" => "",
                        "total_amount" => "",
                        "time_year" => "",
                        "annual_amount" => "",
                        "month" => "",
                        "expense_type" => "",
                        "remark" => ""
                    );
                }

                // Return Cluster Activity Data
                $clusArr[] = array(
                    "desc" => $clusAct->code."-".$clusAct->name_kh, // program name
                    "chapter" => "",
                    "account" => "",
                    "sub_account" => "",
                    "no" => "",
                    "line" => "",
                    "code_prog_act" => "10101",
                    "code_cluster_activity" => "",
                    "code_activity" => "",
                    "expense_type" => "",
                    "unit" => "",
                    "quantity" => "",
                    "unit_price" => "",
                    "total_amount" => "",
                    "time_year" => "",
                    "annual_amount" => "",
                    "month" => "",
                    "expense_type" => "",
                    "remark" => "",
                    "children" => $actArr
                );
            }

            // Return SubProgram Data
            $subProArr[] = array(
                "desc" => $subPro->code."-".$subPro->structure_name_kh.':'.$subPro->name_kh, // program name
                "chapter" => "",
                "account" => "",
                "sub_account" => "",
                "no" => "",
                "line" => "",
                "code_prog_act" => "10101",
                "code_cluster_activity" => "",
                "code_activity" => "",
                "expense_type" => "",
                "unit" => "",
                "quantity" => "",
                "unit_price" => "",
                "total_amount" => "",
                "time_year" => "",
                "annual_amount" => "",
                "month" => "",
                "expense_type" => "",
                "remark" => "",
                "children" => $clusArr
            );
        }

        // Return Program Data
        $data[] = array(
            "desc" => $pro->name_kh, // program name
            "chapter" => "",
            "account" => "",
            "sub_account" => "",
            "no" => "",
            "line" => "",
            "code_prog_act" => "10101",
            "code_cluster_activity" => "",
            "code_activity" => "",
            "expense_type" => "",
            "unit" => "",
            "quantity" => "",
            "unit_price" => "",
            "total_amount" => "",
            "time_year" => "",
            "annual_amount" => "",
            "month" => "",
            "expense_type" => "",
            "remark" => "",
            "children" => $subProArr
        );
    }
    return $data;

  }
}