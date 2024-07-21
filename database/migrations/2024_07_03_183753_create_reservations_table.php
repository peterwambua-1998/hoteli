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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->integer('type'); // 1 single or 2 group
            $table->date('date_arrival');
            $table->date('date_departure');
            $table->unsignedBigInteger('meal_plan');
            $table->integer('num_of_pax');
            $table->longText('extra_info')->nullable();
            $table->integer('num_of_vehicles');
            $table->unsignedBigInteger('account_id')->nullable(); // for check in under company account

            // for single
            $table->string('surname')->nullable();
            $table->string('other_names')->nullable();
            $table->string('profession')->nullable();
            $table->string('id_number')->nullable(); // id passport
            $table->string('single_email')->nullable(); // image url
            $table->string('id_url')->nullable(); // image url

            // for organization
            $table->string('org_name')->nullable(); // individual name or corporate name
            $table->string('org_email')->nullable();
            $table->string('vat_registration_number')->nullable();

            // both
            $table->string('telephone')->nullable();
            $table->string('location')->nullable();

            // status
            $table->integer('status')->default(1); // 1 active 2 cancelled 3 changed board
            $table->longText('reason_for_change')->nullable();
            $table->longText('reason_for_rejection')->nullable();
            $table->integer('change_status_to')->nullable(); // used to tell which status to change to
            $table->integer('amend_status')->nullable(); // 1 approved 0 rejected 2 pending
            $table->unsignedBigInteger('amended_board')->nullable(); // which board was selected before approval

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
