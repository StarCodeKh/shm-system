<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_revenue_program_detail', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('rev_pro_id')->nullable();
            $table->unsignedBigInteger('account_group_id')->nullable();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->unsignedBigInteger('sub_account_id')->nullable();
            $table->decimal('finance_law', 20, 4)->nullable();
            $table->decimal('annual_program_plan', 20, 4)->nullable();
            $table->integer('annual_percentage_law')->nullable();
            $table->decimal('s1_total_bp', 20, 4)->nullable();
            $table->decimal('s1_bp_jan', 20, 4)->nullable();
            $table->decimal('s1_bp_feb', 20, 4)->nullable();
            $table->decimal('s1_bp_mar', 20, 4)->nullable();
            $table->decimal('s1_total_est_imp', 20, 4)->nullable();
            $table->decimal('s1_est_imp_jan', 20, 4)->nullable();
            $table->decimal('s1_est_imp_feb', 20, 4)->nullable();
            $table->decimal('s1_est_imp_mar', 20, 4)->nullable();
            $table->decimal('s2_total_bp', 20, 4)->nullable();
            $table->decimal('s2_bp_apr', 20, 4)->nullable();
            $table->decimal('s2_bp_may', 20, 4)->nullable();
            $table->decimal('s2_bp_jun', 20, 4)->nullable();
            $table->decimal('s2_total_adj', 20, 4)->nullable();
            $table->decimal('s2_adj_apr', 20, 4)->nullable();
            $table->decimal('s2_adj_may', 20, 4)->nullable();
            $table->decimal('s2_adj_jun', 20, 4)->nullable();
            $table->decimal('s2_total_est_imp', 20, 4)->nullable();
            $table->decimal('s2_est_imp_apr', 20, 4)->nullable();
            $table->decimal('s2_est_imp_may', 20, 4)->nullable();
            $table->decimal('s2_est_imp_jun', 20, 4)->nullable();
            $table->decimal('s3_total_pb', 20, 4)->nullable();
            $table->decimal('s3_bp_jul', 20, 4)->nullable();
            $table->decimal('s3_bp_aug', 20, 4)->nullable();
            $table->decimal('s3_bp_sep', 20, 4)->nullable();
            $table->decimal('s3_total_est_imp', 20, 4)->nullable();
            $table->decimal('s3_est_imp_jul', 20, 4)->nullable();
            $table->decimal('s3_est_imp_aug', 20, 4)->nullable();
            $table->decimal('s3_est_imp_sep', 20, 4)->nullable();
            $table->decimal('s4_total_bp', 20, 4)->nullable();
            $table->decimal('s4_bp_oct', 20, 4)->nullable();
            $table->decimal('s4_bp_nov', 20, 4)->nullable();
            $table->decimal('s4_bp_dec', 20, 4)->nullable();
            $table->decimal('s4_total_adj', 20, 4)->nullable();
            $table->decimal('s4_adj_oct', 20, 4)->nullable();
            $table->decimal('s4_adj_nov', 20, 4)->nullable();
            $table->decimal('s4_adj_dec', 20, 4)->nullable();
            $table->decimal('s4_total_est_imp', 20, 4)->nullable();
            $table->decimal('s4_est_imp_oct', 20, 4)->nullable();
            $table->decimal('s4_est_imp_nov', 20, 4)->nullable();
            $table->decimal('s4_est_imp_dec', 20, 4)->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->longText('remark')->nullable();
            $table->timestamps();
            $table->index('rev_pro_id');
            $table->index('account_group_id');
            $table->index('account_id');
            $table->index('sub_account_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_revenue_program_detail');
    }
};
