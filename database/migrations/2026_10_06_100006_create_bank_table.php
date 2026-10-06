<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name_en', 255);
            $table->string('name_kh', 255)->nullable();
            $table->string('code', 45)->nullable();
            $table->integer('order_level')->nullable();
            $table->text('address')->nullable();
            $table->string('remark', 120)->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank');
    }
};
