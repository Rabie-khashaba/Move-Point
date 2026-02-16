<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('debt_sheets', function (Blueprint $table) {
            $table->string('status')->default('لم يسدد')->after('advances');
            $table->date('sheet_date')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('debt_sheets', function (Blueprint $table) {
            $table->dropColumn(['status', 'sheet_date']);
        });
    }
};
