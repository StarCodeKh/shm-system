<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_revenue_imple_budget_detail', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('rev_imple_budget_id')->nullable();
            $table->decimal('finance_law', 20, 4)->nullable();
            $table->decimal('actual_imple_budget', 20, 4)->nullable();
            $table->integer('percentage_imple_budget')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->timestamps();
            $table->index('rev_imple_budget_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_revenue_imple_budget_detail');
    }
};
