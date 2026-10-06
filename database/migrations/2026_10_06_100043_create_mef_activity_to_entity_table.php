<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_activity_to_entity', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('cluster_act_to_entity_id')->nullable();
            $table->unsignedBigInteger('activity_id')->nullable();
            $table->unsignedBigInteger('planning_id')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->unsignedBigInteger('entity_member_id')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('cluster_act_to_entity_id');
            $table->index('activity_id');
            $table->index('planning_id');
            $table->index('entity_id');
            $table->index('entity_member_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_activity_to_entity');
    }
};
