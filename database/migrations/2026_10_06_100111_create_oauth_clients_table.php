<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oauth_clients', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('owner_type', 191)->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('name', 255);
            $table->string('provider', 255)->nullable();
            $table->text('redirect_uris');
            $table->text('grant_types');
            $table->string('secret', 100);
            $table->tinyInteger('revoked');
            $table->timestamps();
            $table->index(['owner_type', 'owner_id']);
            $table->index('owner_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oauth_clients');
    }
};
