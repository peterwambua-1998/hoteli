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
        Schema::create('proforma_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->string('inv_number');
            $table->date('delivery_date');
            $table->date('to_date')->nullable();
            $table->date('from_date')->nullable();
            $table->date('tax_date');
            $table->string('invoiced_to');
            $table->string('vat_registration_number');
            $table->double('sub_total');
            $table->double('tax_amount');
            $table->double('levy');
            $table->double('total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proforma_invoices');
    }
};
