<?php

namespace App\Http\Controllers;
use App\Models\Income;
use App\Exports\IncomeExport;
use App\Models\Officer;
use App\Models\Settings\Currency;
use App\Models\Settings\Department;
use App\Models\Settings\Entity;
use App\Models\Settings\PaymentMethod;
use App\Models\Settings\Account;
use App\Models\Settings\Status;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use DB;
use Auth;
use CommonService;
use Maatwebsite\Excel\Facades\Excel;

class IncomeController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
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
        $this->view_title = $this->path.'.entry_title';
        $this->db_table = new Income;
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
        $data = array();
        if(isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_LIST]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_LIST] == true){
            $input = $request->all();
            $filter = CommonService::getFilter($input);
            $data = array(
                "data" => $this->db_table::getData($filter),
                "limit" => config_limit,
                "total" => $this->db_table::getTotal($filter),
                "permissions" => CommonService::permission($this->db_table),
                "role_permissions" => CommonService::getPermissionsViaRoles(),
                "model_name" => $this->db_table->getTable(),
                "data_fields" => $this->db_table::dataFields(),
                "form_attributes" => $this->db_table::formAttributes(),
                "currency_id" => Currency::getSingleOpt(),
                "department_id" => Department::getSingleOpt(),
                "entity_id" => [],
                "payment_method_id" => PaymentMethod::getSingleOpt(),
                "account_code" => Account::getAccountCodeList(),
                "status" => Status::getSingleOpt("STATUS")
            );
            $status = 200;
            $boolen = true;
            $data["message"] = trans('common.msg_save_successfully');
            return response()->json($data, $status);
        }else{
            $status = 500;
            $boolen = false;
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
            $data["data"] = $this->db_table::create($dataFields);
            if($data["data"]){
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
            $dataFields = $this->dataForm($input);
            $table = $table=$this->db_table::find($id);
            $table->update($dataFields);
            if($table){
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

    public function export(Request $request){
        return Excel::download(new IncomeExport, $this->db_table->getTable().'.xlsx');
    }

    public function insertIncome(Request $request)
    {
        try {
            // ✅ Step 1: Validation
            $validated = $request->validate([
                'title_en'       => 'required|string|max:120',
                'title_kh'        => 'nullable|string|max:120',
                'department_id'  => 'nullable|integer',
                'entity_id'      => 'required|integer',
                'account_code'   => 'required|integer',
                'category'       => 'nullable|string|max:120',
                'payment_method_id' => 'required|integer',
                'amount'         => 'required|numeric',
                'exchange_rate'  => 'nullable|numeric',
                'remark'         => 'nullable|string',
                'reference'      => 'nullable|string|max:120',
                'currency_id'    => 'required|integer',
                'status'         => 'required|integer',
                'created_by'     => 'required|integer',
            ]);

            // ✅ Step 2: Insert into DB
            $inserted = Income::insertIncome($validated);

            if ($inserted) {
                return $this->successResponse(
                    ['income' => $validated],
                    'Income record inserted successfully.'
                );
            }

            return $this->errorResponse('Failed to insert income record.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse('Validation failed', 422, $e->errors());
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function getField(){
        $data = array(
            "form_attributes" => $this->db_table::formAttributes(),
            "currency_id" => Currency::getSingleOpt(),
            "payment_method_id" => PaymentMethod::getSingleOpt(),
            "account_code" => Account::getAccountCodeList(),
            "status" => Status::getSingleOpt("STATUS")
            // "officer_id" => isset($filter["data"]["officer_id"])?Officer::getSingleOptById($filter["data"]["officer_id"]):Officer::getSingleOpt(),
            // "department_id" => Department::getSingleOpt(),
            // "entity_id" => [],
        );
        $status = 200;
        return response()->json($data, $status);
    }
    
}