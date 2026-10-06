<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('officer_document', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('officer_id')->nullable();
            $table->longText('document_name');
            $table->longText('attach');
            $table->unsignedBigInteger('status')->nullable();
            $table->timestamps();
            $table->index('officer_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('officer_document');
    }
};
