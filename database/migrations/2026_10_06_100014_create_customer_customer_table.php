<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_customer', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->uuid('id')->primary();
            $table->string('first_name', 15);
            $table->string('last_name', 25);
            $table->string('username', 50);
            $table->string('gender', 10);
            $table->integer('cus_code');
            $table->date('birth_date')->nullable();
            $table->string('operating_unit', 10);
            $table->date('register_date')->nullable();
            $table->string('phone', 20);
            $table->string('document', 100);
            $table->string('business_unit', 3);
            $table->string('img', 100);
            $table->tinyInteger('is_active');
            $table->tinyInteger('is_dead');
            $table->tinyInteger('is_retire');
            $table->tinyInteger('is_unpaid');
            $table->date('created_date')->nullable();
            $table->date('modified_on')->nullable();
            $table->timestamps();  // added: standard timestamps
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_customer');
    }
};
