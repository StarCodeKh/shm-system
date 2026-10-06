<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taxable_salary_history', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->decimal('taxable_salary_monthly_from', 20, 4);
            $table->decimal('taxable_salary_monthly_to', 20, 4);
            $table->integer('rate');
            $table->string('currency_symbol', 6);
            $table->decimal('over_class_tax', 20, 4);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('modified_by')->nullable();
            $table->text('remark')->nullable();
            $table->integer('order_level')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taxable_salary_history');
    }
};
