<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_activity', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('code', 255);
            $table->string('sub_code', 255)->nullable();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->unsignedBigInteger('sub_program_id')->nullable();
            $table->unsignedBigInteger('cluster_activity_id')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->unsignedBigInteger('entity_member_id')->nullable();
            $table->longText('name_en')->nullable();
            $table->longText('name_kh')->nullable();
            $table->longText('remark')->nullable();
            $table->tinyInteger('is_active')->nullable();
            $table->integer('order_level')->nullable();
            $table->tinyInteger('is_routine')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('program_id');
            $table->index('sub_program_id');
            $table->index('cluster_activity_id');
            $table->index('entity_id');
            $table->index('entity_member_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_activity');
    }
};
