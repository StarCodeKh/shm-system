<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Services\FormSchema;
use App\Models\Settings\AccountGroup;
use App\Models\Settings\AccountType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use CommonService;
use App\Models\Settings\Status;

class AccountGroupController extends Controller
{
    protected $db_table;
    protected $view_title;
    protected $lang_path;
    public $path = "admin/setting";

    public function __construct()
    {
        $this->middleware('auth');
        $this->view_title = $this->path.'.entry_account_group_title';
        $this->db_table = new AccountGroup;
        $this->lang_path = $this->path;
        $this->PERMISSION_ACTION_LIST = "list_".$this->db_table->getTable();
        $this->PERMISSION_ACTION_CREATE = "create_".$this->db_table->getTable();
        $this->PERMISSION_ACTION_SHOW = "show_".$this->db_table->getTable();
        $this->PERMISSION_ACTION_EDIT = "edit_".$this->db_table->getTable();
        $this->PERMISSION_ACTION_UPDATE = "update_".$this->db_table->getTable();
        $this->PERMISSION_ACTION_DELETE = "delete_".$this->db_table->getTable();
    }

    public function dataFields(){
        $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().($this->db_table)->getTable());
        $str = "";
        for($i=0; $i < count($data["tables"]); $i++){
            $str .= $data["tables"][$i]->Field.",";
        }
        $newArr = explode(",", $str);
        array_pop($newArr);
        $dataFields = $newArr;
        return $dataFields;
    }

    public function index(Request $request)
    {
        if(isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_LIST]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_LIST] == true){
            $input = $request->all();
            $dataFields = $this->dataFields();
            $filter = CommonService::getFilter($input);
            $accountTypes = AccountType::getAccTypes();
            $data = array(
                "data_fields" => $this->dataFields(),
                "data" => $this->db_table::getAccountGroups($filter),
                "form_attributes" => FormSchema::fields($this->db_table->getTable()),
                "model_name" => $this->db_table->getTable(),
                "account_type_id" => $accountTypes,
                "permissions" => CommonService::permission($this->db_table),
                "role_permissions" => CommonService::getPermissionsViaRoles(),
                "limit" => setting('config_limit'),
                "total" => $this->db_table::getCount($filter)
            );
            $status = 200;
            $data["is_display"] = true;
            $data["message"] = trans('common.msg_save_successfully');
            return response()->json($data, $status);
        } else {
            $status = 500;
            $data["is_display"] = false;
            $data["message"] = trans('common.msg_no_permission_access');
            return response()->json($data, $status);
        }
    }

    public function create()
    {

    }

    public function store(Request $request)
    {
        $input = $request->all();
        $dataFields = $this->dataForm($input);
        $table = $this->db_table::create($dataFields);
        if($table){
            $status = 200;
            $boolen = true;
            $message = trans('common.msg_save_successfully');
        }else{
            $status = 500;
            $boolen = false;
            $message = trans('common.error_msg');
        }
        $data = array(
            "success" => $boolen,
            "message" => $message,
            "data" => $table,
        );
        return response()->json($data, $status);
    }

    public function show($id)
    {
        $table = $table=$this->db_table::find($id);
        $data = array(
            "data" => $table
        );
        return response()->json($data);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        $dataFields = $this->dataForm($input);
        $table = $table=$this->db_table::find($id);
        $table->update($dataFields);
        if($table){
            $status = 200;
            $boolen = true;
            $message = trans('common.msg_update_successfully');
        }else{
            $status = 500;
            $boolen = false;
            $message = trans('common.error_msg');
        }
        $data = array(
            "success" => $boolen,
            "message" => $message,
            "data" => $this->db_table::findOrFail($id)
        );
        return response()->json($data, $status);
    }

    public function dataForm($input){
        $arr = $input;
        $push_array = array_merge(array(["created_by" => Auth::user()->id]));
        $arraySingle = array_merge($arr, $push_array);
        $result = call_user_func_array('array_merge', $arraySingle);
        $dataFields = $result;
        return $dataFields;
    }

    public function destroy($id)
    {
        $table=$this->db_table::find($id);
        $table->update(["status" => Status::DELETED]);
        if($table){
            $status = 200;
            $boolen = true;
            $message = trans('common.msg_delete_successfully');
        }else{
            $status = 500;
            $boolen = false;
            $message = trans('common.error_msg');
        }
        $data = array(
            "success" => $boolen,
            "message" => $message
        );
        return response()->json($data, $status);
    }
}
