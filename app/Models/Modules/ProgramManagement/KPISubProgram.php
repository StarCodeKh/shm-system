<?php

namespace App\Models\Modules\ProgramManagement;

use Illuminate\Database\Eloquent\Model;
use App\Models\Modules\ProgramManagement\SubProgram;

class KPISubProgram extends Model
{
    protected $table = 'mef_kpi_subprogram';
    protected $primaryKey = 'id';
    protected $fillable = [
                            'planning_id',
                            'sub_program_id',
                            'kpi_program_id',
                            'subprog_to_entity_id',
                            'code',
                            'kpi_name_en',
                            'kpi_name_kh',
                            'order_level',
                            'attachment',
                            'year',
                            'progress',
                            'acheivement',
                            'reference_kpi_id',
                            'status',
                            'remark',
                            'is_active',
                            'created_by',
                            'modified_by'
                          ];
    public $timestamps = true;

    public function SubProgram(){
      return $this->belongsTo(SubProgram::class,'sub_program_id');
    }

    public static function dataQuery($filter, $dataInfo, $fillable = array()){
      // dd($dataInfo);
      $query = KPISubProgram::orderBy("order_level");
      $whereClause = $query;   
      $whereClause->orderBy($filter["sort"], $filter["order"]);
      $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);       
      $whereClause->limit($filter["limit"]);
      
      if(isset($dataInfo["data_info"])){
        $whereClause->where("sub_program_id", $dataInfo["data_info"]["id"]);
      }
      // if(isset($input["search_field"])){
      //   for($i=0 ; $i < count($input["search_field"]); $i++){
      //     $field = array_key_first($input["search_field"][$i]);
      //     if (in_array($field, $fillable)) {
      //       $whereClause->where($field, "Like","%".$input["search_field"][$i][$field]."%");
      //     }
      //   }
      // }

      if($filter["search_field"]){
        $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
        $dataFields = $arraySingle;
        if (array_key_exists('sub_program_id', $dataFields)) {
          $whereClause->Where("sub_program_id", $dataFields["sub_program_id"]["value"]);
        }
        if (array_key_exists('code', $dataFields)) {
          $whereClause->Where("code", $dataFields["code"]);
        }
        if (array_key_exists('kpi_name_en', $dataFields)) {
          $whereClause->Where("kpi_name_en", "Like", "%".$dataFields["kpi_name_en"]."%");
        }
        if (array_key_exists('kpi_name_kh', $dataFields)) {
          $whereClause->Where("kpi_name_kh", "Like", "%".$dataFields["kpi_name_kh"]."%");
        }
      }
      return $whereClause;
    }

    public static function getKPISubProgram($filter, $dataInfo, $fillable){
      $data = array();
      $whereClause = KPISubProgram::dataQuery($filter, $dataInfo, $fillable);
      $whereClause->whereNull("status");
      $result = collect($whereClause->get());
      foreach($result as $row){
        // Customize your data column here
        $data[] = array(
          "id" => $row->id,
          "planning_id" => $row->planning_id,
          "sub_program" => array("label" => (config_language == "en")?$row->SubProgram->code."-".$row->SubProgram->name_en:$row->SubProgram->code."-".$row->SubProgram->name_kh, "value" => $row->SubProgram->id),
          "sub_program_id" => array("label" => (config_language == "en")?$row->SubProgram->code."-".$row->SubProgram->name_en:$row->SubProgram->code."-".$row->SubProgram->name_kh, "value" => $row->SubProgram->id),
          "kpi_program_id" => $row->kpi_program_id,
          "subprog_to_entity_id" => $row->subprog_to_entity_id,
          "code" => $row->code,
          "kpi_name_en" => $row->kpi_name_en,
          "kpi_name_kh" => $row->kpi_name_kh,
          "order_level" => $row->order_level,
          "attachment" => $row->attachment,
          "year" => $row->year,
          "progress" => $row->progress,
          "acheivement" => $row->acheivement,
          "reference_kpi_id" => $row->reference_kpi_id,
          "status" => $row->status,
          "remark" => $row->remark,
          "is_active" => $row->is_active,
          "created_by" => $row->created_by,
          "created_by" => $row->created_by,
        );
      }
      return $data;
    }

    public static function getCount($filter, $dataInfo, $fillable){
      $whereClause = KPISubProgram::dataQuery($filter, $dataInfo, $fillable);
      $result = $whereClause->count();
      return $result;
    }
}
