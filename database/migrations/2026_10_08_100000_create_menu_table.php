<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The sidebar menu, editable in Administration › Menus (seeded by MenuSeeder).
 *
 * Three kinds of rows, nested with parent_id:
 *   section  a heading ("System"); top level only
 *   group    a collapsible entry ("Members"); top level or in a section
 *   link     a page (url); top level, in a section or in a group
 * A row is shown to a user who has its permission (empty = everyone); sections and groups
 * without any visible link are hidden.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('menu')->cascadeOnDelete();
            $table->string('type', 10)->default('link');        // section | group | link
            $table->string('name');                             // English label, used when i18n has no translation
            $table->string('i18n')->nullable();                 // translation key of the label
            $table->string('url')->nullable();                  // links only
            $table->string('slug')->unique();                   // stable key (seeder, active highlight)
            $table->string('icon', 64)->nullable();             // feather icon, e.g. UsersIcon
            $table->string('permission')->nullable();           // permission name needed to see it
            $table->unsignedInteger('order_level')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index(['parent_id', 'order_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
