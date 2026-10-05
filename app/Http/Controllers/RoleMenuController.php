<?php

namespace App\Http\Controllers;

use App\Models\UserDevice;
use App\Models\Settings\Title;
use App\Models\Settings\Entity;
use App\Models\Settings\Position;
use App\Models\Settings\RoleUser;
use App\Models\Settings\RoleMenu;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\User;
use DB;
use Auth;
use DateTime;
use CommonService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleMenuController extends Controller
{   
    protected $PERMISSION_ACTION_LIST;
    protected $PERMISSION_ACTION_CREATE;
    protected $PERMISSION_ACTION_SHOW;
    protected $PERMISSION_ACTION_EDIT;
    protected $PERMISSION_ACTION_UPDATE;
    protected $PERMISSION_ACTION_DELETE;
    protected $db_table;
    public $path = "admin/setting";

    public function __construct()
    {
        $this->middleware('auth');
        $this->view_title = $this->path.'.en_user';
        $this->db_table = new RoleMenu;
        $this->lang_path = $this->path;

        $this->PERMISSION_ACTION_LIST = "list_".$this->db_table->getTable();
        $this->PERMISSION_ACTION_CREATE = "create_".$this->db_table->getTable();
        $this->PERMISSION_ACTION_SHOW = "show_".$this->db_table->getTable();
        $this->PERMISSION_ACTION_EDIT = "edit_".$this->db_table->getTable();
        $this->PERMISSION_ACTION_UPDATE = "update_".$this->db_table->getTable();
        $this->PERMISSION_ACTION_DELETE = "delete_".$this->db_table->getTable();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $input = $request->all();
        $dataFields = $this->dataFields();
        $filter = CommonService::getFilter($input);
        $data = array(
            "data" => $this->db_table::getData($filter),
            "permissions" => CommonService::permission($this->db_table),
            "role_permissions" => CommonService::getPermissionsViaRoles()
        );
        $status = 200;
        $data["is_display"] = true;
        $data["message"] = trans('common.msg_save_successfully');
        return response()->json($data, $status);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = array();
        if(isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_CREATE]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_CREATE] == true){
            $input = $request->all();
            $dataFields = $this->dataForm($input);
            $dataFields["password"] = bcrypt($dataFields["password"]);
            $query = $this->db_table::create($dataFields);
            if($query){
                $arraySingle = call_user_func_array('array_merge', $input);
                $user = User::find($query->id);
                $user->assignRole($arraySingle["role"]);

                $permissions = Permission::get();
                foreach ($permissions as $permission) {
                    $user->givePermissionTo($permissions);
                }

                $data["data"] = $query;
                $status = 200;
                $boolen = true;
                $message = trans('common.msg_save_successfully');
            }else{
                $status = 500;
                $boolen = false;
                $message = trans('common.error_msg');
            }
        }else{
            $status = 500;
            $boolen = false;
            $message = trans('common.msg_no_permission_access');
        }
        $data = array(
            "success" => $boolen,
            "message" => $message,
            "data" => $data,
        );
        return response()->json($data, $status);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {   
        $data = array();
        if(isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_SHOW]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_SHOW] == true){
            $query = $this->db_table::find($id);
            if($query){
                $status = 200;
                $boolen = true;
                $message = trans('common.msg_successfully');
                $data["data"] = $query;
                $data["entity_id"] = Entity::getSingleOpt();
                $data["position"] = Position::getSingleOpt();
                $data["title"] = Title::getSingleOpt();
            }else{
                $status = 500;
                $boolen = false;
                $message = trans('common.error_msg');
            }
        }else{
            $status = 500;
            $boolen = false;
            $message = trans('common.msg_no_permission_access');
        }
        $data = array(
            "success" => $boolen,
            "message" => $message,
            "data" => $data,
        );
        return response()->json($data, $status);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = array();
        if(isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_UPDATE]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_UPDATE] == true){
            $input = $request->all();
            $arraySingle = call_user_func_array('array_merge', $input);
            // dd($arraySingle["role"]);
            // return false;

            $dataFields = $this->dataForm($input);
            if(isset($dataFields["password"]) && isset($dataFields["confirm_password"]) && $dataFields["password"] !== $dataFields["confirm_password"]){
                $status = 200;
                $boolen = false;
                $message = "លេខសម្ងាត់បញ្ជាក់ពុំផ្ទៀងផ្ទាត់គ្នា!";
            }else{
                if(isset($dataFields["password"])){
                    $dataFields["password"] = bcrypt($dataFields["password"]);
                }

                $table = $this->db_table::find($id);
                $table->update($dataFields);
                if($table){
                    // Assign Role to user
                    $user = User::findOrFail($id);
                    $user->assignRole($arraySingle["role"]);
                    // Assign Permission to User

                    $permissions = Permission::get();
                    foreach ($permissions as $permission) {
                        $user->givePermissionTo($permissions);
                    }

                    // Reponse Result
                    $data = $this->db_table::findOrFail($id);
                    $status = 200;
                    $boolen = true;
                    $message = trans('common.msg_update_successfully');
                }else{
                    $status = 500;
                    $boolen = false;
                    $message = trans('common.error_msg');
                }
            }
        }else{
            $status = 500;
            $boolen = false;
            $message = trans('common.msg_no_permission_access');
        }
        $data = array(
            "success" => $boolen,
            "message" => $message,
            "data" => $data
        );
        return response()->json($data, $status);
    }

    public function dataForm($input){
        $arr = $input;
        $pushArray = array_merge(array(["created_by" => Auth::user()->id]));
        $arraySingle = array_merge($arr, $pushArray);
        $result = call_user_func_array('array_merge', $arraySingle);
        $dataFields = $result;
        return $dataFields;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_DELETE]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_DELETE] == true){
            $table=$this->db_table::find($id);
            $table->update(["status" => 4]);
            if($table){
                $status = 200;
                $boolen = true;
                $message = trans('common.msg_delete_successfully');
            }else{
                $status = 500;
                $boolen = false;
                $message = trans('common.error_msg');
            }
            
        }else{
            $status = 500;
            $boolen = false;
            $message = trans('common.msg_no_permission_access');
        }
        $data = array(
            "success" => $boolen,
            "message" => $message
        );
        return response()->json($data, $status);
    }

    public function dataFields(){
        // Loop Data Field From Table to put in array for mapping data field to search data table
        $data["tables"] = DB::select('show columns from '.env("DB_PREFIX").($this->db_table)->getTable());
        $str = "";
        for($i=0; $i < count($data["tables"]); $i++){
            $str .= $data["tables"][$i]->Field.",";
        }
        $newArr = explode(",", $str);
        array_pop($newArr);
        $dataFields = $newArr;
        return $dataFields;
    }

    public function getProfile(Request $request){
        $id = Auth::user()->id;
        $table = $this->db_table::find($id);
        $data = array(
            "data" => $table,
            "form_attributes" => $this->db_table::formAttributes(),
        );
        return response()->json($data);
    }

    public function getUserDevice(Request $request){
        $input = $request->all();
        if(isset($input["user_id"]) && $input["user_id"]!=null){
            $input["user_id"] = $input["user_id"];
        }else{
            $input["user_id"] = Auth::user()->id;
        }
        $filter = CommonService::getFilter($input);
        $table = UserDevice::getData($filter);
        $data = array(
            "data" => $table,
            "limit" => config_limit,
            "total" => UserDevice::getTotal($filter),
            "data_fields" => UserDevice::dataFields(),
            "form_attributes" => $this->db_table::formAttributes()
        );
        return response()->json($data);
    }

    public function updateProfile(Request $request){
        $id = Auth::user()->id;
        $data = $request->all();
        $table = $this->db_table::where('id', $id)->update([
            "name" => $data["name"],
            "gender" => $data["gender"],
            "dob" => $data["dob"],
            "email" => $data["email"],
            "avatar" => $data["avatar"]
        ]);
        $data = array(
            "data" => $data
        );
        return response()->json($data);
    }
}
