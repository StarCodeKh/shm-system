<?php

namespace App\Models\Modules\BudgetArrangement;

use Illuminate\Database\Eloquent\Model;
use App\Models\Modules\ProgramManagement\Objective;
use App\Models\Modules\ProgramManagement\Program;
use App\Models\Modules\ProgramManagement\SubProgram;
use DB;

class BSPSubProgramToEntity extends Model
{
    protected $table = 'mef_subprogram_to_entity';
    protected $fillable = [
                            'sub_program_id',
                            'planning_id',
                            'entity_id',
                            'entity_member_id',
                            'created_by',
                            'modified_by'
                        ];

    public $timestamps = true;
    
    public function SubProgram(){
      return $this->belongsTo(SubProgram::class, 'sub_program_id');
    }

    public static function getDataBySubProgs(){
        $objArr = array();
        $objectives = Objective::orderBy("order_level")->get();
        foreach($objectives as $objective){
            $objId = $objective->id;
            $proArr = array();
            $programs = Program::where("objective_id", $objId)->orderBy("order_level")->get();
            foreach($programs as $program){
                $progId = $program->id;
                $subProgsArr = array();
                $s2entities = DB::table("mef_subprogram_to_entity as s2e")
                            ->select("s2e.id", "s.name_en", "s.name_kh", "s.order_level")
                            ->Join("mef_subprogram as s", "s.id", "=", "s2e.sub_program_id")
                            ->where("s2e.planning_id", config_planning_year)
                            ->where("s.program_id", $progId)
                            ->get();
                
                foreach($s2entities as $s2entity){
                    $id = $s2entity->id;
                    $ca2entities = DB::table("mef_cluster_activity_to_entity as ca2e")
                                    ->select("ca.id", "ca.name_en", "ca.name_kh", "ca.order_level")
                                    ->join("mef_cluster_activity as ca", "ca.id", "=", "ca2e.sub_program_id")
                                    ->where("ca2e.sub_prog_to_entity_id", $id)
                                    ->get();
                    // Indicators
                    $indicatorSubprogs = BSPSubProgramToEntity::getKPIs("mef_kpi_subprogram", "", "", $id);
                    
                    $clusterAct2EntityArr = array();
                    foreach($ca2entities as $ca2entity){
                        $id = $ca2entity->id;
                        $act2Entities = DB::table("mef_activity_to_entity as a2e")
                                        ->select("a.id", "a.name_en", "a.name_kh", "a.order_level")
                                        ->join("mef_activity as a", "a.id", "=", "a2e.activity_id")
                                        ->where("a2e.cluster_act_to_entity_id", $id)
                                        ->get();
                        $act2EntityArr = array();
                        foreach($act2Entities as $act2Entity){
                            $act2EntityArr[] = array(
                                "id" => $act2Entity->id,
                                "name_en" => $act2Entity->name_en,
                                "name_kh" => $act2Entity->name_kh,
                                "order_level" => $act2Entity->order_level
                            );
                        }

                        $clusterAct2EntityArr[] = array(
                            "id" => $ca2entity->id,
                            "name_en" => $ca2entity->name_en,
                            "name_kh" => $ca2entity->name_kh,
                            "order_level" => $ca2entity->order_level,
                            "activities" => $act2EntityArr
                        );

                    }
                    $subProgsArr[] = array(
                        "id" => $s2entity->id,
                        "name_en" => $s2entity->name_en,
                        "name_kh" => $s2entity->name_kh,
                        "order_level" => $s2entity->order_level,
                        "cluster_activies" => $clusterAct2EntityArr,
                        "indicators" => $indicatorSubprogs
                    );
                }
                $proArr[] = array(
                    "code" => $program->code,
                    "sub_code" => $program->sub_code,
                    "name_en" => $program->name_en,
                    "name_kh" => $program->name_kh,
                    "remark" => $program->remark,
                    "order_level" => $program->order_level,
                    "created_by" => $program->created_by,
                    "modified_by" => $program->modified_by,
                    "sub_programs" => $subProgsArr
                );
            }
            $objArr[] = array(
                "code" => $objective->code,
                "name_en" => $objective->name_en,
                "name_kh" => $objective->name_kh,
                "remark" => $objective->remark,
                "order_level" => $objective->order_level,
                "programs" => $proArr
            );
        }
        return $objArr; 
    }

    public static function getKPIs($table, $clause, $order, $id){
        $indicatorArr = array();
        $indicators = DB::table("mef_kpi_subprogram")
                        ->where("planning_id", config_planning_year)
                        ->where("subprog_to_entity_id", $id)
                        ->orderBy("order_level")->get();
        foreach($indicators as $indicator){
            $indicatorArr[] = array(
                "kpi_name_en" => $indicator->kpi_name_en,
                "kpi_name_kh" => $indicator->kpi_name_kh,
                "code" => $indicator->code,
            );
        }
        return $indicatorArr;
    }
}