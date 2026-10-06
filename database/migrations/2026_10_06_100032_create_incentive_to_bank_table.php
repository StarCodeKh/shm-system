<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incentive_to_bank', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('incentive_id')->nullable();
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->text('remark')->nullable();
            $table->decimal('total_incentive', 20, 4)->nullable();
            $table->decimal('exchange_rate', 20, 4)->nullable();
            $table->timestamps();  // added: standard timestamps
            $table->index('incentive_id');
            $table->index('bank_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incentive_to_bank');
    }
};
