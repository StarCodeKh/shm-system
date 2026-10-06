<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;
use Auth;

class EntityMember extends Model
{
  protected $table = 'entity_member';
  // protected  $primaryKey = 'id';
  protected $fillable = [
                          "user_id",
                          "photo",
                          "identity_no",
                          "title",
                          "fullname",
                          "first_name",
                          "last_name",
                          "latin",
                          "duty",
                          "duty_id",
                          "entity",
                          "entity_id",
                          "gender",
                          "address",
                          "email",
                          "position",
                          "department",
                          "telephone_1",
                          "telephone_2",
                          "order_level",
                          "created_by",
                          "modified_by",
                          "is_active",
                        ];
                        
  public $timestamps = true;

  public function Entity(){
    return $this->belongsTo(Entity::class,'entity_id');
  }
  
  public static function getMemberByEntity($filter){
    $data = array();
    $query = EntityMember::orderByFilter($filter);
    $whereClause = $query;
    $whereClause->whereNotDeleted();
    $whereClause->orderByLevel()->get();
    if(Auth::user()->entity_id !=0 || Auth::user()->entity_id !=null){
      $whereClause->where("entity_id", Auth::user()->entity_id);
    }
    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
    $whereClause->limit($filter["limit"]);
    
    if($filter["search_field"]){
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if (array_key_exists('entity_id', $dataFields)) {
        $whereClause->Where("entity_id", 'Like', '%'.$dataFields["entity_id"]["value"].'%');
      }
      if (array_key_exists('fullname', $dataFields)) {
        $whereClause->Where("fullname", 'Like', '%'.$dataFields["fullname"].'%');
      }
      if (array_key_exists('latin', $dataFields)) {
        $whereClause->Where("latin", 'Like', '%'.$dataFields["latin"].'%');
      }
    }

    $queryResult = collect($whereClause->get());

    foreach($queryResult as $row){
      $data[] = array(
        'id' => $row->id,
        'entity' => $row->Entity->code.'-'.$row->Entity->name_kh,
        'entity_id' => $row->entity_id,
        'fullname' => $row->fullname,
        'is_active' => $row->is_active,
      );
    }
    return $data;
  }
  
  public static function getMembers(){
    $query = EntityMember::whereNotDeleted()->orderByLevel()->get();
    $data = array();
    foreach($query as $row){
      $data[] = array(
        "label" => $row->fullname,
        "value" => $row->id,
      );
    }
    return $data;
  }

  public static function getMemberByEntityID($param){
    $query = EntityMember::whereNotDeleted()->orderByLevel()->where("entity_id", $param["param"]["value"])->get();
    $data = array();
    foreach($query as $row){
      $data[] = array(
        "label" => $row->fullname,
        "value" => $row->id,
      );
    }
    return $data;
  }

  public static function getEntityMembers($filter){
    $data = array();
    $query = Entity::orderByFilter($filter);
    $whereClause = $query;
    $whereClause->whereNotDeleted();
    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);       
    $whereClause->limit($filter["limit"]);

    if($filter["search_field"]){
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if (array_key_exists("name_kh", $dataFields)) {
        $whereClause->Where("name_kh", $dataFields["name_kh"]);
      }
      if (array_key_exists("name_en", $dataFields)) {
        $whereClause->Where("name_en", $dataFields["name_en"]);
      }
    }
    $results = collect($whereClause->get());
    foreach($results as $row){
      $data[] = array(
        "user_id" => $row->user_id,
        "photo" => $row->photo,
        "identity_no" => $row->identity_no,
        "title" => $row->title,
        "fullname" => $row->fullname,
        "first_name" => $row->first_name,
        "last_name" => $row->last_name,
        "latin" => $row->latin,
        "duty" => $row->duty,
        "duty_id" => $row->duty_id,
        "entity" => $row->entity,
        "entity_id" => $row->entity_id,
        "gender" => $row->gender,
        "address" => $row->address,
        "email" => $row->email,
        "position" => $row->position,
        "department" => $row->department,
        "telephone_1" => $row->telephone_1,
        "telephone_2" => $row->telephone_2,
        "order_level" => $row->order_level,
        "created_by" => $row->created_by,
        "modified_by" => $row->modified_by,
        "is_active" => $row->is_active,
      );
    }
    return $data;
  }

  public static function getCount($filter){
    $query = EntityMember::orderByFilter($filter);
    $whereClause = $query;
    $whereClause->whereNotDeleted();
    if($filter["search_field"]){
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if (array_key_exists('entity_id', $dataFields)) {
        $whereClause->Where("entity_id", 'Like', '%'.$dataFields["entity_id"]["value"].'%');
      }
      if (array_key_exists('fullname', $dataFields)) {
        $whereClause->Where("fullname", 'Like', '%'.$dataFields["fullname"].'%');
      }
      if (array_key_exists('latin', $dataFields)) {
        $whereClause->Where("latin", 'Like', '%'.$dataFields["latin"].'%');
      }
    }
    $total = $whereClause->count();
    return $total;
  }
}