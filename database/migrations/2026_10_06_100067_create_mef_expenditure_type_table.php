<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_expenditure_type', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('expdt_group_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->longText('name_en')->nullable();
            $table->longText('name_kh')->nullable();
            $table->longText('remark')->nullable();
            $table->integer('order_level')->nullable();
            $table->tinyInteger('is_active')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();  // added: standard timestamps
            $table->index('expdt_group_id');
            $table->index('parent_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_expenditure_type');
    }
};
