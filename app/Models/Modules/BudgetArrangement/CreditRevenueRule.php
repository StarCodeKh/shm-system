<?php

namespace App\Models\Modules\BudgetArrangement;
use Illuminate\Database\Eloquent\Model;
use DB;

class CreditRevenueRule extends Model
{
    protected $table = 'mef_credit_revenue_rule';
    protected $fillable = [
                            'planning_id',
                            'entity_id',
                            "name_kh",
                            'issue_date',
                            'authorize_by',
                            'approved_by',
                            'status',
                            'remark',
                            'created_by',
                            'modified_by'
                        ];
    
    public $timestamps = true;

    public static function getCreditRevenueRule(){
        $data = array();
        $query = DB::table("mef_credit_revenue_rule as crr")
                    ->join("entity as e", "e.id", "crr.entity_id")
                    ->where("crr.planning_id", config_planning_year);
        $whereClause = $query;
        $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);       
        $whereClause->limit($filter["limit"]);
        if($filter["search_field"]){
            $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
            $dataFields = $arraySingle;
            if (array_key_exists('entity_id', $dataFields)) {
                $whereClause->Where("crr.entity_id", $dataFields["entity_id"]["value"]);
            }
        }
        $results = collect($whereClause->get());

        foreach($results as $result){
        }
    }
}
