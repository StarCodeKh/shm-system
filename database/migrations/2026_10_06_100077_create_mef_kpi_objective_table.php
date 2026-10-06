<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_kpi_objective', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('planning_id')->nullable();
            $table->unsignedBigInteger('obj_id')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('code', 50);
            $table->longText('kpi_name_en')->nullable();
            $table->longText('kpi_name_kh')->nullable();
            $table->integer('order_level')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('planning_id');
            $table->index('obj_id');
            $table->index('entity_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_kpi_objective');
    }
};
