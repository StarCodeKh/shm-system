<?php

namespace App\Models;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\Settings\PaymentTerm;
use App\Models\Settings\Status;
use App\Models\Settings\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use DB;
use Auth;
use CommonService;
use App\Jobs\InsertDataJob;
class OfficerPaymentRecurring extends Model implements Auditable
{
  use \OwenIt\Auditing\Auditable;
  protected $table = 'officer_payment_recurring';
  protected $primaryKey = 'id';
  protected $fillable = [];
  protected $guarded = [];
  public $timestamps = true;
  protected $casts = [
    'schedule_start_date' => 'datetime:'.("Y-m-d"),
    'schedule_end_date' => 'datetime:'.("Y-m-d"),
  ];
  public function officer(){
    return $this->belongsTo(Officer::class, 'officer_id');
  }

  public function status()
  {
    return $this->belongsTo(Status::class, "status");
  }

  public function currency()
  {
    return $this->belongsTo(Currency::class, "currency_id");
  }

  public function payables()
    {
      return $this->hasMany(OfficerPayable::class, "officer_id");
    }

  /**
     * Determine if the recurring schedule is active for a given month.
     */
    public function isActiveForMonth(Carbon $month): bool
    {
      return $this->status == 13 // mean status active
          && $this->schedule_start_date->lessThanOrEqualTo($month->endOfMonth())
          && ($this->schedule_end_date === null || $this->schedule_end_date->greaterThanOrEqualTo($month->startOfMonth()));
    }

    /**
     * Get the next due date for this recurring payment based on its term.
     * This is a simplified example and might need more complex logic for edge cases.
     */
    public function getNextDueDate(Carbon $currentMonth, $officerId): Carbon
    {
      // Find the last payable for this recurring schedule
      $lastPayable = OfficerPayable::where("officer_id", $officerId)
                                    ->whereIn("status", [15,16,19,20])
                                    ->orderBy('due_date', "desc")
                                    ->first();
      if ($lastPayable) {
        // If a payable exists, the next due date is based on its due_date
        if ($this->payment_term === 'Monthly') {
          return $lastPayable->due_date->addMonth()->startOfMonth();
        } elseif ($this->payment_term === 'Yearly') {
          return $lastPayable->due_date->addYear()->startOfMonth();
        }
      } else {
        // If no payable exists, the first due date is based on schedule_start_date
        if ($this->payment_term === 'Monthly') {
          return $this->schedule_start_date->startOfMonth();
        } elseif ($this->payment_term === 'Yearly') {
          return $this->schedule_start_date->startOfMonth();
        }
      }

      return $currentMonth->startOfMonth(); // Fallback
  }


  public static function dataFields(){
    // Loop Data Field From Table to put in array for mapping data field to search data table
    $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new OfficerPaymentRecurring)->getTable());
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
    $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new OfficerPaymentRecurring)->getTable());
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
    $query = OfficerPaymentRecurring::orderByLevel()->whereNotDeleted()->get();
    $data = array();
    foreach($query as $row){
      $data[] = array(
        "label" => $row->name_kh,
        "value" => $row->id,
      );
    }
    return $data;
  }

  public static function checkQuery($filter){
    // $query = DB::TABLE("officer_payment_recurring")
    //         ->Join("officer","officer.id", "=", "officer_payment_recurring.officer_id")
    //         ->select('officer_payment_recurring.*', DB::raw("CONCAT(tbl_officer.first_name, ' ', tbl_officer.last_name) AS officer"));
    $query = OfficerPaymentRecurring::select("*")->with("officer")->with("status")->with("currency");
    $whereClause = $query;
    if(isset($filter["data"]["officer_id"])){
      $whereClause->where("officer_payment_recurring.officer_id", $filter["data"]["officer_id"]);
    }
    $whereClause->orderByFilter($filter);
    $d = "";
    if($filter["search_field"]){
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if(isset($filter["search_field"])){
        for($i=0 ; $i < count($filter["search_field"]); $i++){
          $field = array_key_first($filter["search_field"][$i]);
          if (array_key_exists($field, $dataFields)) {
            if(isset($filter["search_field"][$i][$field]["value"])){
              $whereClause->Where("officer_payment_recurring.".$field, 'Like', '%'.$filter["search_field"][$i][$field]["value"].'%');
            }else{
              $whereClause->Where("officer_payment_recurring.".$field, 'Like', '%'.$filter["search_field"][$i][$field].'%');
            }
          }
        }
      }
    }

    $whereClause->whereNotDeleted("officer_payment_recurring.status");
    return $whereClause;
  }

  public static function getData($filter){
    $whereClause = OfficerPaymentRecurring::checkQuery($filter);
    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
    $whereClause->limit($filter["limit"]);
    $data = collect($whereClause->get());
    return $data;
  }

  public static function getTotal($filter){
    $whereClause = OfficerPaymentRecurring::checkQuery($filter);
    $total = $whereClause->count();
    return $total;
  }

  public static function executePayment($request){
    $input = $request;
    $executed_num = $request["executed_num"];
    $input["amount"] = $request["recurring_amount"];
    $input["created_by"] = Auth::user()->id;
    $input["account_id"] = 0; // place your COA Accounting ID for account receivable
    $input["status"] = 15; // Status = 15 mean not yet process payment
    unset($input['id']);
    unset($input['officer']);
    unset($input['executed_num']);
    unset($input['recurring_amount']);
    unset($input['schedule_start_date']);
    unset($input['schedule_end_date']);
    unset($input['is_default']);
    unset($input['currency']);
    for($i=0; $i < $executed_num; $i++){
      $month = $i+1;
      $input["payment_date"] = date("Y").'-'.$month.'-01';
      $queryCheck = OfficerPayable::where("officer_id", $input["officer_id"])
                                    ->where("payment_date", $input["payment_date"])
                                    ->whereNotIn("status", [4])
                                    ->first();
      if(!$queryCheck){
        OfficerPayable::create($input);
      }
    }
    OfficerPaymentRecurring::executeRegistrationFee($input["officer_id"]);
    return true;
  }

  public static function executeRegistrationFee($officerId){
    $unpaidStatus = 15;
    $query = OfficerPayable::where("officer_id", $officerId)->where("payment_term", 'Membership Registration Fee')->first();
    if(!$query){
      $paymentTerm = PaymentTerm::where("type", "Registration")->first();
      // dd("test");
      $data = array(
        "code" => "00001",
        "officer_id" => $officerId,
        "account_id" => "",
        "amount" => $paymentTerm->recurring_amount,
        "currency_id" => $paymentTerm->currency_id,
        "payment_term" => $paymentTerm->name_en,
        "payment_date" => date("Y-m-d"),
        "status" => 15,
        "remark" => "",
      );
      OfficerPayable::create($data);
    }
    return true;
  }
  
  // Use this to execute payable
  // Step to Execute Payable By Running from Schedules
  public static function executePayableOfficers(){
    $query = Officer::where("status", 13)->get();
    // foreach($query as $row){
    //   $currentDate = date("Y");
    //   $checkHasExePayable = OfficerPaymentRecurring::where("officer_id", $row->id)->exists();
    //   if($checkHasExePayable){
        
    //   }
    // }
  }

  public static function executePayables($request){
    $input = $request;
    $excecutePayables = DB::table("officer as f")
                        ->select("pr.*")
                        ->Join("officer_payment_recurring as pr", "f.id", "=", "pr.officer_id")
                        ->groupBy("pr.officer_id")
                        ->orderBy("pr.id", "desc")
                        ->where("pr.status", 13)
                        ->whereNotIn("f.status", [26,27,14,20,4])
                        ->get();
    $data = [];
    foreach($excecutePayables as $excecutePayable){
      $currentDate = date("Y");
      $officerPayable = OfficerPayable::where("officer_id", $excecutePayable->officer_id)
                        ->whereRaw("DATE_FORMAT(payment_date, '%Y') LIKE ?", $currentDate)
                        ->first();
      if(!$officerPayable){
        $data[] = array(
          "code" => rand(100000000,999999999),
          "officer_id" => $excecutePayable->officer_id,
          "account_id" => "",
          "payment_method_id" => "",
          "amount" => $excecutePayable->recurring_amount,
          "currency_id" => $excecutePayable->currency_id,
          "payment_term" => $excecutePayable->payment_term,
          "payment_date" => date("Y-m-d"),
          "status" => 15, // status=15 means unpaid
          "remark" => "",
          "attach" => "",
          "created_by" => Auth::user()->id,
        );
      }
    }

    collect($data)->chunk(500)->each(function ($chunk) {
      InsertDataJob::dispatch($chunk->toArray());
    });

    return true;
  }

  
}