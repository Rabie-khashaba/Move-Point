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
        // Add indexes to leads table
        Schema::table('leads', function (Blueprint $table) {
            $table->index('status');
            $table->index('governorate_id');
            $table->index('source_id');
            $table->index('created_at');
        });

        // Add indexes to users table
        Schema::table('users', function (Blueprint $table) {
            $table->index('phone');
            $table->index('type');
        });

        // Add indexes to employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('department_id');
        });

        // Add indexes to companies table
        Schema::table('companies', function (Blueprint $table) {
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes from leads table
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['governorate_id']);
            $table->dropIndex(['source_id']);
            $table->dropIndex(['created_at']);
        });

        // Remove indexes from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['phone']);
            $table->dropIndex(['type']);
        });

        // Remove indexes from employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['department_id']);
        });

        // Remove indexes from companies table
        Schema::table('companies', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });
    }
};
