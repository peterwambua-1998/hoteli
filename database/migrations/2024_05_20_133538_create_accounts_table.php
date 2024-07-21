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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->integer('type'); // 1 corporate 2. individual
            $table->string('name'); // individual name or corporate name
            $table->string('email');
            $table->string('telephone');
            $table->string('location');
            $table->string('vat_registration_number')->nullable();
            $table->string('profession')->nullable();
            $table->string('id_number')->nullable();  //id passport
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
