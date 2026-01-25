<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
    

        // Add reason_id to follow_ups table
        Schema::table('lead_followups', function (Blueprint $table) {
            $table->foreignId('reason_id')->nullable()->constrained('reasons')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('lead_followups', function (Blueprint $table) {
            $table->dropForeign(['reason_id']);
            $table->dropColumn('reason_id');
        });

        Schema::dropIfExists('reasons');
    }
};
