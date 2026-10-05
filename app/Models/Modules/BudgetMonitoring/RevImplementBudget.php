<?php

namespace App\Models\Modules\BudgetMonitoring;
use Illuminate\Database\Eloquent\Model;

class RevImplementBudget extends Model
{
    protected $table = 'mef_revenue_imple_budget';
    protected $fillable = [
                            "planning_id",
                            "entity_id",
                            "finance_law",
                            "actual_imple_budget",
                            "percentage_imple_budget",
                            "status",
                            "order_level",
                            "created_by",
                            "modified_by"
                        ];
                        
    public $timestamps = true;

    public static function getRevImpleBudget($filter){
        $data = array();
        $queryObj = RevImplementBudget::orderBy($filter["sort"], $filter["order"]);
        $whereClause = $queryObj;
        $whereClause->whereNotIn("status", [4])->orWhereNull("status");
        $whereClause->offset(($filter["page_number"] - 1) * $filter["limit"]);       
        $whereClause->limit($filter["limit"]);
        if($filter["search_field"]){
        $arraySingle = call_user_func_array('array_merge', $filter["search_field"]);
        $dataFields = $arraySingle;
        if (array_key_exists('entity_id', $dataFields)) {
            $whereClause->Where("id", $dataFields["entity_id"]["value"]);
        }
        }
        $query = collect($whereClause->get());
        foreach($query as $row){ 
            $data[] = array(
                "planning_id" => $row->planning_id,
                "entity_id" => $row->entity_id,
                "finance_law" => $row->finance_law,
                "actual_imple_budget" => $row->actual_imple_budget,
                "percentage_imple_budget" => $row->percentage_imple_budget,
                "status" => $row->status,
                "created_by" => $row->created_by,
                "modified_by" => $row->modified_by,
            );
        }
        return $data;
  
    }
}