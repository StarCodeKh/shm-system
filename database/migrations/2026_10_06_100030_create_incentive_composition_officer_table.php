<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incentive_composition_officer', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();  // added: table had no primary key
            $table->unsignedBigInteger('incentive_composition_request_id')->nullable();
            $table->unsignedBigInteger('from_officer_id')->nullable();
            $table->unsignedBigInteger('to_officer_id')->nullable();
            $table->timestamps();  // added: standard timestamps
            $table->index('incentive_composition_request_id', 'incentive_composition_of_incentive_composition_re_3e35f6_ind');
            $table->index('from_officer_id');
            $table->index('to_officer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incentive_composition_officer');
    }
};
