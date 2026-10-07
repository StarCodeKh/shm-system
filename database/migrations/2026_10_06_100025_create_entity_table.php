<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('code', 45)->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('name_en')->nullable();
            $table->string('name_kh')->nullable();
            $table->string('color', 50)->nullable();
            $table->string('abbreviation', 45)->nullable();
            $table->integer('limit_member')->nullable();
            $table->longText('deputy_commissionerofficer')->nullable();
            $table->longText('professional_officer')->nullable();
            $table->integer('order_level')->nullable()->default(1);
            $table->unsignedBigInteger('status')->nullable();
            $table->boolean('is_active')->nullable()->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('parent_id');
            $table->index('department_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity');
    }
};
