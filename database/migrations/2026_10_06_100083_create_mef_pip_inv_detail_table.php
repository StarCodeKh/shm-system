<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_pip_inv_detail', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();  // added: table had no primary key
            $table->unsignedBigInteger('pip_inv_id')->nullable();
            $table->longText('attribute_en')->nullable();
            $table->longText('attribute_kh')->nullable();
            $table->decimal('est_costing_year_0', 20, 4)->nullable();
            $table->decimal('est_costing_year_1', 20, 4)->nullable();
            $table->decimal('est_costing_year_2', 20, 4)->nullable();
            $table->decimal('s_fin_year_0', 20, 4)->nullable();
            $table->decimal('s_fin_year_1', 20, 4)->nullable();
            $table->decimal('s_fin_year_2', 20, 4)->nullable();
            $table->decimal('add_req_fin_year_0', 20, 4)->nullable();
            $table->decimal('add_req_fin_year_1', 20, 4)->nullable();
            $table->decimal('add_req_fin_year_2', 20, 4)->nullable();
            $table->timestamps();  // added: standard timestamps
            $table->index('pip_inv_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_pip_inv_detail');
    }
};
