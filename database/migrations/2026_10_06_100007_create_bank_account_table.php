<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_account', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('name_en', 255);
            $table->string('name_kh', 255)->nullable();
            $table->string('account_group', 255);
            $table->string('account_name', 255);
            $table->string('account_type', 255);
            $table->string('account_number', 255);
            $table->decimal('open_balance', 20, 4)->nullable();
            $table->string('currency', 50)->nullable();
            $table->string('code', 45)->nullable();
            $table->text('address')->nullable();
            $table->string('remark', 120)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->tinyInteger('is_default')->nullable()->default(0);
            $table->dateTime('issue_date')->nullable();
            $table->unsignedBigInteger('status')->nullable();  // added: standard soft-delete flag (4 = deleted)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_account');
    }
};
