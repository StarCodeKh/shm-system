<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('officer', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->mediumText('photo')->nullable();
            $table->string('first_name', 150);
            $table->string('last_name', 150);
            $table->string('latin', 150)->nullable();
            $table->string('gender', 45)->nullable();
            $table->date('dob')->nullable();
            $table->string('identity_no', 50)->nullable();
            $table->string('identity_member_no', 50);
            $table->date('register_date')->nullable();
            $table->string('nationality', 250)->nullable();
            $table->string('title', 255)->nullable();
            $table->string('duty', 50)->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->text('address')->nullable();
            $table->string('email', 120)->nullable();
            $table->string('bank', 45)->nullable();
            $table->string('bank_code', 45)->nullable();
            $table->string('bank_account_no', 45)->nullable();
            $table->string('updated_bank_acc', 45)->nullable();
            $table->string('position', 255)->nullable();
            $table->string('telephone_1', 45)->nullable();
            $table->string('is_nssf_member', 100);
            $table->string('nssf_card_no', 100)->nullable();
            $table->string('married_status', 100)->nullable();
            $table->text('social_media_fb')->nullable();
            $table->text('social_media_telegram')->nullable();
            $table->text('social_media_whatsapp')->nullable();
            $table->text('social_media_alt')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->text('emergency_relative')->nullable();
            $table->text('emergency_person')->nullable();
            $table->string('telephone_2', 45)->nullable();
            $table->longText('remark')->nullable();
            $table->integer('order_level')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('user_id');
            $table->index('department_id');
            $table->index('entity_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('officer');
    }
};
