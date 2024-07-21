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
        Schema::create('bill_receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_id');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->string('receipt_number');
            $table->string('payment_method'); // 1. cash 2. mpesa 3. bank transfer  4. cheque
            $table->string('payment_code')->nullable();
            $table->float('sub_total');
            $table->float('tax_amount');
            $table->float('amount');
            $table->float('balance')->nullable();
            $table->float('paid_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_receipts');
    }
};
