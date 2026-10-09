<?php

    namespace App\Http\Controllers;
    use App\Imports\OfficerImport;
    use App\Models\Officer;
    use App\Models\OfficerPaymentRecurring;
    use App\Models\Settings\Department;
    use App\Models\Settings\Position;
    use App\Models\Settings\Title;
    use App\Models\Settings\Bank;
    use App\Models\Settings\Status;
    use App\Models\Settings\PaymentTerm;
    use Illuminate\Http\Request;
    use Auth;
    use CommonService;
    class OfficerController extends BaseController
    {
        protected $PERMISSION_ACTION_LIST;
        protected $PERMISSION_ACTION_CREATE;
        protected $PERMISSION_ACTION_SHOW;
        protected $PERMISSION_ACTION_UPDATE;
        protected $PERMISSION_ACTION_DELETE;
        protected $db_table;

        public function __construct()
        {
            $this->middleware('auth');
            $this->db_table = new Officer;

            $this->PERMISSION_ACTION_LIST = "list_".$this->db_table->getTable();
            $this->PERMISSION_ACTION_CREATE = "create_".$this->db_table->getTable();
            $this->PERMISSION_ACTION_SHOW = "show_".$this->db_table->getTable();
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
                    "limit" => $filter["limit"],
                    "total" => $this->db_table::getTotal($filter),
                    "summary" => array(
                        "total" => $this->db_table::getTotal($filter),
                        "male" => $this->db_table::getTotalOfficerByGender($filter, "male"),
                        "female" => $this->db_table::getTotalOfficerByGender($filter, "female"),
                    ),
                    "permissions" => CommonService::permission($this->db_table),
                    "role_permissions" => CommonService::getPermissionsViaRoles(),
                    "model_name" => $this->db_table->getTable(),
                    "data_fields" => $this->db_table::dataFields(),
                    "form_attributes" => $this->db_table::formAttributes(),
                    "department_id" => Department::getSingleOpt(),
                    "entity_id" => [],
                    "position" => CommonService::getSingleOpt(new Position, $filter),
                    "bank" => CommonService::getSingleOpt(new Bank, $filter),
                    "title" => CommonService::getSingleOpt(new Title, $filter),
                    "gender" => Status::getValueOpt("GENDER"),
                    "married_status" => Status::getValueOpt("MARRIED_STATUS"),
                    "emergency_relative" => Status::getValueOpt("RELATIONSHIP"),
                    "status" => Status::getSingleOpt(["STATUS", "OFFICER"]),
                    "is_nssf_member" => Status::getSingleOpt("NSSF"),
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
            $data = array();
            if(isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_LIST]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_LIST] == true){
                $input = array();
                $filter = CommonService::getFilter($input);
                $data = array(
                    "data_fields" => $this->db_table::dataFields(),
                    "form_attributes" => $this->db_table::formAttributes(),
                    "department_id" => Department::getSingleOpt(),
                    "entity_id" => [],
                    "position" => CommonService::getSingleOpt(new Position, $filter),
                    "bank" => CommonService::getSingleOpt(new Bank, $filter),
                    "title" => CommonService::getSingleOpt(new Title, $filter),
                    "gender" => Status::getValueOpt("GENDER"),
                    "married_status" => Status::getValueOpt("MARRIED_STATUS"),
                    "emergency_relative" => Status::getValueOpt("RELATIONSHIP"),
                    "status" => Status::getSingleOpt(["STATUS", "OFFICER"]),
                    "is_nssf_member" => Status::getSingleOpt("NSSF"),
                );
                $status = 200;
                $boolen = true;
                $message = __('common.msg_successfully');
                $data = array(
                    "success" => $boolen,
                    "message" => $message,
                    "data" => $data
                );
                return response()->json($data, $status);
            }else{
                $status = 500;
                $boolen = false;
                $message = trans('common.msg_no_permission_access');
                $data = array(
                    "success" => $boolen,
                    "message" => $message,
                    "data" => $data
                );
                return response()->json($data, $status);
            }
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
                $input[] = array("identity_member_no" => rand(100000000,9999999999));
                $dataFields = $this->dataForm($input);
                $data["data"] = $this->db_table::create($dataFields);
                if($data["data"]){
                    // When Create Member will generate Payment Recurring Cus when the schedule run all Recurring payment will be due every time
                    $getPaymentTerm = PaymentTerm::where("status", 1)->where("is_default", 1)->first();
                    OfficerPaymentRecurring::insert([
                        "officer_id" => $data["data"]["id"],
                        "payment_term" => $getPaymentTerm->name_kh, //
                        "recurring_amount" => $getPaymentTerm->recurring_amount,
                        "currency_id" => $getPaymentTerm->currency_id,
                        "executed_num" => $getPaymentTerm->execute_number,
                        "schedule_start_date" => $dataFields["register_date"],
                        "schedule_end_date" => date("Y-m-d", strtotime("2100-01-01")),
                        "remark" => "",
                        "is_default" => 1,
                        "status" => Status::ACTIVE, // Status = 13 means active
                    ]);
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
            $filter = [];
            if(isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_SHOW]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_SHOW] == true){
                $query = $this->db_table::find($id);
                if($query){
                    $input["officer_id"] = $id;
                    $input["page_number"] = 1;
                    $input["limit"] = 1;
                    $filter = CommonService::getFilter($input);
                    $status = 200;
                    $boolen = true;
                    $message = trans('common.msg_successfully');
                    $data["data"] = Officer::getData($filter)[0];
                    $data["department_id"] = Department::getSingleOpt();
                    $data["entity_id"] = [];
                    $data["position"] = CommonService::getSingleOpt(new Position, $filter);
                    $data["bank"] = CommonService::getSingleOpt(new Bank, $filter);
                    $data["title"] = CommonService::getSingleOpt(new Title, $filter);
                    $data["status"] = Status::getSingleOpt(["STATUS", "OFFICER"]);
                    $data["is_nssf_member"] = Status::getSingleOpt("NSSF");
                    $data["form_attributes"] = $this->db_table::formAttributes();
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

        public function import(Request $request){
            return CommonService::importSpreadsheet(new OfficerImport, $request);
        }

        public function createOfficer(Request $request)
        {
            try {
                // -----------------------------
                // 1. VALIDATION RULES
                // -----------------------------
                $validated = $request->validate([
                    'photo'                => 'required|string',
                    'first_name'           => 'required|string|max:150',
                    'last_name'            => 'required|string|max:150',
                    'latin'                => 'required|string|max:150',
                    'identity_no'          => 'required|string|max:50',
                    'identity_member_no'   => 'required|string|max:50',
                    'dob'                  => 'required|date',
                    'title'                => 'required|string|max:255',
                    'status'               => 'required',
                    // Optional
                    'nationality'          => 'nullable|string|max:250',
                    'duty'                 => 'nullable|string|max:255',
                    'entity_id'            => 'nullable|integer',
                    'department_id'        => 'nullable|integer',
                    'gender'               => 'nullable|string|max:20',
                    'address'              => 'nullable|string',
                    'email'                => 'nullable|email|max:255',
                    'bank_id'              => 'nullable|string|max:255',
                    'bank_name'            => 'nullable|string|max:255',
                    'bank_code'            => 'nullable|string|max:45',
                    'bank_account_no'      => 'nullable|string|max:45',
                    'updated_bank_acc'     => 'nullable|string|max:45',
                    'position'             => 'nullable|string|max:255',
                    'telephone_1'          => 'nullable|string|max:45',
                    'telephone_2'          => 'nullable|string|max:45',
                    'bank'                 => 'nullable|string|max:255',
                    'register_date'        => 'nullable|date',
                    'married_status'       => 'nullable|string|max:50',
                    'emergency_person'     => 'nullable|string|max:255',
                    'emergency_relative'   => 'nullable|string|max:50',
                    'is_nssf_member'       => 'nullable',
                    'nssf_card_no'         => 'nullable|string|max:100',
                    'social_media_fb'      => 'nullable|string|max:255',
                    'social_media_telegram'=> 'nullable|string|max:255',
                    'social_media_whatsapp'=> 'nullable|string|max:255',
                    'social_media_alt'     => 'nullable|string|max:255',
                    'remark'               => 'nullable|string',
                    'order_level'          => 'nullable|integer',
                    'created_by'           => 'required|integer'
                ]);

                // ✅ Step 2: Insert into DB
                $inserted = Officer::insertOfficer($validated);
                if ($inserted) {
                    return $this->successResponse(
                        ['officer' => $validated],
                        __('common.msg_save_successfully')
                    );
                }
                return $this->errorResponse(__('common.error_msg'));

            } catch (\Illuminate\Validation\ValidationException $e) {
                return $this->errorResponse(
                    'Validation failed',
                    422,
                    $e->errors()
                );
            } catch (Exception $e) {
                return $this->errorResponse($e->getMessage(), 500);
            }
        }
    }
