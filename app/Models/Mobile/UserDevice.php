<?php

namespace App\Models\Mobile;
use Illuminate\Database\Eloquent\Model;
use DB;
class UserDevice extends Model
{
    protected $table = 'user_device';
    protected $primaryKey = 'id';
    protected $fillable = [
                            'user_id',
                            'device_type',
                            'telephone',
                            'device_type',
                            'device_token',
                            'remark',
                            'created_at'
                          ];
    
  public static function CheckUserDevice($request){
    $query = UserDevice::where("device_token", $request["device_token"])->first();
    if($query){
      return true;
    }else{
      return false;
    }
  }
    public $timestamps = false;

}