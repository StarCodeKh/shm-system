<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permission_group', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name', 255)->nullable();
            $table->integer('order_level')->nullable();
            $table->timestamps();  // added: standard timestamps
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_group');
    }
};
