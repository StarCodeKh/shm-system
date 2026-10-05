<?php

namespace App\Models\Modules\ProgramManagement;

use Illuminate\Database\Eloquent\Model;
use App\Models\Modules\ProgramManagement\ClusterActivity;
use App\Models\Modules\ProgramManagement\Activity;
use App\Models\Modules\ProgramManagement\CostingToEntity;
use App\Models\Settings\Entity;
use App\Models\Settings\EntityMember;
use DB;

class Task extends Model
{
  protected $table = 'mef_task';
  protected $fillable = [
                          "id",
                          "planning_id",
                          "program_id",
                          "sub_program_id",
                          "cluster_activity_id",
                          "activity_id",
                          "entity_id",
                          "entity_member_id",
                          "code",
                          "name_en",
                          "name_kh",
                          "start_date",
                          "end_date",
                          "order_level",
                          "assign_to",
                          "status",
                          "remark",
                          "attachment",
                          "assign_to",
                          "is_routine",
                          "created_by",
                          "modified_by",
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

  public static function getTaskByAct($filter){
    $data = Activity::getActByCluster($filter);
    // $data = array();
    // $queryObj = Activity::orderBy($filter["sort"], $filter["order"]);
    // $whereClause = $queryObj;
    // $whereClause->whereNotIn("status", [4])->orWhere("status", null);
    // $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);       
    // $whereClause->limit($filter["limit"]);

    // if($filter["search_field"]){
    //   $whereClause->offset((1 - 1) * $filter["limit"]);    
    //   $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
    //   $dataFields = $arraySingle;
    //   if (array_key_exists('cluster_activity_id', $dataFields)) {
    //     $whereClause->Where("cluster_activity_id", $dataFields["cluster_activity_id"]["value"]);
    //   }
    //   if (array_key_exists('code', $dataFields)) {
    //     $whereClause->Where("code", $dataFields["code"]);
    //   }
    //   if (array_key_exists('activity_id', $dataFields)) {
    //     $whereClause->Where("id", $dataFields["activity_id"]["value"]);
    //   }
    //   if (array_key_exists('entity_id', $dataFields)) {
    //     $whereClause->Where("id", $dataFields["entity_id"]["value"]);
    //   }
    // }
    // $query = collect($whereClause->get());

    // foreach($query as $row){
    //   $activityId = $row->id;
    //   $cdata = array(); //cdata = children data
    //   $queryAct = Task::orderBy("order_level");
    //   $whereClauseAct = $queryAct;
    //   $whereClauseAct->whereNull("status");
    //   $whereClauseAct->where("activity_id", $activityId);

    //   if($filter["search_field"]){
    //     $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
    //     $dataFields = $arraySingle;
    //     if (array_key_exists('code', $dataFields)) {
    //       $whereClauseAct->Where("code", $dataFields["code"]);
    //     }
    //     // if (array_key_exists('id', $dataFields)) {
    //     //   $whereClauseAct->Where("id", $dataFields["id"]);
    //     // }
    //     if (array_key_exists('activity_id', $dataFields)) {
    //       $whereClauseAct->Where("activity_id", $dataFields["activity_id"]);
    //     }
    //     if (array_key_exists('entity_id', $dataFields)) {
    //       $whereClauseAct->Where("entity_id", $dataFields["entity_id"]["value"]);
    //     }
        
    //     // if (array_key_exists('cluster_activity_id', $dataFields)) {
    //     //   $whereClauseAct->Where("cluster_activity_id", $dataFields["cluster_activity_id"]["value"]);
    //     // }
    //     // if (array_key_exists('activity_id', $dataFields)) {
    //     //   $whereClauseAct->Where("activity_id", $dataFields["activity_id"]["value"]);
    //     // }
    //     // if (array_key_exists('entity_id', $dataFields)) {
    //     //   $whereClauseAct->Where("entity_id", $dataFields["entity_id"]["value"]);
    //     // }
    //     if (array_key_exists("name_en", $dataFields)) {
    //       $whereClauseAct->Where("name_en", "Like", "%".$dataFields["name_en"]."%");
    //     }
    //     if (array_key_exists("name_kh", $dataFields)) {
    //       $whereClauseAct->Where("name_kh", "Like", "%".$dataFields["name_kh"]."%");
    //     }
    //   }
      
    //   $result = collect($whereClauseAct->get());

    //   // if(count($result) > 0){
    //     foreach($result as $crow){
    //       $cdata[] = array(
    //         'id' => $crow->id,
    //         'code' => $crow->code,
    //         'cluster_activity_id' => array(
    //           "label" => $crow->ClusterActivity->code.'-'.$crow->ClusterActivity->name_kh,
    //           "value" => $crow->ClusterActivity->id
    //         ),
    //         'activity_id' => array(
    //           "label" => $crow->Activity->code.'-'.$crow->Activity->name_kh,
    //           "value" => $crow->Activity->id
    //         ),
    //         'task_id' => array(
    //           "label" => $crow->name_kh.'-'.$crow->name_kh,
    //           "value" => $crow->id
    //         ),
    //         "name_en" => $crow->name_en,
    //         "name_kh" => $crow->name_kh,
    //         'start_date' => $crow->start_date,
    //         'end_date' => $crow->end_date,
    //         'is_routine' => $crow->is_routine,
    //         'remark' => $crow->remark,
    //         'order_level' => $crow->order_level,
    //       );
    //     }
        
    //     $data[] = array(
    //       'id' => $row->id,
    //       'code' => $row->code,
    //       "name_en" => $row->name_en,
    //       "name_kh" => $row->name_kh,
    //       'entity' => isset($row->Entity)?$row->Entity->code.'-'.$row->Entity->name_kh:"",
    //       'entity_member' => isset($row->EntityMember)?$row->EntityMember->name_kh:"",
    //       'cluster_activity_id' => array(
    //         "label" => $row->ClusterActivity->code.'-'.$row->ClusterActivity->name_kh,
    //         "value" => $row->ClusterActivity->id
    //       ),
    //       'activity_id' => array(
    //         "label" => $row->code.'-'.$row->name_kh,
    //         "value" => $row->id
    //       ),
    //       'entity_id' => array(
    //         "label" => isset($row->Entity)?$row->Entity->code.'-'.$row->Entity->name_kh:"",
    //         "value" => isset($row->Entity)?$row->Entity->id:""
    //       ),
    //       'entity_member_id' => array(
    //         "label" => isset($row->EntityMember)?$row->EntityMember->name_kh:"",
    //         "value" => isset($row->EntityMember)?$row->EntityMember->id:""
    //       ),
    //       'field' => 'activity_id',
    //       'remark' => $row->remark,
    //       'order_level' => $row->order_level,
    //       'children' => $cdata
    //     );
    //   // }
    // }
    return $data;
  }

  public static function getTaskDetail($dataInfo){
    $data = array();
    $taskId = $dataInfo["data_info"]["id"];
    $query = CostingToEntity::where("task_id", $taskId)
                ->whereNotIn("status", [4])->orWhere("status", null)
                ->orderBy("order_level")
                ->get();
    foreach($query as $row){
      $costingArr = array();
      $queryCosting = DB::table("mef_costing_to_entity as c2e")
                        ->select("i.name_en", "i.name_kh","u.name_kh as unit","cd2e.*")
                        ->join("mef_costing_detail_to_entity as cd2e", "c2e.id", "=", "cd2e.costing_id")
                        ->join("mef_item as i", "i.id", "=", "cd2e.id")
                        ->join("unit as u", "u.id", "=", "cd2e.unit_id")
                        ->where("c2e.task_id", $taskId)
                        ->where("cd2e.costing_id", $taskId)
                        ->orderBy('cd2e.order_level')
                        ->get();

      foreach($queryCosting as $rowCosting){
        $costingArr[] = array(
          'account_code' => $rowCosting->account_code,
          'sub_account_code' => $rowCosting->sub_account_code,
          "name_en" => $rowCosting->name_en,
          "name_kh" => $rowCosting->name_kh,
          'line_no' => $rowCosting->line_no,
          'item_id' => $rowCosting->item_id,
          'unit_id' => $rowCosting->unit_id,
          'unit' => $rowCosting->unit,
          'qty' => $rowCosting->qty,
          'flag_type' => $rowCosting->flag_type,
          'cost' => number_format($rowCosting->cost),
          'unit_price' => number_format($rowCosting->unit_price),
          'total_amount' => number_format($rowCosting->total_amount),
          'total_annual_amount' => number_format($rowCosting->total_annual_amount),
          'time_annual' => $rowCosting->time_annual,
          'month' => $rowCosting->month,
          'expense_type' => $rowCosting->expense_type,
          'currency_id' => $rowCosting->currency_id,
          'currency' => $rowCosting->currency,
          'exchange_rate' => $rowCosting->exchange_rate,
          'remark' => $rowCosting->remark,
          'status' => $rowCosting->status,
          'created_by' => $rowCosting->created_by,
          'modified_by' => $rowCosting->modified_by,
        );
      }

      $data[] = array(
        "id" => $row->id,
        // "cluster_activity_id" => $row->cluster_activity_id,
        // "activity_id" => $row->activity_id,
        'cluster_activity_id' => array(
          "label" => $row->ClusterActivity?$row->ClusterActivity->code.'-'.$row->ClusterActivity->name_kh:"",
          "value" => $row->ClusterActivity?$row->ClusterActivity->id:""
        ),
        'activity_id' => array(
          "label" => $row->Activity?$row->Activity->code.'-'.$row->Activity->name_kh:"",
          "value" => $row->Activity?$row->Activity->id:""
        ),
        'task_id' => array(
          "label" => $row->name_kh,
          "value" => $row->id
        ),
        // "entity_id" => $row->entity_id,
        // "entity_member_id" => $row->entity_member_id,
        "code" => $row->code,
        "name_en" => $row->name_en,
        "name_kh" => $row->name_kh,
        // "start_date" => $row->start_date,
        // "end_date" => $row->end_date,
        "order_level" => $row->order_level,
        "status" => $row->status,
        // "assign_to" => $row->assign_to,
        // "status_id" => $row->status_id,
        // "remark" => $row->remark,
        // "attachment" => $row->attachment,
        // "assign_to" => $row->assign_to,
        // "is_routine" => $row->is_routine,
        "children" => $costingArr,
        // "limit" => 5,
        // "total" => 10

      );
    }
    return $data;
  }

  public static function getTskByAct($filter, $input){
    $queryData = Task::orderBy($filter["sort"], $filter["order"]);
    $whereClause = $queryData;
    $whereClause->where("activity_id", $input["param"]["value"]);
    $whereClause->whereNotIn("status", [4])->orWhere("status", null);
    $whereClause->orderBy("order_level");
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
}