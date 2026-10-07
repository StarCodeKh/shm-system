<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currency', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name_en', 120)->nullable();
            $table->string('name_kh', 120)->nullable();
            $table->integer('rate')->nullable();
            $table->string('abbreviation', 50)->nullable();
            $table->string('symbol_left', 50)->nullable();
            $table->string('symbol_right', 50)->nullable();
            $table->text('remark')->nullable();
            $table->boolean('is_default')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->integer('order_level')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currency');
    }
};
