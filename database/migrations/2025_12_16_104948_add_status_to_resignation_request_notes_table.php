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
        Schema::table('resignation_request_notes', function (Blueprint $table) {
            $table->enum('status', ['approved', 'rejected'])->nullable()->after('note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resignation_request_notes', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
