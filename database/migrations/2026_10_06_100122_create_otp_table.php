<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->longText('device_token');
            $table->string('telephone', 120);
            $table->string('otp_code', 45);
            $table->tinyInteger('is_verify')->default(0);
            $table->dateTime('expired_date')->nullable();
            $table->text('remark')->nullable();
            $table->timestamps();  // added: standard timestamps
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp');
    }
};
