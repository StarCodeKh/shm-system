<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incentive_to_entity', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();  // added: table had no primary key
            $table->unsignedBigInteger('incentive_id')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->timestamps();  // added: standard timestamps
            $table->index('incentive_id');
            $table->index('entity_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incentive_to_entity');
    }
};
