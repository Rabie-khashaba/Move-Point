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
        Schema::table('users', function (Blueprint $table) {
            // Drop existing columns that don't match the model
            $table->dropColumn(['name', 'email', 'email_verified_at', 'remember_token']);
            
            // Add new columns to match the User model
            $table->string('phone')->unique();
            $table->enum('type', ['employee', 'supervisor', 'representative', 'admin'])->default('employee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Restore original columns
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            
            // Remove new columns
            $table->dropColumn(['phone', 'type']);
        });
    }
};
