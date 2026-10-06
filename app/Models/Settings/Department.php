<?php

namespace App\Models\Settings;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use Auth;
class Department extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable,HasRoles;
    protected $table = 'department';
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $guarded = [];
    public $timestamps = false;

    public static function getSingleOpt(){
      $data = array();
      $query = Department::whereNotDeleted();
      $whereClause = $query;
      $whereClause->orderByLevel();
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

    public static function checkQuery($filter){
      $whereClause = "";
      $query = Department::orderByFilter($filter);
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
    
    $whereClause->whereNotDeleted();
    return $whereClause;
  }

  public static function getData($filter){
    $data = [];
    $whereClause = Department::checkQuery($filter);
    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
    $whereClause->limit($filter["limit"]);
    $data = collect($whereClause->get());
    return $data;
  }

  public static function getTotal($filter){
    $total = 0;
    $whereClause = Department::checkQuery($filter);
    $total = $whereClause->count();
    return $total;
  }

  public static function getDepartments(){
    $filter = array(
      "page_number" => 1,
      "limit" => 10,
      "sort" => "order_level",
      "order" => "asc",
      "search_field" => []
    );
    $query = Department::getData($filter);
    $data = array();
    foreach($query as $row){
      $data[] = array(
        "id" => $row->id,
        "name" => $row->name_kh
      );
    }
    return $data;
  }
}
