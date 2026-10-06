<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_staff_salary_to_position', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('planning_id')->nullable();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->unsignedBigInteger('sub_account_id')->nullable();
            $table->unsignedBigInteger('position_id')->nullable();
            $table->unsignedBigInteger('position_type_group_id')->nullable();
            $table->unsignedBigInteger('position_type_id')->nullable();
            $table->decimal('position_rule', 20, 4)->nullable();
            $table->decimal('new_credit', 20, 4)->nullable();
            $table->decimal('imple_begin_6_month', 20, 4)->nullable();
            $table->decimal('estimate_last_6_month', 20, 4)->nullable();
            $table->decimal('annual_estimate_year', 20, 4)->nullable();
            $table->integer('percentage_credit_law')->nullable();
            $table->integer('number_framework')->nullable();
            $table->decimal('new_framwork', 20, 4)->nullable();
            $table->decimal('terminate_framework', 20, 4)->nullable();
            $table->decimal('annual_new_framework', 20, 4)->nullable();
            $table->decimal('annual_terminate_framework', 20, 4)->nullable();
            $table->decimal('basic_include_framework', 20, 4)->nullable();
            $table->integer('number_index')->nullable();
            $table->decimal('cost_unit_index', 20, 4)->nullable();
            $table->decimal('cost_index_plan', 20, 4)->nullable();
            $table->decimal('annual_functional_amount', 20, 4)->nullable();
            $table->decimal('position_amount_plan', 20, 4)->nullable();
            $table->decimal('annual_salary_plan', 20, 4)->nullable();
            $table->decimal('annual_percentage_rule', 20, 4)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('planning_id');
            $table->index('account_id');
            $table->index('sub_account_id');
            $table->index('position_id');
            $table->index('position_type_group_id');
            $table->index('position_type_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_staff_salary_to_position');
    }
};
