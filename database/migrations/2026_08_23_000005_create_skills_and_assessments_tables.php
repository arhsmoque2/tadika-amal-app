<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('domain_category'); // e.g., "Tunjang Komunikasi (BM)", "Tunjang Kerohanian & Moral", "Perkembangan Fizikal & Estetika", "Sains Awal & Matematik"
            $table->string('code')->nullable(); // e.g., "BM 1.1", "SA 2.1"
            $table->string('name'); // e.g., "Mendengar dan memberi respons dengan bertatasusila"
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('skill_scales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g. "Menguasai", "Sedang Menguasai", "Belum Menguasai"
            $table->string('shortname'); // "TM", "SM", "BM"
            $table->unsignedTinyInteger('value'); // 1, 2, 3
            $table->string('color')->default('emerald'); // emerald, amber, rose
            $table->timestamps();
        });

        Schema::create('skill_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cohort_id')->constrained('cohorts')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained('skills')->cascadeOnDelete();
            $table->foreignId('skill_scale_id')->nullable()->constrained('skill_scales')->nullOnDelete();
            $table->string('evaluation_period')->default('Akhir Tahun'); // Penggal 1, Penggal 2, Akhir Tahun
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->date('evaluated_at')->nullable();
            $table->timestamps();

            $table->unique(['school_id', 'cohort_id', 'student_id', 'skill_id', 'evaluation_period'], 'eval_student_skill_period_unique');
        });

        Schema::create('assessment_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('cohort_id')->constrained('cohorts')->cascadeOnDelete();
            $table->string('academic_year');
            $table->string('period'); // "Penggal 1", "Penggal 2", "Tahunan"
            $table->text('teacher_comments')->nullable();
            $table->text('headmaster_comments')->nullable();
            $table->string('generated_pdf_path')->nullable();
            $table->boolean('is_finalized')->default(false);
            $table->foreignId('finalized_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_reports');
        Schema::dropIfExists('skill_evaluations');
        Schema::dropIfExists('skill_scales');
        Schema::dropIfExists('skills');
    }
};
