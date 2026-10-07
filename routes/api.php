<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\COAController;
use App\Http\Controllers\CompositionRequestController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FileManagerController;
use App\Http\Controllers\IncentiveController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\MobileIncentiveController;
use App\Http\Controllers\Modules\BudgetArrangement\CeilingEntityController;
use App\Http\Controllers\Modules\BudgetArrangement\CeilingRuleController;
use App\Http\Controllers\Modules\BudgetArrangement\PIPInvestmentController;
use App\Http\Controllers\Modules\BudgetMonitoring\CreditMovementController;
use App\Http\Controllers\Modules\BudgetMonitoring\RevImplementBudgetController;
use App\Http\Controllers\Modules\BudgetMonitoring\RevProgramController;
use App\Http\Controllers\Modules\ProgramManagement\ActivityController;
use App\Http\Controllers\Modules\ProgramManagement\ClusterActivityController;
use App\Http\Controllers\Modules\ProgramManagement\KPIClusterActivityController;
use App\Http\Controllers\Modules\ProgramManagement\KPIProgramController;
use App\Http\Controllers\Modules\ProgramManagement\KPISubProgramController;
use App\Http\Controllers\Modules\ProgramManagement\ObjectiveController;
use App\Http\Controllers\Modules\ProgramManagement\ProgramController;
use App\Http\Controllers\Modules\ProgramManagement\SubProgramController;
use App\Http\Controllers\Modules\ProgramManagement\TaskController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OfficerController;
use App\Http\Controllers\OfficerDocumentController;
use App\Http\Controllers\OfficerIncentiveController;
use App\Http\Controllers\OfficerPayableController;
use App\Http\Controllers\OfficerPaymentController;
use App\Http\Controllers\OfficerPaymentRecurringController;
use App\Http\Controllers\OfficerRequestController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LookupController;
use App\Http\Controllers\RoleMenuController;
use App\Http\Controllers\Settings\AccountController;
use App\Http\Controllers\Settings\AccountGroupController;
use App\Http\Controllers\Settings\AccountTypeController;
use App\Http\Controllers\Settings\AccountTypeGroupController;
use App\Http\Controllers\Settings\AuditController;
use App\Http\Controllers\Settings\BankController;
use App\Http\Controllers\Settings\BudgetTemplateController;
use App\Http\Controllers\Settings\BudgetTemplateItemCostController;
use App\Http\Controllers\Settings\CeilingGroupController;
use App\Http\Controllers\Settings\CeilingTypeController;
use App\Http\Controllers\Settings\CreditMovementTypeController;
use App\Http\Controllers\Settings\CurrencyController;
use App\Http\Controllers\Settings\CurrencyExchangeController;
use App\Http\Controllers\Settings\DepartmentController;
use App\Http\Controllers\Settings\EntityController;
use App\Http\Controllers\Settings\EntityMemberController;
use App\Http\Controllers\Settings\ExpenditureGroupController;
use App\Http\Controllers\Settings\ExpenditureTypeController;
use App\Http\Controllers\Settings\FinanceResourceController;
use App\Http\Controllers\Settings\ItemCategoryController;
use App\Http\Controllers\Settings\ItemController;
use App\Http\Controllers\Settings\LetterTypeController;
use App\Http\Controllers\Settings\PaymentMethodController;
use App\Http\Controllers\Settings\PaymentTermController;
use App\Http\Controllers\Settings\PaymentTypeController;
use App\Http\Controllers\Settings\PlanningController;
use App\Http\Controllers\Settings\PositionController;
use App\Http\Controllers\Settings\ProjectStatusController;
use App\Http\Controllers\Settings\ProjectTypeController;
use App\Http\Controllers\Settings\RoleController;
use App\Http\Controllers\Settings\StatusController;
use App\Http\Controllers\Settings\TitleController;
use App\Http\Controllers\Settings\UnitController;
use App\Http\Controllers\TaxableSalaryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Authentication
Route::prefix('auth')->group(function () {
    // Sign-in: at most 10 attempts a minute per client, against password guessing
    Route::middleware(['cors', 'global_site', 'throttle:10,1'])->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('device_login', [AuthController::class, 'loginMobile']);
        Route::post('device_login_otp', [AuthController::class, 'loginMobileOTP']);
        Route::post('verify_otp', [AuthController::class, 'VerifyMobileOTP']);
    });

    Route::middleware(['auth:api', 'global_site', 'cors'])->group(function () {
        // Creating accounts needs a signed-in user with create_users (it was open to anyone)
        Route::post('signup', [AuthController::class, 'signup']);
        Route::get('user', [AuthController::class, 'user']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

// Mobile app
Route::prefix('v1/mobile')->middleware(['auth:api', 'global_site', 'module_permission'])->group(function () {
    Route::resource('department', DepartmentController::class)->only(['index', 'show']);
    Route::resource('incentive', MobileIncentiveController::class)->only(['index', 'show', 'edit', 'update', 'destroy']);
    Route::get('summary', [MobileIncentiveController::class, 'summary']);
    Route::resource('notification', NotificationController::class);
    Route::post('FCMregisterDevice', [AuthController::class, 'registerDevice']);

    Route::controller(DashboardController::class)->group(function () {
        Route::get('total-members-latest-five-year', 'getOfficerGrowth');
        Route::get('total-expneses', 'totalExpense');
        Route::get('total-incomes', 'totalIncome');
        Route::get('total-remaining-balance', 'getRemainingBalance');
        Route::post('quarterly-summary', 'quarterlySummary');
        Route::get('total-incentive-by-category', 'getTotalIncentiveByCategory');
        Route::get('officer-payment-summary', 'getOfficerPaymentSummary');
        Route::post('dashboard-summary', 'getDashboardSummary');
        Route::get('total-members', 'getTotalOfficers');
        Route::post('user-detail', 'getUserDetail');
        Route::post('notification-listing', 'getNotificationListing');
        Route::post('income-listing', 'getIncomeListing');
        Route::post('expense-listing', 'getExpenseListing');
        Route::post('officer-payment-listing', 'getOfficerPaymentListing');
        Route::post('officer-incentive-listing', 'getOfficerIncentiveListing');
        Route::post('officer-request-listing', 'getOfficerRequestListing');
        Route::post('officer-listing', 'getOfficersListing');
        Route::get('status-list', 'getStatusList');
        Route::get('title-list', 'getTitleList');
        Route::get('payment-method-list', 'getPaymentMethodList');
        Route::get('account-list', 'getAccountList');
        Route::get('duty-list', 'getDutyList');
        Route::get('position-list', 'getPositionList');
        Route::get('entity-list', 'getEntityList');
    });

    Route::post('register-expense', [ExpenseController::class, 'insertExpense']);
    Route::post('register-income', [IncomeController::class, 'insertIncome']);
    Route::post('register-officer', [OfficerController::class, 'createOfficer']);
    Route::post('profile', [UserController::class, 'updateProfile']);
});

// Web app
// module_permission: every store / update / destroy needs the permission its controller declares
Route::prefix('v1')->middleware(['auth:api', 'global_site', 'cors', 'module_permission'])->group(function () {

    // Officer
    Route::prefix('officer')->group(function () {
        Route::post('import', [OfficerController::class, 'import']);
        Route::post('export', [OfficerController::class, 'export']);

        // Payment recurring
        Route::post('execute_payment_recurring', [OfficerPaymentRecurringController::class, 'executePayment']);
        Route::post('payment_recurring/search', [OfficerPaymentRecurringController::class, 'index']);
        Route::apiResource('payment_recurring', OfficerPaymentRecurringController::class);

        // Payable
        Route::post('payable/officer_data_payable', [OfficerPayableController::class, 'getGeneralDataForPayable']);
        Route::post('payable/officer_info', [OfficerPayableController::class, 'getPayableInfoByOfficer']);
        Route::post('payable/import', [OfficerPayableController::class, 'import']);
        Route::get('payable/get_field', [OfficerPayableController::class, 'getField']);
        Route::post('payable/search', [OfficerPayableController::class, 'index']);
        Route::apiResource('payable', OfficerPayableController::class);

        // Request
        Route::get('request/get_field', [OfficerRequestController::class, 'getField']);
        Route::post('request/search', [OfficerRequestController::class, 'index']);
        Route::apiResource('request', OfficerRequestController::class);

        // Incentive
        Route::post('incentive/import', [OfficerIncentiveController::class, 'import']);
        Route::get('incentive/get_field', [OfficerIncentiveController::class, 'getField']);
        Route::post('incentive/search', [OfficerIncentiveController::class, 'index']);
        Route::apiResource('incentive', OfficerIncentiveController::class);

        // Payment
        Route::get('payment/get_field', [OfficerPaymentController::class, 'getField']);
        Route::post('payment/search', [OfficerPaymentController::class, 'index']);
        Route::apiResource('payment', OfficerPaymentController::class);

        // Document
        Route::post('document/search', [OfficerDocumentController::class, 'index']);
        Route::apiResource('document', OfficerDocumentController::class);

        // Officer
        Route::controller(OfficerController::class)->name('officer.')->group(function () {
            Route::post('search', 'index')->name('search');
            Route::get('form', 'create')->name('create');
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('{post}', 'update')->name('update');
            Route::delete('{post}', 'destroy')->name('destroy');
            Route::get('{post}', 'show')->name('show');
        });
    });

    Route::post('update_officer_user', [OfficerController::class, 'updateIncentiveOfficer']);
    Route::get('officer_group_by_entities', [OfficerController::class, 'getOfficerGroupByEntities']);
    Route::get('officer_by_entities', [OfficerController::class, 'getOfficerByEntities']);
    Route::get('officer_list_by_entities', [OfficerController::class, 'getOfficerListByEntities']);

    // Chart of account
    Route::post('account/chart_account/search', [COAController::class, 'index']);
    Route::post('account/chart_account/import', [COAController::class, 'import']);
    Route::post('account/chart_account/export', [COAController::class, 'export']);
    Route::apiResource('account/chart_account', COAController::class);

    // Expense
    Route::get('expense/get_field', [ExpenseController::class, 'getField']);
    Route::post('expense/search', [ExpenseController::class, 'index']);
    Route::post('expense/export', [ExpenseController::class, 'export']);
    Route::apiResource('expense', ExpenseController::class);

    // Income
    Route::get('income/get_field', [IncomeController::class, 'getField']);
    Route::post('income/search', [IncomeController::class, 'index']);
    Route::post('income/export', [IncomeController::class, 'export']);
    Route::apiResource('income', IncomeController::class);

    // Incentive
    Route::post('incentive/officer_by_bank', [IncentiveController::class, 'getIncentiveOfficerByBanks']);
    Route::post('incentive/search', [IncentiveController::class, 'index']);
    Route::apiResource('incentive', IncentiveController::class);

    // Composition request
    Route::post('composition_request/search', [CompositionRequestController::class, 'index']);
    Route::apiResource('composition_request', CompositionRequestController::class);

    // Role menu
    Route::post('role_menu/permission/index', [RoleMenuController::class, 'index']);

    // Module: Program management
    Route::prefix('module/program_management')->group(function () {
        Route::post('objective/search', [ObjectiveController::class, 'index']);
        Route::get('objective/overview', [ObjectiveController::class, 'overview']); // objectives > programs > sub-program count
        Route::apiResource('objective', ObjectiveController::class);

        Route::post('program/search', [ProgramController::class, 'index']);
        Route::post('program_by_obj', [ProgramController::class, 'getProgByObj']);
        Route::apiResource('program', ProgramController::class);

        Route::post('sub_program/search', [SubProgramController::class, 'index']);
        Route::post('entity_by_subprog', [SubProgramController::class, 'getEntityBySubprog']);
        Route::post('sub_program_by_prog', [SubProgramController::class, 'getSubProgByProg']);
        Route::apiResource('sub_program', SubProgramController::class);

        Route::post('cluster_activity/search', [ClusterActivityController::class, 'index']);
        Route::post('get_cluster_act_by_subprog', [ClusterActivityController::class, 'getClusActBySprog']);
        Route::post('get_cluster_act', [ClusterActivityController::class, 'getClusterAct']);
        Route::apiResource('cluster_activity', ClusterActivityController::class);

        Route::post('activity/search', [ActivityController::class, 'index']);
        Route::post('get_entity_by_activity', [ActivityController::class, 'getEntityByAct']);
        Route::post('get_act', [ActivityController::class, 'getActByCluster']);
        Route::apiResource('activity', ActivityController::class);

        Route::post('task/search', [TaskController::class, 'index']);
        Route::post('get_task_detail', [TaskController::class, 'getTaskDetail']);
        Route::post('get_task_by_act', [TaskController::class, 'getTaskByAct']);
        Route::apiResource('task', TaskController::class);

        // KPI
        Route::post('kpi_cluster_activity/get_kpi', [KPIClusterActivityController::class, 'index']);
        Route::apiResource('kpi_cluster_activity', KPIClusterActivityController::class);
        Route::post('kpi_program/get_kpi', [KPIProgramController::class, 'index']);
        Route::apiResource('kpi_program', KPIProgramController::class);
        Route::post('kpi_sub_program/get_kpi', [KPISubProgramController::class, 'index']);
        Route::apiResource('kpi_sub_program', KPISubProgramController::class);
    });

    // Module: Budget monitoring
    Route::prefix('module/budget_monitoring')->group(function () {
        Route::post('income_arrangement/search', [RevProgramController::class, 'index']);
        Route::post('income_imple_result/search', [RevImplementBudgetController::class, 'index']);
        Route::post('credit_movement/search', [CreditMovementController::class, 'index']);
        Route::apiResource('credit_movement', CreditMovementController::class);
    });

    // Module: Budget arrangement
    Route::prefix('module/budget_arrangement')->group(function () {
        Route::post('ceiling_rule/search', [CeilingRuleController::class, 'index']);
        Route::apiResource('ceiling_rule', CeilingRuleController::class);
        Route::post('ceiling_entity/search', [CeilingEntityController::class, 'index']);
        Route::apiResource('ceiling_entity', CeilingEntityController::class);
        Route::post('pip/search', [PIPInvestmentController::class, 'index']);
        Route::apiResource('pip', PIPInvestmentController::class);
    });

    // Settings
    Route::prefix('setting')->group(function () {
        Route::post('expenditure_group/search', [ExpenditureGroupController::class, 'index']);
        Route::apiResource('expenditure_group', ExpenditureGroupController::class);
        Route::post('expenditure_type/search', [ExpenditureTypeController::class, 'index']);
        Route::apiResource('expenditure_type', ExpenditureTypeController::class);

        Route::post('budget_template_item_cost/get_item_cost', [BudgetTemplateItemCostController::class, 'index']);
        Route::apiResource('budget_template_item_cost', BudgetTemplateItemCostController::class);
        Route::post('budget_template/search', [BudgetTemplateController::class, 'index']);
        Route::apiResource('budget_template', BudgetTemplateController::class);

        Route::post('account_type_group/search', [AccountTypeGroupController::class, 'index']);
        Route::apiResource('account_type_group', AccountTypeGroupController::class);

        Route::get('account_type/export', [AccountTypeController::class, 'export']);
        Route::post('account_type/search', [AccountTypeController::class, 'index']);
        Route::apiResource('account_type', AccountTypeController::class);

        Route::post('account_group/search', [AccountGroupController::class, 'index']);
        Route::apiResource('account_group', AccountGroupController::class);

        Route::post('account/get_account_by_group', [AccountController::class, 'getAccountByGroup']);
        Route::post('account/get_sub_account', [AccountController::class, 'getSubAccount']);
        Route::match(['get', 'post'], 'account/export', [AccountController::class, 'export']);
        Route::post('account/search', [AccountController::class, 'index']);
        Route::apiResource('account', AccountController::class);

        Route::post('department/import', [DepartmentController::class, 'import']);
        Route::post('department/export', [DepartmentController::class, 'export']);
        Route::post('department/search', [DepartmentController::class, 'index']);
        Route::apiResource('department', DepartmentController::class);

        Route::post('get_member_by_entity', [EntityMemberController::class, 'getMemberByEntity']);
        Route::post('entity_member/search', [EntityMemberController::class, 'index']);
        Route::apiResource('entity_member', EntityMemberController::class);

        Route::post('entity/get_entity_by_dept', [EntityController::class, 'getOptionByDept']);
        Route::post('entity/import', [EntityController::class, 'import']);
        Route::post('entity/export', [EntityController::class, 'export']);
        Route::post('entity/search', [EntityController::class, 'index']);
        Route::apiResource('entity', EntityController::class);

        Route::post('position/search', [PositionController::class, 'index']);
        Route::apiResource('position', PositionController::class);

        Route::post('title/search', [TitleController::class, 'index']);
        Route::apiResource('title', TitleController::class);

        Route::post('unit/search', [UnitController::class, 'index']);
        Route::apiResource('unit', UnitController::class);

        Route::post('item/search', [ItemController::class, 'index']);
        Route::apiResource('item', ItemController::class);

        Route::post('item_category/search', [ItemCategoryController::class, 'index']);
        Route::apiResource('item_category', ItemCategoryController::class);

        Route::post('currency/search', [CurrencyController::class, 'index']);
        Route::apiResource('currency', CurrencyController::class);

        Route::post('currency_exchange/search', [CurrencyExchangeController::class, 'index']);
        Route::apiResource('currency_exchange', CurrencyExchangeController::class);

        Route::post('bank/search', [BankController::class, 'index']);
        Route::apiResource('bank', BankController::class);

        Route::apiResource('status', StatusController::class);

        Route::post('role/set_permission', [RoleController::class, 'setPermission']);
        Route::post('role/search', [RoleController::class, 'index']);
        Route::apiResource('role', RoleController::class);

        Route::post('ceiling_group/search', [CeilingGroupController::class, 'index']);
        Route::apiResource('ceiling_group', CeilingGroupController::class);

        Route::post('ceiling_type/search', [CeilingTypeController::class, 'index']);
        Route::apiResource('ceiling_type', CeilingTypeController::class);

        Route::post('planning/search', [PlanningController::class, 'index']);
        Route::apiResource('planning', PlanningController::class);

        Route::post('finance_resource/search', [FinanceResourceController::class, 'index']);
        Route::apiResource('finance_resource', FinanceResourceController::class);
        Route::post('project_status/search', [ProjectStatusController::class, 'index']);
        Route::apiResource('project_status', ProjectStatusController::class);
        Route::post('project_type/search', [ProjectTypeController::class, 'index']);
        Route::apiResource('project_type', ProjectTypeController::class);

        Route::post('audit/search', [AuditController::class, 'index']);
        Route::apiResource('audit', AuditController::class);

        Route::post('credit_movement_type/search', [CreditMovementTypeController::class, 'index']);
        Route::apiResource('credit_movement_type', CreditMovementTypeController::class);

        Route::post('letter_type/search', [LetterTypeController::class, 'index']);
        Route::apiResource('letter_type', LetterTypeController::class);

        Route::post('payment_method/search', [PaymentMethodController::class, 'index']);
        Route::apiResource('payment_method', PaymentMethodController::class);

        Route::post('payment_term/search', [PaymentTermController::class, 'index']);
        Route::apiResource('payment_term', PaymentTermController::class);

        Route::post('payment_type/get_amount_payment_type', [PaymentTypeController::class, 'getAmountByPaymentType']);
        Route::post('payment_type/import', [PaymentTypeController::class, 'import']);
        Route::post('payment_type/export', [PaymentTypeController::class, 'export']);
        Route::post('payment_type/search', [PaymentTypeController::class, 'index']);
        Route::apiResource('payment_type', PaymentTypeController::class);
    });

    Route::apiResource('taxable_salary', TaxableSalaryController::class);
    Route::apiResource('bank_account', BankAccountController::class);
    // Current planning year (config_planning_year), used by the budget screens for their year headers
    Route::get('configuration/planning', [ConfigurationController::class, 'planning']);
    Route::apiResource('configuration', ConfigurationController::class)->only(['index', 'show', 'update']);

    // Notification
    Route::get('nofication/unreadnotification', [NotificationController::class, 'getUnreadNotifications']);
    Route::post('notification/search', [NotificationController::class, 'index']);
    Route::post('send_notification', [NotificationController::class, 'sendNotification']);
    Route::apiResource('notification', NotificationController::class);

    // User
    // Dropdown choices by field name, for forms whose page didn't send them
    Route::get('lookup/{field}', [LookupController::class, 'show'])->where('field', '[a-z_]+');

    Route::post('user/device', [UserController::class, 'getUserDevice']);
    Route::post('user/profile', [UserController::class, 'getProfile']);
    Route::post('user_index', [UserController::class, 'index']);
    Route::apiResource('user', UserController::class);

    // Report
    Route::prefix('report')->controller(ReportController::class)->group(function () {
        Route::get('summary', 'summary');
        Route::get('summary_income_outcome', 'summaryIncomeOutcome');
        Route::get('income_by_sector', 'incomeBySector');
        Route::get('income_by_entity', 'incomeByEntity');
        Route::get('expense_by_entity', 'expenseByEntity');
        Route::get('expense_by_sector', 'expenseBySector');
        Route::get('income_outcome_by_quarter', 'getIncomeOutcomeByQuarter');
        Route::post('convert/convert_annex_lm_to_excel', 'convertAnnexLMToExcel');
        Route::post('convert/convert_annex_bank_to_excel', 'convertAnnexBankToExcel');
        Route::post('convert/convert_annex_gdnt_excel', 'convertAnnexGDNTToExcel');

        // PDF reports: officers, income, expense, officer_incentive, officers_payment, ...
        Route::get('types', 'types');
        Route::post('{type}/data', 'preview')->where('type', '[a-z_]+');
        Route::post('{type}', 'generate')->where('type', '[a-z_]+');
    });

    // File manager
    Route::post('file_manager/search', [FileManagerController::class, 'index']);
    Route::post('file_manager/upload', [FileManagerController::class, 'fileImageUploadService']);
    Route::apiResource('file_manager', FileManagerController::class);
    Route::post('file_upload', [FileManagerController::class, 'fileUploadService']);
    Route::post('file_income_upload', [FileManagerController::class, 'fileIncomeUploadService']);
});
