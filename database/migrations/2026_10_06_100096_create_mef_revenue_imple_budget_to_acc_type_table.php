<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_revenue_imple_budget_to_acc_type', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('planning_id')->nullable();
            $table->unsignedBigInteger('rev_imple_budget_id')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->unsignedBigInteger('account_type_group_id')->nullable();
            $table->unsignedBigInteger('account_type_id')->nullable();
            $table->unsignedBigInteger('account_group_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->timestamps();  // added: standard timestamps
            $table->index('planning_id');
            $table->index('rev_imple_budget_id', 'mef_revenue_imple_budget_rev_imple_budget_id_e93d29_ind');
            $table->index('entity_id');
            $table->index('account_type_group_id', 'mef_revenue_imple_budget_account_type_group_id_1ca381_ind');
            $table->index('account_type_id');
            $table->index('account_group_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_revenue_imple_budget_to_acc_type');
    }
};
