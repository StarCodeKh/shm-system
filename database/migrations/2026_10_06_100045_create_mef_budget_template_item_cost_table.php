<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_budget_template_item_cost', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('budget_template_id')->nullable();
            $table->unsignedBigInteger('account_group_id')->nullable();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->unsignedBigInteger('sub_account_id')->nullable();
            $table->string('code', 255)->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->longText('name_en')->nullable();
            $table->longText('name_kh')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->decimal('cost', 20, 4)->nullable();
            $table->integer('qty')->nullable();
            $table->decimal('unit_price', 20, 4)->nullable();
            $table->decimal('total_amount', 20, 4)->nullable();
            $table->integer('time_annual')->nullable();
            $table->decimal('total_annual_amount', 20, 4)->nullable();
            $table->integer('month')->nullable();
            $table->string('is_reg_exp', 255)->nullable();
            $table->longText('expense_type')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->string('currency', 255)->nullable();
            $table->decimal('exchange_rate', 20, 4)->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->tinyInteger('is_active')->nullable();
            $table->longText('remark')->nullable();
            $table->integer('order_level')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('budget_template_id');
            $table->index('account_group_id');
            $table->index('account_id');
            $table->index('sub_account_id');
            $table->index('item_id');
            $table->index('unit_id');
            $table->index('currency_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_budget_template_item_cost');
    }
};
