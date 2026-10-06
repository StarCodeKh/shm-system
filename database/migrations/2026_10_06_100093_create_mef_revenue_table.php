<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_revenue', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('planning_id')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->unsignedBigInteger('sub_account_id')->nullable();
            $table->decimal('imple_rev_budget', 20, 4)->nullable();
            $table->integer('rule_basic_rev_plan')->nullable();
            $table->integer('rule_qty_rev_plan')->nullable();
            $table->decimal('rule_budget_credit', 20, 4)->nullable();
            $table->decimal('imple_budget_semester_1', 20, 4)->nullable();
            $table->decimal('imple_budget_semester_2', 20, 4)->nullable();
            $table->decimal('annual_estimate', 20, 4)->nullable();
            $table->integer('percentage_rule')->nullable();
            $table->decimal('basic_rev_plan', 20, 4)->nullable();
            $table->integer('qty_rev_plan')->nullable();
            $table->decimal('total_rev_plan', 20, 4)->nullable();
            $table->decimal('percentage_rev_plan', 20, 4)->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('planning_id');
            $table->index('entity_id');
            $table->index('account_id');
            $table->index('sub_account_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_revenue');
    }
};
