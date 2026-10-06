<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('officer_payment', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->unsignedBigInteger('officer_id')->nullable();
            $table->unsignedBigInteger('ref_payable_id')->nullable();
            $table->string('receipt_no', 120);
            $table->decimal('amount', 20, 4);
            $table->dateTime('payment_date')->nullable();
            $table->dateTime('issue_date')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->decimal('exchange_rate', 20, 4)->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->text('note')->nullable();
            $table->longText('attach')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('account_id');
            $table->index('officer_id');
            $table->index('ref_payable_id');
            $table->index('currency_id');
            $table->index('payment_method_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('officer_payment');
    }
};
