<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('account_id')->nullable();
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->string('receipt_number');
            /**
             * 1. cash  2. mpesa  3. bank transfer  4. cheque 
             * 5. package  6. complimentary
             */
            $table->string('payment_method'); // 
            $table->string('payment_code')->nullable();
            $table->integer('sub_total');
            $table->integer('tax_amount');
            $table->string('amount');
            $table->string('balance')->nullable();
            $table->integer('paid_amount');
            $table->boolean('withholding')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
