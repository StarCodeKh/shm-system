<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_member', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->unsignedBigInteger('duty_id')->nullable();
            $table->string('position_id', 120)->nullable();
            $table->longText('photo')->nullable();
            $table->string('identity_no', 50)->nullable();
            $table->string('title', 50)->nullable();
            $table->text('fullname')->nullable();
            $table->text('first_name')->nullable();
            $table->text('last_name')->nullable();
            $table->text('latin')->nullable();
            $table->string('gender', 45)->nullable();
            $table->string('address', 45)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('department', 120)->nullable();
            $table->string('telephone_1', 45)->nullable();
            $table->string('telephone_2', 45)->nullable();
            $table->tinyInteger('is_active')->nullable();
            $table->integer('order_level')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('user_id');
            $table->index('entity_id');
            $table->index('duty_id');
            $table->index('status');
            $table->index('position_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_member');
    }
};
