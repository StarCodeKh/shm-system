<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_income_group', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->longText('title_en');
            $table->longText('title_kh');
            $table->integer('order_level')->nullable();
            $table->tinyInteger('is_active')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();  // added: standard timestamps
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_income_group');
    }
};
