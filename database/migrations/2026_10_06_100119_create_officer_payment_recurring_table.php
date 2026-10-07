<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('officer_payment_recurring', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('officer_id')->nullable();
            $table->string('payment_term', 255);
            $table->decimal('recurring_amount', 20, 4);
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->integer('executed_num');
            $table->dateTime('schedule_start_date')->nullable();
            $table->dateTime('schedule_end_date')->nullable();
            $table->text('remark')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->boolean('is_default');
            $table->timestamps();
            $table->index('officer_id');
            $table->index('currency_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('officer_payment_recurring');
    }
};
