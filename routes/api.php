<?php

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\OfficerController;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AccountTypeExport;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get("/users", function() {
    $users = User::factory()->count(100)->make();
    return $users;
})->middleware('permission:view dashboard');

Route::group([
    'prefix' => 'auth'
], function () {
    Route::group([
        'middleware' => ['cors', 'global_site']
    ], function() {
        Route::post('login', 'AuthController@login');
        Route::post('device_login', 'AuthController@loginMobile');
        Route::post('device_login_otp', 'AuthController@loginMobileOTP');
        Route::post('verify_otp', 'AuthController@VerifyMobileOTP');
        Route::post('signup', 'AuthController@signup');
    });
    Route::group([
        'middleware' => ['auth:api', 'global_site', 'cors']
    ], function() {
        Route::get('user', 'AuthController@user');
        Route::post('logout', 'AuthController@logout');
    });
});

Route::group([
    'prefix' => 'v1/mobile'
], function () {
    Route::group([
        'middleware' => ['auth:api', 'global_site']
    ], function() {
        Route::resource('department', 'DepartmentController');
        Route::resource('incentive', 'MobileIncentiveController');
        Route::get('summary', 'MobileIncentiveController@summary');
        Route::resource('notification', 'NotificationController');
        Route::post('FCMregisterDevice', 'AuthController@registerDevice');
        Route::get('total-members-latest-five-year', 'DashboardController@getOfficerGrowth');
        Route::get('total-expneses', 'DashboardController@totalExpense');
        Route::get('total-incomes', 'DashboardController@totalIncome');
        Route::get('total-remaining-balance', 'DashboardController@getRemainingBalance');
        Route::post('quarterly-summary', 'DashboardController@quarterlySummary');
        Route::get('total-incentive-by-category', 'DashboardController@getTotalIncentiveByCategory');
        Route::get('officer-payment-summary', 'DashboardController@getOfficerPaymentSummary');
        Route::post('dashboard-summary', 'DashboardController@getDashboardSummary');
        Route::get('total-members', 'DashboardController@getTotalOfficers');
        Route::post('user-detail', 'DashboardController@getUserDetail');
        Route::post('notification-listing', 'DashboardController@getNotificationListing');
        Route::post('income-listing', 'DashboardController@getIncomeListing');
        Route::post('expense-listing', 'DashboardController@getExpenseListing');
        Route::post('officer-payment-listing', 'DashboardController@getOfficerPaymentListing');
        Route::post('officer-incentive-listing', 'DashboardController@getOfficerIncentiveListing');
        Route::post('officer-request-listing', 'DashboardController@getOfficerRequestListing');
        Route::post('officer-listing', 'DashboardController@getOfficersListing');
        Route::post('register-expense', 'ExpenseController@insertExpense');
        Route::post('register-income', 'IncomeController@insertIncome');
        Route::post('register-officer', 'OfficerController@createOfficer');
        Route::get('status-list', 'DashboardController@getStatusList');
        Route::get('title-list', 'DashboardController@getTitleList');
        Route::get('payment-method-list', 'DashboardController@getPaymentMethodList');
        Route::get('account-list', 'DashboardController@getAccountList');
        Route::get('duty-list', 'DashboardController@getDutyList');
        Route::get('position-list', 'DashboardController@getPositionList');
        Route::get('entity-list', 'DashboardController@getEntityList');
        Route::post('profile', 'UserController@updateProfile');
    });
});

Route::group([
    'prefix' => 'v1/officer'
], function () {
    Route::group([
        'middleware' => ['auth:api', 'global_site', 'cors']
    ], function() {
        // Officer Management
        Route::post('/import', 'OfficerController@import');
        Route::post('/export', 'OfficerController@export');

        Route::post('/execute_payable_officers', 'OfficerPaymentRecurringController@executePayableOfficers'); // Execute all Payable By Schedule If officer not yet time to renewal
        Route::post('/execute_payment_recurring', 'OfficerPaymentRecurringController@executePayment'); // Execute all payable officers when not yet make a payment for monthly and yearly
        Route::post('/execute_payable', 'OfficerPaymentRecurringController@executePayable'); // Generate payable when officers have been generated in payable table
        Route::post('/payment_recurring/search', 'OfficerPaymentRecurringController@index');
        Route::resource('/payment_recurring', 'OfficerPaymentRecurringController');
        
        Route::post('/payable/officer_data_payable', 'OfficerPayableController@getGeneralDataForPayable');
        Route::post('/payable/officer_info', 'OfficerPayableController@getPayableInfoByOfficer');
        Route::post('/payable/import', 'OfficerPayableController@import');
        Route::get('/payable/get_field', 'OfficerPayableController@getField');
        Route::post('/payable/search', 'OfficerPayableController@index');
        Route::resource('/payable', 'OfficerPayableController');

        Route::get('/request/get_field', 'OfficerRequestController@getField');
        Route::post('/request/search', 'OfficerRequestController@index');
        Route::resource('/request', 'OfficerRequestController');

        Route::post('/incentive/import', 'OfficerIncentiveController@import');
        Route::post('/incentive/export', 'OfficerIncentiveController@export');

        Route::get('/incentive/get_field', 'OfficerIncentiveController@getField');
        Route::post('/incentive/search', 'OfficerIncentiveController@index');
        Route::resource('/incentive', 'OfficerIncentiveController');
        
        Route::get('/payment/get_field', 'OfficerPaymentController@getField');
        Route::post('/payment/search', 'OfficerPaymentController@index');
        Route::resource('/payment', 'OfficerPaymentController');

        Route::post('/document/search', 'OfficerDocumentController@index');
        Route::resource('/document', 'OfficerDocumentController');
        
        // Route::post('officer/search', 'OfficerController@index')->middleware('permission:create officer');
        // Route::get('officer', 'OfficerController')->middleware('permission:create officer');
        // Route::get('officer', 'OfficerController')->middleware('permission:create officer');
        // Route::get('officer', 'OfficerController')->middleware('permission:create officer');
        // Route::get('officer', 'OfficerController')->middleware('permission:create officer');

        // Now override middleware per method
        Route::post('/search', [OfficerController::class, 'index'])->name('index');
        Route::get('/form', [OfficerController::class, 'create'])->name('create');
        Route::get('/', [OfficerController::class, 'index'])->name('index');
        Route::post('/', [OfficerController::class, 'store'])->name('store');
        Route::put('/{post}', [OfficerController::class, 'update'])->name('update');
        Route::delete('/{post}', [OfficerController::class, 'destroy'])->name('destroy');
        Route::get('/{post}', [OfficerController::class, 'show'])->name('show');
    });
});


Route::group([
    'prefix' => 'v1/account'
], function () {
    Route::group([
        'middleware' => ['auth:api', 'global_site', 'cors']
    ], function() {
       // Chart Of Account Management
        Route::post('/chart_account/search', 'COAController@index');
        Route::resource('/chart_account', 'COAController');
        Route::post('/chart_account/import', 'COAController@import');
        Route::post('/chart_account/export', 'COAController@export');
        
    });
});


Route::group([
    'prefix' => 'v1'
], function () {
    Route::group([
        'middleware' => ['auth:api', 'global_site', 'cors']
    ], function() {
        // Expense
        Route::get('/expense/get_field', 'ExpenseController@getField');
        Route::resource('/expense', 'ExpenseController');
        Route::post('/expense/search', 'ExpenseController@index');
        Route::post('/expense/export', 'ExpenseController@export');
        // Income
        Route::get('/income/get_field', 'IncomeController@getField');
        Route::resource('/income', 'IncomeController');
        Route::post('/income/search', 'IncomeController@index');
        Route::post('/income/export', 'IncomeController@export');

         //Role Menu
        Route::post('role_menu/permission/index', 'RoleMenuController@index');

        // គ្រប់គ្រងកម្មវិធី
        Route::resource('module/program_management/kpi_objective', 'Modules\ProgramManagement\KPIObjectiveController');
        Route::post('module/program_management/objective/search', 'Modules\ProgramManagement\ObjectiveController@index');
        Route::post('module/program_management/program/search', 'Modules\ProgramManagement\ProgramController@index');
        Route::post('module/program_management/sub_program/search', 'Modules\ProgramManagement\SubProgramController@index');
        Route::post('module/program_management/cluster_activity/search', 'Modules\ProgramManagement\ClusterActivityController@index');
        Route::post('module/program_management/activity/search', 'Modules\ProgramManagement\ActivityController@index');
        
        Route::post('module/program_management/get_task_detail', 'Modules\ProgramManagement\TaskController@getTaskDetail');
        Route::post('module/program_management/get_task_by_act', 'Modules\ProgramManagement\TaskController@getTaskByAct');
        Route::post('module/program_management/task/search', 'Modules\ProgramManagement\TaskController@index');

        Route::post('module/program_management/get_entity_by_activity', 'Modules\ProgramManagement\ActivityController@getEntityByAct');
        Route::post('module/program_management/program_by_obj', 'Modules\ProgramManagement\ProgramController@getProgByObj');
        Route::post('module/program_management/entity_by_subprog', 'Modules\ProgramManagement\SubProgramController@getEntityBySubprog');
        Route::post('module/program_management/sub_program_by_prog', 'Modules\ProgramManagement\SubProgramController@getSubProgByProg');
        Route::post('module/program_management/get_cluster_act_by_subprog', 'Modules\ProgramManagement\ClusterActivityController@getClusActBySprog');
        Route::post('module/program_management/get_cluster_act', 'Modules\ProgramManagement\ClusterActivityController@getClusterAct');
        Route::post('module/program_management/get_act', 'Modules\ProgramManagement\ActivityController@getActByCluster');
        Route::post('module/program_management/get_activity_by_subprog', 'Modules\ProgramManagement\ActivityController@getActBySubProg');

        Route::resource('module/program_management/objective', 'Modules\ProgramManagement\ObjectiveController');
        Route::resource('module/program_management/program', 'Modules\ProgramManagement\ProgramController');
        Route::resource('module/program_management/sub_program', 'Modules\ProgramManagement\SubProgramController');
        Route::resource('module/program_management/cluster_activity', 'Modules\ProgramManagement\ClusterActivityController');
        Route::resource('module/program_management/activity', 'Modules\ProgramManagement\ActivityController');
        Route::resource('module/program_management/task', 'Modules\ProgramManagement\TaskController');
        // Module Budget Monitoring
        Route::post('module/budget_monitoring/income_arrangement/search', 'Modules\BudgetMonitoring\RevProgramController@index');
        Route::post('module/budget_monitoring/income_imple_result/search', 'Modules\BudgetMonitoring\RevImplementBudgetController@index');
        Route::post('module/budget_monitoring/credit_movement/search', 'Modules\BudgetMonitoring\CreditMovementController@index');
        Route::resource('module/budget_monitoring/credit_movement', 'Modules\BudgetMonitoring\CreditMovementController');
        // KPI
        Route::post('module/program_management/kpi_cluster_activity/get_kpi', 'Modules\ProgramManagement\KPIClusterActivityController@index');
        Route::resource('module/program_management/kpi_cluster_activity', 'Modules\ProgramManagement\KPIClusterActivityController');

        Route::post('module/program_management/kpi_program/get_kpi', 'Modules\ProgramManagement\KPIProgramController@index');
        Route::resource('module/program_management/kpi_program', 'Modules\ProgramManagement\KPIProgramController');

        Route::post('module/program_management/kpi_sub_program/get_kpi', 'Modules\ProgramManagement\KPISubProgramController@index');
        Route::resource('module/program_management/kpi_sub_program', 'Modules\ProgramManagement\KPISubProgramController');

        // PIP
        Route::post('module/budget_arrangement/ceiling_rule/search', 'Modules\BudgetArrangement\CeilingRuleController@index');
        Route::resource('module/budget_arrangement/ceiling_rule', 'Modules\BudgetArrangement\CeilingRuleController');
        Route::post('module/budget_arrangement/ceiling_entity/search', 'Modules\BudgetArrangement\CeilingEntityController@index');
        Route::resource('module/budget_arrangement/ceiling_entity', 'Modules\BudgetArrangement\CeilingEntityController');
        Route::post('module/budget_arrangement/pip/pip_info', 'Modules\BudgetArrangement\PIPInvestmentController@getPipInfo');
        Route::post('module/budget_arrangement/pip/search', 'Modules\BudgetArrangement\PIPInvestmentController@index');
        Route::resource('module/budget_arrangement/pip', 'Modules\BudgetArrangement\PIPInvestmentController');
        // BPS
        Route::post('module/budget_arrangement/bsp_program/search', 'Modules\BudgetArrangement\BSPProgramController@index');
        Route::resource('module/budget_arrangement/bsp_program', 'Modules\BudgetArrangement\BSPProgramController');
        // PB
        Route::resource('module/budget_arrangement/pb_costing', 'Modules\BudgetArrangement\PBCostingController');
        Route::resource('module/budget_arrangement/pb_revenue', 'Modules\BudgetArrangement\PBRevenueController');
        Route::resource('module/budget_arrangement/pb_expenditure', 'Modules\BudgetArrangement\PBExpenditureController');
        // Settings

        // Expenditure Group
        Route::post('setting/expenditure_group/search', 'Settings\ExpenditureGroupController@index');
        Route::post('setting/expenditure_type/search', 'Settings\ExpenditureTypeController@index');

        Route::resource('setting/expenditure_group', 'Settings\ExpenditureGroupController');
        Route::resource('setting/expenditure_type', 'Settings\ExpenditureTypeController');

        Route::post('setting/budget_template_item_cost/get_item_cost', 'Settings\BudgetTemplateItemCostController@index');
        Route::resource('setting/budget_template_item_cost', 'Settings\BudgetTemplateItemCostController');
        Route::post('setting/budget_template/search', 'Settings\BudgetTemplateController@index');
        Route::resource('setting/budget_template', 'Settings\BudgetTemplateController');

        Route::post('setting/account_type_group/search', 'Settings\AccountTypeGroupController@index');
        Route::resource('setting/account_type_group', 'Settings\AccountTypeGroupController');
        
        Route::get('setting/account_type/export', 'Settings\AccountTypeController@export');
        Route::post('setting/account_type/search', 'Settings\AccountTypeController@index');
        Route::resource('setting/account_type', 'Settings\AccountTypeController');
        
        Route::post('setting/account_group/search', 'Settings\AccountGroupController@index');
        Route::resource('setting/account_group', 'Settings\AccountGroupController');

        Route::post('setting/account/get_account_by_group', 'Settings\AccountController@getAccountByGroup');
        Route::post('setting/account/get_sub_account', 'Settings\AccountController@getSubAccount');
        Route::get('setting/account/export', 'Settings\AccountController@export');
        Route::post('setting/account/search', 'Settings\AccountController@index');
        Route::resource('setting/account', 'Settings\AccountController');
        Route::post('setting/account/export', 'Settings\AccountController@export');

        Route::post('setting/department/import', 'Settings\DepartmentController@import');
        Route::post('setting/department/export', 'Settings\DepartmentController@export');
        Route::post('setting/department/search', 'Settings\DepartmentController@index');
        Route::resource('setting/department', 'Settings\DepartmentController');

        Route::post('setting/get_member_by_entity', 'Settings\EntityMemberController@getMemberByEntity');
        Route::post('setting/entity_member/search', 'Settings\EntityMemberController@index');
        Route::resource('setting/entity_member', 'Settings\EntityMemberController');

        Route::post('setting/entity/get_entity_by_dept', 'Settings\EntityController@getOptionByDept');
        Route::post('setting/entity/import', 'Settings\EntityController@import');
        Route::post('setting/entity/export', 'Settings\EntityController@export');
        Route::post('setting/entity/search', 'Settings\EntityController@index');
        Route::resource('setting/entity', 'Settings\EntityController'); 

        Route::post('setting/position/search', 'Settings\PositionController@index');
        Route::resource('setting/position', 'Settings\PositionController');
        
        Route::post('setting/title/search', 'Settings\TitleController@index');
        Route::resource('setting/title', 'Settings\TitleController');

        Route::post('setting/unit/search', 'Settings\UnitController@index');
        Route::resource('setting/unit', 'Settings\UnitController');

        Route::post('setting/item/search', 'Settings\ItemController@index');
        Route::resource('setting/item', 'Settings\ItemController');

        Route::post('setting/item_category/search', 'Settings\ItemCategoryController@index');
        Route::resource('setting/item_category', 'Settings\ItemCategoryController');
        
        Route::post('setting/currency/search', 'Settings\CurrencyController@index');
        Route::resource('setting/currency', 'Settings\CurrencyController');

        Route::post('setting/currency_exchange/search', 'Settings\CurrencyExchangeController@index');
        Route::resource('setting/currency_exchange', 'Settings\CurrencyExchangeController');
        
        Route::resource('sector', 'SectorController');
        Route::resource('duty', 'DutyController');

        Route::post('setting/bank/search', 'Settings\BankController@index');
        Route::resource('setting/bank', 'Settings\BankController');

        Route::post('setting/status/search', 'Settings\StatusController@index');
        Route::resource('setting/status', 'Settings\StatusController');

        Route::post('setting/role/create_permission', 'Settings\RoleController@createPermission');
        Route::post('setting/role/set_permission', 'Settings\RoleController@setPermission');
        Route::post('setting/role/search', 'Settings\RoleController@index');
        Route::resource('setting/role', 'Settings\RoleController');
        Route::resource('taxable_salary', 'TaxableSalaryController');
        Route::resource('notificatoin_type', 'NotificationTypeController');
        Route::resource('circular', 'CircularController');
        Route::get('setting/permission/menu/list', 'Settings\PermissionMenuSettingController@index');
        
        // Notification
        
        Route::post('notification_type/search', 'NotificationTypeController@index');
        Route::resource('notification_type', 'NotificationTypeController');
        
        Route::post('nofication/readnotification', 'NotificationController@readNotification');
        Route::get('nofication/unreadnotification', 'NotificationController@getUnreadNotifications');
        Route::post('notification/search', 'NotificationController@index');
        Route::resource('notification', 'NotificationController');
        
        Route::post('setting/ceiling_group/search', 'Settings\CeilingGroupController@index');
        Route::resource('setting/ceiling_group', 'Settings\CeilingGroupController');

        Route::post('setting/ceiling_type/search', 'Settings\CeilingTypeController@index');
        Route::resource('setting/ceiling_type', 'Settings\CeilingTypeController');

        // Setting Budget Arrangement
        Route::post('setting/planning/search', 'Settings\PlanningController@index');
        Route::resource('setting/planning', 'Settings\PlanningController');

        Route::post('setting/finance_resource/search', 'Settings\FinanceResourceController@index');
        Route::post('setting/project_status/search', 'Settings\ProjectStatusController@index');
        Route::post('setting/project_type/search', 'Settings\ProjectTypeController@index');

        Route::resource('setting/finance_resource', 'Settings\FinanceResourceController');
        Route::resource('setting/project_status', 'Settings\ProjectStatusController');
        Route::resource('setting/project_type', 'Settings\ProjectTypeController');

        // Auditrail Log
        Route::post('setting/audit/search', 'Settings\AuditController@index');
        Route::resource('setting/audit', 'Settings\AuditController');

        Route::post('setting/credit_movement_type/search', 'Settings\CreditMovementTypeController@index');
        Route::resource('setting/credit_movement_type', 'Settings\CreditMovementTypeController');

        Route::post('setting/letter_type/search', 'Settings\LetterTypeController@index');
        Route::resource('setting/letter_type', 'Settings\LetterTypeController');

        // Officer
        Route::post('update_officer_user', 'OfficerController@updateIncentiveOfficer');
        Route::post('incentive/officer_by_bank', 'IncentiveController@getIncentiveOfficerByBanks');

        Route::post('incentive/search', 'IncentiveController@index');
        Route::resource('incentive', 'IncentiveController');

        Route::resource('document_type', 'DocumentTypeController');
        Route::resource('document_template', 'DocumentTemplateController');

        Route::post('composition_request/search', 'CompositionRequestController@index');
        Route::resource('composition_request', 'CompositionRequestController');
        
        // User
        Route::resource('user/officer', 'UserController');
        Route::post('user/officer', 'UserController@getUserByOfficers');
        Route::post('user/device', 'UserController@getUserDevice');
        Route::post('user/profile', 'UserController@getProfile');
        Route::post('user_index', 'UserController@index');
        Route::resource('user', 'UserController');
        // Route::post('/profile', 'UserController@updateProfile');
        
        // Payment Method
        Route::post('setting/payment_method/search', 'Settings\PaymentMethodController@index');
        Route::resource('setting/payment_method', 'Settings\PaymentMethodController');

        // Payment Term
        Route::post('setting/payment_term/search', 'Settings\PaymentTermController@index');
        Route::resource('setting/payment_term', 'Settings\PaymentTermController');

        // Payment Type
        Route::post('setting/payment_type/get_amount_payment_type', 'Settings\PaymentTypeController@getAmountByPaymentType');
        Route::post('setting/payment_type/import', 'Settings\PaymentTypeController@import');
        Route::post('setting/payment_type/export', 'Settings\PaymentTypeController@export');
        Route::post('setting/payment_type/search', 'Settings\PaymentTypeController@index');
        Route::resource('setting/payment_type', 'Settings\PaymentTypeController');

        Route::resource('category', 'CategoryController');
        Route::resource('account', 'AccountController');
        Route::resource('bank_account', 'BankAccountController');
        Route::resource('account_group', 'AccountGroupController');
        Route::resource('configuration', 'ConfigurationController');
        Route::post('report/income', 'ReportController@incomeReport');
        Route::post('report/expense', 'ReportController@expenseReport');
        Route::post('report/officer_incentive', 'ReportController@officerIncentiveReport');
        Route::post('report/officers_request', 'ReportController@officersRequestReport');
        Route::post('report/officers_payment', 'ReportController@officersPaymentReport');
        Route::post('report/officers_payable', 'ReportController@officersPayableReport');
        Route::post('report/officers', 'ReportController@officersReport');
        Route::post('report/sales', 'ReportController@showSalesReport');
        Route::get('report/summary', 'ReportController@summary');
        Route::get('report/summary_income_outcome', 'ReportController@summaryIncomeOutcome');
        Route::get('report/income_by_sector', 'ReportController@incomeBySector');
        Route::get('report/income_by_entity', 'ReportController@incomeByEntity');
        Route::get('report/expense_by_entity', 'ReportController@expenseByEntity');
        Route::get('report/expense_by_sector', 'ReportController@expenseBySector');
        Route::get('report/income_outcome_by_quarter', 'ReportController@getIncomeOutcomeByQuarter');
        Route::get('report/income_tax_monthly', 'ReportController@incomeTaxMonthly');
        Route::get('report/top_rank_income', 'ReportController@getTopRankEntity');
        Route::post('report/convert/convert_excel_by_bank', 'ReportController@convertToExcelByBank');
        Route::post('report/convert/convert_annex_lm_to_excel', 'ReportController@convertAnnexLMToExcel');
        Route::post('report/convert/convert_annex_bank_to_excel', 'ReportController@convertAnnexBankToExcel');
        Route::post('report/convert/convert_annex_gdnt_excel', 'ReportController@convertAnnexGDNTToExcel');
        Route::post('report/convert/convert_summary_income_outcome_excel', 'ReportController@convertSummaryIncomeOutcomeToExcel');
        // getOfficerByEntities
        Route::get('officer_group_by_entities', 'OfficerController@getOfficerGroupByEntities');
        Route::get('officer_by_entities', 'OfficerController@getOfficerByEntities');
        Route::get('officer_list_by_entities', 'OfficerController@getOfficerListByEntities');
        // Upload File
        Route::post('upload_objective', 'FileManagerController@fileUploadObjective');
        Route::post('upload_program', 'FileManagerController@fileUploadProgram');
        Route::post('upload_subprogram', 'FileManagerController@fileUploadSubProgram');
        Route::post('upload_cluster_activity', 'FileManagerController@fileUploadClusterActivity');
        Route::post('upload_activity', 'FileManagerController@fileUploadActivity');
        
        Route::post('upload_account', 'FileManagerController@fileUploadAccount');
        Route::post('upload_sub_account', 'FileManagerController@fileUploadSubAccount');
        Route::post('upload_account_group', 'FileManagerController@fileUploadAccountGroup');
        Route::post('upload_entity_member', 'FileManagerController@fileUploadEntityMember');
        Route::post('upload_entity', 'FileManagerController@fileUploadEntity');
        Route::post('upload_ceiling_entity', 'FileManagerController@fileUploadCeilingEntity');
        Route::post('upload_ceiling_data_entity', 'FileManagerController@fileUploadCeilingDataEntity');
        Route::post('upload_user', 'FileManagerController@fileUploadUser');
        // PIP
        Route::post('upload_pip_investment', 'FileManagerController@fileUploadPIPInvestment');
        Route::post('upload_pip_inv_detail', 'FileManagerController@fileUploadPIPInvDetail');
        // BSP
        // Import Assign SubProgram, Cluster Activity, Activity to Entity
        Route::post('upload_bsp_assign_program', 'FileManagerController@fileUploadBSPAssignProgram');
        // PB
        Route::post('upload_pb_costing', 'FileManagerController@fileUploadPBCosting');
        Route::post('upload_pb_revenue', 'FileManagerController@fileUploadPBRevenue');
        Route::post('upload_pb_expenditure', 'FileManagerController@fileUploadPBExpenditure');
        // BM
        Route::post('upload_rev_program', 'FileManagerController@fileUploadBMRevProgramImport');
        Route::post('upload_exp_program', 'FileManagerController@fileUploadBMExpProgramImport');

        Route::post('upload_item', 'FileManagerController@fileUploadItem');
        Route::post('upload_unit', 'FileManagerController@fileUploadUnit');

        Route::post('file_manager/search', 'FileManagerController@index');
        Route::post('file_manager/upload', 'FileManagerController@fileImageUploadService');
        Route::resource('file_manager', 'FileManagerController');

        Route::post('file_upload', 'FileManagerController@fileUploadService');
        Route::post('file_officer_upload', 'FileManagerController@fileUploadOfficer');
        Route::post('file_officer_expense_upload', 'FileManagerController@OfficerIncentiveExpenseImport');
        Route::post('file_income_upload', 'FileManagerController@fileIncomeUploadService');
        Route::post('file_outcome_upload', 'FileManagerController@fileOutcomeUploadService');
        // Send Push Notification
        Route::post('send_notification', 'NotificationController@sendNotification');
        // New Function
        Route::get('send_notification_api', 'NotificationController@index');
    });
});