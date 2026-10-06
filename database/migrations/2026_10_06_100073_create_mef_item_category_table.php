<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_item_category', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->longText('name_en')->nullable();
            $table->longText('name_kh')->nullable();
            $table->integer('order_level')->nullable();
            $table->longText('remark')->nullable();
            $table->tinyInteger('is_active')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_item_category');
    }
};
