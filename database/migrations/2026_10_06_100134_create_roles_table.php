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
            $table->string('name');
            $table->string('guard_name');
            // Which records the role may see (App\Services\DataScope): all | entity | own | member
            $table->string('data_scope', 20)->default('all');
            $table->string('remark', 255)->nullable();
            $table->integer('order_level')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->boolean('is_active');
            $table->boolean('is_delete')->nullable();
            $table->boolean('is_default')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
            $table->index('entity_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
