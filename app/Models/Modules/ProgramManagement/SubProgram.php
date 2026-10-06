<?php

namespace App\Models\Modules\ProgramManagement;

use Illuminate\Database\Eloquent\Model;
use App\Models\Settings\Entity;
use App\Models\Settings\EntityMember;
use DB;

class SubProgram extends Model
{
    protected $table = 'mef_subprogram';
    protected $primaryKey = 'id';
    protected $fillable = [
                            'code',
                            'sub_code',
                            'program_id',
                            'entity_id',
                            'entity_member_id',
                            'structure_name_en',
                            'structure_name_kh',
                            "name_en",
                            "name_kh",
                            'remark',
                            'administrative_class',
                            'is_active',
                            'order_level',
                            'created_by',
                            'modified_by'
                          ];
    public $timestamps = true;
    
    public function Program(){
      return $this->belongsTo(Program::class,'program_id');
    }
    public function Entity(){
      return $this->belongsTo(Entity::class,'entity_id');
    }
    public function EntityMember(){
      return $this->belongsTo(EntityMember::class,'entity_member_id');
    }

    public static function getSubPrograms(){
      $query = SubProgram::where("is_active", 1)->orderByLevel()->get();
      $data = array();
      foreach($query as $row){
        $data[] = array(
          "label" => $row->code.'-'.$row->structure_name_kh.":".$row->name_kh,
          "value" => $row->id,
        );
      }
      return $data;
    }

    public static function getSubProgramsByProgs($param){
      $queryData = SubProgram::where("is_active", 1)->orderByLevel();
      $whereClause = $queryData;
      if(!empty( $param["param"]["value"])){
        $whereClause->where("program_id", $param["param"]["value"]);
      }
      $query = collect($whereClause->get());
      $data = array();
      foreach($query as $row){
        $data[] = array(
          "label" => $row->code.'-'.$row->structure_name_kh.":".$row->name_kh,
          "value" => $row->id,
        );
      }
      return $data;
    }

    public static function getEntityBySubProg($param){
      $query = DB::table("mef_subprogram as sp")
                ->select("e.id", "e.code", "e.name_kh", "e.name_en")
                ->join("entity as e", "e.id", "=", "sp.entity_id")
                ->where("sp.id", $param["param"]["value"])
                ->get();
                
      $data = array();
      foreach($query as $row){
        $data[] = array(
          "label" => $row->code.'-'.$row->name_kh,
          "value" => $row->id,
        );
      }
      return $data;
    }

    public static function getSubProgramByProgram($filter){
      $data = array();
      $queryObj = Program::orderByFilter($filter);
      $whereClause = $queryObj;
      $whereClause->whereNotDeleted();
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

      foreach($query as $row){
        $parentId = $row->id;
        $cdata = array(); //cdata = children data
       
        $querySPrograms = SubProgram::orderByLevel();
        $whereClauseSPro = $querySPrograms;
        $whereClauseSPro->where("program_id", $parentId);

        if($filter["search_field"]){
          $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
          $dataFields = $arraySingle;
          if (array_key_exists('code', $dataFields)) {
            $whereClauseSPro->Where("code", "Like", "%".$dataFields["code"]."%");
          }
          if (array_key_exists('sub_program_id', $dataFields)) {
            $whereClauseSPro->Where("id", $dataFields["sub_program_id"]["value"]);
          }
          if (array_key_exists('entity_id', $dataFields)) {
            $whereClauseSPro->Where("entity_id", $dataFields["entity_id"]["value"]);
          }
          if (array_key_exists("name_en", $dataFields)) {
            $whereClauseSPro->Where("name_en", "Like", "%".$dataFields["name_en"]."%");
          }
          if (array_key_exists("name_kh", $dataFields)) {
            $whereClauseSPro->Where("name_kh", "Like", "%".$dataFields["name_kh"]."%");
          }
        }
        $querySubPrograms = collect($whereClauseSPro->get());
        foreach($querySubPrograms as $crow){
          $subProId = $crow->id;
          $indicatorArr = array();
          $kpis = KPISubProgram::whereNull("status")->where("sub_program_id", $subProId)->get();
          foreach($kpis as $kpi){
            $indicatorArr[] = array(
              "code" => $kpi->code,
              "kpi_name_en" => $kpi->kpi_name_en,
              "kpi_name_kh" => $kpi->kpi_name_kh,
              "order_level" => $kpi->order_level,
              "status" => $kpi->status,
            );
          }
          // if(count($indicatorArr) > 0){
          //   $indicatorData = array(
          //     "data" => $indicatorArr
          //   );
          // }

          $cdata[] = array(
            'sub_program_id' => array(
              "label" => (config_language == "en")?$crow->code."-".$crow->name_en:$crow->code."-".$crow->name_kh,
              "value" => $crow->id,
            ),
            'program_id' => array(
              "label" => isset($crow->Program)&&config_language == "en"?$crow->Program->code."-".$crow->Program->name_en:$crow->Program->code."-".$crow->Program->name_kh,
              "value" => isset($crow->Program)?$crow->Program->id:"",
            ),
            'id' => $crow->id,
            'code' => $crow->code,
            'name' => (config_language == "en")?$crow->code."-".$crow->name_en:$crow->code."-".$crow->name_kh,
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
        // if(count($cdata) > 0){
        $data[] = array(
          'id' => $row->id,
          'code' => $row->code,
          'name' => $row->name_kh,
          "name_en" => $row->name_en,
          "name_kh" => $row->name_kh,
          'program_id' => array(
            "label" => $row->name_kh,
            "value" => $row->id
          ),
          'field' => 'program_id',
          // 'remark' => $row->remark,
          // 'order_level' => $row->order_level,
          'children' => $cdata
        );
        // }
      }
      return $data;
    }

    public static function _getSubProgramByProgram($filter){
      $data = array();
      $query = Program::orderByLevel()->get();
      foreach($query as $row){
        $parentId = $row->id;
        $cdata = array(); //cdata = children data
        $querySubPrograms = SubProgram::where("program_id", $parentId)->orderByLevel()->get();
        foreach($querySubPrograms as $crow){
          
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

          $cdata[] = array(
            'id' => $crow->id,
            'code' => $crow->code,
            'program_id' => $crow->program_id,
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
            'indicator' => $indicatorData,
          );
        }
        
        $data[] = array(
          'id' => $row->id,
          'code' => $row->code,
          "name_en" => $row->name_en,
          "name_kh" => $row->name_kh,
          'field' => 'program_id',
          // 'remark' => $row->remark,
          // 'order_level' => $row->order_level,
          'children' => $cdata
        );
      }
      return $data;
    }

    public static function getCount($filter){
      $query = SubProgram::orderByFilter($filter);
      $whereClause = $query;
      $whereClause->whereNotDeleted();
      if($filter["search_field"]){
        $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
        $dataFields = $arraySingle;
        if (array_key_exists('sub_program_id', $dataFields)) {
          $whereClause->Where("id", $dataFields["sub_program_id"]["value"]);
        }
      }
      $total = $whereClause->count();
      return $total;
    }
}
