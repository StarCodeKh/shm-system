<?php
namespace App\Models\Settings;

use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use DB;
use CommonService;
use Spatie\Permission\Traits\HasRoles;

class RoleMenu extends Model implements Auditable
{
  use \OwenIt\Auditing\Auditable,HasRoles;
  
  protected $table = 'role_menu';
  protected $primaryKey = 'id';
  protected $fillable = [];
  protected $guarded = [];
  public $timestamps = true;

  public static function dataFields() {
    $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new RoleMenu)->getTable());
    $str = "";
    for($i=0; $i < count($data["tables"]); $i++){
      if($data["tables"][$i]->Null == "NO" && $data["tables"][$i]->Field != 'id'){
        $str .= $data["tables"][$i]->Field.",";
      }
    }
    $newArr = explode(",", $str);
    array_pop($newArr);
    $dataFields = $newArr;
    return $dataFields;
  }

  public static function formAttributes(){
    $formAttributes = array();
    $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new RoleMenu)->getTable());
    for($i=0; $i < count($data["tables"]); $i++){
      if($data["tables"][$i]->Field != "id" && $data["tables"][$i]->Field != "created_by" && $data["tables"][$i]->Field != "modified_by" && $data["tables"][$i]->Field != "created_at" && $data["tables"][$i]->Field != "updated_at"){
        $formAttributes[] = array(
          "name" => $data["tables"][$i]->Field,
          "type" => CommonService::convertDataType($data["tables"][$i]),
          "required" => $data["tables"][$i]->Null=="NO"?true:false,
        );
      }
    }
    return $formAttributes;
  }

  public static function getSingleOpt(){
    $query = RoleMenu::orderByLevel()->whereNotDeleted()->get();
    $data = array();
    foreach($query as $row){
      $data[] = array(
        "label" => $row->name,
        "value" => $row->id,
      );
    }
    return $data;
  }

  public static function getSingleOptById($param){
    $query = RoleMenu::orderByLevel()->where("id", $param)->get();
    $data = array();
    foreach($query as $row){
      $data[] = array(
        "label" => $row->first_name . $row->last_name . " (".$row->identity_no.")",
        "value" => $row->id,
      );
    }
    return $data;
  }

  public static function checkQuery($filter){
    $whereClause = "";
    $query = RoleMenu::orderByFilter($filter);
    $whereClause = $query;
    // if($filter["search_field"]){
    //   $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
    //   $dataFields = $arraySingle;
    //   if(isset($filter["search_field"])){
    //     for($i=0 ; $i < count($filter["search_field"]); $i++) {
    //       $field = array_key_first($filter["search_field"][$i]);
    //       if (array_key_exists($field, $dataFields)) {
    //         if(isset($filter["search_field"][$i][$field]["value"])){
    //           $whereClause->Where($field, 'Like', '%'.$filter["search_field"][$i][$field]["value"].'%');
    //         } else {
    //           $whereClause->Where($field, 'Like', '%'.$filter["search_field"][$i][$field].'%');
    //         }
    //       }
    //     }
    //   }
    // }
    
    //$whereClause->whereNotIn("status", [4]);
    //$whereClause->orWhereNull("status");
    return $whereClause;
  }

  public static function getData($filter){
    $data = [];
    $whereClause = RoleMenu::checkQuery($filter);
    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
    $whereClause->limit($filter["limit"]);
    $data = collect($whereClause->get());
    return $data;
  }

  public static function getTotal($filter){
    $total = 0;
    $whereClause = RoleMenu::checkQuery($filter);
    $total = $whereClause->count();
    return $total;
  }

  // Set Permission 
  public static function setPermission($input) {
    $roleName = $input["role_info"]["name"];
    $role = RoleMenu::findByName($roleName);
    DB::table("role_has_permissions")->where("role_id", $role->id)->delete();
    if($role){
      $permissions = [];
      $verifyPermission = $input["permission"];
      $allPermissions = Permission::get();
      foreach ($allPermissions as $permission) {
        $permissionInfo = Permission::findByName($permission->name);
        if($permission->name == $verifyPermission[$permission->name] && $verifyPermission[$permission->name] == true){
          $role->givePermissionTo($permission->name);
        }
      }
    }
    return true;
  }

  public static function createPermission($input) {
      $check = DB::table("permission_group")->where("name", $input["permission_group"])->first();
      $id = 0;
      if(!$check){
        $id = DB::table("permission_group")->insertGetId(
                [
                  "name" => $input["permission_group"],
                ]);
      }else{
        $id = $check->id;
      }
      $permissions = $input["permissions"]; 
      foreach ($permissions as $permission) {
        Permission::firstOrCreate(['group' => $id, 'name' => $permission]);
      }
      return true;
  }

}