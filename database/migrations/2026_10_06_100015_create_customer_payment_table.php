<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_payment', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->uuid('id')->primary();
            $table->integer('payment_no');
            $table->string('payment_id', 10);
            $table->string('customer_id', 50);
            $table->string('document', 100);
            $table->string('create_by', 50);
            $table->string('record_by', 50);
            $table->dateTime('payment_date')->nullable();
            $table->string('payment_type', 50);
            $table->string('description', 500);
            $table->decimal('amount', 15, 0);
            $table->dateTime('entry_date')->nullable();
            $table->tinyInteger('is_active');
            $table->string('bu', 5)->nullable();
            $table->string('ou', 10)->nullable();
            $table->timestamps();  // added: standard timestamps
            $table->index('payment_id');
            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_payment');
    }
};
