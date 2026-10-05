<?php

namespace App\Models\Modules\BudgetArrangement;

use Illuminate\Database\Eloquent\Model;
use App\Models\Modules\ProgramManagement\Program;
use App\Models\Modules\ProgramManagement\SubProgram;
use DB;
use CommonService;

class PIPInvestment extends Model
{
    protected $table = 'mef_pip_investment';
    protected $fillable = [
                            'entity_id',
                            'planning_id',
                            'program_id',
                            'sub_program_id',
                            'cluster_activity_id',
                            'pip_no',
                            'project_status_id',
                            'project_type_id',
                            'project_name_en',
                            'project_name_kh',
                            'currency_exchange_id',
                            'exchange_rate',
                            'column_type',
                            'status',
                            'est_costing_year_0',
                            'est_costing_year_1',
                            'est_costing_year_2',
                            's_fin_year_0',
                            's_fin_year_1',
                            's_fin_year_2',
                            'add_req_fin_year_0',
                            'add_req_fin_year_1',
                            'add_req_fin_year_2',
                            'finance_resource_id',
                            'grading_no',
                            'order_level',
                            "pip_4_strategy",
                            "pip_participant_project",
                            "pip_field",
                            "pip_subproject_location",
                            "pip_project_aim",
                            "pip_project_description",
                            "pip_resonable_project",
                            "pip_benefit_project",
                            "pip_effectivenes_environment",
                            "pip_gender_analysis",
                            "pip_project_imple_capacity",
                            "pip_project_imple_status",
                            "pip_partner_participation",
                            'created_by',
                            'modified_by'
                        ];
    public $timestamps = true;

    public function SubProgram(){
      return $this->belongsTo(SubProgram::class,'sub_program_id');
    }

    public static function getPIPInvestment($filter){
        $summary = array();
        for($i=0; $i <= 1-1; $i++){
            // $programs = Program::orderBy("order_level", "ASC")->get();
            $data = array();
            $queryPro = Program::orderBy($filter["sort"], $filter["order"]);
            $whereClause = $queryPro;
            $whereClause->whereNotIn("status", [4])->orWhere("status", null);
            $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);       
            $whereClause->limit($filter["limit"]);

            if($filter["search_field"]){
                $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
                $dataFields = $arraySingle;
                if (array_key_exists('program_id', $dataFields)) {
                    $whereClause->Where("id", $dataFields["program_id"]["value"]);
                }
                if (array_key_exists('entity_id', $dataFields)) {
                    $whereClause->Where("id", $dataFields["entity_id"]["value"]);
                }
            }
            $query = collect($whereClause->get());

            $summary_total_est_costing_year_0 = 0;
            $summary_total_s_fin_year_0 = 0;
            $summary_total_add_req_fin_year_0 = 0;

            $summary_total_est_costing_year_1 = 0;
            $summary_total_s_fin_year_1 = 0;
            $summary_total_add_req_fin_year_1 = 0;

            $summary_total_est_costing_year_2 = 0;
            $summary_total_s_fin_year_2 = 0;
            $summary_total_add_req_fin_year_2 = 0;

            foreach($query as $program){
                $programId = $program->id;
                $querySubPro = SubProgram::orderBy("order_level", "ASC");
                $clauseSubPro = $querySubPro;
                $clauseSubPro->where("program_id", $programId);

                if($filter["search_field"]){
                    $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
                    $dataFields = $arraySingle;
                    if (array_key_exists('program_id', $dataFields)) {
                        $clauseSubPro->Where("program_id", $dataFields["program_id"]["value"]);
                    }
                    if (array_key_exists('sub_program_id', $dataFields)) {
                        $clauseSubPro->Where("id", $dataFields["sub_program_id"]["value"]);
                    }
                    if (array_key_exists('entity_id', $dataFields)) {
                        $clauseSubPro->Where("identity_id", $dataFields["entity_id"]["value"]);
                    }
                }

                $resultSubPro = collect($clauseSubPro->get());
                $subProgs = array();
                
                $grand_total_est_costing_year_0 = 0;
                $grand_total_s_fin_year_0 = 0;
                $grand_total_add_req_fin_year_0 = 0;

                $grand_total_est_costing_year_1 = 1;
                $grand_total_s_fin_year_1 = 1;
                $grand_total_add_req_fin_year_1 = 1;

                $grand_total_est_costing_year_2 = 2;
                $grand_total_s_fin_year_2 = 2;
                $grand_total_add_req_fin_year_2 = 2;

                foreach($resultSubPro as $subProgram){
                    $subProgramId = $subProgram->id;
                    $queryPIPInv = DB::table("mef_pip_investment as pip")
                                        ->select("pip.*", "fr.id as finance_resource_id", "fr.name_kh as finance_resource", "pt.id as project_type_id", "pt.name_kh as project_type", "ps.id as project_status_id", "ps.name_kh as project_status", "e.code as entity_code","e.id as entity_id", "e.name_kh as entity")
                                        ->join("entity as e", "e.id", "=", "pip.entity_id")
                                        ->join("mef_project_status as ps", "ps.id", "=", "pip.project_status_id")
                                        ->join("mef_project_type as pt", "pt.id", "=", "pip.project_type_id")
                                        ->join("mef_finance_resource as fr", "fr.id", "=", "pip.finance_resource_id");
                    
                    // PIPInvestment::orderBy("order_level");
                    $clausePIPInv = $queryPIPInv;
                    $clausePIPInv->where("pip.sub_program_id", $subProgramId);
                    $clausePIPInv->where("pip.planning_id", config_planning_year);

                    if($filter["search_field"]){
                        $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
                        $dataFields = $arraySingle;
                        if (array_key_exists('project_name_en', $dataFields)) {
                            $clausePIPInv->where("pip.project_name_en", "Like", "%".$dataFields["project_name_en"]."%");
                        }
                        if (array_key_exists('project_name_kh', $dataFields)) {
                            $clausePIPInv->where("pip.project_name_kh", "Like", "%".$dataFields["project_name_kh"]."%");
                        }
                        if (array_key_exists('pip_no', $dataFields)) {
                            $clausePIPInv->where("pip.pip_no", "Like", "%".$dataFields["pip_no"]."%");
                        }
                        if (array_key_exists('sub_program_id', $dataFields)) {
                            $clausePIPInv->where("pip.sub_program_id", $dataFields["sub_program_id"]["value"]);
                        }
                        if (array_key_exists('entity_id', $dataFields)) {
                            $clausePIPInv->where("pip.entity_id", $dataFields["entity_id"]["value"]);
                        }
                        if (array_key_exists('project_status_id', $dataFields)) {
                            $clausePIPInv->where("pip.project_status_id", $dataFields["project_status_id"]["value"]);
                        }
                        if (array_key_exists('finance_resource_id', $dataFields)) {
                            $clausePIPInv->where("pip.finance_resource_id", $dataFields["finance_resource_id"]["value"]);
                        }
                    }
                    
                    $resultPIPInv = collect($clausePIPInv->get());
                    $pipInvPros = array();
                    
                    $total_est_costing_year_0 = 0;
                    $total_s_fin_year_0 = 0;
                    $total_add_req_fin_year_0 = 0;

                    $total_est_costing_year_1 = 0;
                    $total_s_fin_year_1 = 0;
                    $total_add_req_fin_year_1 = 0;

                    $total_est_costing_year_2 = 0;
                    $total_s_fin_year_2 = 0;
                    $total_add_req_fin_year_2 = 0;

                    foreach($resultPIPInv as $pipInvestment){
                        $pipInvestmentId = $pipInvestment->id;
                        $pipInvProDetails = DB::table("mef_pip_inv_detail")->where("pip_inv_id", $pipInvestmentId)->get();
                        $pipInvestmentData = array();
                        $total_est_costing_year_0 += $pipInvestment->est_costing_year_0;
                        $total_s_fin_year_0 += $pipInvestment->s_fin_year_0;
                        $total_add_req_fin_year_0 += $pipInvestment->add_req_fin_year_0;

                        $total_est_costing_year_1 += $pipInvestment->est_costing_year_1;
                        $total_s_fin_year_1 += $pipInvestment->s_fin_year_1;
                        $total_add_req_fin_year_1 += $pipInvestment->add_req_fin_year_1;

                        $total_est_costing_year_2 += $pipInvestment->est_costing_year_2;
                        $total_s_fin_year_2 += $pipInvestment->s_fin_year_2;
                        $total_add_req_fin_year_2 += $pipInvestment->add_req_fin_year_2;
                        
                        foreach($pipInvProDetails as $pipInvProDetail){
                            $pipInvestmentData[] = array(
                                "name" => $pipInvProDetail->attribute_kh,
                                "values" => array(
                                    "", 
                                    "", 
                                    "", 
                                    CommonService::formatDoubleNum($pipInvProDetail->est_costing_year_0),
                                    CommonService::formatDoubleNum($pipInvProDetail->est_costing_year_1),
                                    CommonService::formatDoubleNum($pipInvProDetail->est_costing_year_2),
                                    CommonService::formatDoubleNum($pipInvProDetail->est_costing_year_0 + $pipInvProDetail->est_costing_year_1 + $pipInvProDetail->est_costing_year_2),
                                    CommonService::formatDoubleNum($pipInvProDetail->s_fin_year_0),
                                    CommonService::formatDoubleNum($pipInvProDetail->s_fin_year_1),
                                    CommonService::formatDoubleNum($pipInvProDetail->s_fin_year_2),
                                    CommonService::formatDoubleNum($pipInvProDetail->s_fin_year_0 + $pipInvProDetail->s_fin_year_1 + $pipInvProDetail->s_fin_year_2), 
                                    CommonService::formatDoubleNum($pipInvProDetail->add_req_fin_year_0),
                                    CommonService::formatDoubleNum($pipInvProDetail->add_req_fin_year_1),
                                    CommonService::formatDoubleNum($pipInvProDetail->add_req_fin_year_2),
                                    CommonService::formatDoubleNum($pipInvProDetail->add_req_fin_year_0 + $pipInvProDetail->add_req_fin_year_1 + $pipInvProDetail->add_req_fin_year_2),
                                    "", 
                                    ""
                                ),
                            );
                        }

                        $pipInvPros[] = array(
                            "id" => $pipInvestment->id,
                            "name" => $pipInvestment->project_name_kh,
                            "project_name_en" => $pipInvestment->project_name_en,
                            "project_name_kh" => $pipInvestment->project_name_kh,
                            "entity_id" => array(
                                "label" => $pipInvestment->entity_code."-".$pipInvestment->entity,
                                "value" => $pipInvestment->entity_id
                            ),//$pipInvestment->entity_id,
                            "program_id" => array(
                                "label" => $program->code."-".$program->name_kh,
                                "value" => $programId
                            ),//$programId,
                            "sub_program_id" => array(
                                "label" => $subProgram->code."-".$subProgram->name_kh,
                                "value" => $subProgram->id
                            ),// $pipInvestment->sub_program_id,
                            "pip_no" => $pipInvestment->pip_no,
                            "project_status_id" => array(
                                "label" => $pipInvestment->project_status,
                                "value" => $pipInvestment->project_status_id
                            ),//$pipInvestment->project_status_id,
                            "project_type_id" => array(
                                "label" => $pipInvestment->project_type,
                                "value" => $pipInvestment->project_type_id
                            ),//$pipInvestment->project_type_id,
                            // "finance_resource" => $pipInvestment->finance_resource,
                            "finance_resource_id" => array(
                                "label" => $pipInvestment->finance_resource,
                                "value" => $pipInvestment->finance_resource_id
                            ),//$pipInvestment->finance_resource_id,
                            "data" => $pipInvestmentData,
                            "values" => array(
                                            $pipInvestment->pip_no,
                                            $pipInvestment->project_status,
                                            $pipInvestment->project_type,
                                            CommonService::formatDoubleNum($pipInvestment->est_costing_year_0),
                                            CommonService::formatDoubleNum($pipInvestment->est_costing_year_1),
                                            CommonService::formatDoubleNum($pipInvestment->est_costing_year_2),
                                            CommonService::formatDoubleNum($pipInvestment->est_costing_year_0 + $pipInvestment->est_costing_year_1 + $pipInvestment->est_costing_year_2),
                                            CommonService::formatDoubleNum($pipInvestment->s_fin_year_0),
                                            CommonService::formatDoubleNum($pipInvestment->s_fin_year_1),
                                            CommonService::formatDoubleNum($pipInvestment->s_fin_year_2),
                                            CommonService::formatDoubleNum(($pipInvestment->s_fin_year_0 + $pipInvestment->s_fin_year_1 + $pipInvestment->s_fin_year_2)), 
                                            CommonService::formatDoubleNum($pipInvestment->add_req_fin_year_0),
                                            CommonService::formatDoubleNum($pipInvestment->add_req_fin_year_1),
                                            CommonService::formatDoubleNum($pipInvestment->add_req_fin_year_2),
                                            CommonService::formatDoubleNum(($pipInvestment->add_req_fin_year_0 + $pipInvestment->add_req_fin_year_1 + $pipInvestment->add_req_fin_year_2)),
                                            "", 
                                            ""
                                        ),
                        );
                    }
                    if(count($pipInvPros) > 0){
                        $subProgs[] = array(
                            // "id" => $subProgram->id,
                            // "entity_id" => $subProgram->entity_id,
                            // "program_id" => $programId,
                            // "sub_program_id" => $subProgram->id,
                            "entity_id" => array(
                                "label" => $pipInvestment->entity_code."-".$pipInvestment->entity,
                                "value" => $pipInvestment->entity_id
                            ),$pipInvestment->entity_id,
                            "program_id" => array(
                                "label" => $program->code."-".$program->name_kh,
                                "value" => $programId
                            ),//$programId,
                            "sub_program_id" => array(
                                "label" => $subProgram->code."-".$subProgram->name_kh,
                                "value" => $subProgram->id
                            ),// $pipInvestment->sub_program_id,

                            "name" => $subProgram->structure_name_kh."-".$subProgram->name_kh,
                            "dataDetails" => $pipInvPros,
                            "values" => array(
                                                "",
                                                "",
                                                "",
                                                CommonService::formatDoubleNum($total_est_costing_year_0),
                                                CommonService::formatDoubleNum($total_est_costing_year_1),
                                                CommonService::formatDoubleNum($total_est_costing_year_2),
                                                "",
                                                "",
                                                "",
                                                "",
                                                "",
                                                "",
                                                "",
                                                "",
                                                "",
                                                "", 
                                                ""
                                            ),
                        );
                    }

                    $grand_total_est_costing_year_0 += $total_est_costing_year_0;
                    $grand_total_est_costing_year_1 += $total_est_costing_year_1;
                    $grand_total_est_costing_year_2 += $total_est_costing_year_2;

                    $grand_total_s_fin_year_0 += $total_s_fin_year_0;
                    $grand_total_s_fin_year_1 += $total_s_fin_year_1;
                    $grand_total_s_fin_year_2 += $total_s_fin_year_2;

                    $grand_total_add_req_fin_year_0 += $total_add_req_fin_year_0;
                    $grand_total_add_req_fin_year_1 += $total_add_req_fin_year_1;
                    $grand_total_add_req_fin_year_2 += $total_add_req_fin_year_2;
                }
                
                if(count($subProgs) > 0){
                    $data[] = array(
                        "summary" => array(
                            "id" => $program->id,
                            "name" => $program->name_kh,
                            "values" => array(
                                                "",
                                                "",
                                                "",
                                                CommonService::formatDoubleNum($grand_total_est_costing_year_0),
                                                CommonService::formatDoubleNum($grand_total_est_costing_year_1),
                                                CommonService::formatDoubleNum($grand_total_est_costing_year_2),
                                                CommonService::formatDoubleNum($grand_total_s_fin_year_0),
                                                CommonService::formatDoubleNum($grand_total_s_fin_year_1),
                                                CommonService::formatDoubleNum($grand_total_s_fin_year_2),
                                                CommonService::formatDoubleNum($grand_total_add_req_fin_year_0),
                                                CommonService::formatDoubleNum($grand_total_add_req_fin_year_1),
                                                CommonService::formatDoubleNum($grand_total_add_req_fin_year_2),
                                                "",
                                                "",
                                                "",
                                                "",
                                                ""
                                            ),
                        ),
                        "children" => $subProgs
                    );
                }

                $summary_total_est_costing_year_0 += $grand_total_est_costing_year_0;
                $summary_total_est_costing_year_1 += $grand_total_est_costing_year_1;
                $summary_total_est_costing_year_2 += $grand_total_est_costing_year_2;

                $summary_total_s_fin_year_0 += $summary_total_s_fin_year_0;
                $summary_total_s_fin_year_1 += $summary_total_s_fin_year_1;
                $summary_total_s_fin_year_2 += $summary_total_s_fin_year_2;

                $grand_total_add_req_fin_year_0 += $grand_total_add_req_fin_year_0;
                $grand_total_add_req_fin_year_1 += $grand_total_add_req_fin_year_1;
                $grand_total_add_req_fin_year_2 += $grand_total_add_req_fin_year_2;
            }
            $summary[] = array(
                "name" => "សរុបរួមក្រសួង​សេដ្ឋ​កិច្ច​និង​ហិរញ្ញវត្ថ",
                "remark" => "",
                "status" => 1,
                "order_level" => "",
                "hasColspan" => false,
                "values" => array(
                                    "",
                                    "",
                                    "",
                                    $summary_total_est_costing_year_0,
                                    $summary_total_est_costing_year_1,
                                    $summary_total_est_costing_year_2,
                                    $summary_total_s_fin_year_0,
                                    $summary_total_s_fin_year_1,
                                    $summary_total_s_fin_year_2,
                                    $grand_total_add_req_fin_year_0,
                                    $grand_total_add_req_fin_year_1,
                                    $grand_total_add_req_fin_year_2,
                                    "",
                                    "",
                                    "",
                                    "",
                                    "",
                                    ""
                                ),
                "data" => $data
            );
        }
        $result = $summary;
        return $result;
    }

    public static function __getPIPInvestment(){
        $programs = Program::orderBy("order_level", "ASC")->get();
        $data = array();
        // foreach($programs as $program){
        //     $programId = $program->id;
        //     $subPrograms = SubProgram::orderBy("order_level", "ASC")->where("program_id", $programId)->get();
        //     $subProgs = array();
        //     foreach($subPrograms as $subProgram){
        //         $subProgramId = $subProgram->id;
        //         $pipInvestments = PIPInvestment::where("sub_program_id", $subProgramId)->where("planning_id", config_planning_year)->orderBy("order_level")->get();
        //         $pipInvPros = array();
        //         foreach($pipInvestments as $pipInvestment){
        //             $pipInvestmentId = $pipInvestment->id;
        //             $pipInvProDetails = DB::table("mef_pip_inv_detail")->where("pip_inv_id", $pipInvestmentId)->get();
        //             $pipInvestmentData = array();
        //             foreach($pipInvProDetails as $pipInvProDetail){
        //                 $pipInvestmentData[] = array(
        //                     ""
        //                 );
        //             }

        //             $pipInvPros[] = array(
        //                 "pip_no" => $pipInvestment->pip_no,
        //                 "project_status" => $pipInvestment->project_status,
        //                 "project_type" => $pipInvestment->project_type,
        //                 "project_name_en" => $pipInvestment->project_name_en,
        //                 "project_name_kh" => $pipInvestment->project_name_kh,
        //                 "exchange_rate" => $pipInvestment->exchange_rate,
        //                 "finance_resource_id" => $pipInvestment->finance_resource_id,
        //                 "status" => $pipInvestment->status,
        //                 "est_costing_year_0" => $pipInvestment->est_costing_year_0,
        //                 "est_costing_year_1" => $pipInvestment->est_costing_year_1,
        //                 "est_costing_year_2" => $pipInvestment->est_costing_year_2,
        //                 "s_fin_year_0" => $pipInvestment->s_fin_year_0,
        //                 "s_fin_year_1" => $pipInvestment->s_fin_year_1,
        //                 "s_fin_year_2" => $pipInvestment->s_fin_year_2,
        //                 "grading_no" => $pipInvestment->grading_no,
        //                 "order_level" => $pipInvestment->order_level,
        //                 "created_by" => $pipInvestment->created_by,
        //                 "modified_by" => $pipInvestment->modified_by,
        //                 "created_at" => $pipInvestment->created_at,
        //                 "updated_at" => $pipInvestment->updated_at
        //             );
        //         } 
        //         $subProgs[] = array(
        //             "id" => $subProgram->id,
        //             "code" => $subProgram->code,
        //             "sub_code" => $subProgram->sub_code,
        //             "name_kh" => $subProgram->name_kh,
        //             "name_en" => $subProgram->name_en,
        //             "order_level" => $subProgram->order_level,
        //             "created_by" => $subProgram->created_by,
        //             "pipInvPros" => $pipInvPros
        //         );
        //     }
        //     $data[] = array(
        //         "id" => $program->id,
        //         "code" => $program->code,
        //         "sub_code" => $program->sub_code,
        //         "name_kh" => $program->name_kh,
        //         "name_en" => $program->name_en,
        //         "order_level" => $program->order_level,
        //         "created_by" => $program->created_by,
        //         "sub_programs" => $subProgs
        //     );
        // }
        return $data;
    }
}