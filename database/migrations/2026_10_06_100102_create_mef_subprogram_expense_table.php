<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_subprogram_expense', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('sub_program_id')->nullable();
            $table->unsignedBigInteger('planning_id')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->decimal('total_expense', 20, 4)->nullable();
            $table->timestamps();  // added: standard timestamps
            $table->index('sub_program_id');
            $table->index('planning_id');
            $table->index('entity_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_subprogram_expense');
    }
};
