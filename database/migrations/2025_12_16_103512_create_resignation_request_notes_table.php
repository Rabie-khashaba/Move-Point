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
        Schema::create('resignation_request_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resignation_request_id')->constrained('resignation_requests')->onDelete('cascade');
            $table->text('note');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['approved', 'rejected'])->nullable();

            $table->timestamps();

            $table->index('resignation_request_id');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resignation_request_notes');
    }
};
