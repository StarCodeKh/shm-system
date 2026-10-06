<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_activity_expense', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('activity_id')->nullable();
            $table->unsignedBigInteger('account_group_id')->nullable();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->timestamps();  // added: standard timestamps
            $table->index('activity_id');
            $table->index('account_group_id');
            $table->index('account_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_activity_expense');
    }
};
