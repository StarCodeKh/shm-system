<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('name', 255);
            $table->string('guard_name', 255)->nullable();
            $table->string('remark', 255)->nullable();
            $table->integer('order_level')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->tinyInteger('is_active');
            $table->tinyInteger('is_delete')->nullable();
            $table->tinyInteger('is_default')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('entity_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
