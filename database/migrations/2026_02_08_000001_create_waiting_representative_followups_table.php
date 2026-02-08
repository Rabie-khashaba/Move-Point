<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waiting_representative_followups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('waiting_representative_id');
            $table->string('status'); // لم يرد / متابعة مرة اخري / تغيير الشركه
            $table->date('follow_up_date')->nullable();
            $table->text('note');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('waiting_representative_id', 'wrf_waiting_rep_fk')
                ->references('id')->on('waiting_representatives')->onDelete('cascade');
            $table->foreign('created_by', 'wrf_created_by_fk')
                ->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waiting_representative_followups');
    }
};
