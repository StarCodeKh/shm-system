<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_ceiling_entity', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('planning_id')->nullable();
            $table->unsignedBigInteger('ceiling_rule_id')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->unsignedBigInteger('entity_parent_id')->nullable();
            $table->integer('ceiling_exp_group');
            $table->unsignedBigInteger('program_id')->nullable();
            $table->unsignedBigInteger('sub_program_id')->nullable();
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
            $table->unsignedBigInteger('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('planning_id');
            $table->index('ceiling_rule_id');
            $table->index('entity_id');
            $table->index('entity_parent_id');
            $table->index('program_id');
            $table->index('sub_program_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_ceiling_entity');
    }
};
