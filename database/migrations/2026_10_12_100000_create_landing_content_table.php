<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Content of the public landing page (/welcome), edited in Settings › Landing page.
 * One row per block and language: the hero, each feature card and each live number.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_content', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('landing_section', 16);            // hero | feature | stat
            $table->string('language_code', 3);               // language.code, e.g. kh, en
            $table->string('headline', 191);
            $table->text('description')->nullable();
            $table->string('landing_icon', 32)->nullable();   // feather icon name, e.g. users
            $table->string('landing_stat', 32)->nullable();   // stat blocks: which number (officers, entities, ...)
            $table->string('image', 191)->nullable();         // hero picture
            $table->integer('order_level')->default(0);
            $table->unsignedBigInteger('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index(['language_code', 'landing_section', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_content');
    }
};
