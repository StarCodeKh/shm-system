<?php

namespace App\Models\Modules\ProgramManagement;

use Illuminate\Database\Eloquent\Model;
use App\Models\Settings\Entity;
use App\Models\Settings\EntityMember;
use DB;

class Activity extends Model
{
  protected $table = 'mef_activity';
  protected $fillable = [
                          'code',
                          'program_id',
                          'sub_program_id',
                          'cluster_activity_id',
                          'entity_id',
                          'entity_member_id',
                          'name_en',
                          "name_kh",
                          'remark',
                          'is_active',
                          'order_level',
                          'created_by',
                          'modified_by'
                        ];
  public $timestamps = true;
  public function Program(){
    return $this->belongsTo(Program::class,'program_id');
  }
  public function SubProgram(){
    return $this->belongsTo(SubProgram::class,'sub_program_id');
  }
  public function ClusterActivity(){
    return $this->belongsTo(ClusterActivity::class,'cluster_activity_id');
  }
  public function Activity(){
    return $this->belongsTo(Activity::class,'activity_id');
  }
  public function Entity(){
    return $this->belongsTo(Entity::class,'entity_id');
  }
  public function EntityMember(){
    return $this->belongsTo(EntityMember::class,'entity_member_id');
  }
  public static function getActivities($param){
    $queryData = Activity::where("is_active", 1);
    $whereClause = $queryData;
    $whereClause->orderByLevel();
    if(!empty($param["param"]["value"])){
      $whereClause->where("cluster_activity_id", $param["param"]["value"]);
    }
    $query = collect($whereClause->get());
    $data = array();
    foreach($query as $row){
      $data[] = array(
        "label" => (config_language == "en")?$row->code.'-'.$row->name_en:$row->code.'-'.$row->name_kh,
        "value" => $row->id,
      );
    }
    return $data;
  }
  public static function getActByCluster($filter){
    $data = array();
    $queryObj = ClusterActivity::orderByFilter($filter);
    $whereClause = $queryObj;
    $whereClause->whereNotDeleted();
    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);       
    $whereClause->limit($filter["limit"]);

    if($filter["search_field"]){
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if (array_key_exists('cluster_activity_id', $dataFields)) {
        $whereClause->Where("id", $dataFields["cluster_activity_id"]["value"]);
      }
      if (array_key_exists('entity_id', $dataFields)) {
        $whereClause->Where("id", $dataFields["entity_id"]["value"]);
      }
    }
    $query = collect($whereClause->get());

    foreach($query as $row){
      $parentId = $row->id;
      $cdata = array(); //cdata = children data
      $queryAct = Activity::orderByLevel();
      $whereClauseAct = $queryAct;
      $whereClauseAct->whereNotDeleted();
      $whereClauseAct->where("cluster_activity_id", $parentId);
      
      if($filter["search_field"]){
        $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
        $dataFields = $arraySingle;
        if (array_key_exists('code', $dataFields)) {
          $whereClauseAct->Where("code", $dataFields["code"]);
        }
        if (array_key_exists('activity_id', $dataFields)) {
          $whereClauseAct->Where("id", $dataFields["activity_id"]["value"]);
        }
        if (array_key_exists("name_en", $dataFields)) {
          $whereClauseAct->Where("name_en", "Like", "%".$dataFields["name_en"]."%");
        }
        if (array_key_exists("name_kh", $dataFields)) {
          $whereClauseAct->Where("name_kh", "Like", "%".$dataFields["name_kh"]."%");
        }
      }
      $result = collect($whereClauseAct->get());

      if(count($result) > 0){
        foreach($result as $crow){
          $indicators = array(
            "id" => "id",
            "code" => "code",
            "kpi_name_en" => "kpi_name_en",
            "kpi_name_kh" => "kpi_name_kh",
            "order_level" => "order_level",
            "status" => "status",
          );
          $indicatorData = array(
            "data" => $indicators
          );
          // Task
          $taskArr = array();
          $queryTask = Task::where("activity_id", $crow->id)
                              ->orderByLevel()
                              ->whereNotDeleted()
                              ->get();
          
          foreach($queryTask as $task){
            // Costing
            $costingArr = array();
            $queryCostings = DB::table("mef_costing_to_entity")
                                  ->where("task_id", $task->id)
                                  ->orderByLevel()
                                  ->whereNotDeleted()
                                  // ->orWhere("status", 0)
                                  // ->where("planning_id", config_planning_year)
                                  ->get();

            // Costing Detail
            foreach($queryCostings as $costing){
              $queryCostingDetails = DB::table("mef_costing_detail_to_entity")
                                  ->where("costing_id", $costing->id)
                                  ->orderByLevel()
                                  ->whereNotDeleted()
                                  ->get();

              $costingDetailArr = array();
              foreach($queryCostingDetails as $cdetail){
                $costingDetailArr[] = array(
                  "id" => $cdetail->id,
                  'task_id' => array(
                    "label" => (config_language == "en")?$task->code.'-'.$task->name_en:$task->code.'-'.$task->name_kh,
                    "value" => $task->id
                  ),
                  "name_en" => $cdetail->name_en,
                  "name_kh" => $cdetail->name_kh,
                  "name" => (config_language == "en")?$cdetail->name_en:$cdetail->name_kh,
                  "code" => $cdetail->line_no,
                  "group_chapter" => "",//$cdetail->group_chapter,
                  "no" => "",//$cdetail->no,
                  "line" => "",//$cdetail->line,
                  "code_cluster_activity" => "", //$cdetail->code_cluster_activity,
                  "code_activity" => "", //$cdetail->code_activity,
                  "sub_account" => "", //$cdetail->sub_account,
                  "is_reg_exp" => "", //$cdetail->is_reg_exp,
                  "unit" => "", //$cdetail->unit,
                  "quantity" => "", //$cdetail->quantity,
                  "currency" => $cdetail->currency,
                  "unit_price" => $cdetail->unit_price,
                  "total_amount" => $cdetail->total_amount,
                  "time_year" => "", //$cdetail->time_year,
                  "annual_amount" => "", //$cdetail->annual_amount,
                  "month" => "", //$cdetail->month,
                  "expense_type" => "", //$cdetail->expense_type,
                  "remark" => $cdetail->remark
                );
              }

              $costingArr[] = array(
                "id" => $costing->id,
                'program_id' => array(
                  "label" => $row->Program?(config_language == "en")?$row->Program->code.'-'.$row->Program->name_en:$row->Program->code.'-'.$row->Program->name_kh:"",
                  "value" => $row->Program?$row->Program->$row->id:""
                ),
                'sub_program_id' => array(
                  "label" => $row->SubProgram?$row->SubProgram->code.'-'.(config_language == "en")?$row->SubProgram->name_en:$row->SubProgram->name_kh:"",
                  "value" => $row->SubProgram?$row->SubProgram->id:""
                ),
                'cluster_activity_id' => array(
                  "label" => $row->ClusterActivity?$row->ClusterActivity->code.'-'.(config_language == "en")?$row->ClusterActivity->name_en:$row->ClusterActivity->name_kh:"",
                  "value" => $row->ClusterActivity?$row->ClusterActivity->id:""
                ),
                'activity_id' => array(
                  "label" => (config_language == "en")?$crow->code.'-'.$crow->name_en:$crow->code.'-'.$crow->name_kh,
                  "value" => $crow->id
                ),
                'task_id' => array(
                  "label" => (config_language == "en")?$task->code.'-'.$task->name_en:$task->code.'-'.$task->name_kh,
                  "value" => $task->id
                ),
                "name" => (config_language == "en")?$costing->code.": ".$costing->name_en:$costing->code.": ".$costing->name_kh,
                "name_kh" => $costing->name_kh,
                "name_en" => $costing->name_en,
                "children" => $costingDetailArr
              );
            }

            $taskArr[] = array(
              "id" => $task->id,
              "code" => $task->code,
              'program_id' => array(
                "label" => $task->Program?$task->Program->code.'-'.(config_language == "en")?$task->Program->name_en:$task->Program->name_kh:"",
                "value" => $task->Program?$task->Program->id:""
              ),
              'sub_program_id' => array(
                "label" => $task->SubProgram?$task->SubProgram->code.'-'.(config_language == "en")?$task->SubProgram->name_en:$task->SubProgram->name_kh:"",
                "value" => $task->SubProgram?$task->SubProgram->id:""
              ),
              'cluster_activity_id' => array(
                "label" => $task->ClusterActivity?$task->ClusterActivity->code.'-'.(config_language == "en")?$task->ClusterActivity->name_en:$task->ClusterActivity->name_kh:"",
                "value" => $task->ClusterActivity?$task->ClusterActivity->id:""
              ),
              'activity_id' => array(
                "label" => $task->Activity?$task->Activity->code.'-'.(config_language == "en")?$task->Activity->name_en:$task->Activity->name_kh:"",
                "value" => $task->Activity?$task->Activity->id:""
              ),
              'task_id' => array(
                "label" => $task->code.'-'.(config_language == "en")?$task->name_en:$task->name_kh,
                "value" => $task->id
              ),
              "name" => $task->code.": ".(config_language == "en")?$task->name_en:$task->name_kh,
              "name_en" => $task->name_en,
              "name_kh" => $task->name_kh,
              "order_level" => $task->order_level,
              "start_date" => $task->start_date,
              "end_date" => $task->end_date,
              "remark" => $task->remark,
              "children" => $costingArr
            );
          }

          $cdata[] = array(
            'id' => $crow->id,
            'code' => $crow->code,
            'sub_program_id' => [],
            'program_id' => array(
              "label" => $crow->Program?$crow->Program->code.'-'.(config_language == "en")?$crow->Program->name_en:$crow->Program->name_kh:"",
              "value" => $crow->Program?$crow->Program->id:""
            ),
            'sub_program_id' => array(
              "label" => $crow->SubProgram?$crow->SubProgram->code.'-'.(config_language == "en")?$crow->SubProgram->name_en:$crow->SubProgram->name_kh:"",
              "value" => $crow->SubProgram?$crow->SubProgram->id:""
            ),
            'cluster_activity_id' => array(
              "label" => $crow->ClusterActivity?$crow->ClusterActivity->code.'-'.(config_language == "en")?$crow->ClusterActivity->name_en:$crow->ClusterActivity->name_kh:"",
              "value" => $crow->ClusterActivity?$crow->ClusterActivity->id:""
            ),
            'activity_id' => array(
              "label" => $crow->code.'-'.(config_language == "en")?$crow->name_en:$crow->name_kh,
              "value" => $crow->id
            ),
            "name" => (config_language == "en")?$crow->code."-".$crow->name_en:$crow->code."-".$crow->name_kh,
            "name_en" => $crow->name_en,
            "name_kh" => $crow->name_kh,
            'entity' => isset($crow->Entity)?$crow->Entity->name_kh:"",
            'entity_id' => array(
              "label" => isset($crow->Entity)?$crow->Entity->code."-".$crow->Entity->name_kh:"",
              "value" => isset($crow->Entity)?$crow->Entity->id:"",
            ),
            'entity_member' => isset($crow->EntityMember)?$crow->EntityMember->fullname:"",
            'entity_member_id' => array(
              "label" => isset($crow->EntityMember)?$crow->EntityMember->fullname:"",
              "value" => isset($crow->EntityMember)?$crow->EntityMember->id:"",
            ),
            // 'remark' => $crow->remark,
            'order_level' => $crow->order_level,
            'indicator' => $indicatorData,
            'children' => $taskArr
          );
        }
        
        $data[] = array(
          'id' => $row->id,
          'code' => $row->code,
          'group_chapter' => '',
          "name" => (config_language == "en")?$row->name_en:$row->name_kh,
          "name_en" => $row->name_en,
          "name_kh" => $row->name_kh,
          'program_id' => array(
            "label" => $row->Program?$row->Program->code.'-'.(config_language == "en")?$row->Program->name_en:$row->Program->name_kh:"",
            "value" => $row->Program?$row->Program->id:""
          ),
          'sub_program_id' => array(
            "label" => $row->SubProgram?$row->SubProgram->code.'-'.(config_language == "en")?$row->SubProgram->name_en:$row->SubProgram->name_kh:"",
            "value" => $row->SubProgram?$row->SubProgram->id:""
          ),
          'cluster_activity_id' => array(
            "label" => $row->code.'-'.(config_language == "en")?$row->name_en:$row->name_kh,
            "value" => $row->id
          ),
          'field' => 'cluster_activity_id',
          'order_level' => $row->order_level,
          'children' => $cdata
        );
      }
    }
    return $data;
  }
  public static function getCount($filter){
    $query = Activity::orderByFilter($filter);
    $whereClause = $query;
    $whereClause->whereNotDeleted();
    if($filter["search_field"]){
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if (array_key_exists('activity_id', $dataFields)) {
        $whereClause->Where("id", $dataFields["activity_id"]["value"]);
      }
      if (array_key_exists('cluster_activity_id', $dataFields)) {
        $whereClause->Where("cluster_activity_id", $dataFields["cluster_activity_id"]["value"]);
      }
    }

    $total = $whereClause->count();
    return $total;
  }
}
