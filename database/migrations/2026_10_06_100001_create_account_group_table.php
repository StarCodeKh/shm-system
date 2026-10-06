<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_group', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('group', 120);
            $table->string('code', 50);
            $table->longText('name_en')->nullable();
            $table->longText('name_kh')->nullable();
            $table->longText('description')->nullable();
            $table->integer('order_level')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_group');
    }
};
