<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('location', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->longText('name_en')->nullable();
            $table->longText('name_kh')->nullable();
            $table->longText('remark')->nullable();
            $table->integer('order_level')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location');
    }
};
