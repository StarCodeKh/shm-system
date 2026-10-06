<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_credit_movement', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('planning_id')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->integer('exp_type')->nullable();
            $table->string('doc_no', 50)->nullable();
            $table->date('date_adv')->nullable();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->unsignedBigInteger('sub_account_id')->nullable();
            $table->unsignedBigInteger('clus_id')->nullable();
            $table->decimal('amount_adv', 20, 4)->nullable();
            $table->decimal('remain_balance', 20, 4)->nullable();
            $table->decimal('increase', 20, 4)->nullable();
            $table->decimal('decrease', 20, 4)->nullable();
            $table->integer('check')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('planning_id');
            $table->index('entity_id');
            $table->index('account_id');
            $table->index('sub_account_id');
            $table->index('clus_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_credit_movement');
    }
};
