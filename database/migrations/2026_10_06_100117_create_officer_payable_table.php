<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('officer_payable', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->string('code', 5)->nullable();
            $table->unsignedBigInteger('officer_id')->nullable();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->decimal('amount', 20, 4);
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->string('payment_term', 120);
            $table->date('payment_date')->nullable();
            $table->date('due_date')->nullable();
            $table->unsignedBigInteger('status')->nullable();
            $table->longText('remark')->nullable();
            $table->longText('attach')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->timestamps();
            $table->index('officer_id');
            $table->index('account_id');
            $table->index('payment_method_id');
            $table->index('currency_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('officer_payable');
    }
};
