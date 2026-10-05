<?php

namespace App\Http\Controllers;

use App\Models\UserDevice;
use App\Models\Officer;
use App\Models\Settings\Title;
use App\Models\Settings\Department;
use App\Models\Settings\Entity;
use App\Models\Settings\Position;
use App\Models\Settings\RoleUser;
use App\Models\Settings\Status;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use DB;
use Auth;
use CommonService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class UserController extends Controller
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
        $this->db_table = new User;
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
        if(isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_LIST]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_LIST] == true){
            if(isset($input["officer_id"])){
                $input["officer_id"] = $input["officer_id"];
            }
            $filter = CommonService::getFilter($input);
            $genders = array(
                [
                    "label" => "ប្រុស",
                    "value" => "ប្រុស"
                ],[
                    "label" => "ស្រី",
                    "value" => "ស្រី"
                ],
            );
            $entities = Entity::getSingleOpt();
            $roles = "";//Role::getRoleValues($filter);
            $titles = Title::getSingleOpt();
            $positions = Position::getSingleOpt();
            
            $data = array(
                "data" => $this->db_table::getData($filter),
                "permissions" => CommonService::permission($this->db_table),
                "role_permissions" => CommonService::getPermissionsViaRoles(),
                "model_name" => $this->db_table->getTable(),
                // "entity_id" => $entities,
                "officer_id" => isset($filter["data"]["officer_id"])?Officer::getSingleOptById($filter["data"]["officer_id"]):Officer::getSingleOpt(),
                "role_id" => RoleUser::getSingleOpt(),
                "role" => RoleUser::getSingleValueOpt(),
                "gender" => $genders,
                "title" => $titles,
                "position" => $positions,
                "status" => Status::getSingleOpt("STATUS"),
                "form_attributes" => User::formAttributes(),
                "data_fields" => User::dataFields(),
                "limit" => config_limit,
                "total" => User::getTotal($filter)
            );
            $status = 200;
            $data["is_display"] = true;
            $data["message"] = trans('common.msg_save_successfully');
            return response()->json($data, $status);
        }else{
            $status = 500;
            $data["is_display"] = false;
            $data["message"] = trans('common.msg_no_permission_access');
            return response()->json($data, $status);
        }
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
            // $validator = Validator::make($dataFields, [
            //     'username' => 'required|string|unique:users,username',
            //     'email' => 'required|email|unique:users,email',
            //     'password' => 'required|confirmed|min:6',
            // ]);
            $rules = array(
                'username' => 'required|string|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed|min:6|string|regex:/[A-Z]/|regex:/[@$!%*#?&]/'
            );
            $messages = [
                'password.confirmed' => 'Password not match!',
                'password.regex' => 'Password at least one uppercase[A-Z] and on special character [@$!%*#?&]!',
            ];
            $validator = Validator::make($dataFields, $rules,$messages);

            if ($validator->fails()) {
                return response()->json([
                    "success" => false,
                    'message' => $validator->errors(),
                ], 200);
            }
        
            unset($dataFields['password_confirmation']);
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
                $data["role"] = RoleUser::getSingleValueOpt();
                $data["departments"] = Department::getSingleOpt();
                $data["entities"] = Entity::getSingleOpt();
                // $data["entity_id"] = Entity::getSingleOpt();
                $data["position"] = Position::getSingleOpt();
                $data["title"] = Title::getSingleOpt();
                $data["status"] = Title::getSingleOpt("STATUS");
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
            $dataFields = $this->dataForm($input);
            $user = User::where("id", $id)->first();

            if($user->username!=$dataFields["username"]){
                $validator = Validator::make($dataFields, [
                    'username' => 'required|string|max:255|unique:users,username'
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        "success" => false,
                        'message' => $validator->errors(),
                    ], 200);
                }
            }
            if($user->email!=$dataFields["email"]){
                $validator = Validator::make($dataFields, [
                    'email' => 'required|email|unique:users,email,'
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        "success" => false,
                        'message' => $validator->errors(),
                    ], 200);
                }
            }
        
            if(isset($dataFields["password"])){
                // $validator = Validator::make($dataFields, [
                //     'password' => 'required|confirmed|min:6|string|regex:/[0-9]/|regex:/[A-Z]/',
                // ]);
                // if ($validator->fails()) {
                //     return response()->json([
                //         "success" => false,
                //         'message' => $validator->errors(),
                //     ], 200);
                // }
                // 'regex:/[a-z]/',            // at least one lowercase
                // 'regex:/[A-Z]/',            // at least one uppercase
                // 'regex:/[0-9]/',            // at least one digit
                // 'regex:/[@$!%*#?&]/',       // at least one special char
                $rules = array('password' => 'confirmed|min:6|string|regex:/[A-Z]/|regex:/[@$!%*#?&]/');
                $messages = [
                    'password.confirmed' => 'Password not match!',
                    'password.regex' => 'Password at least one uppercase[A-Z] and on special character [@$!%*#?&]!',
                ];
                $validator = Validator::make($dataFields, $rules,$messages);
                if ($validator->fails()) {
                    return response()->json([
                        "success" => false,
                        'message' => $validator->errors(),
                    ], 200);
                }

                $dataFields["password"] = bcrypt($dataFields["password"]);
            }else{
                unset($dataFields['password']);
            }
            unset($dataFields['password_confirmation']);
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

    // public function getUserByOfficers(Request $request){
    //     $input = $request->all();
    //     $data = array();
    //     $filter = CommonService::getFilter($input);
    //     $filter["officer_id"] = $input["officer_id"];
    //     $genders = array(
    //         [
    //             "label" => "ប្រុស",
    //             "value" => "ប្រុស"
    //         ],[
    //             "label" => "ស្រី",
    //             "value" => "ស្រី"
    //         ],
    //     );
    //     $entities = Entity::getSingleOpt();
    //     $roles = "";//Role::getRoleValues($filter);
    //     $titles = Title::getSingleOpt();
    //     $positions = Position::getSingleOpt();
    //     $data = array(
    //         "data" => $this->db_table::getData($filter),
    //         "permissions" => CommonService::permission($this->db_table),
    //         "role_permissions" => CommonService::getPermissionsViaRoles(),
    //         "model_name" => $this->db_table->getTable(),
    //         // "entity_id" => $entities,
    //         "role_id" => RoleUser::getSingleOpt(),
    //         "gender" => $genders,
    //         "title" => $titles,
    //         "position" => $positions,
    //         "status" => Status::getSingleOpt("STATUS"),
    //         "form_attributes" => User::formAttributes(),
    //         "data_fields" => User::dataFields(),
    //         "limit" => config_limit,
    //         "total" => User::getTotal($filter)
    //     );
    //     return response()->json($data);
    // }

    public function getProfile(Request $request){
        
        $input = $request->all();
        $data = array();
        $officer = "";
        if(isset($input["officer_id"]) && $input["officer_id"]!=null){
            $officer = Officer::where("id", $input["officer_id"])->first();
            if($officer){
                $id = $officer->user_id;
            }else{
                $id = null;
            }
        }else{
            $id = Auth::user()->id;
        }
        // dd($this->db_table::getProfile($id));
        $data["data"] = $this->db_table::getProfile($id);
        $data["form_attributes"] = $this->db_table::formAttributes();
        $data["role"] = RoleUser::getSingleValueOpt();
        $data["departments"] = Department::getSingleOpt();
        $data["entities"] = Entity::getSingleOpt();
        // $data["entity_id"] = Entity::getSingleOpt();
        $data["position"] = Position::getSingleOpt();
        $data["title"] = Title::getSingleOpt();
        $data["status"] = Title::getSingleOpt("STATUS");
        $data = array(
            "data" => $data
        );
        return response()->json($data);
    }

    public function getUserDevice(Request $request){
        $input = $request->all();
        if(isset($input["officer_id"]) && $input["officer_id"]!=null){
            $filter["officer_id"] = $input["officer_id"];
            // $officer = Officer::where("id", (int)$input["officer_id"])->first();
            // if($officer){
            //     $input["user_id"] = $officer->user_id;
            // }else{
            //     $input["user_id"] = null;
            // }
        }
        // else{
        //     // $input["user_id"] = Auth::user()->id;
        // }
        $filter = CommonService::getFilter($input);
        $table = UserDevice::getData($filter);
        $data = array(
            "data" => $table,
            "limit" => config_limit,
            "total" => UserDevice::getTotal($filter),
            "permissions" => CommonService::permission($this->db_table),
            "role_permissions" => CommonService::getPermissionsViaRoles(),
            "model_name" => $this->db_table->getTable(),
            "data_fields" => UserDevice::dataFields(),
            "form_attributes" => $this->db_table::formAttributes(),
            // "permissions" => CommonService::permission($this->db_table),
            // "role_permissions" => CommonService::getPermissionsViaRoles(),
            // "model_name" => $this->db_table->getTable(),
        );
        return response()->json($data);
    }

    public function updateProfile(Request $request){
        $id = Auth::user()->id;
        $data = $request->all();
        $table = $this->db_table::where('id', $id)->update([
            "fullname" => $data["fullname"],
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
