<?php

namespace App\Models\Settings;
use OwenIt\Auditing\Contracts\Auditable;
use CommonService;
use Illuminate\Database\Eloquent\Model;
use DB;

class Title extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'title';
    protected  $primaryKey = 'id';
    protected $fillable = [
        "name_en",
        "name_kh",
        "status",
        "created_by",
        "modified_by",
        ];
    public $timestamps = true;

    public static function dataFields(){
        // Loop Data Field From Table to put in array for mapping data field to search data table
        $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new Position)->getTable());
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
        $formAttributes = array(
            [
                "name" => "officer_id",
                "type" => "select",
                "required" => true,
            ],
            [
                "name" => "amount",
                "type" => "text",
                "required" => true
            ],
            [
                "name" => "payment_date",
                "type" => "date",
                "required" => true
            ],
            [
                "name" => "payment_issue",
                "type" => "date",
                "required" => true
            ],
            [
                "name" => "currency_id",
                "type" => "select",
                "required" => false
            ],
            [
                "name" => "status",
                "type" => "checkbox",
                "required" => false
            ],
        );
        return $formAttributes;
    }

    public static function getSingleOpt(){
        $query = Title::orderByLevel()->whereNotDeleted()->get();
        $data = array();
        foreach($query as $row){
            $data[] = array(
                "label" => CommonService::localName($row),
                "value" => $row->name_kh,
            );
        }
        return $data;
    }

    public static function checkQuery($filter){
        $query = Title::orderByFilter($filter);
        $whereClause = $query;
        $whereClause->whereNotDeleted();
        if($filter["search_field"]){
            $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
            $dataFields = $arraySingle;
            if (array_key_exists("name_kh", $dataFields) && $dataFields["name_kh"]!=null) {
                $whereClause->Where("name_kh", $dataFields["name_kh"]);
            }
            if (array_key_exists("name_en", $dataFields) && $dataFields["name_en"]!=null) {
                $whereClause->Where("name_en", $dataFields["name_en"]);
            }
        }
        return $whereClause;
    }

    public static function getData($filter){
        $whereClause = Title::checkQuery($filter);
        $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);
        $whereClause->limit($filter["limit"]);
        $data = collect($whereClause->get());
        return $data;
    }

    public static function getTotal($filter){
        $whereClause = Title::checkQuery($filter);
        $total = $whereClause->count();
        return $total;
    }
}
