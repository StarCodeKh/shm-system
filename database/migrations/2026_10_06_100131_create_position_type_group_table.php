<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('position_type_group', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->longText('name_en');
            $table->longText('name_kh');
            $table->integer('order_level')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();  // added: standard timestamps
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('position_type_group');
    }
};
