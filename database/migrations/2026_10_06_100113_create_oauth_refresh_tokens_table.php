<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('oauth_refresh_tokens', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('id', 100)->primary();
            $table->string('access_token_id', 100);
            $table->tinyInteger('revoked');
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();  // added: standard timestamps
            $table->index('access_token_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oauth_refresh_tokens');
    }
};
