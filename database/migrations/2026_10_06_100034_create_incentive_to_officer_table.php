<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incentive_to_officer', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('incentive_id')->nullable();
            $table->unsignedBigInteger('officer_id')->nullable();
            $table->string('entity', 150)->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('position', 150)->nullable();
            $table->string('duty', 150)->nullable();
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->string('bank', 50)->nullable();
            $table->string('bank_account_no', 50)->nullable();
            $table->string('identity_card', 50)->nullable();
            $table->string('bank_code', 50)->nullable();
            $table->string('index', 45)->nullable();
            $table->string('achievement', 45)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->decimal('incentive', 20, 4);
            $table->decimal('tax', 20, 4)->nullable();
            $table->decimal('return_amount', 20, 4)->nullable();
            $table->decimal('incentive_after_tax', 20, 4)->nullable();
            $table->decimal('reminder_amount', 20, 4)->nullable();
            $table->string('remark', 255)->nullable();
            $table->dateTime('issue_from_date')->nullable();
            $table->dateTime('issue_to_date')->nullable();
            $table->tinyInteger('is_notify')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->index('incentive_id');
            $table->index('officer_id');
            $table->index('entity_id');
            $table->index('bank_id');
            $table->index('reference_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incentive_to_officer');
    }
};
