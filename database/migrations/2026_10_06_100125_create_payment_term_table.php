<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_term', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('type', 100);
            $table->string('name_kh');
            $table->string('name_en');
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->integer('execute_number');
            $table->decimal('recurring_amount', 20, 4);
            $table->integer('order_level');
            $table->longText('remark')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->boolean('is_default')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('currency_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_term');
    }
};
