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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->integer('type'); // 1. express checkin 2. reserved check-in
            $table->integer('bill_options'); // 1. guest 2. company
            $table->integer('acc_paid_by'); // 1. guest 2. company
            /**
             * bill options == 1 (guest) account_id select
             * bill options == 2 (company) account_id select
             */
            $table->unsignedBigInteger('account_id'); 
            $table->unsignedBigInteger('package_id')->nullable(); 
            $table->unsignedBigInteger('meal_plan_id')->nullable(); 
            $table->unsignedBigInteger('company_id')->nullable();

            // single
            $table->string('surname')->nullable();
            $table->string('other_names')->nullable();
            $table->string('profession')->nullable();
            $table->string('id_number')->nullable(); // id passport
            $table->string('email')->nullable(); // image url
            $table->string('id_url')->nullable(); // image url

            $table->string('telephone');

            $table->integer('extras_paid_by'); // 1. guest 2. company
            $table->dateTime('check_in');
            $table->dateTime('check_out');

            $table->integer('pax');
            $table->integer('num_of_vehicles');
            $table->integer('bill_interval')->nullable(); // 1. daily 2. checkout
            $table->longText('extra_details')->nullable();

            // down payment
            $table->double('down_payment')->default(0);

            // under age son
            $table->boolean('underage_child')->default(0); // 1. yes 0. no
            $table->boolean('different_room')->default(0); // 1. yes 0. no
            $table->integer('num_of_underage')->nullable();

            // status
            $table->boolean('status')->default(1); // 1. active 0 not active

            // day
            $table->unsignedBigInteger('day_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
