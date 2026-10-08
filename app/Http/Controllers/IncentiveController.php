<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Settings\CurrencyExchange;
use App\Models\Incentive;
use App\Models\TaxableSalary;
use App\Models\NotificationUser;
use Auth;
use DB;
use CommonService;

class IncentiveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $db_table;
    public $path = "admin/setting";

    public function __construct()
    {
        $this->middleware('auth');
        $this->view_title = $this->path.'.entry_title';
        $this->dbTable = new Incentive;
        $this->langPath = $this->path;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $input = $request->all();
        $filter = CommonService::getFilter($input);
        $data = array(
            "data" => $this->dbTable::getData($filter),
            "limit" => config_limit,
            "total" => $this->dbTable::getTotal($filter),
            "data_fields" => $this->dbTable::dataFields(),
        );
        return response()->json($data);
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
        $input = $request->all();
        $allowCalTax = $input["allow_cal_tax"];
        $numMonth = CommonService::GetNumberOfMonth($input["issue_from_date"], $input["issue_to_date"]);
        $dateCreate = date("Y-m-d", strtotime($input["issue_date"]));
        $issueFromDate = date("Y-m-d", strtotime($input["issue_from_date"]));
        $issueToDate = date("Y-m-d", strtotime($input["issue_to_date"]));

        $totalIncentive = 0;
        $totalTax = 0;
        $totalRemind = 0;
        $i2Officers = $input["officerEntities"];
        for($jj = 0; $jj < sizeof($i2Officers); $jj++){
            $officers = $i2Officers[$jj]["officers"];
            for($kk = 0; $kk < sizeof($officers); $kk++){
                $totalIncentive += round($officers[$kk]["incentive"]);
                $totalTax += $this->calTaxableSalary((round($officers[$kk]["incentive"]) * $numMonth), $allowCalTax);
                if(intval($numMonth) > 1){
                    $totalRemind += round($officers[$kk]["incentive"] * ($numMonth - 1));
                }
            }
        }
        $insertGetId = Incentive::insertGetId([
            'title' => $input["title"],
            'khmer_date' => $input["khmer_date"],
            'khmer_day' => $input["khmer_day"],
            'sent_to' => $input["sent_to"],
            'prepared_by' => $input["prepared_by"],
            'seen_by' => $input["seen_by"],
            'approved_by' => $input["approved_by"],
            'reference' => $input["reference"],
            'letter_type' => $input["letter_type"],
            'total_entity' => count($input["entity"]),
            'total_officer' => count($input["officerEntities"]),
            'currency'  => 'Reil',
            'total_reminder' => $totalRemind,
            'total_incentive' => $totalIncentive,
            'total_tax' => $totalTax,
            'total_incentive_after_tax' => (floatval(($totalIncentive * $numMonth)) - floatval($totalTax)),
            'exchange_rate' => CurrencyExchange::rate(),
            'issue_date' => $dateCreate,
            'issue_from_date' => $issueFromDate,
            'issue_to_date' => $issueToDate,
            'effective_date' => $input["effective_date"],
            'attach_fille' => $input["attach_fille"],
            'allow_cal_tax' => $input["allow_cal_tax"],
            'created_by' => Auth::user()->id,
            'modified_by' => Auth::user()->id,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        $i2Banks = $input["bank"];
        for($i = 0; $i < sizeof($i2Banks); $i++){
            DB::table("incentive_to_bank")->insert(
                [
                    "incentive_id" => $insertGetId,
                    "bank_id" => $i2Banks[$i]["id"]
                ]
            );
        }

        $i2Entities = $input["entity"];
        for($j = 0; $j < sizeof($i2Entities); $j++){
            DB::table("incentive_to_entity")->insert(
                [
                    "incentive_id" => $insertGetId,
                    "entity_id" => $i2Entities[$j]["id"]
                ]
            );
        }

        for($j = 0; $j < sizeof($i2Officers); $j++){
            $officers = $i2Officers[$j]["officers"];
            for($k = 0; $k < sizeof($officers); $k++){
                    $bankAccOfficer = '';
                    if($officers[$k]["updated_bank_acc"] != null){
                        $bankAccOfficer = $officers[$k]["updated_bank_acc"];
                    }else{
                        $bankAccOfficer = $officers[$k]["bank_account_no"];
                    }

                    $totalOfficerIncentive = 0;
                    $taxByOfficer = 0;
                    $incentiveAfterTaxByOfficer = 0;
                    $totalRemindByOfficer = 0;
                    if($officers[$k]["incentive"] > 0){
                        $totalOfficerIncentive = round($officers[$k]["incentive"]);
                        $taxByOfficer = $this->calTaxableSalary((round($officers[$k]["incentive"] * $numMonth)), $allowCalTax);
                        if($numMonth > 1){
                            $totalRemindByOfficer = round($officers[$k]["incentive"] * ($numMonth - 1));
                        }
                    }
                    DB::table("incentive_to_officer")->insert(
                        [
                            "incentive_id" => $insertGetId,
                            "officer_id" => $officers[$k]["id"],
                            "bank_id" => $officers[$k]["bank_id"]?$officers[$k]["bank_id"]:0,
                            "bank" => $officers[$k]["bank_name"],
                            "bank_account_no" => $bankAccOfficer,
                            "bank_code" => $officers[$k]["bank_code"],
                            "entity" => $officers[$k]["entity"],
                            "entity_id" => $officers[$k]["entity_id"],
                            "position" => $officers[$k]["position"],
                            "duty" => $officers[$k]["duty"],
                            "incentive" => $totalOfficerIncentive,
                            "tax" => $taxByOfficer,
                            "incentive_after_tax" => (floatval(($officers[$k]["incentive"] * $numMonth)) - $this->calTaxableSalary(($officers[$k]["incentive"] * $numMonth), $allowCalTax)),
                            "reminder_amount" => $totalRemindByOfficer,
                            "issue_from_date" => date("Y-m-d", strtotime($input["issue_from_date"])),
                            "issue_to_date" => date("Y-m-d", strtotime($input["issue_to_date"])),
                            "created_by" => Auth::user()->id,
                        ]
                    );

                    if($officers[$k]["user_id"] != null){
                        NotificationUser::insert(
                            [
                                'user_id' => $officers[$k]["user_id"],
                                'officer_id' => $officers[$k]["id"],
                                'title' => $input["title"],
                                'description' => $input["title"],
                                'status' => '1',
                                'push_count' => '0',
                                'remark' => $input["title"],
                                'created_at' => date("Y-m-d H:i:s")
                            ]
                        );
                    }
            }
        }

        if($insertGetId){
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
            "data" => $insertGetId,
        );
        return response()->json($data, $status);
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

    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $data = $this->getFormIncentive($id);
        return response()->json($data);
    }

    public function getFormIncentive($id){
        $table = Incentive::find($id);
        $getIncentiveByBanks = Incentive::getIncentiveByBanks($id);
        $getIncentiveByEntities = Incentive::getIncentiveByEntities($id, 0);
        $incentiveByBanks = array();
        foreach($getIncentiveByBanks as $getIncentiveByBank){
            $incentiveByBanks[] = array(
                "no" => $this->convertToKhmerNumber($getIncentiveByBank["no"]),
                "id" => $getIncentiveByBank["id"],
                "label" => $getIncentiveByBank["label"],
                "total_incentive" => $this->convertToKhmerNumber(number_format($getIncentiveByBank["total_incentive"]), 0),
                "total_incentive_in_word" => $this->num2khtext($getIncentiveByBank["total_incentive"], true),
                "exchange_rate" => $this->convertToKhmerNumber($getIncentiveByBank["exchange_rate"])
            );
        }

        $entityData = array();
        $total_officer_entity = 0;
        foreach($getIncentiveByEntities as $getIncentiveByEntity){
            $officerData = array();
            $total_officer_entity += count($getIncentiveByEntity["officers"]);
            foreach($getIncentiveByEntity["officers"] as $officer){
                $officerData[] = array(
                    "no" => $this->convertToKhmerNumber($officer["no"]),
                    "identity_no" => $officer["identity_no"],
                    "first_name" => $officer["first_name"],
                    "last_name" => $officer["last_name"],
                    "bank" => $officer["bank"],
                    "bank_account_no" => $officer["bank_account_no"],
                    "latin" => $officer["latin"],
                    "position" => $officer["position"],
                    "duty" => $officer["duty"],
                    "index" => $officer["index"],
                    "incentive" => $this->convertToKhmerNumber(number_format($officer["incentive"]), 0),
                    "total_incentive_with_reminder" => $this->convertToKhmerNumber(number_format($officer["total_incentive_with_reminder"]), 0),
                    "tax" => $this->convertToKhmerNumber(number_format($officer["tax"]), 0),
                    "return_amount" => $this->convertToKhmerNumber(number_format($officer["return_amount"]), 0),
                    "reminder_amount" => $this->convertToKhmerNumber(number_format($officer["reminder_amount"]), 0),
                    "incentive_after_tax" => $this->convertToKhmerNumber(number_format($officer["incentive_after_tax"], 0)),
                );
            }
            $entityData[] = array(
                "no" => $this->convertToKhmerNumber($getIncentiveByEntity["no"]),
                "id" => $getIncentiveByEntity["id"],
                "label" => $getIncentiveByEntity["label"],
                "code" => $getIncentiveByEntity["code"],
                "entity" => $getIncentiveByEntity["label"],
                "total_incentive" => $this->convertToKhmerNumber(number_format(($getIncentiveByEntity["total_incentive"]), 0)),
                "total_incentive_with_reminder" => $this->convertToKhmerNumber(number_format(($getIncentiveByEntity["total_incentive_with_reminder"]), 0)),
                "total_officer" => $this->convertToKhmerNumber(number_format($getIncentiveByEntity["total_officer"], 0)),
                "officers" => $officerData
            );
        }

        $incentiveInfo = array(
            "id" => $table->id,
            "title" => $table->title,
            "khmer_date" => $table->khmer_date,
            "khmer_day" => $table->khmer_day,
            "khmer_year" => $this->convertToKhmerNumber(date("Y", strtotime($table["issue_from_date"]))),
            "count_month" => CommonService::GetNumberOfMonth($table["issue_from_date"], $table["issue_to_date"]),
            "khmer_month" => $this->convertToKhmerMonth(date("m", strtotime($table["issue_from_date"]))),
            "khmer_from_month" => $this->convertToKhmerMonth(date("m", strtotime($table["issue_from_date"] . " +1 days"))),
            "khmer_to_month" => $this->convertToKhmerMonth(date("m", strtotime($table["issue_to_date"]))),
            "khmer_current_year" => $this->convertToKhmerNumber(date("Y", strtotime($table["issue_date"]))),
            "khmer_current_day" => $this->convertToKhmerNumber(date("d", strtotime($table["issue_date"]))),
            "khmer_current_date" => $this->convertToKhmerDay(date("D", strtotime($table["issue_date"]))),
            "khmer_current_month" => $this->convertToKhmerMonth(date("m", strtotime($table["issue_date"]))),

            "letter_type" => $table->letter_type,
            "sent_to" => $table->sent_to,
            "prepared_by" => $table->prepared_by,
            "seen_by" => $table->seen_by,
            "approved_by" => $table->approved_by,
            "reference" => $table->reference,
            "issue_date" => date("Y-m-d", strtotime($table->issue_date)),
            "issue_from_date" => date("Y-m-d", strtotime($table->issue_from_date)),
            "issue_to_date" => date("Y-m-d", strtotime($table->issue_to_date)),
            "issue_date_khmer" => date("Y-m-d", strtotime($table->issue_date)),
            "effective_date" => $table->effective_date,
            "attach_fille" => $table->attach_fille,
            "is_approve" => $table->is_approve,
            "total_incentive" => number_format($table->total_incentive),
            "total_incentive_in_khmer" => $this->convertToKhmerNumber(number_format($table->total_incentive), 0),
            "total_incentive_in_word" => $this->num2khtext($table->total_incentive, true),

            "total_incentive_with_reminder_in_khmer" => $this->convertToKhmerNumber(number_format(($table->total_reminder + $table->total_incentive)), 0),
            "total_incentive_with_reminder_in_word" => $this->num2khtext($table->total_reminder + $table->total_incentive, true),
            "total_return_amount_in_khmer" => $this->convertToKhmerNumber(number_format($table->total_return_amount), 0),
            "total_incentive_after_tax_in_khmer" => $this->convertToKhmerNumber(number_format($table->total_incentive_after_tax), 0),
            "total_reminder_amount_in_khmer" => $this->convertToKhmerNumber(number_format($table->total_reminder), 0),

            "currency" => $table->currency,
            "total_tax" => $table->total_tax,
            "total_tax_in_khmer" => $this->convertToKhmerNumber(number_format(($table->total_tax), 0)),
            "total_tax_in_word" => $this->num2khtext($table->total_tax, true),
            "total_entity" => $this->convertToKhmerNumber($table->total_entity),
            "total_officer" => $this->convertToKhmerNumber($total_officer_entity),
            "exchange_rate" => $table->exchange_rate,
            "remark" => $table->remark,
            "created_at" => $table->created_at,
            "updated_at" => $table->updated_at,
        );
        $dataArray = array(
            "general" => $incentiveInfo,
            "total_bank" => $this->convertToKhmerNumber(count($incentiveByBanks) + 1),
            "incentiveByBanks" => $incentiveByBanks,
            "incentiveByEntities" => $entityData,
        );
        $data = array(
            "data" => $dataArray
        );
        return $data;
    }

    public function getIncentiveOfficerByBanks(Request $request){
        $input = $request->all();
        $getIncentiveByBanks = Incentive::getIncentiveOfficerByBanks($input["incentiveId"], $input["bankId"]);
        $getIncentiveByEntities = Incentive::getIncentiveByEntities($input["incentiveId"], $input["bankId"]);
        $getIncentiveListByEntities = Incentive::getIncentiveListByEntities($input["incentiveId"], $input["bankId"]);
        $incentiveByBanks = array();
        $k=0;
        foreach($getIncentiveByBanks as $getIncentiveByBank){
            $incentiveByBanks[] = array(
                "no" => $this->convertToKhmerNumber($getIncentiveByBank["no"]),
                "entity" => $getIncentiveByBank["entity"],
                "total_incentive_after_tax" => $this->convertToKhmerNumber(number_format($getIncentiveByBank["total_incentive_after_tax"]), 0),
                "total_incentive" => $this->convertToKhmerNumber(number_format($getIncentiveByBank["total_incentive"]), 0),
                "total_incentive_in_word" => $this->num2khtext($getIncentiveByBank["total_incentive"], true),
                "exchange_rate" => $this->convertToKhmerNumber($getIncentiveByBank["exchange_rate"])
            );
            $k++;
        }

        $entityData = array();
        $total_officer_entity = 0;
        $sum_incentive_after_tax = 0;
        $p = 0;
        foreach($getIncentiveByEntities as $getIncentiveByEntity){
            $officerData = array();
            $total_officer_entity += count($getIncentiveByEntity["officers"]);

            foreach($getIncentiveByEntity["officers"] as $officer){

                $officerData[] = array(
                    "no" => $this->convertToKhmerNumber($officer["no"]),
                    "identity_no" => $officer["identity_no"],
                    "title" => $officer["title"],
                    "first_name" => $officer["first_name"],
                    "last_name" => $officer["last_name"],
                    "bank" => $officer["bank"],
                    "bank_account_no" => $officer["bank_account_no"],
                    "latin" => $officer["latin"],
                    "position" => $officer["position"],
                    "duty" => $officer["duty"],
                    "index" => $officer["index"],
                    "incentive" => $this->convertToKhmerNumber(number_format($officer["incentive"]), 0),
                    "tax" => $this->convertToKhmerNumber(number_format($officer["tax"]), 0),
                    "return_amount" => $this->convertToKhmerNumber(number_format($officer["return_amount"]), 0),
                    "reminder_amount" => $this->convertToKhmerNumber(number_format($officer["reminder_amount"]), 0),
                    "incentive_after_tax" => $this->convertToKhmerNumber(number_format($officer["incentive_after_tax"], 0)),
                );
            }
            $sum_incentive_after_tax += $getIncentiveByEntity["total_incentive_after_tax"];
            $entityData[] = array(
                "no" => $this->convertToKhmerNumber($getIncentiveByEntity["no"]),
                "id" => $getIncentiveByEntity["id"],
                "label" => $getIncentiveByEntity["label"],
                "code" => $getIncentiveByEntity["code"],
                "entity" => $getIncentiveByEntity["label"],
                "total_incentive" => $this->convertToKhmerNumber(number_format($getIncentiveByEntity["total_incentive"], 0)),
                "total_incentive_after_tax" => $this->convertToKhmerNumber(number_format($getIncentiveByEntity["total_incentive_after_tax"], 0)),
                "total_officer" => $this->convertToKhmerNumber(number_format($getIncentiveByEntity["total_officer"], 0)),
                "officers" => $officerData
            );
            $p++;
        }

        // Row Officers #################
        $rawOfficerData = array();
        foreach($getIncentiveListByEntities as $officerRaw){
            $rawOfficerData[] = array(
                "no" => $this->convertToKhmerNumber($officerRaw["no"]),
                "identity_no" => $officerRaw["identity_no"],
                "title" => $officerRaw["title"],
                "first_name" => $officerRaw["first_name"],
                "last_name" => $officerRaw["last_name"],
                "bank" => $officerRaw["bank"],
                "bank_account_no" => $officerRaw["bank_account_no"],
                "latin" => $officerRaw["latin"],
                "position" => $officerRaw["position"],
                "duty" => $officerRaw["duty"],
                "index" => $officerRaw["index"],
                "incentive" => $this->convertToKhmerNumber(number_format($officerRaw["incentive"]), 0),
                "tax" => $this->convertToKhmerNumber(number_format($officerRaw["tax"]), 0),
                "return_amount" => $this->convertToKhmerNumber(number_format($officerRaw["return_amount"]), 0),
                "reminder_amount" => $this->convertToKhmerNumber(number_format($officerRaw["reminder_amount"]), 0),
                "incentive_after_tax" => $this->convertToKhmerNumber(number_format($officerRaw["incentive_after_tax"], 0)),
            );
        }

        $data = array(
            "incentiveByBanks" => $incentiveByBanks,
            "incentiveByEntities" => $entityData,
            "incentiveOfficerListByEntities" => $rawOfficerData,
            "totalBank" => $this->convertToKhmerNumber(count($incentiveByBanks)),
            "sum_incentive_after_tax" => $this->convertToKhmerNumber(number_format($sum_incentive_after_tax, 0)),
            "sum_incentive_after_tax_inword" => $this->num2khtext($sum_incentive_after_tax, true),
        );
        return response()->json($data);
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

    public function convertToKhmerMonth($monthNumber) {
        $month = [
           '01' => 'មករា',
           '02' => 'កុម្ភះ',
           '03' => 'មីនា',
           '04' => 'មេសា',
           '05' => 'ឧសភា',
           '06' => 'មិថុនា',
           '07' => 'កក្កដា',
           '08' => 'សីហា',
           '09' => 'កញ្ញា',
           '10' => 'តុលា',
           '11' => 'វិច្ឆិកា',
           '12' => 'ធ្នូ',
        ];
        return $month[$monthNumber];
    }

    public function convertToKhmerDay($str) {
        $day = [
           'Sun' => 'អាទិត្យ',
           'Mon' => 'ច័ន្ទ',
           'Tue' => 'អង្គារ',
           'Wed' => 'ពុធ',
           'Thu' => 'ព្រហស្បត្តិ៍',
           'Fri' => 'សុក្រ',
           'Sat' => 'សៅរ៍'
        ];
        return $day[$str];
    }

    public function num2khtext($complete_char,$enableThousand){
        //function for split uft8 character
        // function mb_str_split( $string ) {
        //     //Split at all position not after the start: ^
        //     //and not before the end: $
        //     return preg_split('/(?<!^)(?!$)/u', $string );
        // }
        //remove left zeros
        $cleanStr = ltrim($complete_char, '0');
        //split number/string to array
        $num_arr = mb_str_split($cleanStr);
        $translated=''; $addThousand=false;
        //string array
        $khNUMTxt = array('','មួយ','ពីរ','បី','បួន','ប្រាំ');
        $twoLetter = array('','ដប់','ម្ភៃ','សាមសិប','សែសិប','ហាសិប','ហុកសិប','ចិតសិប','ប៉ែតសិប','កៅសិប');
        $khNUMLev = array('','','','រយ','ពាន់','មឿន','សែន','លាន');
        $khnum = array('០','១','២','៣','៤','៥','៦','៧','៨','៩');
        //loop to check each number character
        foreach($num_arr as $key=>$value){
        //convert khmer number to latin number if found
        if(in_array($value,$khnum)){$value = array_search($value,$khnum);}
        //allow only number
        if(!is_numeric($value)){return '';}
        //check what pos the charactor in
        $pos = count($num_arr) - ($key);
        if($pos>count($khNUMLev)-1){$pos=($pos % count($khNUMLev))+2;}
        //enable or diable read in thousand
        if($enableThousand and ($pos == 5 or $pos == 6)){$pos = $pos-3;}
        //concatenate number as text
        if($pos==2){
        $translated .= $twoLetter[$value];
        }else{
        if($value>5){$translated .=  $khNUMTxt[5].$khNUMTxt[$value - 5];}else{$translated .= $khNUMTxt[$value];}
        }
        //work for thousand
        if($pos==2 or $pos == 3 or $pos == 4){
        if($value>0){$addThousand=true;}
        }
        //concatenate number level
        if($value>0 or ($pos==4 and $addThousand and $enableThousand) or $pos==7){
        $translated .= $khNUMLev[$pos];
        }
        //make addthousand to default value (false)
        if($pos==4){$addThousand=false;}
        }
        return $translated;
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
        $input = $request->all();
        $allowCalTax = $input["allow_cal_tax"];
        $numMonth = CommonService::GetNumberOfMonth($input["issue_from_date"], $input["issue_to_date"]);
        $dateCreate = date("Y-m-d", strtotime($input["issue_date"]));
        $issueFromDate = date("Y-m-d", strtotime($input["issue_from_date"]));
        $issueToDate = date("Y-m-d", strtotime($input["issue_to_date"]));
        $totalIncentive = 0;
        $totalTax = 0;
        $totalRemind = 0;
        $i2Officers = $input["officerEntities"];
        for($jj = 0; $jj < sizeof($i2Officers); $jj++){
            $officers = $i2Officers[$jj]["officers"];
            for($kk = 0; $kk < sizeof($officers); $kk++){
                $totalIncentive += round($officers[$kk]["incentive"]);
                $totalTax += $this->calTaxableSalary((round($officers[$kk]["incentive"]) * $numMonth), $allowCalTax);
                if($numMonth > 1){
                    $totalRemind += round($officers[$kk]["incentive"] * ($numMonth - 1));
                }
            }
        }
        $table = Incentive::Where('id', $id)->update(
            [
                'title' => $input["title"],
                'khmer_date' => $input["khmer_date"],
                'khmer_day' => $input["khmer_day"],
                'sent_to' => $input["sent_to"],
                'prepared_by' => $input["prepared_by"],
                'seen_by' => $input["seen_by"],
                'approved_by' => $input["approved_by"],
                'reference' => $input["reference"],
                'letter_type' => $input["letter_type"],
                'total_entity' => count($input["entity"]),
                'total_officer' => count($input["officerEntities"]),
                'currency'  => 'Reil',
                'total_incentive' => $totalIncentive,
                'total_tax' => $totalTax,
                'total_reminder' => $totalRemind,
                'total_incentive_after_tax' => (floatval($totalIncentive * $numMonth) - floatval($totalTax)),
                'exchange_rate' => CurrencyExchange::rate(),
                'allow_cal_tax' => $input["allow_cal_tax"],
                'issue_date' => $dateCreate,
                'issue_from_date' => $issueFromDate,
                'issue_to_date' => $issueToDate,
                'effective_date' => $dateCreate,
                'attach_fille' => $input["attach_fille"],
                'modified_by' => Auth::user()->id,
            ]
        );

        DB::table("incentive_to_bank")->Where("incentive_id", $id)->update(["status" => 4]);
        $i2Banks = $input["bank"];
        for($i = 0; $i < sizeof($i2Banks); $i++){
            DB::table("incentive_to_bank")->insert(
                [
                    "incentive_id" => $id,
                    "bank_id" => $i2Banks[$i]["id"]
                ]
            );
        }

        DB::table("incentive_to_entity")->Where("incentive_id", $id)->update(["status" => 4]);
        $i2Entities = $input["entity"];
        for($j = 0; $j < sizeof($i2Entities); $j++){
            DB::table("incentive_to_entity")->insert(
                [
                    "incentive_id" => $id,
                    "entity_id" => $i2Entities[$j]["id"]
                ]
            );
        }

        DB::table("incentive_to_officer")->Where("incentive_id", $id)->update(["status" => 4]);
        for($j = 0; $j < sizeof($i2Officers); $j++){
            $officers = $i2Officers[$j]["officers"];
            for($k = 0; $k < sizeof($officers); $k++){
                $bankAccOfficer = '';
                if($officers[$k]["updated_bank_acc"] != null){
                    $bankAccOfficer = $officers[$k]["updated_bank_acc"];
                }else{
                    $bankAccOfficer = $officers[$k]["bank_account_no"];
                }
                $totalOfficerIncentive = 0;
                $taxByOfficer = 0;
                $incentiveAfterTaxByOfficer = 0;
                $totalRemindByOfficer = 0;
                if($officers[$k]["incentive"] > 0){
                    $totalOfficerIncentive = round($officers[$k]["incentive"]);
                    $taxByOfficer = $this->calTaxableSalary((round($officers[$k]["incentive"] * $numMonth)), $allowCalTax);
                    if($numMonth > 1){
                        $totalRemindByOfficer = round($officers[$k]["incentive"] * ($numMonth - 1));
                    }
                }
                DB::table("incentive_to_officer")->insert(
                    [
                        "incentive_id" => $id,
                        "officer_id" => $officers[$k]["id"],
                        "bank_id" => $officers[$k]["bank_id"]?$officers[$k]["bank_id"]:0,
                        "bank" => $officers[$k]["bank_name"],
                        "bank_account_no" => $bankAccOfficer,
                        "bank_code" => $officers[$k]["bank_code"],
                        "entity" => $officers[$k]["entity"],
                        "entity_id" => $officers[$k]["entity_id"],
                        "position" => $officers[$k]["position"],
                        "duty" => $officers[$k]["duty"],
                        "incentive" => $totalOfficerIncentive,
                        "tax" => $taxByOfficer,
                        "incentive_after_tax" => (floatval($totalOfficerIncentive * $numMonth) - floatval($taxByOfficer)),
                        "reminder_amount" => $totalRemindByOfficer,
                        "issue_from_date" => date("Y-m-d", strtotime($input["issue_from_date"])),
                        "issue_to_date" => date("Y-m-d", strtotime($input["issue_to_date"])),
                        "created_by" => Auth::user()->id,
                    ]
                );
            }
        }

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
            "message" => $message
        );
        return response()->json($data, $status);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $table = Incentive::where('id', $id)->update(["status" => 4]);
        DB::table("incentive_to_bank")->Where("incentive_id", $id)->update(["status" => 4]);
        DB::table("incentive_to_entity")->Where("incentive_id", $id)->update(["status" => 4]);
        DB::table("incentive_to_officer")->Where("incentive_id", $id)->update(["status" => 4]);

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
