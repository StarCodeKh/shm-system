<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_expenditure_imple_budget_detail', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('exp_imple_budget_id')->nullable();
            $table->decimal('finance_rule', 20, 4)->nullable();
            $table->decimal('actual_imple_budget', 20, 4)->nullable();
            $table->integer('percentage_imple_budget')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->timestamps();
            $table->index('exp_imple_budget_id', 'mef_expenditure_imple_bu_exp_imple_budget_id_efc660_ind');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_expenditure_imple_budget_detail');
    }
};
