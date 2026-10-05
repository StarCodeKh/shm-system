<?php

namespace App\Models\Modules\BudgetMonitoring;
use App\Models\Modules\BudgetMonitoring\RevProgramDetail;
use App\Models\Modules\ProgramManagement\Program;
use App\Models\Settings\Entity;
use App\Models\Settings\Account;
use App\Models\Settings\AccountGroup;
use App\Models\Modules\ProgramManagement\SubProgram;
use App\Models\Modules\ProgramManagement\ClusterActivity;
use App\Models\Modules\ProgramManagement\Activity;
use Illuminate\Database\Eloquent\Model;
use DB;

class CreditMovement extends Model
{
  protected $table = 'mef_credit_movement';
  protected $fillable = [
                          "planning_id",
                          "entity_id",
                          "exp_type",
                          "doc_no",
                          "date_adv",
                          "account_id",
                          "sub_account_id",
                          "clus_id",
                          "amount_adv",
                          "remain_balance",
                          "increase",
                          "decrease",
                          "check",
                          "status",
                          "created_by",
                          "modified_by"
                        ];
                        
  public $timestamps = true;

  
  public static function getCredMovement($filter){
    $data = array();
    $queryObj = CreditMovement::orderBy($filter["sort"], $filter["order"]);
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
    $children  = array();
    foreach($query as $row){ 
      
    }
    return $data;
  }
}