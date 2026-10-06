<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_ceiling_rule', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('planning_id')->nullable();
            $table->text('name_en')->nullable();
            $table->text('name_kh')->nullable();
            $table->dateTime('issue_date')->nullable();
            $table->string('authorize_by', 255)->nullable();
            $table->string('approved_by', 255)->nullable();
            $table->string('status', 255)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('planning_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_ceiling_rule');
    }
};
