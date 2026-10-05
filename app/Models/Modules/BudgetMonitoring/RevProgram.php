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

class RevProgram extends Model
{
  protected $table = 'mef_revenue_program';
  protected $fillable = [
                          "planning_id",
                          "entity_id",
                          "currency",
                          "currency_id",
                          "exchange_rate",
                          "finance_law",
                          "status",
                          "created_by",
                          "modified_by"
                        ];
                        
  public $timestamps = true;

  public function Entity(){
    return $this->belongsTo('App\Models\Settings\Entity','entity_id');
  }
  public function SubProgram(){
    return $this->belongsTo('App\Models\Modules\ProgramManagement\SubProgram','entity_id');
  }
  
  public static function getRevPro($filter){
    $data = array();
    $queryObj = RevProgram::orderBy($filter["sort"], $filter["order"]);
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
      $queryRevProDetails = RevProgramDetail::orderBy($filter["sort"], $filter["order"])
                            ->whereNotIn("status", [4])->orWhereNull("status")
                            ->where("rev_pro_id", $row->id)
                            ->get();

      foreach($queryRevProDetails as $queryRevProDetail){
        $children[] = array(
          "id" => 1,
          "account_group" => array("id" => 1, "code" => $queryRevProDetail->AccountGroup?$queryRevProDetail->AccountGroup->code:"", "name" => $queryRevProDetail->AccountGroup?$queryRevProDetail->AccountGroup->name_kh:""),
          "account" => array("id" => 1, "code" => $queryRevProDetail->Account?$queryRevProDetail->Account->code:"", "name" => $queryRevProDetail->Account?$queryRevProDetail->Account->name_kh:""),
          "sub_account" => array("id" => 1, "code" => $queryRevProDetail->SubAccount?$queryRevProDetail->SubAccount->code:"", "name" => $queryRevProDetail->SubAccount?$queryRevProDetail->Account->name_kh:""),
          "entity" => array("id" => 1, "name" => "អគ្គនាយកដ្ឋាន" ),
          "finance_law" => $queryRevProDetail->finance_law,
          "total_amount" => "12",
          "total_percentage_implementing_rule" => "12",
          "costing_plan_semester1" => array(
              // គ្រោងដើមឆ្នាំ
              "year_planning" => array(
                "s1_total_bp" => "555",
                "s1_bp_jan" => "1",
                "s1_bp_feb" => "2",
                "s1_bp_mar" => "3",
              ),
              "impl_est" => array(
                  "s1_total_est_imp" => "5554",
                  "s1_est_imp_jan" => "1", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ១
                  "s1_est_imp_feb" => "2", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ២
                  "s1_est_imp_mar" => "3", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ៣
              )
              ),
          "costing_plan_semester2" => array (
              // គ្រោងដើមឆ្នាំ
              "year_planning" => array (
                "s2_total_bp" => "2222",
                "s2_bp_apr" => "1",
                "s2_bp_may" => "2",
                "s2_bp_jun" => "3",
              ),
              "impl_est" => array(
                "s2_total_est_imp" =>"3333",
                "s2_est_imp_apr" =>"1", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ១
                "s2_est_imp_may" =>"2", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ២
                "s1_est_imp_jun" =>"3", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ៣
              )
              ),
          "costing_plan_semester3" => array(
              // គ្រោងដើមឆ្នាំ
              "year_planning" => array(
                "s3_total_pb" => "555",
                "s3_bp_jul" => "1",
                "s3_bp_aug" => "2",
                "s3_bp_sep" => "3",
              ),
              "impl_est" => array(
                "s3_total_est_imp" => "5554",
                "s3_est_imp_jul" => "1", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ១
                "s3_est_imp_aug" => "2", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ២
                "s3_est_imp_sep" => "3", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ៣
              )
            ),
          "costing_plan_semester4" => array(
              // គ្រោងដើមឆ្នាំ
              "year_planning" => array(
                  "s4_total_bp" => "555",
                  "s4_bp_oct" => "1",
                  "s4_bp_nov" => "2",
                  "s4_bp_dec" => "3",
              ),
              "impl_est" => array(
                "s4_total_est_imp" => "5554",
                "s4_est_imp_oct" => "1", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ១
                "s4_est_imp_nov" => "2", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ២
                "s4_est_imp_dec" => "3", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ៣
              )
          ),
          "remark" => "កំណត់ត្រា"
        );
      }
        $data[] = array(
          "id" => 1,
          "account_group" => "",
          "account" => "",
          "sub_account" => "",
          "entity" => $row->SubProgram->sub_code.".".$row->SubProgram->structure_name_kh."-".$row->Entity->name_kh,
          "finance_law" => $row->finance_law,
          "total_amount" => "12",
          "total_percentage_implementing_rule" => "12",
          "costing_plan_semester1" => array(
              // គ្រោងដើមឆ្នាំ
              "year_planning" => array(
                "s1_total_bp" => "555",
                "s1_bp_jan" => "1",
                "s1_bp_feb" => "2",
                "s1_bp_mar" => "3",
              ),
              "impl_est" => array(
                  "s1_total_est_imp" => "5554",
                  "s1_est_imp_jan" => "1", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ១
                  "s1_est_imp_feb" => "2", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ២
                  "s1_est_imp_mar" => "3", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ៣
              )
              ),
          "costing_plan_semester2" => array (
              // គ្រោងដើមឆ្នាំ
              "year_planning" => array (
                "s2_total_bp" => "2222",
                "s2_bp_apr" => "1",
                "s2_bp_may" => "2",
                "s2_bp_jun" => "3",
              ),
              "impl_est" => array(
                "s2_total_est_imp" =>"3333",
                "s2_est_imp_apr" =>"1", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ១
                "s2_est_imp_may" =>"2", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ២
                "s1_est_imp_jun" =>"3", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ៣
              )
              ),
          "costing_plan_semester3" => array(
              // គ្រោងដើមឆ្នាំ
              "year_planning" => array(
                "s3_total_pb" => "555",
                "s3_bp_jul" => "1",
                "s3_bp_aug" => "2",
                "s3_bp_sep" => "3",
              ),
              "impl_est" => array(
                "s3_total_est_imp" => "5554",
                "s3_est_imp_jul" => "1", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ១
                "s3_est_imp_aug" => "2", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ២
                "s3_est_imp_sep" => "3", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ៣
              )
            ),
          "costing_plan_semester4" => array(
              // គ្រោងដើមឆ្នាំ
              "year_planning" => array(
                  "s4_total_bp" => "555",
                  "s4_bp_oct" => "1",
                  "s4_bp_nov" => "2",
                  "s4_bp_dec" => "3",
              ),
              "impl_est" => array(
                "s4_total_est_imp" => "5554",
                "s4_est_imp_oct" => "1", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ១
                "s4_est_imp_nov" => "2", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ២
                "s4_est_imp_dec" => "3", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ៣
              )
          ),
          "remark" => "កំណត់ត្រា",
          "order_level" => 1,
          "children" => array(
            [
              "id" => 2,
              "account_group" => "",
              "account" => "",
              "sub_account" => "",
              "entity" => "ប្រតិបត្ដិការមិនឆ្លងកាត់អគ្គនាយកដ្ឋានរតនាគារជាតិ",
              "finance_law" => "12",
              "total_amount" => "12",
              "total_percentage_implementing_rule" => "12",
              "order_level" => 2,
              "costing_plan_semester1" => array(
                  // គ្រោងដើមឆ្នាំ
                  "year_planning" => array(
                      "total_amount" => "555",
                      "amount_plan_month_1" => "1",
                      "amount_plan_month_2" => "2",
                      "amount_plan_month_3" => "3",
                  ),
                  "impl_est" => array(
                      "impl_est_amount" => "5554",
                      "imple_amount_plan_month_1" => "1", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ១
                      "imple_amount_plan_month_2" => "2", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ២
                      "imple_amount_plan_month_3" => "3", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ៣
                  )
                  ),
              "costing_plan_semester2" => array(
                  // គ្រោងដើមឆ្នាំ
                  "year_planning" => array(
                      "total_amount" => "2222",
                      "amount_plan_month_4" => "1",
                      "amount_plan_month_5" => "2",
                      "amount_plan_month_6" => "3",
                  ),
                  "impl_est" => array(
                      "impl_est_amount" =>"3333",
                      "imple_amount_plan_month_4" =>"1", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ១
                      "imple_amount_plan_month_5" =>"2", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ២
                      "imple_amount_plan_month_6" =>"3", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ៣
                  )
                  ),
              "costing_plan_semester3" => array(
                  // គ្រោងដើមឆ្នាំ
                  "year_planning" => array(
                      "total_amount" => "555",
                      "amount_plan_month_7" => "1",
                      "amount_plan_month_8" => "2",
                      "amount_plan_month_9" => "3",
                  ),
                  "impl_est" => array(
                      "impl_est_amount" => "5554",
                      "imple_amount_plan_month_7" => "1", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ១
                      "imple_amount_plan_month_8" => "2", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ២
                      "imple_amount_plan_month_9" => "3", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ៣
                  )
              ),
              "costing_plan_semester4" => array(
                  // គ្រោងដើមឆ្នាំ
                  "year_planning" => array(
                      "total_amount" => "555",
                      "amount_plan_month_10" => "1",
                      "amount_plan_month_11" => "2",
                      "amount_plan_month_12" => "3",
                  ),
                  "impl_est" => array(
                      "impl_est_amount" => "5554",
                      "imple_amount_plan_month_10" => "1", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ១
                      "imple_amount_plan_month_11" => "2", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ២
                      "imple_amount_plan_month_12" => "3", // imple = implementing_amount ប៉ាន់ស្មានអនុវត្តខែ៣
                  )
              ),
              "remark" => "កំណត់ត្រា",
              "children" => $children
            ]
          )
        );
          // $data[] = array(
          //   "id" => $row->id,
          //   "planning_id" => $row->planning_id,
          //   "entity_id" => $row->Entity->id,
          //   "entity" => $row->Entity->name_kh,
          //   "currency" => $row->currency,
          //   "currency_id" => $row->currency_id,
          //   "exchange_rate" => $row->exchange_rate,
          //   "status" => $row->status,
          //   "created_by" => $row->created_by,
          //   "modified_by" => $row->modified_by,
          // );
    }
    return $data;
  }
}