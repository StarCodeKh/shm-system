<?php

namespace App\Models\Modules\ProgramManagement;

use Illuminate\Database\Eloquent\Model;
use App\Models\Settings\Entity;
use App\Models\Settings\EntityMember;

class ClusterActivity extends Model
{
    protected $table = 'mef_cluster_activity';
    protected $fillable = [
                            'code',
                            'sub_code',
                            'sub_program_id',
                            'entity_id',
                            'entity_member_id',
                            "name_en",
                            "name_kh",
                            'remark',
                            'order_level',
                            'created_by',
                            'modified_by'
                          ];
    public $timestamps = true;
    
    public function SubProgram(){
      return $this->belongsTo(SubProgram::class,'sub_program_id');
    }
    public function Entity(){
      return $this->belongsTo(Entity::class,'entity_id');
    }
    public function EntityMember(){
      return $this->belongsTo(EntityMember::class,'entity_member_id');
    }
  
    public static function getClusActBySprog($filter, $input){
      $queryData = ClusterActivity::orderBy($filter["sort"], $filter["order"]);
      $whereClause = $queryData;
      $whereClause->where("sub_program_id", $input["param"]["value"]);
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

    public static function getClusterActBySProgram($filter){
      $data = array();
      $queryObj = SubProgram::where("is_active", 1);
      $whereClause = $queryObj;

      // If has param search_field
      if(isset($filter["search_field"])){
        $whereClause->orderBy($filter["sort"], $filter["order"]);
        $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);       
        $whereClause->limit($filter["limit"]);

        if($filter["search_field"]){
            $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
            $dataFields = $arraySingle;
          if (array_key_exists('sub_program_id', $dataFields)) {
            $whereClause->Where("id", $dataFields["sub_program_id"]["value"]);
          }
          if (array_key_exists('entity_id', $dataFields)) {
            $whereClause->Where("id", $dataFields["entity_id"]["value"]);
          }
        }
        // if (array_key_exists('sub_program_id', $dataFields)) {
        //   $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
        //   $dataFields = $arraySingle;
        //   $whereClause->Where("id", $dataFields["sub_program_id"]["value"]);
        // }
        // if (array_key_exists('entity_id', $dataFields)) {
        //   $whereClause->Where("entity_id", $dataFields["entity_id"]["value"]);
        // }
      }      

      // Query Result
      $query = collect($whereClause->get());
      foreach($query as $row){
        $parentId = $row->id;
        $cdata = array();
        // $result = ClusterActivity::where("sub_program_id", $parentId)->orderBy("order_level")->get();
        $queryClusAct = ClusterActivity::orderBy("order_level");
        $whereClauseClusAct = $queryClusAct;
        $whereClauseClusAct->where("is_active", 1);
        $whereClauseClusAct->where("sub_program_id", $parentId);
        if($filter["search_field"]){
          $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
          $dataFields = $arraySingle;
          if (array_key_exists('code', $dataFields)) {
            $whereClauseClusAct->Where("code", $dataFields["code"]);
          }
          if (array_key_exists('entity_id', $dataFields)) {
            $whereClauseClusAct->Where("id", $dataFields["entity_id"]["value"]);
          }
          if (array_key_exists("name_en", $dataFields)) {
            $whereClauseClusAct->Where("name_en", "Like", "%".$dataFields["name_en"]."%");
          }
          if (array_key_exists("name_kh", $dataFields)) {
            $whereClauseClusAct->Where("name_kh", "Like", "%".$dataFields["name_kh"]."%");
          }
        }
        
        $queryClusActProgs = collect($whereClauseClusAct->get());
        foreach($queryClusActProgs as $crow){
          $indicatorArr = array();
          $sproId = $crow->id;
          $kpis = KPIClusterActivity::where("status", "!=", 4)->where("cluster_activity_id", $sproId)->get();
          foreach($kpis as $kpi){
            $indicatorArr[] = array(
              "code" => $kpi->code,
              "kpi_name_en" => $kpi->kpi_name_en,
              "kpi_name_kh" => $kpi->kpi_name_kh,
              "order_level" => $kpi->order_level,
              "status" => $kpi->status,
            );
          }

          $cdata[] = array(
            'sub_program_id' => array(
              "label" => isset($crow->SubProgram)?$crow->SubProgram->name_kh:"",
              "value" => isset($crow->SubProgram)?$crow->SubProgram->id:"",
            ),
            'cluster_activity_id' => array(
              "label" => (config_language == "en")?$crow->code."-".$crow->name_en:$crow->code."-".$crow->name_kh,
              "value" => $crow->id,
            ),
            'id' => $crow->id,
            'code' => $crow->code,
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
            'remark' => $crow->remark,
            'order_level' => $crow->order_level,
            'indicator' => array("data" => $indicatorArr),
          );
        }
        
        $data[] = array(
          'id' => $row->id,
          'code' => $row->code,
          "name" => $row->code.'-'.(config_language == "en")?$row->name_en:$row->name_kh,
          "name_en" => $row->name_en,
          "name_kh" => $row->name_kh,
          'sub_program_id' => array(
            "label" => $row->code.'-'.$row->structure_name_kh.':'.$row->name_kh,
            "value" => $row->id
          ),
          'field' => 'sub_program_id',
          // 'remark' => $row->remark,
          // 'order_level' => $row->order_level,
          'children' => $cdata
        );
      }
      return $data;
    }

    public static function getClusterActs($param){
      $queryData = ClusterActivity::where("is_active", 1);
      $whereClause = $queryData;
      $whereClause->orderBy("order_level");
      if(!empty($param["param"]["value"])){
        $whereClause->where("sub_program_id", $param["param"]["value"]);
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

    public static function getCount($filter){
      $query = ClusterActivity::orderBy($filter["sort"], $filter["order"]);
      $whereClause = $query;
      $whereClause->whereNotIn("status", [4])->orWhere("status", null);
      if($filter["search_field"]){
        $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
        $dataFields = $arraySingle;
        if (array_key_exists('cluster_activity_id', $dataFields)) {
          $whereClause->Where("id", $dataFields["cluster_activity_id"]["value"]);
        }
      }

      $total = $whereClause->count();
      return $total;
    }
}
