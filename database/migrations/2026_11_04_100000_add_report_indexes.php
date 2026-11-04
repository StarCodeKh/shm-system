<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Indexes for the dashboard, reports and list pages once the old SHM data is in (about 8,000 members,
 * 70,000 payables, 60,000 payments).
 * - (officer_id, status, currency_id, amount): per-member paid / unpaid totals read from the index alone
 * - dates: every period filter (dashboard, reports) and the newest-first lists
 * - identity_member_no: member search and db:import-shm matching
 */
return new class extends Migration
{
    private array $indexes = [
        'officer_payable' => [['officer_id', 'status', 'currency_id', 'amount'], ['due_date'], ['status', 'payment_date']],
        'officer_payment' => [['officer_id', 'status', 'currency_id', 'amount'], ['issue_date'], ['currency_id', 'issue_date']],
        'officer_incentive' => [['issue_date']],
        'income' => [['issue_date']],
        'expense' => [['issue_date']],
        'officer' => [['register_date'], ['identity_member_no']],
    ];

    public function up(): void
    {
        foreach ($this->indexes as $table => $indexes) {
            Schema::table($table, function (Blueprint $blueprint) use ($indexes) {
                foreach ($indexes as $columns) {
                    $blueprint->index($columns);
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->indexes as $table => $indexes) {
            Schema::table($table, function (Blueprint $blueprint) use ($indexes) {
                foreach ($indexes as $columns) {
                    $blueprint->dropIndex($columns);
                }
            });
        }
    }
};
