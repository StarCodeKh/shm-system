<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('config', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('code', 64);
            $table->string('key', 64);
            $table->longText('value')->nullable();
            $table->timestamps();  // added: standard timestamps
            $table->unique(['code', 'key']);
            $table->index('entity_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('config');
    }
};
