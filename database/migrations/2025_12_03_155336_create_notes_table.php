<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable()->comment('Lead/Client ID');
            $table->unsignedBigInteger('measurement_id')->nullable()->comment('Measurement ID');
            $table->unsignedBigInteger('visit_id')->nullable()->comment('Visit ID');
            $table->text('notes')->nullable()->comment('Note content');
            $table->unsignedBigInteger('employee_id')->nullable()->comment('Employee who created the note');
            $table->date('date')->nullable()->comment('Note date');
            $table->timestamps();

            // Foreign keys
            $table->foreign('client_id')->references('id')->on('leads')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');

            // Indexes for better performance
            $table->index('client_id');
            $table->index('measurement_id');
            $table->index('visit_id');
            $table->index('employee_id');
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
