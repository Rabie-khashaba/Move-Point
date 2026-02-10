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
        Schema::create('representative_inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('representative_id')->constrained('representatives')->cascadeOnDelete();
            $table->string('inquiry_type')->nullable();
            $table->string('inquiry_field_result')->nullable();
            $table->text('inquiry_field_notes')->nullable();
            $table->json('inquiry_field_attachments')->nullable();
            $table->string('inquiry_security_result')->nullable();
            $table->text('inquiry_security_notes')->nullable();
            $table->json('inquiry_security_attachments')->nullable();
            $table->string('security_inactive_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('representative_inquiries');
    }
};