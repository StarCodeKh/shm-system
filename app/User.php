<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Settings\Entity;
use App\Models\Settings\RoleUser;
use App\Models\Settings\Status;
use App\Models\Officer;
use App\Models\UserDevice;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use App\Authenticatable;
use Illuminate\Support\Facades\DB;
use Auth;
use CommonService;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements Auditable
// class Officer extends Model implements Auditable
{ 
  use \OwenIt\Auditing\Auditable;
  use HasFactory, HasRoles, Notifiable, HasApiTokens;
  public $guard_name = 'api';
  protected $table = 'users';
  protected $primaryKey = 'id';
  protected $guarded = [];
  protected $fillable = [];

  public $timestamps = true;
  
  public function entity(){
    return $this->belongsTo(Entity::class, 'entity_id');
  }

  public function role(){
      return $this->belongsTo(RoleUser::class,'role_id');
  }

  public function status(){
      return $this->belongsTo(Status::class,'status');
  }

  public function devices()
  {
      return $this->hasMany(UserDevice::class, 'user_id');
  }

  public function officer(){
    return $this->belongsTo(Officer::class,'officer_id');
  }


  // Add routeNotificationForFcm
  public function routeNotificationForFcm()
  {
      return $this->device_token; // Must store device token in DB
  }

    // public static function
  public static function dataFields(){
    // Loop Data Field From Table to put in array for mapping data field to search data table
    $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new User)->getTable());
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
    $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new User)->getTable());
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

  public static function checkQuery($filter){
    $data = array();
    if(isset($filter["data"]["officer_id"])){
      $query = User::with('roles')->with('permissions')->with('officer')->with('status')->where("officer_id", (int)$filter["data"]["officer_id"]);
    }else{
      $query = User::with('roles')->with('permissions')->with('status')->orderByFilter($filter);
    }
    $whereClause = $query;
    // $whereClause->orwhereNull("status");
    // $whereClause->where("is_default", 0);

    if(Auth::user()->entity_id !=0 || Auth::user()->entity_id !=null){
      $whereClause->where("entity_id", Auth::user()->entity_id);
    }
    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);       
    $whereClause->limit($filter["limit"]);
    
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
    if(!isset($filter["data"]["officer_id"])){
    }
    return $whereClause;
  }

  public static function getData($filter){
    $whereClause = User::checkQuery($filter);
    $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
    $whereClause->limit($filter["limit"]);
    $data = collect($whereClause->get());
    return $data;
  }

  public static function getTotal($filter){
    $whereClause = User::checkQuery($filter);
    $total = $whereClause->count();
    return $total;
  }

  public static function getOfficerInfo($userId){
      $query = DB::table("incentive_officer as io")
                ->Select("io.*")
                ->Where("io.user_id", $userId)
                ->Where('io.status', 1)
                ->first();
      return $query;
  }

  public static function getProfile($id){
    $query = User::with('officer')->with("entity")->where("id", (int)$id);
    $whereClause = $query;
    if(Auth::user()->entity_id !=0 || Auth::user()->entity_id !=null){
      $whereClause->where("entity_id", Auth::user()->entity_id);
    }
    
    $data = collect($whereClause->first());
    return $data;
  }

  public static function checkHasUser($username){
      $query = User::Where("username", $username)->first();
      $boolen = true;
      if($query){
          $boolen = true;
      }else{
          $boolen = false;
      }
      return $boolen;
  }

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
      'password', 'remember_token',
  ];

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
      'email_verified_at' => 'datetime',
  ];
}
