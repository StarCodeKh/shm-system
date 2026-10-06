<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audits', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('user_type', 255)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('event', 255);
            $table->string('auditable_type', 255);
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->text('url')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 1023)->nullable();
            $table->string('tags', 255)->nullable();
            $table->timestamps();
            $table->index('user_id');
            $table->index('auditable_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
