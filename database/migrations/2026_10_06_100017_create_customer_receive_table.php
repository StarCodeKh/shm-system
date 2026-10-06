<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_receive', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->uuid('id')->primary();
            $table->integer('receipt_no');
            $table->string('receipt_id', 10);
            $table->string('customer_id', 50);
            $table->decimal('price', 10, 2);
            $table->integer('year');
            $table->string('create_by', 50);
            $table->string('receive_by', 50);
            $table->dateTime('receive_date')->nullable();
            $table->tinyInteger('is_cancel');
            $table->tinyInteger('is_register');
            $table->string('bu', 5)->nullable();
            $table->string('ou', 10)->nullable();
            $table->integer('month');
            $table->integer('receipt_order');
            $table->dateTime('entry_date')->nullable();
            $table->string('month_remark', 10);
            $table->timestamps();  // added: standard timestamps
            $table->index('receipt_id');
            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_receive');
    }
};
