<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_evaluate_kpi_subprogram', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('kpi_subprogram_id')->nullable();
            $table->decimal('progress', 3, 0)->nullable();
            $table->longText('achievement')->nullable();
            $table->longText('challenge')->nullable();
            $table->longText('suggestion')->nullable();
            $table->longText('remark')->nullable();
            $table->dateTime('issue_date')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('kpi_subprogram_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_evaluate_kpi_subprogram');
    }
};
