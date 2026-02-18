<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE resignation_request_notes
            MODIFY COLUMN status ENUM(
                'approved',
                'rejected',
                'no_reply',
                'follow_up_again',
                'other'
            ) NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE resignation_request_notes
            MODIFY COLUMN status ENUM(
                'approved',
                'rejected'
            ) NULL
        ");
    }
};
