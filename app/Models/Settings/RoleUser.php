<?php

namespace App\Models\Settings;
use Spatie\Permission\Models\Role;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use DB;
use CommonService;
use Spatie\Permission\Traits\HasRoles;
class RoleUser extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable,HasRoles;

    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $guarded = [];
    public $timestamps = true;

    // public static function
    public static function dataFields(){
        // Loop Data Field From Table to put in array for mapping data field to search data table
        $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new Role)->getTable());
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
        $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new Role)->getTable());
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
        $query = RoleUser::orderByLevel()->whereNotDeleted()->get();
        $data = array();
        foreach($query as $row){
            $data[] = array(
                "label" => $row->name,
                "value" => $row->id,
            );
        }
        return $data;
    }

    public static function getSingleValueOpt(){
        $query = RoleUser::orderByLevel()->whereNotDeleted()->get();
        $data = array();
        foreach($query as $row){
            $data[] = array(
                "label" => $row->name,
                "value" => $row->name,
            );
        }
        return $data;
    }

    public static function getSingleOptById($param){
        $query = RoleUser::orderByLevel()->where("id", $param)->get();
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
        $query = RoleUser::orderByFilter($filter);
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
        $whereClause = RoleUser::checkQuery($filter);
        $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
        $whereClause->limit($filter["limit"]);
        $data = collect($whereClause->get());
        return $data;
    }

    public static function getTotal($filter){
        $total = 0;
        $whereClause = RoleUser::checkQuery($filter);
        $total = $whereClause->count();
        return $total;
    }

    // Set Permission
    public static function setPermission($input){
        // $permissions = [
        //   'list_roles',
        //   'create_roles',
        //   'show_roles',
        //   'edit_roles',
        //   'update_roles',
        //   'delete_roles',

        //   'list_officer',
        //   'create_officer',
        //   'show_officer',
        //   'edit_officer',
        //   'update_officer',
        //   'delete_officer',

        //   'list_users',
        //   'create_users',
        //   'show_users',
        //   'edit_users',
        //   'update_users',
        //   'delete_users',

        //   'list_account_type',
        //   'create_account_type',
        //   'show_account_type',
        //   'edit_account_type',
        //   'update_account_type',
        //   'delete_account_type',

        //   'list_letter_type',
        //   'create_letter_type',
        //   'show_letter_type',
        //   'edit_letter_type',
        //   'update_letter_type',
        //   'delete_letter_type',

        //   'list_bank',
        //   'create_bank',
        //   'show_bank',
        //   'edit_bank',
        //   'update_bank',
        //   'delete_bank',

        //   'list_position',
        //   'create_position',
        //   'show_position',
        //   'edit_position',
        //   'update_position',
        //   'delete_position',
        // ];
        // $role = RoleUser::findByName("admin");
        // foreach ($permissions as $permission) {
        //   Permission::firstOrCreate(['name' => $permission]);
        //   // $user->givePermissionTo($permissions);
        //   // $role->givePermissionTo($permission);
        // }
        // dd($input["role_info"]["name"]);
        $role = Role::where("name", $input["role_info"]["name"] ?? null)->first();
        if (!$role) {
            return false;
        }
        // { permission_name: true|false }; names that are missing or false are revoked
        $granted = collect($input["permission"] ?? [])
            ->filter(fn ($value) => $value === true || $value === 1 || $value === "1" || $value === "true")
            ->keys();
        $names = Permission::where("guard_name", $role->guard_name)->whereIn("name", $granted)->pluck("name")->all();
        $role->syncPermissions($names);
        return true;
    }

    public static function createPermission($input){
        // $permissions = [
            //     'list_roles',
            //     'create_roles',
            //     'show_roles',
            //     'edit_roles',
            //     'update_roles',
            //     'delete_roles',
        // ]
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
