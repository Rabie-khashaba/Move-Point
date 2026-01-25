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
        Schema::table('representatives', function (Blueprint $table) {
            $table->unsignedBigInteger('converted_active_by')
                ->nullable()
                ->after('converted_to_active_date');

            $table->foreign('converted_active_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('representatives', function (Blueprint $table) {
            $table->dropForeign(['converted_active_by']);
            $table->dropColumn('converted_active_by');
        });
    }
};
