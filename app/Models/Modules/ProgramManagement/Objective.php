<?php

namespace App\Models\Modules\ProgramManagement;

use Illuminate\Database\Eloquent\Model;
use DB;

class Objective extends Model
{
  protected $table = 'mef_objective';
  protected $primaryKey = 'id';
  protected $fillable = [
                          'id',
                          'code',
                          'sub_code',
                          "name_en",
                          "name_kh",
                          'remark',
                          'order_level'
                        ];
  public $timestamps = true;

  public static function getObjectives(){
    $query = Objective::where("is_active", 1)->orderBy("order_level")->get();
    $data = array();
    foreach($query as $row){
      $data[] = array(
        "label" => $row->name_kh,
        "value" => $row->id,
      );
    }
    return $data;
  }

  public static function getAllClusterPrograms($filter){
    $data = array();
    $queryObj = Objective::orderBy("order_level");
    $whereClause = $queryObj;
    $whereClause->whereNotIn("status", [4])->orWhere("status", null);
    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);       
    $whereClause->limit($filter["limit"]);

    if($filter["search_field"]){
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if (array_key_exists('objective_id', $dataFields)) {
        $whereClause->Where("id", $dataFields["objective_id"]["value"]);
      }
      if (array_key_exists("name_kh", $dataFields)) {
        $whereClause->Where("name_kh", 'Like', '%'.$dataFields["name_kh"].'%');
      }
      if (array_key_exists("name_en", $dataFields)) {
        $whereClause->Where("name_en", 'Like', '%'.$dataFields["name_en"].'%');
      }
    }
    $query = collect($whereClause->get());
    
    foreach($query as $row){
      $proArr = array();
      // Program
      $queryPro = Program::where("is_active", 1)->where("objective_id", $row->id)->orderBy("order_level")->get();
      foreach($queryPro as $pro){
        $subProArr = array();
        $proIndArr = array();
        $queryProIndicator = DB::table("mef_kpi_program")
                                ->where("planning_id", config_planning_year)
                                ->where("program_id", $pro->id)
                                ->orderBy("order_level")
                                ->get();
        foreach($queryProIndicator as $proIndicator){
          $proIndArr[] = array(
            "id" => $proIndicator->id,
            "code" => $proIndicator->code,
            "indicator" => $proIndicator->kpi_name_kh
          );
        }

        // Sub Program
        $querySubPro = SubProgram::where("is_active", 1)->where("program_id", $pro->id)->orderBy("order_level")->get();
        foreach($querySubPro as $subPro){
          $cluActArr = array();
          $subProInd = array();
          $querySubProIndicator = DB::table("mef_kpi_subprogram")
                                      ->where("planning_id", config_planning_year)
                                      ->where("sub_program_id", $pro->id)
                                      ->orderBy("order_level")
                                      ->get();
          foreach($querySubProIndicator as $subProIndicator){
            $subProInd[] = array(
              "id" => $subProIndicator->id,
              "code" => $subProIndicator->code,
              "indicator" => $subProIndicator->kpi_name_kh
            );
          }
          // Cluster Activity
          $queryClusAct = ClusterActivity::where("is_active", 1)->where("sub_program_id", $subPro->id)->orderBy("order_level")->get();
          
          foreach($queryClusAct as $clusAct){
            $actArr = array();
            $clusActIndArr = array();

            // Activity
            $queryAct = Activity::where("is_active", 1)->where("cluster_activity_id", $clusAct->id)->orderBy("order_level")->get();
            
            $queryClusActIndicator = DB::table("mef_kpi_cluster_activity")
                                        ->where("cluster_activity_id", $pro->id)
                                        ->where("planning_id", config_planning_year)
                                        ->orderBy("order_level")
                                        ->get();
            foreach($queryClusActIndicator as $clusActIndicator){
              $clusActIndArr[] = array(
                "id" => $clusActIndicator->id,
                "code" => $clusActIndicator->code,
                "indicator" => $clusActIndicator->kpi_name_kh
              );
            }

            foreach($queryAct as $act){
              $actIndArr = array();
              $queryActIndicator = DB::table("mef_kpi_activity")
                                        ->where("activity_id", $act->id)
                                        ->where("planning_id", config_planning_year)
                                        ->orderBy("order_level")
                                        ->get();
                foreach($queryActIndicator as $actIndicator){
                  $actIndArr[] = array(
                    "id" => $actIndicator->id,
                    "code" => $actIndicator->code,
                    "indicator" => $actIndicator->kpi_name_kh
                  );
                }
                // Task
                $taskArr = array();
                // $queryTask = DB::table("mef_task")
                //                         ->where("activity_id", $act->id)
                //                         ->orderBy("order_level")
                //                         ->whereNull("status")
                //                         ->orWhere("status", 0)
                //                         ->get();
                // foreach($queryTask as $task){
                //   // Costing
                //   $costingArr = array();
                //   $queryCostings = DB::table("mef_costing_to_entity")
                //                         ->where("activity_id", $act->id)
                //                         ->orderBy("order_level")
                //                         ->whereNull("status")
                //                         ->orWhere("status", 0)
                //                         ->where("planning_id", config_planning_year)
                //                         ->get();
                //   // Costing Detail
                //   foreach($queryCostings as $costing){
                //     $queryCostingDetails = DB::table("mef_costing_detail_to_entity")
                //                         ->where("costing_id", $costing->id)
                //                         ->orderBy("order_level")
                //                         ->whereNull("status")
                //                         ->orWhere("status", 0)
                //                         ->get();
                //     $costingDetailArr = array();
                //     // foreach($queryCostingDetails as $cdetail){
                //     //   $costingDetailArr[] = array(
                //     //     "id" => $cdetail->id,
                //     //     "name" => (config_language == "en")?$cdetail->line_no.'-'.$cdetail->name_en:$cdetail->line_no.'-'.$cdetail->name_kh,
                //     //     "code" => $cdetail->line_no,
                //     //     "group_chapter" => "",
                //     //     "no" => "",
                //     //     "line_no" => $cdetail->line_no,
                //     //     "code_cluster_activity" => "",
                //     //     "code_activity" => "",
                //     //     "sub_account" => "",
                //     //     "is_reg_exp" => "",
                //     //     "unit" => "",
                //     //     "quantity" => "",
                //     //     "currency" => "",
                //     //     "unit_price" => "",
                //     //     "total_amount" => "",
                //     //     "time_year" => "",
                //     //     "annual_amount" => "",
                //     //     "month" => "",
                //     //     "expense_type" => "",
                //     //     "remark" => "",
                //     //   );
                //     // }

                //     $costingArr[] = array(
                //       "id" => $costing->id,
                //       "name_kh" => $costing->name_kh,
                //       "name_en" => $costing->name_en,
                //       "children" => $costingDetailArr
                //     );
                //   }

                //   $taskArr[] = array(
                //     "id" => $task->id,
                //     "code" => $task->code,
                //     "name" => $task->code.": ".$task->name_kh,
                //     "order_level" => $task->order_level,
                //     "children" => $costingArr
                //   );
                // }
                
                $actArr[] = array(
                  "id" => $act->id,
                  "name" => $act->code.": ".$act->name_kh,
                  "indicator" => $actIndArr,
                  "order_level" => $act->order_level,
                  "children" => $taskArr
                );
            }
            $cluActArr[] = array(
              "id" => $clusAct->id,
              "name" => (config_language == "en")?$clusAct->code.": ".$clusAct->name_en:$clusAct->code.": ".$clusAct->name_kh,
              'objective_id' => array(
                "label" => (config_language == "en")?$row->code.'-'.$row->name_en:$row->code.'-'.$row->name_kh,
                "value" => $row->id
              ),
              'program_id' => array(
                "label" => (config_language == "en")?$pro->code.'-'.$pro->name_en:$pro->code.'-'.$pro->name_kh,
                "value" => $pro->id
              ),
              'sub_program_id' => array(
                "label" => (config_language == "en")?$subPro->code.'-'.$subPro->name_en:$subPro->code.'-'.$subPro->name_kh,
                "value" => $subPro->id
              ),
              'cluster_activity_id' => array(
                "label" => (config_language == "en")?$clusAct->code.":".$clusAct->name_en:$clusAct->code.":".$clusAct->name_kh,
                "value" => $clusAct->id,
              ),
              'entity' => isset($clusAct->Entity)?$clusAct->Entity->code."-".$clusAct->Entity->name_kh:"",
              'entity_id' => array(
                "label" => isset($clusAct->Entity)?$clusAct->Entity->code."-".$clusAct->Entity->name_kh:"",
                "value" => isset($clusAct->Entity)?$clusAct->Entity->id:"",
              ),
              'entity_member' => isset($clusAct->EntityMember)?$clusAct->EntityMember->fullname:"",
              'entity_member_id' => array(
                "label" => isset($clusAct->EntityMember)?$clusAct->EntityMember->fullname:"",
                "value" => isset($clusAct->EntityMember)?$clusAct->EntityMember->id:"",
              ),
              "order_level" => $clusAct->order_level,
              "indicator" => $clusActIndArr,
              "children"=> $actArr
            );
          }
          $subProArr[] = array(
            "id" => $subPro->id,
            "name" => $subPro->code."-".$subPro->structure_name_kh.":".$subPro->name_kh,
            'entity' => isset($subPro->Entity)?$subPro->Entity->name_kh:"",
            'entity_id' => array(
              "label" => isset($subPro->Entity)?$subPro->Entity->code."-".$subPro->Entity->name_kh:"",
              "value" => isset($subPro->Entity)?$subPro->Entity->id:"",
            ),
            'entity_member' => isset($subPro->EntityMember)?$subPro->EntityMember->fullname:"",
            'entity_member_id' => array(
              "label" => isset($subPro->EntityMember)?$subPro->EntityMember->fullname:"",
              "value" => isset($subPro->EntityMember)?$subPro->EntityMember->id:"",
            ),
            "order_level" => $subPro->order_level,
            "indicator" => $subProInd,
            "children"=> $cluActArr
          );
        }
        $proArr[] = array(
          "id" => $pro->id,
          "name" => $pro->code.': '.$pro->name_kh,
          'entity' => isset($pro->Entity)?$pro->Entity->name_kh:"",
          'entity_id' => array(
            "label" => isset($pro->Entity)?$pro->Entity->code."-".$pro->Entity->name_kh:"",
            "value" => isset($pro->Entity)?$pro->Entity->id:"",
          ),
          'entity_member' => isset($pro->EntityMember)?$pro->EntityMember->fullname:"",
          'entity_member_id' => array(
            "label" => isset($pro->EntityMember)?$pro->EntityMember->fullname:"",
            "value" => isset($pro->EntityMember)?$pro->EntityMember->id:"",
          ),
          "order_level" => $pro->order_level,
          "indicator" => $proIndArr,
          "children"=> $subProArr
        );
      }

      $data[] = array(
        "id" => $row->id,
        "code" => $row->code,
        "name" => $row->code."-".$row->name_kh,
        "name_en" => $row->code."-".$row->name_en,
        "name_kh" => $row->code."-".$row->name_kh,
        "order_level" => $row->order_level,
        "hasColspan" => true,
        "colspan"=> 4,
        "indicator" => array(),
        "children" => $proArr
      );
    }
    return $data;
  }

  public static function getCount($filter){
    $queryObj = Objective::orderBy($filter["sort"], $filter["order"]);
    $whereClause = $queryObj;
    $whereClause->whereNotIn("status", [4])->orWhere("status", null);
    $total = $whereClause->count();
    return $total;
  }
}
