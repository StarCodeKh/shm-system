<?php

namespace App\Models;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;
use DB;

class CompositionRequest extends Model implements Auditable
{
  use \OwenIt\Auditing\Auditable;
  protected $table = 'incentive_composition_request';
  protected  $primaryKey = 'id';
  protected $guarded;
  protected $fillable = [
                            'request_title',
                            'reference',
                            'request_from',
                            'description',
                            'letter_type',
                            'status',
                            'entity',
                            'entity_id',
                            'attach_file',
                            'request_date',
                            'effective_date',
                            'created_by',
                            'modified_by',
                            'is_approved'
                          ];
    public $timestamps = true;

    public static function dataFields(){
      // Loop Data Field From Table to put in array for mapping data field to search data table
      $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().(new CompositionRequest)->getTable());
      $str = "";
      for($i=0; $i < count($data["tables"]); $i++){
          $str .= $data["tables"][$i]->Field.",";
      }
      $newArr = explode(",", $str);
      array_pop($newArr);
      $dataFields = $newArr;
      return $dataFields;
    }

    public static function getSingleOpt(){
      $query = CompositionRequest::orderByLevel()->whereNotDeleted()->get();
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
      $query = CompositionRequest::orderByFilter($filter);
      $whereClause = $query;
      $whereClause->whereNotDeleted();
      if($filter["search_field"]){
        $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
        $dataFields = $arraySingle;
        if (array_key_exists("first_name", $dataFields) && $dataFields["first_name"]!=null) {
          $whereClause->Where("first_name", $dataFields["first_name"]);
        }
        if (array_key_exists("last_name", $dataFields) && $dataFields["last_name"]!=null) {
          $whereClause->Where("last_name", $dataFields["last_name"]);
        }
      }
      return $whereClause;
    }

    public static function getData($filter){
      $whereClause = CompositionRequest::checkQuery($filter);
      $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);       
      $whereClause->limit($filter["limit"]);
      $data = collect($whereClause->get());
      return $data;
    }

    public static function getTotal($filter){
      $whereClause = CompositionRequest::checkQuery($filter);
      $total = $whereClause->count();
      return $total;
    }
}
