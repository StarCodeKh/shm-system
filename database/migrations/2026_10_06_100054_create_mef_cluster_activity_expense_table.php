<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mef_cluster_activity_expense', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('cluster_activity_id')->nullable();
            $table->string('tbl_mef_cluster_activity_expensecol', 45)->nullable();
            $table->timestamps();  // added: standard timestamps
            $table->index('cluster_activity_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mef_cluster_activity_expense');
    }
};
