<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Announcements & WhatsApp Broadcasts
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cohort_id')->nullable()->constrained('cohorts')->nullOnDelete();
            $table->string('title');
            $table->text('content');
            $table->enum('category', ['urgent', 'event', 'general', 'holiday'])->default('general');
            $table->enum('target_audience', ['all', 'parents', 'teachers', 'cohort'])->default('all');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Lesson Plans (Rancangan Pengajaran Harian - RPH)
        Schema::create('lesson_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cohort_id')->constrained('cohorts')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->unsignedSmallInteger('week_number'); // Minggu 1, 2, ... 42
            $table->string('theme'); // e.g., "Diri Saya", "Anggota Badan", "Amali Wuduk"
            $table->string('tunjang_kspk'); // Tunjang Komunikasi, Kerohanian, etc.
            $table->text('learning_objectives');
            $table->text('activities');
            $table->text('materials_needed')->nullable();
            $table->text('reflection_notes')->nullable();
            $table->date('plan_date');
            $table->timestamps();
        });

        // 3. Teacher Personal Tasks & Checklists
        Schema::create('teacher_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->string('title');
            $table->text('notes')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // 4. Teacher Leaves & Substitute Teachers (Guru Ganti)
        Schema::create('teacher_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('substitute_teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->enum('leave_type', ['annual', 'medical', 'emergency', 'maternity', 'unpaid'])->default('medical');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason');
            $table->string('attachment_path')->nullable(); // Medical certificate (MC)
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_leaves');
        Schema::dropIfExists('teacher_tasks');
        Schema::dropIfExists('lesson_plans');
        Schema::dropIfExists('announcements');
    }
};
