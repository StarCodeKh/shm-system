<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_status', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('group', 50);
            $table->text('title_en')->nullable();
            $table->text('title_kh')->nullable();
            $table->text('criteria')->nullable();
            $table->text('remark')->nullable();
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
        Schema::dropIfExists('mef_status');
    }
};
