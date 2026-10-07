<?php

namespace App\Models;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;
use DB;
use CommonService;
use App\User;
class UserDevice extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'user_device';
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $guarded = [];
    public $timestamps = true;

    public static function dataFields(){
        // Loop Data Field From Table to put in array for mapping data field to search data table
        $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new UserDevice)->getTable());
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
        $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new UserDevice)->getTable());
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
        $users = User::select("id")->where("officer_id", $filter["data"]["officer_id"])->get();
        $userId = "";
        foreach($users as $user){
            $userId.= $user->id.",";
        }
        $newUserId = explode(",", $userId);
        array_pop($newUserId);
        $query = UserDevice::whereIn("user_id", $newUserId)->orderByFilter($filter);
        $whereClause = $query;
        if(isset($filter["data"]["user_id"])){
            $whereClause->where("user_id", $filter["data"]["user_id"]);
        }
        if($filter["search_field"]){
            $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
            $dataFields = $arraySingle;
            if(isset($filter["search_field"])){
                for($i=0 ; $i < count($filter["search_field"]); $i++){
                    $field = array_key_first($filter["search_field"][$i]);
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
        $whereClause = $query;
        return $whereClause;
    }

    public static function getData($filter){
        $whereClause = UserDevice::checkQuery($filter);
        $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
        $whereClause->limit($filter["limit"]);
        $data = collect($whereClause->get());
        return $data;
    }

    public static function getTotal($filter){
        $whereClause = UserDevice::checkQuery($filter);
        $total = $whereClause->count();
        return $total;
    }

}
