<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Which records a role may see (App\Services\DataScope): all | entity | own | member.
 * Existing roles get "all", so nothing changes until a role is given a narrower scope in the role editor.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('roles', 'data_scope')) return;
        Schema::table('roles', function (Blueprint $table) {
            $table->string('data_scope', 20)->default('all')->after('guard_name');
        });
    }

    public function down(): void
    {
        if (!Schema::hasColumn('roles', 'data_scope')) return;
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('data_scope');
        });
    }
};
