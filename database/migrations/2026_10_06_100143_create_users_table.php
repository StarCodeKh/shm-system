<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('officer_id')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('fullname');
            $table->string('latin');
            $table->string('username', 120);
            $table->string('email', 120);
            $table->string('password', 255)->nullable();
            $table->string('title', 120)->nullable();
            $table->string('department', 120)->nullable();
            $table->string('position', 120)->nullable();
            $table->string('telephone_1', 120)->nullable();
            $table->string('telephone_2', 120)->nullable();
            $table->longText('avatar')->nullable();
            $table->string('gender', 50)->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->date('dob')->nullable();
            $table->tinyInteger('receive_daily_notifications')->nullable();
            $table->tinyInteger('receive_all_notification')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->longText('device_token')->nullable();
            $table->timestamps();
            $table->unique('username');
            $table->unique('email');
            $table->index('officer_id');
            $table->index('entity_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
