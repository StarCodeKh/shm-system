<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_budget_type', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->longText('title_en')->nullable();
            $table->longText('title_kh')->nullable();
            $table->longText('remark')->nullable();
            $table->integer('order_level')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();  // added: standard timestamps
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_budget_type');
    }
};
