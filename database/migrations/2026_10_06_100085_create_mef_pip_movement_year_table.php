<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_pip_movement_year', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('planing_id')->nullable();
            $table->unsignedBigInteger('pip_data_column_id')->nullable();
            $table->year('year');
            $table->integer('order_level')->nullable();
            $table->timestamps();  // added: standard timestamps
            $table->index('planing_id');
            $table->index('pip_data_column_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_pip_movement_year');
    }
};
