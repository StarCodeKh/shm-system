<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('officer_request', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('officer_id')->nullable();
            $table->string('code', 100)->nullable();
            $table->string('request_title', 255);
            $table->decimal('amount', 20, 4);
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->dateTime('request_date')->nullable();
            $table->dateTime('issue_date')->nullable();
            $table->string('bank', 50)->nullable();
            $table->string('bank_account_no', 120)->nullable();
            $table->string('bank_code', 120)->nullable();
            $table->text('reference')->nullable();
            $table->decimal('tax', 20, 4)->nullable();
            $table->decimal('amount_after_tax', 20, 4)->nullable();
            $table->integer('order_level')->nullable();
            $table->longText('remark')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('officer_id');
            $table->index('currency_id');
            $table->index('payment_method_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('officer_request');
    }
};
