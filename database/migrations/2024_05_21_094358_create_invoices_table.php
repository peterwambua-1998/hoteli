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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->unsignedBigInteger('quotation_id')->nullable();
            $table->string('inv_number');
            $table->date('delivery_date');
            $table->date('tax_date');
            $table->date('to_date')->nullable();
            $table->date('from_date')->nullable();
            $table->string('invoiced_to');
            $table->string('vat_registration_number')->nullable();
            $table->double('sub_total');
            $table->double('tax_amount');
            $table->double('levy');
            $table->double('total');
            $table->integer('pos_used')->nullable(); // 1. bar 2. accommodation 3. kitchen 4. swimming 5. direct sales
            $table->integer('table_number')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->longText('description')->nullable();
            $table->boolean('voided')->default(0); // 0 not voided, 1 voided
            $table->boolean('from_accommodation')->default(0); // 0 no 1 yes
            /**
             * 1. cash 2. mpesa 3. bank transfer  4. cheque 
             * 5. package 6 complimentary
             */
            $table->integer('cleared_by')->nullable();

            // discount
            $table->double('discount_amount')->nullable();
            
            // day
            $table->unsignedBigInteger('day_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
