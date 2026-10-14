<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// The member form treats the NSSF card question as optional, and members imported from the old system have no answer
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('officer', function (Blueprint $table) {
            $table->string('is_nssf_member', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('officer', function (Blueprint $table) {
            $table->string('is_nssf_member', 100)->nullable(false)->change();
        });
    }
};
