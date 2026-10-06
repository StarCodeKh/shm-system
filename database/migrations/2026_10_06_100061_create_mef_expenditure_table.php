<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_expenditure', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('planning_id')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->unsignedBigInteger('account_group_id')->nullable();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->unsignedBigInteger('sub_account_id')->nullable();
            $table->decimal('imple_income_budget', 20, 4)->nullable();
            $table->decimal('annual_credit', 20, 4)->nullable();
            $table->decimal('review_credit', 20, 4)->nullable();
            $table->decimal('new_credit', 20, 4)->nullable();
            $table->decimal('imple_budget_semester_1', 20, 4)->nullable();
            $table->decimal('imple_estimate_budget_semester_2', 20, 4)->nullable();
            $table->decimal('total_estimate_budget', 20, 4)->nullable();
            $table->integer('percent_credit_budget_rule')->nullable();
            $table->decimal('new_credit_budget', 20, 4)->nullable();
            $table->decimal('total_budget_plan', 20, 4)->nullable();
            $table->integer('percentage_credit_budget_plan')->nullable();
            $table->integer('percentage_new_credit_rule_plan')->nullable();
            $table->decimal('total_compare_year', 20, 4)->nullable();
            $table->integer('percentage_compare_credit_rule')->nullable();
            $table->decimal('non_regular_expense', 20, 4)->nullable();
            $table->longText('description_inc_dec')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('planning_id');
            $table->index('entity_id');
            $table->index('account_group_id');
            $table->index('account_id');
            $table->index('sub_account_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_expenditure');
    }
};
