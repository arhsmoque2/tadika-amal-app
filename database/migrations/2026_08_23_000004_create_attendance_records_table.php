<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cohort_id')->constrained('cohorts')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('date');
            $table->enum('status', ['hadir', 'tidak_hadir', 'sakit', 'cuti'])->default('hadir');
            $table->string('reason')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['school_id', 'cohort_id', 'student_id', 'date'], 'student_cohort_daily_att_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
