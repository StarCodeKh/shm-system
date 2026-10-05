<?php

namespace App\Models\Modules\ProgramManagement;

use Illuminate\Database\Eloquent\Model;
use App\Models\Modules\ProgramManagement\Objective;
use App\Models\Modules\ProgramManagement\SubProgram;
use App\Models\Modules\ProgramManagement\KPIProgram;
use App\Models\Settings\Entity;
use App\Models\Settings\EntityMember;

class Program extends Model
{
  protected $table = 'mef_program';
  protected $fillable = [
                          'code',
                          'sub_code',
                          'objective_id',
                          'entity_id',
                          'entity_member_id',
                          "name_en",
                          "name_kh",
                          'remark',
                          'order_level',
                          'is_active',
                          'created_by',
                          'modified_by'
                        ];
  public $timestamps = true;

  public function Objective(){
    return $this->belongsTo(Objective::class,'objective_id');
  }

  public function Entity(){
    return $this->belongsTo(Entity::class,'entity_id');
  }

  public function EntityMember(){
    return $this->belongsTo(EntityMember::class,'entity_member_id');
  }

  public static function getSubProgramByProgram($filter){
    $data = array();
    $query = Program::orderBy("order_level")->get();
    foreach($query as $row){
      $parentId = $row->id;
      $cdata = array(); //cdata = children data
      $querySubPrograms = SubProgram::where("program_id", $parentId)->orderBy("order_level")->get();
      foreach($querySubPrograms as $crow){
        $cdata[] = array(
          'id' => $crow->id,
          'code' => $crow->code,
          'program_id' => $crow->program_id,
          "name_en" => $crow->name_en,
          "name_kh" => $crow->name_kh,
          'entity_id' => $crow->entity_id,
          'entity_member_id' => $crow->entity_member_id,
          // 'remark' => $crow->remark,
          'order_level' => $crow->order_level,
        );
      }
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
      $data[] = array(
        'id' => $row->id,
        'code' => $row->code,
        "name_en" => $row->name_en,
        "name_kh" => $row->name_kh,
        'field' => 'program_id',
        'indicator' => $indicatorData,
        // 'remark' => $row->remark,
        // 'order_level' => $row->order_level,
        'children' => $cdata
      );
    }
    return $data;
  }

  public static function getProgramByObj($filter){
    $data = array();
    // $queryObj = Objective::orderBy($filter["sort"], $filter["order"]);
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
      if (array_key_exists('entity_id', $dataFields)) {
        $whereClause->Where("id", $dataFields["entity_id"]["value"]);
      }
    }
    $query = collect($whereClause->get());
    
    foreach($query as $row){
      $objId = $row->id;
      $cdata = array();
      $queryPrograms = Program::orderBy("order_level");
      $whereClausePro = $queryPrograms;
      $whereClausePro->where("objective_id", $objId);

      if($filter["search_field"]){
        $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
        $dataFields = $arraySingle;
        if (array_key_exists('code', $dataFields)) {
          $whereClausePro->Where("id", $dataFields["code"]);
        }
        if (array_key_exists('program_id', $dataFields)) {
          $whereClausePro->Where("id", $dataFields["program_id"]["value"]);
        }
        if (array_key_exists('entity_id', $dataFields)) {
          $whereClausePro->Where("entity_id", $dataFields["entity_id"]["value"]);
        }
        if (array_key_exists("name_en", $dataFields)) {
          $whereClausePro->Where("name_en", "Like", "%".$dataFields["name_en"]."%");
        }
        if (array_key_exists("name_kh", $dataFields)) {
          $whereClausePro->Where("name_kh", "Like", "%".$dataFields["name_kh"]."%");
        }
      }

      $queryProgs = collect($whereClausePro->get());
      foreach($queryProgs as $crow){
        $indicatorArr = array();
        $proId = $crow->id;
        $kpis = KPIProgram::whereNull("status")->where("program_id", $proId)->get();
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
          'id' => $crow->id,
          'code' => $crow->code,
          'objective' => $row->name_kh,
          'objective_id' => array(
            "label" => $row->name_kh,
            "value" => $row->id,
          ),
          'program_id' => array(
            "label" => (config_language == "en")?$crow->code."-".$crow->name_en:$crow->code."-".$crow->name_kh,
            "value" => $crow->id,
          ),
          //$crow->objective_id,
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
      $data[] = array(
        'id' => $row->id,
        'code' => $row->code,
        "name" => (config_language == "en")?$row->code."-".$row->name_en:$row->code."-".$row->name_kh,
        "name_en" => $row->name_en,
        "name_kh" => $row->name_kh,
        'objective_id' => array(
          "label" => $row->name_kh,
          "value" => $row->id
        ),
        'field' => 'objective_id',
        // 'remark' => $row->remark,
        // 'order_level' => $row->order_level,
        'children' => $cdata
      );
    }
    return $data;
  }

  public static function getPrograms($param){
    $queryData = Program::where("is_active", 1)->orderBy("order_level");
      $whereClause = $queryData;
      if(!empty($param["param"]["value"]) && $param["param"]!=""){
        $whereClause->where("objective_id", $param["param"]["value"]);
      }
      $query = collect($whereClause->get());
      $data = array();
      foreach($query as $row){
        $data[] = array(
          "label" => (config_language == "en")?$row->code."-".$row->name_en:$row->code."-".$row->name_kh,
          "value" => $row->id,
        );
      }
      return $data;
    // $query = Program::where("is_active", 1)->orderBy("order_level")->get();
    // $data = array();
    // foreach($query as $row){
    //   $data[] = array(
    //     "label" => $row->code.'-'.$row->name_kh,
    //     "value" => $row->id,
    //   );
    // }
    // return $data;
  }

  public static function getCount($filter){
    $query = Program::orderBy($filter["sort"], $filter["order"]);
    $whereClause = $query;
    $whereClause->whereNotIn("status", [4])->orWhere("status", null);
    if($filter["search_field"]){
        $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
        $dataFields = $arraySingle;
        if (array_key_exists('program_id', $dataFields)) {
          $whereClause->Where("id", $dataFields["program_id"]["value"]);
        }
      }
    $total = $whereClause->count();
    return $total;
  }

}
