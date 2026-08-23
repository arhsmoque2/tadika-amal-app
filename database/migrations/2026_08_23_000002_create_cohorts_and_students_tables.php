<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cohorts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->string('name'); // e.g. "6 Tahun - Umar Al-Khattab"
            $table->string('age_group')->default('6'); // 4, 5, 6
            $table->string('academic_year'); // e.g. "2026"
            $table->integer('capacity')->default(25);
            $table->string('color')->default('#3b82f6');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cohort_id')->nullable()->constrained('cohorts')->nullOnDelete();
            $table->string('name');
            $table->string('mykid')->nullable()->index();
            $table->enum('gender', ['Lelaki', 'Perempuan'])->default('Lelaki');
            $table->date('birth_date')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('guardian_email')->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->text('allergies_medical')->nullable();
            $table->json('dynamic_data')->nullable(); // Extended JSON Schema fields (ADR-003)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
        Schema::dropIfExists('cohorts');
    }
};
