<?php

namespace App\Http\Controllers;
use App\Models\Settings\Account;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\User;
use DB;
use Auth;
use DateTime;
use CommonService;
class ChartAccountController extends Controller
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
        $this->db_table = new Account;
        $this->langPath = $this->path;

        $this->PERMISSION_ACTION_LIST = "list_chart_account";
        $this->PERMISSION_ACTION_CREATE = "create_chart_account";
        $this->PERMISSION_ACTION_SHOW = "show_chart_account";
        $this->PERMISSION_ACTION_EDIT = "edit_chart_account";
        $this->PERMISSION_ACTION_UPDATE = "update_chart_account";
        $this->PERMISSION_ACTION_DELETE = "delete_chart_account";
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
                "data" => $this->db_table::getChatAccounts($filter),
                "limit" => config_limit,
                "total" => 0, //$this->db_table::getTotal($filter),
                "permissions" => CommonService::permission($this->db_table),
                "role_permissions" => CommonService::getPermissionsViaRoles(),
                "model_name" => $this->db_table->getTable(),
                // "data_fields" => $this->db_table::dataFields(),
                // "form_attributes" => $this->db_table::formAttributes(),
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
        $filter = [];
        if(isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_SHOW]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_SHOW] == true){
            $query = $this->db_table::find($id);
            if($query){
                $status = 200;
                $boolen = true;
                $message = trans('common.msg_successfully');
                $data["data"] = $query;
                $data["entity_id"] = CommonService::getSingleOpt(new Entity, $filter);
                $data["position"] = CommonService::getSingleOpt(new Position, $filter);
                $data["bank_id"] = CommonService::getSingleOpt(new Bank, $filter);
                $data["title"] = CommonService::getSingleOpt(new Title, $filter);
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

    public function updateIncentiveOfficer(Request $request){
        $input = $request->all();
        $id = $input["id"];
        if($input['user_id'] == null){
            // Role Id 4 is default role for mobile user
            $createUserId = User::insertGetId(
                [
                    "role_id" => 4,
                    "username" => $input["username"],
                    "password" => bcrypt($input["password"])
                ]
            );
            $table = Officer::where('id', $id)->update([
                "user_id" => $createUserId
            ]);
        }else{
            if($input["password"] != null){
                $table = User::where('id', $input['user_id'])->update([
                    "password" => bcrypt($input["password"])
                ]);
            }
        }
        $message = trans('incentive_officer.message_update');
        $data = array(
            "success" => true,
            "message" => $message
        );
        return response()->json($data); 
    }

    public function getOfficerGroupByEntities(Request $request){
        $input = $request->all();
        $entity_id = $input["entity_id"];
        $issueFromDate = date("Y-m-d", strtotime($input["issue_from_date"]));
        $issueToDate = date("Y-m-d", strtotime($input["issue_to_date"]));
        $allowCalTax = $input["allow_cal_tax"];
        // dd($input["issue_from_date"]);
        $numMonth = CommonService::GetNumberOfMonth($issueFromDate, $issueToDate);
        $activeOfficerInPeriod = 1; //Officer::activeOfficerInPeriod();
        if($activeOfficerInPeriod){
            $getIncentiveOfficerByEntities = Officer::getOfficerGroupByEntities($entity_id);
            $dataObj = array();
            $total_offier = 0;
            foreach($getIncentiveOfficerByEntities as $row){
                $incentiveOfficers = $row["officers"];
                $total_offier += count($incentiveOfficers);
                $officers = array();
                $i = 1;
                foreach($incentiveOfficers as $incentiveOfficer){
                    $officers[] = array(
                        "no" => $this->convertToKhmerNumber($i),
                        "id" => $incentiveOfficer["id"],
                        "user_id" => $incentiveOfficer["user_id"],
                        "first_name" => $incentiveOfficer["first_name"],
                        "last_name" => $incentiveOfficer["last_name"],
                        "duty" => $incentiveOfficer["duty"],
                        "bank_id" => $incentiveOfficer["bank_id"],
                        "bank_name" => $incentiveOfficer["bank_name"],
                        "bank_code" => $incentiveOfficer["bank_code"],
                        "entity" => $incentiveOfficer["entity"],
                        "entity_id" => $incentiveOfficer["entity_id"],
                        "position" => $incentiveOfficer["position"],
                        "duty" => $incentiveOfficer["duty"],
                        "bank_account_no" => $incentiveOfficer["bank_account_no"],
                        "updated_bank_acc" => $incentiveOfficer["updated_bank_acc"],
                        "tax" => $this->convertToKhmerNumber(number_format($this->calTaxableSalary(round($incentiveOfficer["incentive"]) * $numMonth, $allowCalTax))),
                        "return_amount" => $this->convertToKhmerNumber(number_format(0, 0)),
                        "incentive" => $incentiveOfficer["incentive"],
                        "incentive_kh" => $this->convertToKhmerNumber(number_format($incentiveOfficer["incentive"]), 0),
                        "incentive_after_tax" => $this->convertToKhmerNumber(number_format(floatval($incentiveOfficer["incentive"]) - floatval($this->calTaxableSalary(round($incentiveOfficer["incentive"]) * $numMonth, $allowCalTax)), 0)),
                        "reminder_amount" => $this->convertToKhmerNumber(number_format(round($incentiveOfficer["incentive"] * ($numMonth - 1)), 0)),
                        "total_incentive" => $this->convertToKhmerNumber(number_format(floatval(round($incentiveOfficer["incentive"]) * $numMonth), 0)),
                        "total_incentive_after_tax" => $this->convertToKhmerNumber(number_format((floatval(round($incentiveOfficer["incentive"]) * $numMonth) - floatval($this->calTaxableSalary(round($incentiveOfficer["incentive"]) * $numMonth, $allowCalTax))), 0))
                    );
                    $i++;
                }
                $dataObj[] = array(
                    "entity" => $row["entity"],
                    "officers" => $officers
                );

            }

            $data = array(
                "data" => $dataObj,
                "total" => $total_offier
            );
            $status = 200;
            return response()->json($data, $status);
        }
    }

    public function calTaxableSalary($incentive, $allowCalTax){
        $taxableSalaries = TaxableSalary::get();
        $taxToPay = 0;        
        if($allowCalTax == 'true'){
            if($incentive > 0){
                switch ($incentive) {
                    case ($incentive >= 0 && $incentive <= 1300000):
                        $taxToPay = ((floatval($incentive) / 100) * 0);
                    break;
                    case ($incentive >= 1300001 && $incentive <= 2000000):
                        $taxToPay = (((floatval($incentive) / 100) * 5) - 60000);
                    break;
                    case ($incentive >= 2000001 && $incentive <= 8500000):
                        ($taxToPay = ((floatval($incentive) / 100) * 10) - 160000);
                    break;
                    case ($incentive >= 8500001 && $incentive <= 12500000):
                        $taxToPay = (((floatval($incentive) / 100) * 15) - 585000);
                    break;
                    case ($incentive > 12500000):
                        $taxToPay = (((floatval($incentive) / 100) * 20) - 1210000);
                    break;
                    // default:
                    //     # code...
                    //     break;
                }
            }
        }
        return $taxToPay;
    }

    public function convertToKhmerNumber($number){
        $khmerNumber = [
           '1' => '១',
           '2' => '២',
           '3' => '៣',
           '4' => '៤',
           '5' => '៥',
           '6' => '៦',
           '7' => '៧',
           '8' => '៨',
           '9' => '៩',
           '0' => '០',
           '' => '០',
           'null' => '០'
         ];
         $arrNumbers = str_split($number);
         $newKhmerNumber = '';
         foreach ($arrNumbers as $number){
            if(isset($khmerNumber[$number])){
                $newKhmerNumber .= $khmerNumber[$number];
            }else{
                $newKhmerNumber .= ',';
            }
         }
         return $newKhmerNumber;
    }

    public function getOfficerByEntities(Request $request){
        $input = $request->all();
        $entity_id = $input["entity_id"];
        $getIncentiveOfficerByEntities = Officer::getOfficerByEntities($entity_id);
        $data = array(
            "data" => $getIncentiveOfficerByEntities,
            "total" => count($getIncentiveOfficerByEntities)
        );
        $status = 200;
        return response()->json($data, $status);
    }

    public function getOfficerListByEntities(Request $request){
        $input = $request->all();
        $entity_id = $input["entity_id"];
        $getIncentiveOfficerByEntities = Officer::getOfficerListByEntities($entity_id);
        $data = array(
            "data" => $getIncentiveOfficerByEntities,
            "total" => count($getIncentiveOfficerByEntities)
        );
        $status = 200;
        return response()->json($data, $status);
    }
}
