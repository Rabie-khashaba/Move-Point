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
        Schema::create('representatives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('contact')->nullable();
            $table->string('national_id')->unique();
            $table->decimal('salary', 10, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->foreignId('company_id')->constrained();
            $table->string('bank_account')->nullable();
            $table->string('code')->nullable();
            $table->json('attachments')->nullable();
            $table->boolean('inquiry_checkbox')->default(false);
            $table->foreignId('governorate_id')->constrained();
            $table->foreignId('location_id')->constrained();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('representatives');
    }
};
