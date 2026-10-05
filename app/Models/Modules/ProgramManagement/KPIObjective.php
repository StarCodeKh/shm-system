<?php

  namespace App\Models\Modules\ProgramManagement;

  use Illuminate\Database\Eloquent\Model;
  use App\Models\Modules\ProgramManagement\KPIObjective;
  use OwenIt\Auditing\Contracts\Auditable;

  class KPIObjective extends Model implements Auditable
  {
    use \OwenIt\Auditing\Auditable;

    protected $table = "mef_kpi_objective";
    protected $primaryKey = "id";
    protected $fillable = [
                            "planning_id",
                            "obj_id",
                            "entity_id",
                            "code",
                            "kpi_name_en",
                            "kpi_name_kh",
                            "order_level",
                            "status",
                            "created_by",
                            "modified_by"
                          ];
    public $timestamps = true;

    public static function getKPI($filter){
      $query = KPIObjective::orderBy("order_level");
      $whereClause = $query;
      if($filter["search_field"]){
        $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
        $dataFields = $arraySingle;
        if (array_key_exists('obj_id', $dataFields)) {
          $whereClause->Where("obj_id", $dataFields["obj_id"]);
        }
      }
      $result = collect($whereClause->get());
      return $result;
    }

    public static function getCount($filter){
      $query = KPIObjective::orderBy($filter["sort"], $filter["order"]);
      $whereClause = $query;
      // $whereClause->whereNotIn("status", [4]);
      if($filter["search_field"]){
        $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
        $dataFields = $arraySingle;
        if (array_key_exists('obj_id', $dataFields)) {
          $whereClause->Where("obj_id", $dataFields["obj_id"]);
        }
      }
      $total = $whereClause->count();
      return $total;
    }
  }
