<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetable_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cohort_id')->constrained('cohorts')->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 1 = Isnin, 2 = Selasa, 3 = Rabu, 4 = Khamis, 5 = Jumaat
            $table->time('start_time');
            $table->time('end_time');
            $table->string('subject_name'); // e.g. "Pendidikan Islam", "Literasi Bahasa Melayu", "Sains Awal", "Rehat"
            $table->string('color')->default('#3b82f6');
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cohort_id')->nullable()->constrained('cohorts')->nullOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('event_type')->default('class'); // class, activity, holiday, assembly
            $table->string('color')->default('#10b981');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
        Schema::dropIfExists('timetable_slots');
    }
};
