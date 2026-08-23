<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Morning Health & Gate Screening
        Schema::create('health_screenings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cohort_id')->constrained('cohorts')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('date');
            $table->decimal('temperature', 4, 1)->default(36.5); // e.g. 36.8, 37.5
            $table->json('symptoms')->nullable(); // ['hfmd_rash', 'fever', 'cough', 'flu', 'red_eyes']
            $table->enum('status', ['lulus', 'kuarantin', 'ibu_bapa_dihubungi'])->default('lulus');
            $table->text('remarks')->nullable();
            $table->foreignId('screened_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['school_id', 'cohort_id', 'student_id', 'date'], 'health_screening_daily_unique');
        });

        // 2. Drop-off & Pick-up Security Log
        Schema::create('pickup_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('cohort_id')->constrained('cohorts')->cascadeOnDelete();
            $table->date('date');
            $table->time('pickup_time');
            $table->enum('guardian_type', ['ibu', 'bapa', 'pemandu_van', 'penjaga_sah', 'runner_wakil'])->default('ibu');
            $table->string('guardian_name');
            $table->string('guardian_phone')->nullable();
            $table->boolean('is_late_pickup')->default(false);
            $table->unsignedInteger('late_minutes')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('logged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 3. Fee Invoices & Payments (with LHDN Tax Relief Receipts)
        Schema::create('fee_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('cohort_id')->constrained('cohorts')->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->string('receipt_number')->nullable()->unique();
            $table->string('academic_year'); // e.g. "2026"
            $table->string('month'); // e.g. "Januari", "Februari"
            $table->enum('fee_category', ['yuran_bulanan', 'yuran_pendaftaran', 'yuran_transit', 'yuran_makan_tuisyen'])->default('yuran_bulanan');
            $table->decimal('amount', 8, 2);
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
            $table->enum('payment_method', ['fpx_online', 'bank_transfer', 'cash', 'cheque'])->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('receipt_pdf_path')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 4. Incident & Minor Injury Log (JKM Compliance)
        Schema::create('incident_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('cohort_id')->constrained('cohorts')->cascadeOnDelete();
            $table->date('incident_date');
            $table->time('incident_time');
            $table->string('location'); // Taman Permainan, Bilik Darjah, Tandas, Ruang Makan
            $table->enum('severity', ['ringan', 'sederhana', 'perlu_hospital'])->default('ringan');
            $table->text('incident_description');
            $table->text('injury_details'); // Calar lutut, lebam dahi, luka kecil
            $table->text('first_aid_given'); // Dicuci dengan antiseptik, tuam ais, plaster
            $table->foreignId('witness_teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->boolean('parent_notified')->default(true);
            $table->timestamp('parent_notified_at')->nullable();
            $table->text('followup_actions')->nullable();
            $table->timestamps();
        });

        // 5. Student Moments & Activity Portfolio Gallery
        Schema::create('student_moments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cohort_id')->constrained('cohorts')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('photo_path')->nullable();
            $table->enum('category', ['amali_solat', 'hafazan_iqra', 'sains_kraf', 'sukan_fizikal', 'sosioemosi'])->default('sains_kraf');
            $table->date('activity_date');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_moments');
        Schema::dropIfExists('incident_logs');
        Schema::dropIfExists('fee_invoices');
        Schema::dropIfExists('pickup_logs');
        Schema::dropIfExists('health_screenings');
    }
};
