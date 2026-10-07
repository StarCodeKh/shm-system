<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('title_kh', 500);
            $table->string('title_en', 500);
            $table->bigInteger('account_code');
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->dateTime('issue_date')->nullable();
            $table->string('category', 120)->nullable();
            $table->decimal('amount', 20, 4);
            $table->string('reference', 120)->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->integer('exchange_rate')->default(0);
            $table->unsignedBigInteger('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('payment_method_id');
            $table->index('entity_id');
            $table->index('currency_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense');
    }
};
