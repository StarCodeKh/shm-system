<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_pip_data_column', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->longText('name_en')->nullable();
            $table->longText('name_kh')->nullable();
            $table->longText('is_active')->nullable();
            $table->integer('order_level')->nullable();
            $table->timestamps();  // added: standard timestamps
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_pip_data_column');
    }
};
