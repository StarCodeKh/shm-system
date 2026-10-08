<?php

namespace App\Http\Controllers;

use App\Models\OfficerRequest;
use App\Models\Officer;
use App\Models\Settings\Bank;
use App\Models\Settings\Currency;
use App\Models\Settings\PaymentMethod;
use App\Models\Settings\PaymentType;
use App\Models\Settings\Status;
use App\Models\Settings\Account;
use App\Models\Settings\Department;
use Illuminate\Http\Request;
use App\Notifications\DailyReminderNotification;
use DB;
use App\User;
use App\Models\UserDevice;
use CommonService;
use App\Services\PushNotificationService;
class OfficerRequestController extends Controller
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
        $this->db_table = new OfficerRequest;
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
            $dataList = $this->db_table::getData($filter);
            $data = array(
                "data" => $dataList,
                "limit" => $filter["limit"],
                "total" => $this->db_table::getTotal($filter),
                "summary" => array(
                    "in_review" => $dataList->where('status', 1)->count(),
                    "in_progress" => $dataList->where('status', 11)->count(),
                    "rejected" => $dataList->where('status', 7)->count(),
                    "approved" => $dataList->where('status', 3)->count()
                ),
                "permissions" => CommonService::permission($this->db_table),
                "role_permissions" => CommonService::getPermissionsViaRoles(),
                "model_name" => $this->db_table->getTable(),
                "data_fields" => $this->db_table::dataFields(),
                "form_attributes" => $this->db_table::formAttributes(),
                "form_payment_type" => PaymentType::formAttributes(),
                "code" => Account::getAccountCode(["OE"]),
                "bank" => Bank::getBankByName(),
                "bank_code" => Bank::getSingleOpt(),
                "officer_id" => isset($filter["data"]["officer_id"])?Officer::getSingleOptById($filter["data"]["officer_id"]):Officer::getSingleOpt(),
                "currency_id" => Currency::getSingleOpt(),
                "payment_method_id" => PaymentMethod::getSingleOpt(),
                "request_title" => PaymentType::getSingleOpt(),
                "status" => Status::getSingleOpt("GERNAL"),
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
            if (!empty($data['data']['id'])) {
                $table = $table=$this->db_table::find($data['data']['id']);
                $table->update($dataFields);
                // Status 3 mean this status has been approved so the notification will start notify to users
                if($dataFields["status"] == 3){// Status approved
                    $incentiveData = $dataFields;
                    unset($incentiveData['order_level']);
                    $incentiveData['ref_request_id'] = $table->id;
                    $incentiveData['code'] = $table->code;
                    $incentiveData['request_title'] = $table->request_title;
                    $checkExistRequest = DB::table("officer_incentive")->where("ref_request_id")->first();
                    if(!$checkExistRequest){
                        $result = DB::table("officer_incentive")->insert($incentiveData);
                    }
                    // Send Push Notification
                    $checkExistUser = User::where("officer_id", $dataFields["officer_id"])->first();
                    if($checkExistUser){
                        $userDevices = UserDevice::where("user_id", $checkExistUser->id)->get();
                        $deviceTokens = [];
                        foreach($userDevices as $userDevice){
                            $deviceTokens[] = array(
                                "device_token" => $userDevice->device_token
                            );
                        }
                        // Message to push
                        $currencyCode = Currency::codeOf($table['currency_id']) ?? Currency::KHR;
                        $amount = number_format($table['amount'], 2);
                        $title = "Your request has been approved";
                        $body = "Request for funding for academic members include the amount: " . $amount . " " . $currencyCode;

                        $checkExistUser->notify(new DailyReminderNotification($body));
                        $sendPushNotification = PushNotificationService::sendToMultipleDevices($deviceTokens, $title, $body, $data);
                    }
                    // ####End Send Push Notification
                }

                $status = 200;
                $boolen = true;
                $message = trans('common.msg_save_successfully');
            } else{
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
                // Status 3 mean this status has been approved so the notification will start notify to users
                if($dataFields["status"] == 3){// Status approved
                    $incentiveData = $dataFields;
                    unset($incentiveData['order_level']);
                    $incentiveData['ref_request_id'] = $table->id;
                    $incentiveData['code'] = $table->code;
                    $incentiveData['request_title'] = $table->request_title;
                    $checkExistRequest = DB::table("officer_incentive")->where("ref_request_id")->first();
                    if(!$checkExistRequest){
                        $result = DB::table("officer_incentive")->insert($incentiveData);
                    }
                    $currencyCode = Currency::codeOf($table['currency_id']) ?? Currency::KHR;
                    $amount = number_format($table['amount'], 2);
                    $getOfficer = Officer::where("id", $id)->first();

                    $title = "Request from ".$getOfficer->first_name.' '.$getOfficer->last_name.'-'.$getOfficer->identity_member_no." has been approved";
                    $message = "This Request from member to request the amount: " . $amount . " " . $currencyCode;

                    PushNotificationService::sendToMultipleDevices($title, $message, $type="Request", $data);
                    // ####End Send Push Notification
                }
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
        $pushArray = array_merge(array([]));
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

    /** Make Validation */
    public function validateCheck($param) {
        $data = array();
        $query = $this->db_table::where('code', $param['code'])->first();
        if (!empty($query) ) {
            $status = 200;
            $data = array(
                "success" => false,
                "message" =>   $message = trans('common.msg_request_code_exist', ['code' => $param['code']]),
                "data" => $data,
            );
            return response()->json($data, $status);
        }
    }

    public function getField(){
        $data = array(
            "form_attributes" => $this->db_table::formAttributes(),
            "officer_id" => isset($filter["data"]["officer_id"])?Officer::getSingleOptById($filter["data"]["officer_id"]):Officer::getSingleOpt(),
            "currency_id" => Currency::getSingleOpt(),
            "payment_method_id" => PaymentMethod::getSingleOpt(),
            "status" => Status::getSingleOpt("GERNAL"),
            "department_id" => Department::getSingleOpt(),
            "entity_id" => [],
        );
        $status = 200;
        return response()->json($data, $status);
    }
}
