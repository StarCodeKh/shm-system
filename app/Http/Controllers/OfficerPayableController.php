<?php

namespace App\Http\Controllers;

use App\Models\OfficerPayable;
use App\Models\Settings\CurrencyExchange;
use App\Models\OfficerPayment;
use App\Imports\OfficerPayableImport;
use App\Models\Officer;
use App\Models\Settings\Currency;
use App\Models\Settings\PaymentMethod;
use App\Models\Settings\PaymentType;
use App\Models\Settings\Status;
use App\Models\Settings\Account;
use App\Models\Settings\Department;
use Illuminate\Http\Request;
use Auth;
use CommonService;
use App\Services\PushNotificationService;
use Maatwebsite\Excel\Facades\Excel;

class OfficerPayableController extends Controller
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
        $this->db_table = new OfficerPayable;
        $this->db_table_officer_payment = new OfficerPayment;
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
            $input["sort"] = "payment_date";
            $input["order"] = "desc";
            $filter = CommonService::getFilter($input);
            $dataList = $this->db_table::getData($filter);
            $data = array(
                "data" => $dataList,
                "summary" => array(
                    "total_paid_khr" =>  $this->convertToKhmerNumber(
                        CommonService::formatDoubleNum(collect($dataList->where('status', Status::PAID))->sum('amount'))
                    ),
                    "total_unpaid_khr" =>  $this->convertToKhmerNumber(
                        CommonService::formatDoubleNum(collect($dataList->where('status', Status::UNPAID))->sum('amount'))
                    )
                ),
                "limit" => $filter["limit"],
                "total" => $this->db_table::getTotal($filter),
                "permissions" => CommonService::permission($this->db_table),
                "role_permissions" => CommonService::getPermissionsViaRoles(),
                "model_name" => $this->db_table->getTable(),
                "data_fields" => $this->db_table::dataFields(),
                "form_attributes" => $this->db_table::formAttributes(),
                "officer_id" => isset($filter["data"]["officer_id"])?Officer::getSingleOptById($filter["data"]["officer_id"]):Officer::getSingleOpt(),
                "department_id" => Department::getSingleOpt(),
                "entity_id" => [],
                "currency_id" => Currency::getSingleOpt(),
                "account_id" => Account::getAccountCode(["AR", "FA"]),
                "payment_method_id" => PaymentMethod::getSingleOpt(),
                "request_title" => PaymentType::getSingleOpt(),
                "status" => Status::getSingleOpt("PAYMENT"),
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
        $status_unpaid = "15"; //Unpaid Amount
        $status_paid_amount = "19"; // Paid Amount
        $data = array();
        if(isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_UPDATE]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_UPDATE] == true){
            $input = $request->all();
            $dataFields = $this->dataForm($input);
            $table = $table=$this->db_table::find($id);
            $table->update($dataFields);

            if($table){
                if (in_array($table["status"], [$status_unpaid, $status_paid_amount, 16, 20, 21])) {
                    // Common values
                    $currencyCode = Currency::codeOf($table['currency_id']) ?? Currency::KHR;
                    $amount = number_format($table['amount'], 2);
                    $getOfficer = Officer::where("id", $table->officer_id)->first();
                    // Determine notification title and message
                    switch ($table["status"]) {
                        case $status_unpaid:
                            $title = "Payment Unpaid";
                            $message  = "There is an update regarding the payment from ".$getOfficer->first_name.' '.$getOfficer->last_name.'-'.$getOfficer->identity_member_no.". Please check the details.";
                            break;

                        case $status_paid_amount:
                            $title = "Payment Successful";
                            $message  = "".$getOfficer->first_name.' '.$getOfficer->last_name.'-'.$getOfficer->identity_member_no." has been paid successfully.";
                            break;

                        case 16:
                            $title = "Payment Marked as Void";
                            $message  = "This payment has been marked as void.";
                            break;

                        case 20:
                            $title = "Exceptional Case Update";
                            $message  = "This payment has been flagged as an exceptional case. Please contact the admin for more information.";
                            break;

                        case 21:
                            $title = "Register Fee Reminder";
                            $message  = "Please complete the registration fee.";
                            break;

                        default:
                            $title = "Payment Update";
                            $message  = "There is an update regarding the payment from ".$getOfficer->first_name.' '.$getOfficer->last_name.'-'.$getOfficer->identity_member_no.". Please check the details.";
                    }
                    PushNotificationService::sendToMultipleDevices($title, $message, $type="Payment", $data);
                    $checkIfPaid = $this->db_table_officer_payment::where("ref_payable_id", $id)->first();
                    if(!$checkIfPaid && $dataFields["status"] == $status_paid_amount){
                        $this->insertOfficerPaymentHistory($id);
                    }
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

   public function insertOfficerPaymentHistory($id)
    {
        $get_payable_info = $this->db_table::find($id);
        $dataFields = $this->dataFormOfficerPayment($get_payable_info, $id);
        return $this->db_table_officer_payment::create($dataFields);
    }

    protected function dataFormOfficerPayment($get_payable_info, $ref_payable_id) {
        // Rate from the payable currency to the other one (KHR <-> USD), from the exchange rates in Settings
        $code = Currency::codeOf($get_payable_info->currency_id) ?? Currency::KHR;
        $currency_exchange = CurrencyExchange::rate($code, $code === Currency::KHR ? Currency::USD : Currency::KHR);
        return [
            'account_id'         => $get_payable_info->account_id,
            'officer_id'         => $get_payable_info->officer_id,
            'ref_payable_id'     => $ref_payable_id,
            'receipt_no'         => $this->generateReceiptNumber(),
            'amount'             => $get_payable_info->amount,
            'payment_date'       => $get_payable_info->payment_date,
            'issue_date'         => now(),
            'currency_id'        => $get_payable_info->currency_id,
            'exchange_rate'      => $currency_exchange,
            'payment_method_id'  => $get_payable_info->payment_method_id,
            'note'               => $get_payable_info->remark,
            'attach'             => $get_payable_info->attach,
            'status'             => $get_payable_info->status,
            'created_by'         => $get_payable_info->created_by,
            'modified_by'        => $get_payable_info->modified_by,
            'created_at'         => $get_payable_info->created_at,
            'updated_at'         => $get_payable_info->updated_at,
        ];
    }

    public function generateReceiptNumber()
    {
        $date = now()->format('Ymd');
        $countToday = \DB::table('officer_payment')
            ->whereDate('updated_at', now()->toDateString())
            ->count();
        $nextSeq = $countToday + 1;

        return 'RCPT-' . $date . '-' . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
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

    public function convertToKhmerNumber($number)
    {
        return strtr((string)$number, [
            '0'=>'០','1'=>'១','2'=>'២','3'=>'៣','4'=>'៤',
            '5'=>'៥','6'=>'៦','7'=>'៧','8'=>'៨','9'=>'៩'
        ]);
    }

    public function getField(){
        $data = array(
            "form_attributes" => $this->db_table::formAttributes(),
            "officer_id" => isset($filter["data"]["officer_id"])?Officer::getSingleOptById($filter["data"]["officer_id"]):Officer::getSingleOpt(),
            "department_id" => Department::getSingleOpt(),
            "entity_id" => [],
        );
        $status = 200;
        return response()->json($data, $status);
    }

    public function import(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new OfficerPayableImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>__('common.file_uploaded')], 200);
        }else{
            return response()->json(['success'=>false,'message'=>__('common.file_upload_failed')], 500);
        }
    }

    public function getPayableInfoByOfficer(Request $request){
        $input = $request->all();
        $result = $this->db_table::getPayableInfoByOfficer($input);
        $status = 200;
        $data = array(
                    "officerDetail" => $result["officerDetail"],
                    "payables" => $result["payables"],
                    "paymentHistories" => $result["paymentHistories"],
                    "success" => true
                    );
        return response()->json($data, $status);
    }

    public function getGeneralDataForPayable(Request $request){
        $filters = array(
            "sort" => "id",
            "order" => "desc",
            "page_number" => 0,
            "limit" => setting('config_limit'),
            "keysearch" => $request->all(),
        );
        $officers = Officer::getData($filters);
        $dataOfficers = array();
        foreach($officers as $officer){
            $dataOfficers[] = array(
                "label" => $officer->full_name."-".$officer->identity_member_no,
                "value" => $officer->id
            );
        }
        $data = array(
            "officers" => $dataOfficers,//isset($filter["data"]["officer_id"])?Officer::getOfficerByFilter($filter["data"]["officer_id"]):Officer::getSingleOpt(),
            "currency_id" => Currency::getSingleOpt(),
            "account_id" => Account::getAccountCode(["AR", "FA"]),
            "payment_method_id" => PaymentMethod::getSingleOpt(),
            "status" => Status::getSingleOpt("PAYMENT"),
        );
        $status = 200;
        $boolen = true;
        return response()->json($data, $status);
    }
}
