<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_pip_grading', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('planing_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('pip_no', 255)->nullable();
            $table->decimal('pip_new_costing', 20, 4)->nullable();
            $table->decimal('pip_progress_costing', 20, 4)->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('pip_classification', 50)->nullable();
            $table->longText('remark')->nullable();
            $table->unsignedBigInteger('currency_exchange_id')->nullable();
            $table->decimal('exchange_rate', 20, 4)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('planing_id');
            $table->index('project_id');
            $table->index('entity_id');
            $table->index('currency_exchange_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_pip_grading');
    }
};
