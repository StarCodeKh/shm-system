<?php

  namespace App\Models\Modules\BudgetArrangement;
  use App\Models\Settings\Account;
  use App\Models\Settings\AccountGroup;
  use App\Models\Settings\AccountType;
  use App\Models\Settings\AccountTypeGroup;
  use Illuminate\Database\Eloquent\Model;
  use DB;

  class PBRevenue extends Model
  {
    protected $table = 'mef_revenue';
    protected $fillable = [
                              'planning_id',
                              'entity_id',
                              'account_id',
                              'sub_account_id',
                              'imple_rev_budget',
                              'rule_basic_rev_plan',
                              'rule_qty_rev_plan',
                              'rule_budget_credit',
                              'imple_budget_semester_1',
                              'imple_budget_semester_2',
                              'annual_estimate',
                              'percentage_rule',
                              'basic_rev_plan',
                              'qty_rev_plan',
                              'total_rev_plan',
                              'percentage_rev_plan',
                              'status',
                              'created_by',
                              'modified_by',
                          ];
    public $timestamps = true;

    public static function getRevenues($filter){
      $accountTypeGroups = AccountTypeGroup::orderByLevel()->get();
      $accountTypeGroupArr = array();
      foreach($accountTypeGroups as $accountTypeGroup){
        $accountTypeGroupId = $accountTypeGroup->id;
        // account types are linked to their group through account_group_to_type
        $accountTypes = AccountType::whereIn("id", DB::table("account_group_to_type")
                                          ->where("account_type_group_id", $accountTypeGroupId)
                                          ->pluck("account_type_id"))
                                      ->orderByLevel()
                                      ->get();
        $accTypeArr = array();
        if($accountTypes){
          foreach($accountTypes as $accountType){
            $accountTypeId = $accountType->id;
            $accountGroupArr = array();
            $accountGroups = DB::table("account_group_to_type as ag2t")
                                ->select("ag2t.account_group_id as id","ag.order_level","ag.name_en", "ag.name_kh", "ag.code")
                                ->join("account_group as ag", "ag.id", "ag2t.account_group_id")
                                ->join("account_type as at", "at.id", "ag2t.account_type_id")
                                ->where("ag2t.account_type_group_id", $accountTypeGroupId)
                                ->where("ag2t.account_type_id", $accountTypeId)
                                ->orderBy("ag.order_level")
                                ->get();
            if($accountGroups){
              foreach($accountGroups as $accountGroup){
                $accountGroupId = $accountGroup->id;
                $accounts = Account::where("account_group_id", $accountGroupId)
                                    ->orderBy("code")
                                    ->get();
                $accountArr = array();
                foreach($accounts as $account){
                  $accountId = $account->id;
                  $subAccounts = Account::where("parent_id", $accountId)
                                        ->orderBy("code")
                                        ->get();
                  $subAccountArr = array();
                  foreach($subAccounts as $subAccount){
                    $subAccountId = $subAccount->id;
                    $revenues = PBRevenue::where("entity_id", 1)
                                          ->where("planning_id", config_planning_year)
                                          ->where("sub_account_id", $subAccountId)
                                          ->get();
                    $revenueArr = array();
                    foreach($revenues as $revenue){
                      $revenueArr[] = array(
                        'planning_id'=>$revenue->planning_id,
                        'entity_id'=>$revenue->entity_id,
                        'account_id'=>$revenue->account_id,
                        'sub_account_id'=>$revenue->sub_account_id,
                        'imple_rev_budget'=>$revenue->imple_rev_budget,
                        'rule_basic_rev_plan'=>$revenue->rule_basic_rev_plan,
                        'rule_qty_rev_plan'=>$revenue->rule_qty_rev_plan,
                        'rule_budget_credit'=>$revenue->rule_budget_credit,
                        'imple_budget_semester_1'=>$revenue->imple_budget_semester_1,
                        'imple_budget_semester_2'=>$revenue->imple_budget_semester_2,
                        'annual_estimate'=>$revenue->annual_estimate,
                        'percentage_rule'=>$revenue->percentage_rule,
                        'basic_rev_plan'=>$revenue->basic_rev_plan,
                        'qty_rev_plan'=>$revenue->qty_rev_plan,
                        'total_rev_plan'=>$revenue->total_rev_plan,
                        'percentage_rev_plan'=>$revenue->percentage_rev_plan,
                      );
                    }
                    $subAccountArr[] = array(
                      "code" => $subAccount->code, 
                      "name_en" => $subAccount->name_en,
                      "name_kh" => $subAccount->name_kh,
                      "description" => $subAccount->description,
                      "order_level" => $subAccount->order_level, 
                      "revenues" => $revenueArr
                    );
                  }
                  $accountArr[] = array(
                    "code" => $account->code, 
                    "name_en" => $account->name_en,
                    "name_kh" => $account->name_kh,
                    "description" => $account->description,
                    "order_level" => $account->order_level, 
                    "sub_accounts" => $subAccountArr
                  );
                }
                $accountGroupArr[] = array(
                  "code" => $accountGroup->code,
                  "name_en" => $accountGroup->name_en,
                  "name_kh" => $accountGroup->name_kh,
                  "accounts" => $accountArr
                );
              }
            }
            $accTypeArr[] = array(
              "name_en" => $accountType->name_en,
              "name_kh" => $accountType->name_kh,
              "order_level" => $accountType->order_level,
              "account_groups" => $accountGroupArr
            );
          }
        }
        $accountTypeGroupArr[] = array(
          "name_en" => $accountTypeGroup->name_en,
          "name_kh" => $accountTypeGroup->name_kh,
          "order_level" => $accountTypeGroup->order_level,
          "account_group_types" => $accTypeArr
        );
      }
      return $accountTypeGroupArr;
    }
  }