<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_account', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('subscribe_type', 50);
            $table->string('master_account_name', 50);
            $table->string('primary_email', 50);
            $table->string('primary_contact', 50);
            $table->string('pin_code', 20)->nullable();
            $table->string('business_type', 50);
            $table->string('website', 50)->nullable();
            $table->text('address')->nullable();
            $table->dateTime('expired_account')->nullable();
            $table->string('reference', 50)->nullable();
            $table->string('source', 50)->nullable();
            $table->dateTime('join_date')->nullable();
            $table->timestamps();
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_account');
    }
};
