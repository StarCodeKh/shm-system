<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incentive', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('title', 255);
            $table->string('letter_type', 255);
            $table->string('sent_to', 120)->nullable();
            $table->string('prepared_by', 120)->nullable();
            $table->string('seen_by', 120)->nullable();
            $table->string('approved_by', 120)->nullable();
            $table->string('reference', 120)->nullable();
            $table->dateTime('issue_from_date')->nullable();
            $table->dateTime('issue_to_date')->nullable();
            $table->dateTime('issue_date')->nullable();
            $table->string('khmer_date', 255)->nullable();
            $table->string('khmer_day', 120)->nullable();
            $table->dateTime('effective_date')->nullable();
            $table->longText('attach_fille')->nullable();
            $table->tinyInteger('is_approve')->default(0);
            $table->string('currency', 50)->nullable();
            $table->tinyInteger('is_notify')->default(0);
            $table->integer('notification_count')->default(0);
            $table->decimal('total_incentive', 20, 4)->default(0.0000);
            $table->decimal('total_return', 20, 4)->nullable()->default(0.0000);
            $table->decimal('total_reminder', 20, 4)->nullable()->default(0.0000);
            $table->decimal('total_tax', 20, 4)->default(0.0000);
            $table->decimal('total_incentive_after_tax', 20, 4)->nullable()->default(0.0000);
            $table->integer('total_officer')->nullable();
            $table->integer('total_entity')->nullable();
            $table->integer('exchange_rate')->default(0);
            $table->longText('remark')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incentive');
    }
};
