<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Foreign keys for the relationships the models declare (belongsTo), where the data is clean.
 *
 * Left out on purpose (data or code still uses 0 / broken links, see the data-cleanup notes):
 *   officer_payable.account_id / payment_method_id (0 = not paid yet), users.entity_id (0 = all entities),
 *   entity.parent_id (0 = top level), notifications.notifiable_id (polymorphic),
 *   officer.entity_id (7 officers point to a deleted entity), incentive_to_* (their incentives are missing),
 *   and the mef_* columns that use 0 for "none".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('account', function (Blueprint $table) {
            $table->foreign('status', 'tbl_account_status_foreign')->references('id')->on('mef_status');
        });
        Schema::table('entity', function (Blueprint $table) {
            $table->foreign('department_id', 'tbl_entity_department_id_foreign')->references('id')->on('department');
        });
        Schema::table('entity_member', function (Blueprint $table) {
            $table->foreign('entity_id', 'tbl_entity_member_entity_id_foreign')->references('id')->on('entity');
        });
        Schema::table('expense', function (Blueprint $table) {
            $table->foreign('currency_id', 'tbl_expense_currency_id_foreign')->references('id')->on('currency');
            $table->foreign('entity_id', 'tbl_expense_entity_id_foreign')->references('id')->on('entity');
            $table->foreign('payment_method_id', 'tbl_expense_payment_method_id_foreign')->references('id')->on('payment_method');
            $table->foreign('status', 'tbl_expense_status_foreign')->references('id')->on('mef_status');
        });
        Schema::table('income', function (Blueprint $table) {
            $table->foreign('currency_id', 'tbl_income_currency_id_foreign')->references('id')->on('currency');
            $table->foreign('department_id', 'tbl_income_department_id_foreign')->references('id')->on('department');
            $table->foreign('entity_id', 'tbl_income_entity_id_foreign')->references('id')->on('entity');
            $table->foreign('payment_method_id', 'tbl_income_payment_method_id_foreign')->references('id')->on('payment_method');
            $table->foreign('status', 'tbl_income_status_foreign')->references('id')->on('mef_status');
        });
        Schema::table('notifications', function (Blueprint $table) {
            $table->foreign('status', 'tbl_notifications_status_foreign')->references('id')->on('mef_status');
        });
        Schema::table('officer', function (Blueprint $table) {
            $table->foreign('department_id', 'tbl_officer_department_id_foreign')->references('id')->on('department');
            $table->foreign('status', 'tbl_officer_status_foreign')->references('id')->on('mef_status');
        });
        Schema::table('officer_document', function (Blueprint $table) {
            $table->foreign('officer_id', 'tbl_officer_document_officer_id_foreign')->references('id')->on('officer');
            $table->foreign('status', 'tbl_officer_document_status_foreign')->references('id')->on('mef_status');
        });
        Schema::table('officer_incentive', function (Blueprint $table) {
            $table->foreign('currency_id', 'tbl_officer_incentive_currency_id_foreign')->references('id')->on('currency');
            $table->foreign('officer_id', 'tbl_officer_incentive_officer_id_foreign')->references('id')->on('officer');
            $table->foreign('payment_method_id', 'tbl_officer_incentive_payment_method_id_foreign')->references('id')->on('payment_method');
            $table->foreign('status', 'tbl_officer_incentive_status_foreign')->references('id')->on('mef_status');
        });
        Schema::table('officer_payable', function (Blueprint $table) {
            $table->foreign('currency_id', 'tbl_officer_payable_currency_id_foreign')->references('id')->on('currency');
            $table->foreign('officer_id', 'tbl_officer_payable_officer_id_foreign')->references('id')->on('officer');
            $table->foreign('status', 'tbl_officer_payable_status_foreign')->references('id')->on('mef_status');
        });
        Schema::table('officer_payment', function (Blueprint $table) {
            $table->foreign('currency_id', 'tbl_officer_payment_currency_id_foreign')->references('id')->on('currency');
            $table->foreign('officer_id', 'tbl_officer_payment_officer_id_foreign')->references('id')->on('officer');
            $table->foreign('payment_method_id', 'tbl_officer_payment_payment_method_id_foreign')->references('id')->on('payment_method');
            $table->foreign('status', 'tbl_officer_payment_status_foreign')->references('id')->on('mef_status');
        });
        Schema::table('officer_payment_recurring', function (Blueprint $table) {
            $table->foreign('currency_id', 'tbl_officer_payment_recurring_currency_id_foreign')->references('id')->on('currency');
            $table->foreign('officer_id', 'tbl_officer_payment_recurring_officer_id_foreign')->references('id')->on('officer');
            $table->foreign('status', 'tbl_officer_payment_recurring_status_foreign')->references('id')->on('mef_status');
        });
        Schema::table('officer_request', function (Blueprint $table) {
            $table->foreign('currency_id', 'tbl_officer_request_currency_id_foreign')->references('id')->on('currency');
            $table->foreign('officer_id', 'tbl_officer_request_officer_id_foreign')->references('id')->on('officer');
            $table->foreign('payment_method_id', 'tbl_officer_request_payment_method_id_foreign')->references('id')->on('payment_method');
            $table->foreign('status', 'tbl_officer_request_status_foreign')->references('id')->on('mef_status');
        });
        Schema::table('payment_type', function (Blueprint $table) {
            $table->foreign('currency_id', 'tbl_payment_type_currency_id_foreign')->references('id')->on('currency');
            $table->foreign('status', 'tbl_payment_type_status_foreign')->references('id')->on('mef_status');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('officer_id', 'tbl_users_officer_id_foreign')->references('id')->on('officer');
            $table->foreign('status', 'tbl_users_status_foreign')->references('id')->on('mef_status');
        });
    }

    public function down(): void
    {
        Schema::table('account', function (Blueprint $table) {
            $table->dropForeign('tbl_account_status_foreign');
        });
        Schema::table('entity', function (Blueprint $table) {
            $table->dropForeign('tbl_entity_department_id_foreign');
        });
        Schema::table('entity_member', function (Blueprint $table) {
            $table->dropForeign('tbl_entity_member_entity_id_foreign');
        });
        Schema::table('expense', function (Blueprint $table) {
            $table->dropForeign('tbl_expense_currency_id_foreign');
            $table->dropForeign('tbl_expense_entity_id_foreign');
            $table->dropForeign('tbl_expense_payment_method_id_foreign');
            $table->dropForeign('tbl_expense_status_foreign');
        });
        Schema::table('income', function (Blueprint $table) {
            $table->dropForeign('tbl_income_currency_id_foreign');
            $table->dropForeign('tbl_income_department_id_foreign');
            $table->dropForeign('tbl_income_entity_id_foreign');
            $table->dropForeign('tbl_income_payment_method_id_foreign');
            $table->dropForeign('tbl_income_status_foreign');
        });
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign('tbl_notifications_status_foreign');
        });
        Schema::table('officer', function (Blueprint $table) {
            $table->dropForeign('tbl_officer_department_id_foreign');
            $table->dropForeign('tbl_officer_status_foreign');
        });
        Schema::table('officer_document', function (Blueprint $table) {
            $table->dropForeign('tbl_officer_document_officer_id_foreign');
            $table->dropForeign('tbl_officer_document_status_foreign');
        });
        Schema::table('officer_incentive', function (Blueprint $table) {
            $table->dropForeign('tbl_officer_incentive_currency_id_foreign');
            $table->dropForeign('tbl_officer_incentive_officer_id_foreign');
            $table->dropForeign('tbl_officer_incentive_payment_method_id_foreign');
            $table->dropForeign('tbl_officer_incentive_status_foreign');
        });
        Schema::table('officer_payable', function (Blueprint $table) {
            $table->dropForeign('tbl_officer_payable_currency_id_foreign');
            $table->dropForeign('tbl_officer_payable_officer_id_foreign');
            $table->dropForeign('tbl_officer_payable_status_foreign');
        });
        Schema::table('officer_payment', function (Blueprint $table) {
            $table->dropForeign('tbl_officer_payment_currency_id_foreign');
            $table->dropForeign('tbl_officer_payment_officer_id_foreign');
            $table->dropForeign('tbl_officer_payment_payment_method_id_foreign');
            $table->dropForeign('tbl_officer_payment_status_foreign');
        });
        Schema::table('officer_payment_recurring', function (Blueprint $table) {
            $table->dropForeign('tbl_officer_payment_recurring_currency_id_foreign');
            $table->dropForeign('tbl_officer_payment_recurring_officer_id_foreign');
            $table->dropForeign('tbl_officer_payment_recurring_status_foreign');
        });
        Schema::table('officer_request', function (Blueprint $table) {
            $table->dropForeign('tbl_officer_request_currency_id_foreign');
            $table->dropForeign('tbl_officer_request_officer_id_foreign');
            $table->dropForeign('tbl_officer_request_payment_method_id_foreign');
            $table->dropForeign('tbl_officer_request_status_foreign');
        });
        Schema::table('payment_type', function (Blueprint $table) {
            $table->dropForeign('tbl_payment_type_currency_id_foreign');
            $table->dropForeign('tbl_payment_type_status_foreign');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('tbl_users_officer_id_foreign');
            $table->dropForeign('tbl_users_status_foreign');
        });
    }
};
