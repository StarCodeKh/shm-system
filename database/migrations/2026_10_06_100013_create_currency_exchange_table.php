<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currency_exchange', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->integer('from_currency');
            $table->integer('to_currency');
            $table->decimal('exchange_rate', 20, 4);
            $table->dateTime('issue_date')->nullable();
            $table->longText('remark')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currency_exchange');
    }
};
