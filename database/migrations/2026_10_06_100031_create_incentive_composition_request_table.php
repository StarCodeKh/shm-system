<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incentive_composition_request', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('request_title', 255);
            $table->string('reference', 120)->nullable();
            $table->string('request_from', 255);
            $table->longText('description')->nullable();
            $table->string('letter_type', 120)->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->string('entity', 255)->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->longText('attach_file')->nullable();
            $table->dateTime('request_date')->nullable();
            $table->dateTime('effective_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->boolean('is_approved')->default(0);
            $table->timestamps();
            $table->index('status');
            $table->index('entity_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incentive_composition_request');
    }
};
