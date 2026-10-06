<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('circular', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('achivement', 120);
            $table->integer('incentive')->default(0);
            $table->string('shape', 50)->nullable();
            $table->string('color', 10)->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->integer('order_level')->default(0);
            $table->string('remark', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('circular');
    }
};
