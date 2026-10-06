<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_menu', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('role_id', 14);
            $table->string('menu_id', 30)->nullable();
            $table->string('menu_name', 30)->nullable();
            $table->string('tab_id', 14)->nullable();
            $table->string('tab_name', 14)->nullable();
            $table->string('allw_yn', 1)->nullable();
            $table->string('last_chng_empno', 30)->nullable();
            $table->string('last_chng_dttm', 17)->nullable();
            $table->unsignedBigInteger('status')->nullable();  // added: standard soft-delete flag (4 = deleted)
            $table->timestamps();  // added: standard timestamps
            $table->unique(['role_id', 'menu_id', 'tab_id']);
            $table->index('menu_id');
            $table->index('tab_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_menu');
    }
};
