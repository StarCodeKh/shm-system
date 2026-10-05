<?php

namespace App\Models\Settings;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use DB;
use Auth;

class Entity extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable,HasRoles;
    protected $table = 'entity';
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $guarded = [];
    public $timestamps = true;
    
    public function department(){
      return $this->belongsTo(Department::class,'department_id');
    }
    public function Entity(){
      return $this->belongsTo('App\Models\Entity','department_id');
    }

    public function ParentEntity(){
      return $this->belongsTo(Entity::class,'parent_id');
    }


    public static function getSingleOpt(){
      $data = array();
      $query = Entity::whereNotIn("status", [4])->orWhereNull("status");
      $whereClause = $query;
      $whereClause->orderBy("order_level");
      $whereClause->where("parent_id", 0)->orWhere("parent_id", null);
      if(Auth::user()->entity_id !=0 || Auth::user()->entity_id !=null){
        $whereClause->where("id", Auth::user()->entity_id);
      }
      $queryResult = collect($whereClause->get());
      foreach($queryResult as $row){
        $data[] = array(
          "label" => $row->code.'-'.$row->name_kh,
          "value" => $row->id,
        );
      }
      return $data;
    }

    public static function getOptionByDept($deptId){
      $data = array();
      $query = Entity::where("department_id", $deptId);
      $whereClause = $query;
      $whereClause->orderBy("order_level");
      // $whereClause->where("parent_id", 0)->orWhere("parent_id", null);
      // if(Auth::user()->entity_id !=0 || Auth::user()->entity_id !=null){
      //   $whereClause->where("id", Auth::user()->entity_id);
      // }
      $queryResult = collect($whereClause->get());
      foreach($queryResult as $row){
        $data[] = array(
          "label" => $row->code.'-'.$row->name_kh,
          "value" => $row->id,
        );
      }
      return $data;
    }

    public static function checkQuery($filter){
      $whereClause = "";
      $query = Entity::with("department")->orderBy($filter["sort"], $filter["order"]);
      $whereClause = $query;
      if($filter["search_field"]){
        $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
        $dataFields = $arraySingle;
        if(isset($filter["search_field"])){
          for($i=0 ; $i < count($filter["search_field"]); $i++){
            $field = array_key_first($filter["search_field"][$i]); //array('key1', 'key2', 'key3');
            if (array_key_exists($field, $dataFields)) {
              if(isset($filter["search_field"][$i][$field]["value"])){
                $whereClause->Where($field, 'Like', '%'.$filter["search_field"][$i][$field]["value"].'%');
              }else{
                $whereClause->Where($field, 'Like', '%'.$filter["search_field"][$i][$field].'%');
              }
            }
          }
        }
    }
    
    $whereClause->whereNotIn("status", [4]);
    $whereClause->orWhereNull("status");
    return $whereClause;
  }

  public static function getData($filter){
    $data = [];
    $whereClause = Entity::checkQuery($filter);
    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
    $whereClause->limit($filter["limit"]);
    $data = collect($whereClause->get());
    return $data;
  }

  public static function getTotal($filter){
    $total = 0;
    $whereClause = Entity::checkQuery($filter);
    $total = $whereClause->count();
    return $total;
  }

  public static function getEntityByActivity($param){
    $query = DB::table("mef_activity as a")
              ->select("e.id", "e.code", "e.name_en", "e.name_kh")
              ->join("entity as e", "e.id", "=", "a.entity_id")
              ->where("a.id", $param["param"]["value"])
              ->orderBy("a.code")
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
}
