<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\COAController;
use App\Http\Controllers\CompositionRequestController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FileManagerController;
use App\Http\Controllers\IncentiveController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\MobileIncentiveController;
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
use App\Http\Controllers\PrintController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\Settings\BankController;
use App\Http\Controllers\Settings\CurrencyController;
use App\Http\Controllers\Settings\CurrencyExchangeController;
use App\Http\Controllers\Settings\DepartmentController;
use App\Http\Controllers\Settings\EntityController;
use App\Http\Controllers\Settings\EntityMemberController;
use App\Http\Controllers\Settings\LetterTypeController;
use App\Http\Controllers\Settings\MenuController;
use App\Http\Controllers\Settings\PaymentMethodController;
use App\Http\Controllers\Settings\PaymentTermController;
use App\Http\Controllers\Settings\PaymentTypeController;
use App\Http\Controllers\Settings\PlanningController;
use App\Http\Controllers\Settings\PositionController;
use App\Http\Controllers\Settings\RoleController;
use App\Http\Controllers\Settings\StatusController;
use App\Http\Controllers\Settings\TitleController;
use App\Http\Controllers\TaxableSalaryController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public branding (name, organisation, website, footer) for the footer and sign-in page: no secrets
Route::get('v1/site', [SiteController::class, 'show'])->middleware(['cors', 'throttle:60,1']);

// Authentication
Route::prefix('auth')->group(function () {
    // Sign-in: at most 10 attempts a minute per client, against password guessing
    Route::middleware(['cors', 'throttle:10,1'])->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('device_login', [AuthController::class, 'loginMobile']);
        Route::post('device_login_otp', [AuthController::class, 'loginMobileOTP']);
        Route::post('verify_otp', [AuthController::class, 'VerifyMobileOTP']);
    });

    Route::middleware(['auth:api', 'cors'])->group(function () {
        // Creating accounts needs a signed-in user with create_users (it was open to anyone)
        Route::post('signup', [AuthController::class, 'signup']);
        Route::get('user', [AuthController::class, 'user']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

// Mobile app
Route::prefix('v1/mobile')->middleware(['auth:api', 'module_permission'])->group(function () {
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
Route::prefix('v1')->middleware(['auth:api', 'cors', 'module_permission'])->group(function () {

    // Officer
    Route::prefix('officer')->group(function () {
        Route::post('import', [OfficerController::class, 'import']);

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
    Route::apiResource('account/chart_account', COAController::class);

    // Expense
    Route::get('expense/get_field', [ExpenseController::class, 'getField']);
    Route::post('expense/search', [ExpenseController::class, 'index']);
    Route::apiResource('expense', ExpenseController::class);

    // Income
    Route::get('income/get_field', [IncomeController::class, 'getField']);
    Route::post('income/search', [IncomeController::class, 'index']);
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

    // Sidebar menu (tree) and Administration › Menus
    Route::get('menu/tree', [MenuController::class, 'tree']);
    Route::get('menu/manage', [MenuController::class, 'manage']);
    Route::get('menu/preview', [MenuController::class, 'preview']);
    Route::post('menu/reorder', [MenuController::class, 'reorder']);
    Route::post('menu/{id}/active', [MenuController::class, 'active'])->whereNumber('id');
    Route::post('menu/search', [MenuController::class, 'index']);
    Route::apiResource('menu', MenuController::class);

    // Settings
    Route::prefix('setting')->group(function () {

Route::post('account_type_group/search', [AccountTypeGroupController::class, 'index']);
        Route::apiResource('account_type_group', AccountTypeGroupController::class);

        Route::post('account_type/search', [AccountTypeController::class, 'index']);
        Route::apiResource('account_type', AccountTypeController::class);

        Route::post('account_group/search', [AccountGroupController::class, 'index']);
        Route::apiResource('account_group', AccountGroupController::class);

        Route::post('account/get_account_by_group', [AccountController::class, 'getAccountByGroup']);
        Route::post('account/get_sub_account', [AccountController::class, 'getSubAccount']);
        Route::post('account/search', [AccountController::class, 'index']);
        Route::apiResource('account', AccountController::class);

        Route::post('department/import', [DepartmentController::class, 'import']);
        Route::post('department/search', [DepartmentController::class, 'index']);
        Route::apiResource('department', DepartmentController::class);

        Route::post('get_member_by_entity', [EntityMemberController::class, 'getMemberByEntity']);
        Route::post('entity_member/search', [EntityMemberController::class, 'index']);
        Route::apiResource('entity_member', EntityMemberController::class);

        Route::post('entity/get_entity_by_dept', [EntityController::class, 'getOptionByDept']);
        Route::post('entity/import', [EntityController::class, 'import']);
        Route::post('entity/search', [EntityController::class, 'index']);
        Route::apiResource('entity', EntityController::class);

        Route::post('position/search', [PositionController::class, 'index']);
        Route::apiResource('position', PositionController::class);

        Route::post('title/search', [TitleController::class, 'index']);
        Route::apiResource('title', TitleController::class);

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

Route::post('planning/search', [PlanningController::class, 'index']);
        Route::apiResource('planning', PlanningController::class);

Route::post('audit/search', [AuditController::class, 'index']);
        Route::post('audit/clear', [AuditController::class, 'clear']);
        Route::apiResource('audit', AuditController::class);

Route::post('letter_type/search', [LetterTypeController::class, 'index']);
        Route::apiResource('letter_type', LetterTypeController::class);

        Route::post('payment_method/search', [PaymentMethodController::class, 'index']);
        Route::apiResource('payment_method', PaymentMethodController::class);

        Route::post('payment_term/search', [PaymentTermController::class, 'index']);
        Route::apiResource('payment_term', PaymentTermController::class);

        Route::post('payment_type/get_amount_payment_type', [PaymentTypeController::class, 'getAmountByPaymentType']);
        Route::post('payment_type/import', [PaymentTypeController::class, 'import']);
        Route::post('payment_type/search', [PaymentTypeController::class, 'index']);
        Route::apiResource('payment_type', PaymentTypeController::class);
    });

    Route::apiResource('taxable_salary', TaxableSalaryController::class);
    // Current planning year (config_planning_year), used by the budget screens for their year headers
    Route::get('configuration/planning', [ConfigurationController::class, 'planning']);
    Route::apiResource('configuration', ConfigurationController::class)->only(['index', 'show', 'update']);

    // Notification
    Route::get('nofication/unreadnotification', [NotificationController::class, 'getUnreadNotifications']);
    Route::post('notification/read', [NotificationController::class, 'readNotification']);
    Route::post('notification/read_all', [NotificationController::class, 'readAllNotifications']);
    Route::post('notification/search', [NotificationController::class, 'index']);
    Route::post('send_notification', [NotificationController::class, 'sendNotification']);
    Route::apiResource('notification', NotificationController::class);

    // User
    // Dropdown choices by field name, for forms whose page didn't send them
    Route::get('lookup/form/{table}', [LookupController::class, 'form'])->where('table', '[a-z_]+');
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

        // PDF reports: officers, income, expense, officer_incentive, officers_payment, ...
        Route::get('types', 'types');
        Route::post('{type}/data', 'preview')->where('type', '[a-z_]+');
        Route::post('{type}/excel', 'excel')->where('type', '[a-z_]+');
        Route::post('{type}', 'generate')->where('type', '[a-z_]+');
    });

    // Import: check a file before importing it, and an empty template with the right headings
    Route::post('import/preview', [ImportController::class, 'preview']);
    Route::post('import/file', [ImportController::class, 'file']);
    Route::get('import/template', [ImportController::class, 'template']);

    // Print button of list pages: the rows on screen as the standard PDF document
    Route::post('print', [PrintController::class, 'table']);
    Route::post('print/excel', [PrintController::class, 'excel']);

    // File manager
    Route::post('file_manager/search', [FileManagerController::class, 'index']);
    Route::post('file_manager/upload', [FileManagerController::class, 'fileImageUploadService']);
    Route::apiResource('file_manager', FileManagerController::class);
    Route::post('file_upload', [FileManagerController::class, 'fileUploadService']);
    Route::post('file_income_upload', [FileManagerController::class, 'fileIncomeUploadService']);
});
