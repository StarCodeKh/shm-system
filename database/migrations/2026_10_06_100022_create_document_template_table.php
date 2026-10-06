<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_template', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('document_type_id')->nullable();
            $table->integer('document_name');
            $table->integer('description')->nullable();
            $table->unsignedBigInteger('status')->nullable()->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('document_type_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_template');
    }
};
