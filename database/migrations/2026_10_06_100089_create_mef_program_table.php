<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_program', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('code', 50);
            $table->string('sub_code', 50)->nullable();
            $table->unsignedBigInteger('objective_id')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->unsignedBigInteger('entity_member_id')->nullable();
            $table->text('authorize_person')->nullable();
            $table->longText('name_en');
            $table->longText('name_kh');
            $table->text('remark')->nullable();
            $table->tinyInteger('is_active')->nullable()->default(1);
            $table->tinyInteger('is_routine')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->integer('assign_to')->nullable();
            $table->integer('responsible_by')->nullable();
            $table->integer('order_level')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('objective_id');
            $table->index('entity_id');
            $table->index('entity_member_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_program');
    }
};
