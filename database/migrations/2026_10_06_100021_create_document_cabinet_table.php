<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_cabinet', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('incentive_id')->nullable();
            $table->unsignedBigInteger('cabinet_id')->nullable();
            $table->string('document_name', 255);
            $table->longText('objective')->nullable();
            $table->longText('reference')->nullable();
            $table->dateTime('date')->nullable();
            $table->string('send_to', 255);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->tinyInteger('is_approve')->default(0);
            $table->tinyInteger('send_all_entity')->default(0);
            $table->timestamps();
            $table->unique('incentive_id');
            $table->index('incentive_id');
            $table->index('cabinet_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_cabinet');
    }
};
