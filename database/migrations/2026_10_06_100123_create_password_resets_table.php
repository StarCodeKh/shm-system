<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('email', 255);
            $table->string('token', 255);
            $table->timestamps();  // added: standard timestamps
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_resets');
    }
};
