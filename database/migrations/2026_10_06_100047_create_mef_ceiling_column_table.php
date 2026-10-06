<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_ceiling_column', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('planning_id')->nullable();
            $table->longText('name_en')->nullable();
            $table->longText('name_kh')->nullable();
            $table->integer('order_level')->nullable();
            $table->timestamps();  // added: standard timestamps
            $table->index('planning_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_ceiling_column');
    }
};
