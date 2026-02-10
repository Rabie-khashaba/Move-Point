<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_session_postpones', function (Blueprint $table) {
            $table->foreignId('work_start_id')
                ->nullable()
                ->after('training_session_id')
                ->constrained('work_starts')
                ->nullOnDelete();

            $table->unsignedBigInteger('training_session_id')->nullable()->change();
        });

        Schema::table('waiting_representatives', function (Blueprint $table) {
            $table->string('source')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('training_session_postpones', function (Blueprint $table) {
            $table->dropForeign(['work_start_id']);
            $table->dropColumn('work_start_id');

            $table->unsignedBigInteger('training_session_id')->nullable(false)->change();
        });

        Schema::table('waiting_representatives', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
