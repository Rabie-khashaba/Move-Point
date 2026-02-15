<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('debt_sheets', function (Blueprint $table) {
            $table->id();
            $table->string('star_id')->unique();
            $table->decimal('shortage', 12, 2)->default(0);
            $table->decimal('credit_note', 12, 2)->default(0);
            $table->decimal('advances', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('debt_sheets');
    }
};

