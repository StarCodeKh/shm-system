<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The language table was created without the created_by / modified_by columns every settings table has,
 * so saving a language in Settings › Language failed ("Unknown column 'created_by'").
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('language', function (Blueprint $table) {
            if (! Schema::hasColumn('language', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('status');
            }
            if (! Schema::hasColumn('language', 'modified_by')) {
                $table->unsignedBigInteger('modified_by')->nullable()->after('created_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('language', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'modified_by']);
        });
    }
};
