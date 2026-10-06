<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_pip_investment', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('planning_id')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->unsignedBigInteger('sub_program_id')->nullable();
            $table->unsignedBigInteger('cluster_activity_id')->nullable();
            $table->string('pip_no', 50)->nullable();
            $table->unsignedBigInteger('project_status_id')->nullable();
            $table->unsignedBigInteger('project_type_id')->nullable();
            $table->longText('project_name_en')->nullable();
            $table->longText('project_name_kh')->nullable();
            $table->unsignedBigInteger('currency_exchange_id')->nullable();
            $table->decimal('exchange_rate', 20, 4)->nullable();
            $table->integer('column_type')->nullable();
            $table->unsignedBigInteger('finance_resource_id')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->decimal('est_costing_year_0', 20, 4)->nullable();
            $table->decimal('est_costing_year_1', 20, 4)->nullable();
            $table->decimal('est_costing_year_2', 20, 4)->nullable();
            $table->decimal('s_fin_year_0', 20, 4)->nullable();
            $table->decimal('s_fin_year_1', 20, 4)->nullable();
            $table->decimal('s_fin_year_2', 20, 4)->nullable();
            $table->decimal('add_req_fin_year_0', 20, 4)->nullable();
            $table->decimal('add_req_fin_year_1', 20, 4)->nullable();
            $table->decimal('add_req_fin_year_2', 20, 4)->nullable();
            $table->string('grading_no', 50)->nullable();
            $table->integer('order_level')->nullable();
            $table->longText('pip_4_strategy')->nullable();
            $table->longText('pip_participant_project')->nullable();
            $table->longText('pip_field')->nullable();
            $table->longText('pip_subproject_location')->nullable();
            $table->longText('pip_project_aim')->nullable();
            $table->longText('pip_project_description')->nullable();
            $table->longText('pip_resonable_project')->nullable();
            $table->longText('pip_benefit_project')->nullable();
            $table->longText('pip_effectivenes_environment')->nullable();
            $table->longText('pip_gender_analysis')->nullable();
            $table->longText('pip_project_imple_capacity')->nullable();
            $table->longText('pip_project_imple_status')->nullable();
            $table->longText('pip_partner_participation')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('planning_id');
            $table->index('entity_id');
            $table->index('program_id');
            $table->index('sub_program_id');
            $table->index('cluster_activity_id');
            $table->index('project_status_id');
            $table->index('project_type_id');
            $table->index('currency_exchange_id');
            $table->index('finance_resource_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_pip_investment');
    }
};
