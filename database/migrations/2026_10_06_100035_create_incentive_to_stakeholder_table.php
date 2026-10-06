<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incentive_to_stakeholder', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();  // added: table had no primary key
            $table->unsignedBigInteger('incentive_id')->nullable();
            $table->string('stakeholder', 255)->nullable();
            $table->string('entity', 120)->nullable();
            $table->string('position', 120)->nullable();
            $table->timestamps();  // added: standard timestamps
            $table->index('incentive_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incentive_to_stakeholder');
    }
};
