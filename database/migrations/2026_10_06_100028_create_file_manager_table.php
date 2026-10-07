<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('file_manager', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->longText('image');
            $table->string('file_name');
            $table->text('path');
            $table->string('type', 50);
            $table->longText('size')->nullable();
            $table->longText('extention')->nullable();
            $table->longText('caption')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('file_manager');
    }
};
