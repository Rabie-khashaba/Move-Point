<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('work_starts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('representative_id')->constrained('representatives')->cascadeOnDelete();
            $table->foreignId('governorate_id')->constrained('governorates')->cascadeOnDelete();
            $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();
            $table->unsignedBigInteger('message_id');

            $table->date('date');
            $table->enum('status', ['تم بدء العمل', 'لم يرد', 'pending'])->default('pending');
            $table->timestamps();

            // بعد إنشاء الجدول
            $table->foreign('message_id')->references('id')->on('message_workings')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_starts');
    }
};
