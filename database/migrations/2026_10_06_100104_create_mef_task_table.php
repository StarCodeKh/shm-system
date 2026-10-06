<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_task', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('planning_id')->nullable();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->unsignedBigInteger('sub_program_id')->nullable();
            $table->unsignedBigInteger('cluster_activity_id')->nullable();
            $table->unsignedBigInteger('activity_id')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->unsignedBigInteger('entity_member_id')->nullable();
            $table->text('code')->nullable();
            $table->longText('name_en');
            $table->longText('name_kh');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('order_level')->nullable();
            $table->integer('assign_to')->nullable();
            $table->longText('remark')->nullable();
            $table->longText('attachment')->nullable();
            $table->tinyInteger('is_routine')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('planning_id');
            $table->index('program_id');
            $table->index('sub_program_id');
            $table->index('cluster_activity_id');
            $table->index('activity_id');
            $table->index('entity_id');
            $table->index('entity_member_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_task');
    }
};
