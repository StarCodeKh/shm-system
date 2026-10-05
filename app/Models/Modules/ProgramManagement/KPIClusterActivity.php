<?php

namespace App\Models\Modules\ProgramManagement;

use Illuminate\Database\Eloquent\Model;
use App\Models\Modules\ProgramManagement\ClusterActivity;

class KPIClusterActivity extends Model
{
    protected $table = 'mef_kpi_cluster_activity';
    protected $primaryKey = 'id';
    protected $fillable = [
                            'planning_id',
                            'cluster_activity_id',
                            'kpi_sub_program_id',
                            'cluster_act_to_entity_id',
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

    public function ClusterActivity(){
      return $this->belongsTo(ClusterActivity::class,'cluster_activity_id');
    }

    public static function dataQuery($filter, $dataInfo, $fillable = array()){
      $query = KPIClusterActivity::orderBy("order_level");
      $whereClause = $query;   
      $whereClause->orderBy($filter["sort"], $filter["order"]);
      $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);       
      $whereClause->limit($filter["limit"]);
        // dd($dataInfo);
      if(isset($dataInfo["data_info"])){
        $whereClause->where("cluster_activity_id", $dataInfo["data_info"]["id"]);
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
        if (array_key_exists('cluster_activity_id', $dataFields)) {
          $whereClause->Where("cluster_activity_id", $dataFields["cluster_activity_id"]["value"]);
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

    public static function getKPIClusterAct($filter, $dataInfo, $fillable){
      $data = array();
      $whereClause = KPIClusterActivity::dataQuery($filter, $dataInfo, $fillable);
      $whereClause->where("status", "!=", 4);
      $result = collect($whereClause->get());
      foreach($result as $row){
        // Customize your data column here
        $data[] = array(
          "id" => $row->id,
          "planning_id" => $row->planning_id,
          "cluster_activity" => array("label" => (config_language == "en")?$row->ClusterActivity->code."-".$row->ClusterActivity->name_en:$row->ClusterActivity->code."-".$row->ClusterActivity->name_kh, "value" => $row->ClusterActivity->id),
          "cluster_activity_id" => array("label" => (config_language == "en")?$row->ClusterActivity->code."-".$row->ClusterActivity->name_en:$row->ClusterActivity->code."-".$row->ClusterActivity->name_kh, "value" => $row->ClusterActivity->id),
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
      $whereClause = KPIClusterActivity::dataQuery($filter, $dataInfo, $fillable);
      $result = $whereClause->count();
      return $result;
    }
}
