<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->unsignedBigInteger('model_id');
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type', 255);
            $table->timestamps();  // added: standard timestamps
            $table->primary(['permission_id', 'model_id', 'model_type']);
            $table->index('model_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_has_permissions');
    }
};
