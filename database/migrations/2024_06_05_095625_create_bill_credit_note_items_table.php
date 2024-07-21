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
        Schema::create('bill_credit_note_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_credit_note_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('bill_id');
            $table->float('quantity');
            $table->float('rate');
            $table->float('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_credit_note_items');
    }
};
