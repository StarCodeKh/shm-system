<?php

namespace App\Models;
use App\Models\Settings\Department;
use App\Models\Settings\Entity;
use App\Models\Settings\Status;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use DB;
use CommonService;
class Officer extends Model implements Auditable
{
  use \OwenIt\Auditing\Auditable,HasRoles;
  
  protected $table = 'officer';
  protected $primaryKey = 'id';
  protected $fillable = [];
  // user_id (the member's login account) is set only by OfficerController::updateIncentiveOfficer
  protected $guarded = ['id', 'user_id'];
  public $timestamps = true;


  public function department(){
    return $this->belongsTo(Department::class,'department_id');
  }

  public function entity(){
    return $this->belongsTo(Entity::class,'entity_id');
  }

  public function officerPayable()
  {
    return $this->hasOnehasMany(OfficerPayable::class);
  }

  public function officerRequest()
  {
    return $this->hasOnehasMany(OfficerRequest::class);
  }

  public function status()
  {
    return $this->belongsTo(Status::class, "status");
  }

  public function nssf()
  {
    return $this->belongsTo(Status::class, "is_nssf_member");
  }

  public function title()
  {
    return $this->belongsTo(Title::class, "title");
  }

  public function payables()
  {
    return $this->hasMany(OfficerPayment::class);
  }

  public function paymentHistory()
  {
    return $this->hasMany(OfficerPayment::class);
  }

  /**
     * Check if the member has a pending payable for the given month.
     */
    public function hasPendingPayableForMonth(Carbon $month): bool
    {
        return $this->payables()
                    ->where('due_date', '>=', $month->startOfMonth())
                    ->where('due_date', '<=', $month->endOfMonth())
                    ->where('status', 'pending')
                    ->exists();
    }

    /**
     * Check if the member has already paid for the given month.
     */
    public function hasPaidForMonth(Carbon $month): bool
    {
        return $this->payables()
                    ->where('due_date', '>=', $month->startOfMonth())
                    ->where('due_date', '<=', $month->endOfMonth())
                    ->where('status', 'paid')
                    ->exists();
    }

  

  // public function status()
  // {
  //   return $this->hasOnehasMany(Status::class);
  // }
  
  // public static function
  public static function dataFields(){
    // Loop Data Field From Table to put in array for mapping data field to search data table
    $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new Officer)->getTable());
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
    $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new Officer)->getTable());
    for($i=0; $i < count($data["tables"]); $i++){
      if($data["tables"][$i]->Field != "id" && $data["tables"][$i]->Field != "created_by" && $data["tables"][$i]->Field != "modified_by" && $data["tables"][$i]->Field != "created_at" && $data["tables"][$i]->Field != "updated_at"){
        $formAttributes[] = array(
          "name" => $data["tables"][$i]->Field,
          "type" => CommonService::convertDataTypeWay1($data["tables"][$i]),
          "required" => $data["tables"][$i]->Null=="NO"?true:false,
        );
      }
    }
    return $formAttributes;
  }

  public static function getSingleOpt(){
    $query = Officer::orderByLevel()->whereNotDeleted()->get();
    $data = array();
    foreach($query as $row){
      $data[] = array(
        "label" => $row->first_name . $row->last_name . " (".$row->identity_member_no.")",
        "value" => $row->id,
      );
    }
    return $data;
  }

  public static function getSingleOptById($param){
    $query = Officer::orderByLevel()->where("id", $param)->get();
    $data = array();
    foreach($query as $row){
      $data[] = array(
        "label" => $row->first_name . $row->last_name . " (".$row->identity_member_no.")",
        "value" => $row->id,
      );
    }
    return $data;
  }

  public static function checkQueryResult($filter, $gender){
    $whereClause = "";
    $query = Officer::orderByFilter($filter);
    $whereClause = $query;
    $whereClause->with("status")->with("department")->with("entity")->with("title");
    if(isset($filter["officer_id"])){
      $whereClause->where("id", $filter["officer_id"]);
    }
    if(isset($filter["data"]["officer_id"])){
      $whereClause->where("id", $filter["data"]["officer_id"]);
    }
    if(isset($gender)){
      $whereClause->where("gender", $gender);
    }
    if($filter["search_field"]){
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if(isset($filter["search_field"])){
        for($i=0 ; $i < count($filter["search_field"]); $i++){
          $field = array_key_first($filter["search_field"][$i]); //array('key1', 'key2', 'key3');
          if (array_key_exists($field, $dataFields)) {
            if(isset($filter["search_field"][$i][$field]["value"])){
              $whereClause->Where($field, 'Like', '%'.$filter["search_field"][$i][$field]["value"].'%');
            }else{
              $whereClause->Where($field, 'Like', '%'.$filter["search_field"][$i][$field].'%');
            }
          }
        }
      }
    }
    
    $whereClause->whereNotDeleted();
    return $whereClause;
    
  }

  public static function checkQuery($filter){
    $whereClause = "";
    $query = Officer::orderByFilter($filter);
    $whereClause = $query;
    $whereClause->with("nssf")->with("status")->with("department")->with("entity");
    if(isset($filter["officer_id"])){
      $whereClause->where("id", $filter["officer_id"]);
    }
    if(isset($filter["data"]["officer_id"])){
      $whereClause->where("id", $filter["data"]["officer_id"]?$filter["data"]["officer_id"]:$filter["data"]["officer_id"]["value"]);
    }
    if(isset($filter["search_field"]) && $filter["search_field"]){
      $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
      $dataFields = $arraySingle;
      if(isset($filter["search_field"])){
        for($i=0 ; $i < count($filter["search_field"]); $i++){
          $field = array_key_first($filter["search_field"][$i]); //array('key1', 'key2', 'key3');
          if (array_key_exists($field, $dataFields)) {
            if(isset($filter["search_field"][$i][$field]["value"])){
              $whereClause->Where($field, 'Like', '%'.$filter["search_field"][$i][$field]["value"].'%');
            }else{
              $whereClause->Where($field, 'Like', '%'.$filter["search_field"][$i][$field].'%');
            }
          }
        }
      }
    }
    
    if(isset($filter["keysearch"]["keyword"]) && $filter["keysearch"]["keyword"] != null){
      $keyword = '%'.$filter["keysearch"]["keyword"].'%';
      $whereClause->whereNotIn("status", [4]);
      $whereClause->where(function ($q) use ($keyword) {
        $q->where("first_name", 'Like', $keyword)
          ->orWhere("last_name", 'Like', $keyword)
          ->orWhere("identity_member_no", 'Like', $keyword);
      });
    }else{
      $whereClause->whereNotDeleted();
    }
    return $whereClause;
  }

  public static function getData($filter){
    $query = Officer::checkQuery($filter)
        ->offset(($filter['page_number'] - 1) * $filter['limit'])
        ->limit($filter['limit']);

    return $query->get()->map(function ($item) {
      $item->full_name = $item->first_name . " " . $item->last_name;
      return $item;
    });
  }
  
  public static function getTotal($filter){
    $total = 0;
    $whereClause = Officer::checkQuery($filter);
    $total = $whereClause->count();
    return $total;
  }

  public static function getTotalOfficerByGender($filter, $gender){
    $total = 0;
    $whereClause = Officer::checkQueryResult($filter, $gender);
    $total = $whereClause->count();
    return $total;
  }

  public static function getOfficerListByEntities($entity_id){
    $entityId = explode(",", $entity_id);
    $query = DB::table("incentive_officer as io")
              ->Select("io.*", "d.incentive")
              ->WhereIn("io.entity_id", $entityId)
              // ->Where('io.status', 1)
              ->Join("duty as d", "d.id" , "=", "io.duty_id")
              ->get();
    return $query;
  }

  public static function getOfficerByEntities($entity_id){
    $entityId = explode(",", $entity_id);
    $query = DB::table("incentive_officer as io")
              ->Select("io.*", "d.incentive")
              ->WhereIn("io.entity_id", $entityId)
              ->Where('io.status', 1)
              ->Join("duty as d", "d.id" , "=", "io.duty_id")
              ->get();
    return $query;
  }

  public static function activeOfficerInPeriod(){
    $currentDate = strtotime(date("Y-m-d"));
    $query = CompositionRequest::where("effective_date", '>' ,date("Y-m-d"))->get();
    if($query){
      foreach($query as $row){
        $icos = DB::table("incentive_composition_officer")->where("incentive_composition_request_id", $row->id)->get();
        foreach($icos as $ico){
          Officer::where("id", $ico->from_officer_id)->update(["status" => 0]);
          Officer::where("id", $ico->to_officer_id)->update(["status" => 1]);
        }
      }
    }
    return true;
  }

  public static function getOfficerGroupByEntities($entity_id){
    $entityId = explode(",", $entity_id);
    $query = DB::table("entity as e")
              ->Select("e.*")
              ->WhereIn("e.id", $entityId)
              ->get();
    $data = array();
    foreach($query as $row){
      $id = $row->id;
      $incentiveOfficers = DB::table("incentive_officer as io")
                ->Select("io.*", "d.incentive")
                ->where("io.entity_id", $id)
                ->Where('io.status', 1)
                ->Join("duty as d", "d.id" , "=", "io.duty_id")
                ->OrderBy('d.order_level')
                ->get();
      $officers = array();
      $i = 1;
      foreach($incentiveOfficers as $incentiveOfficer){
        $incentiveAmount = 0;
        if($incentiveOfficer->bank_account_no != null || $incentiveOfficer->updated_bank_acc != null){
          $incentiveAmount = $incentiveOfficer->incentive;
        }
        $officers[] = array(
          "no" => $i,
          "id" => $incentiveOfficer->id,
          "user_id" => $incentiveOfficer->user_id,
          "first_name" => $incentiveOfficer->first_name,
          "last_name" => $incentiveOfficer->last_name,
          "duty" => $incentiveOfficer->duty,
          "bank_id" => $incentiveOfficer->bank_id,
          "bank_name" => $incentiveOfficer->bank_name,
          "bank_code" => $incentiveOfficer->bank_code,
          "entity" => $incentiveOfficer->entity,
          "entity_id" => $incentiveOfficer->entity->name_kh,
          "position" => $incentiveOfficer->position,
          "duty" => $incentiveOfficer->duty,
          "bank_account_no" => $incentiveOfficer->bank_account_no,
          "updated_bank_acc" => $incentiveOfficer->updated_bank_acc,
          "incentive" => $incentiveAmount
        );
        $i++;
      }
      $data[] = array(
        "entity" => $row->name,
        "officers" => $officers
      );

    }
    return $data;
  }

  public static function getUserInfo($officer_id){
    $query = DB::table("incentive_officer as io")
              ->Select("u.*")
              ->Where('io.id', $officer_id)
              ->Join("users as u", "u.id" , "=", "io.user_id")
              ->first();
    return $query;
  }

  public static function getIncetiveByOfficer($officer_id){
    $query = DB::table("incentive_to_officer")
              ->Where('officer_id', $officer_id)
              ->get();
    return $query;
  }

  public static function getCompositionHistory($officer_id){
    $query = DB::table("incentive_composition_officer as ico")
              ->Select("io1.first_name as from_first_name", "io1.last_name as from_last_name", "io1.bank_account_no as from_bank_account_no", "io1.updated_bank_acc as from_updated_bank_acc", "io2.first_name as to_first_name", "io2.first_name as to_last_name", "io2.bank_account_no as to_bank_account_no", "io2.updated_bank_acc as to_updated_bank_acc")
              ->Join('incentive_officer as io1', 'io1.id', '=', 'ico.from_officer_id')
              ->Join('incentive_officer as io2', 'io2.id', '=', 'ico.to_officer_id')
              ->Where('ico.from_officer_id', $officer_id)
              ->get();
    if($query){
      return $query;
    }else{
      return null;
    }
  }

   public static function insertOfficer($data)
  {
        $optional = [
            'nationality', 'duty', 'entity_id', 'department_id', 'gender', 'address', 'email',
            'bank_code', 'bank_account_no', 'updated_bank_acc', 'position', 'telephone_1', 'telephone_2',
            'register_date', 'married_status', 'emergency_person', 'emergency_relative', 'is_nssf_member',
            'nssf_card_no', 'social_media_fb', 'social_media_telegram', 'social_media_whatsapp',
            'social_media_alt', 'remark', 'order_level',
        ];
        $row = [
            'photo'              => $data['photo'],
            'first_name'         => $data['first_name'],
            'last_name'          => $data['last_name'],
            'latin'              => $data['latin'],
            'identity_no'        => $data['identity_no'],
            'identity_member_no' => $data['identity_member_no'],
            'dob'                => $data['dob'],
            'title'              => $data['title'],
            'status'             => $data['status'],
            'bank'               => $data['bank'] ?? $data['bank_name'] ?? null,
            'created_by'         => $data['created_by'],
            'created_at'         => now(),
        ];
        // Optional fields that weren't sent keep the column default
        foreach ($optional as $field) {
            if (array_key_exists($field, $data)) {
                $row[$field] = $data[$field];
            }
        }

        return DB::table('officer')->insert($row);
  }


  // Remove it after done export data officer
  public static function tmpExportOfficers(){

  }

  public static function tmpExportPayments(){

  }

  public static function tmpExportPaymentTypes(){

  }

  public static function tmpExportReceives(){

  }

  
  
}