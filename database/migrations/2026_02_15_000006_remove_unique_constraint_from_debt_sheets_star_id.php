<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('debt_sheets', function (Blueprint $table) {
            $table->dropUnique(['star_id']);
            $table->index('star_id');
        });
    }

    public function down(): void
    {
        Schema::table('debt_sheets', function (Blueprint $table) {
            $table->dropIndex(['star_id']);
            $table->unique('star_id');
        });
    }
};
