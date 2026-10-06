<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_kpi_subprogram', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('planning_id')->nullable();
            $table->unsignedBigInteger('sub_program_id')->nullable();
            $table->unsignedBigInteger('kpi_program_id')->nullable();
            $table->unsignedBigInteger('subprog_to_entity_id')->nullable();
            $table->string('code', 50)->nullable();
            $table->longText('kpi_name_en');
            $table->longText('kpi_name_kh');
            $table->integer('order_level')->nullable();
            $table->longText('attachment')->nullable();
            $table->year('year')->nullable();
            $table->integer('progress')->nullable();
            $table->longText('acheivement')->nullable();
            $table->unsignedBigInteger('reference_kpi_id')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->longText('remark')->nullable();
            $table->tinyInteger('is_active')->nullable()->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('planning_id');
            $table->index('sub_program_id');
            $table->index('kpi_program_id');
            $table->index('subprog_to_entity_id');
            $table->index('reference_kpi_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_kpi_subprogram');
    }
};
