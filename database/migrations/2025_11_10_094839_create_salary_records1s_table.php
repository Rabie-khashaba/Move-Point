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
        Schema::create('salary_records1s', function (Blueprint $table) {
            $table->id();

            $table->string('state')->nullable();
            $table->string('star_id')->nullable();
            $table->string('name')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->string('contractor')->nullable();
            $table->string('hub')->nullable();
            $table->string('zone')->nullable();

            $table->integer('working_days')->nullable();
            $table->decimal('delivered_cash', 15, 2)->nullable();
            $table->decimal('rto', 15, 2)->nullable();
            $table->decimal('exchange', 15, 2)->nullable();
            $table->decimal('crp', 15, 2)->nullable();
            $table->integer('pickups_stops')->nullable();


            $table->decimal('fixed_day', 15, 2)->nullable();
            $table->decimal('variable_pkg', 15, 2)->nullable();
            $table->decimal('total_delivered', 15, 2)->nullable();

            $table->decimal('guarantee_day', 15, 2)->nullable();
            $table->decimal('monthly_guarantee_volume', 15, 2)->nullable();
            $table->decimal('guarantee_volume', 15, 2)->nullable();

            $table->decimal('fixed_salary', 15, 2)->nullable();
            $table->decimal('variable_d_r', 15, 2)->nullable();
            $table->decimal('exchange_variable', 15, 2)->nullable();
            $table->decimal('crp_variable', 15, 2)->nullable();

            $table->integer('pickups_variable')->nullable();

            $table->decimal('fleet_bonus', 15, 2)->nullable();
            $table->decimal('guarantee', 15, 2)->nullable();
            $table->decimal('guarantee_deduction', 15, 2)->nullable();
            $table->decimal('ops_bonus', 15, 2)->nullable();
            $table->decimal('ops_deductions', 15, 2)->nullable();
            $table->decimal('fleet_deduction', 15, 2)->nullable();

            $table->decimal('fake_update', 15, 2)->nullable();
            $table->decimal('total', 15, 2)->nullable();

            $table->string('short_tag')->nullable();
            $table->string('cn')->nullable();

            $table->decimal('loans', 15, 2)->nullable();
            $table->decimal('total_deduction', 15, 2)->nullable();
            $table->decimal('net_salary', 15, 2)->nullable();
            $table->decimal('amounts_on_pilots', 15, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_records1s');
    }
};
