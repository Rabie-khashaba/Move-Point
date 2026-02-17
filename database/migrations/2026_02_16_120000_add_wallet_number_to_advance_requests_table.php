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
        if (!Schema::hasColumn('advance_requests', 'wallet_number')) {
            Schema::table('advance_requests', function (Blueprint $table) {
                $table->string('wallet_number')->nullable()->after('supervisor_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('advance_requests', 'wallet_number')) {
            Schema::table('advance_requests', function (Blueprint $table) {
                $table->dropColumn('wallet_number');
            });
        }
    }
};
