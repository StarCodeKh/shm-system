<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_ceiling_data', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();  // added: table had no primary key
            $table->unsignedBigInteger('ceiling_entity_id')->nullable();
            $table->integer('ceiling_exp_group');
            $table->integer('ceiling_exp_type');
            $table->decimal('ceiling_rule', 20, 4)->nullable();
            $table->decimal('reg_expense', 20, 4)->nullable();
            $table->decimal('non_reg_expense', 20, 4)->nullable();
            $table->decimal('staff_exp', 20, 4)->nullable();
            $table->decimal('out_staff_exp', 20, 4)->nullable();
            $table->decimal('ceiling_tran_year_0', 20, 4)->nullable();
            $table->decimal('ceiling_tran_year_1', 20, 4)->nullable();
            $table->decimal('ceiling_tran_year_2', 20, 4)->nullable();
            $table->decimal('ceiling_tran_year_3', 20, 4)->nullable();
            $table->decimal('increase_rate_year_0', 20, 4)->nullable();
            $table->decimal('increase_rate_year_1', 20, 4)->nullable();
            $table->decimal('increase_rate_year_2', 20, 4)->nullable();
            $table->decimal('increase_rate_year_3', 20, 4)->nullable();
            $table->longText('remark')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('ceiling_entity_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_ceiling_data');
    }
};
