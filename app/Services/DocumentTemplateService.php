<?php

namespace App\Services;

use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DocumentTemplateService
{
    /**
     * Generate personalized Word document from template.
     * Note: Uses PhpOffice\PhpWord\TemplateProcessor if available, with robust fallback.
     */
    public function generateStudentRegistrationDocx(Student $student, string $templatePath = null): string
    {
        $templatePath = $templatePath ?: storage_path('templates/borang_pendaftaran_template.docx');
        $outputDir = storage_path('app/generated_docs');

        if (! file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $fileName = 'Borang_Pendaftaran_' . Str::slug($student->name) . '_' . date('Ymd_His') . '.docx';
        $outputPath = $outputDir . '/' . $fileName;

        if (class_exists(\PhpOffice\PhpWord\TemplateProcessor::class) && file_exists($templatePath)) {
            $processor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
            $processor->setValue('STUDENT_NAME', strtoupper($student->name));
            $processor->setValue('MYKID', $student->mykid ?: '-');
            $processor->setValue('GENDER', $student->gender);
            $processor->setValue('BIRTH_DATE', $student->birth_date ? Carbon::parse($student->birth_date)->format('d/m/Y') : '-');
            $processor->setValue('COHORT_NAME', $student->cohort?->name ?: '-');
            $processor->setValue('GUARDIAN_NAME', $student->guardian_name ?: '-');
            $processor->setValue('GUARDIAN_PHONE', $student->guardian_phone ?: '-');
            $processor->setValue('GUARDIAN_EMAIL', $student->guardian_email ?: '-');
            $processor->setValue('ADDRESS', $student->address ?: '-');
            $processor->setValue('EMERGENCY_CONTACT', $student->emergency_contact ?: '-');
            $processor->setValue('ALLERGIES', $student->allergies_medical ?: 'Tiada');
            $processor->setValue('PRINT_DATE', Carbon::now()->format('d/m/Y'));
            
            $processor->saveAs($outputPath);
        } else {
            // Fallback lightweight document generation / mock file
            file_put_contents($outputPath, "BORANG PENDAFTARAN TADIKA AMAL\nNama: {$student->name}\nMyKid: {$student->mykid}\nKelas: " . ($student->cohort?->name ?? '-'));
        }

        return $outputPath;
    }

    /**
     * Generate Surat Tawaran Kemasukan (Offer Letter).
     */
    public function generateOfferLetterDocx(Student $student, string $academicYear): string
    {
        $outputDir = storage_path('app/generated_docs');
        if (! file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $fileName = 'Surat_Tawaran_' . Str::slug($student->name) . '_' . date('Ymd') . '.docx';
        $outputPath = $outputDir . '/' . $fileName;

        $templatePath = storage_path('templates/surat_tawaran_template.docx');

        if (class_exists(\PhpOffice\PhpWord\TemplateProcessor::class) && file_exists($templatePath)) {
            $processor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
            $processor->setValue('DATE', Carbon::now()->locale('ms')->translatedFormat('d F Y'));
            $processor->setValue('STUDENT_NAME', strtoupper($student->name));
            $processor->setValue('MYKID', $student->mykid ?: '-');
            $processor->setValue('COHORT_NAME', $student->cohort?->name ?: '-');
            $processor->setValue('ACADEMIC_YEAR', $academicYear);
            $processor->setValue('GUARDIAN_NAME', $student->guardian_name ?: 'Ibu Bapa / Penjaga');
            $processor->setValue('SCHOOL_NAME', $student->school?->name ?: 'Tadika Amal');
            
            $processor->saveAs($outputPath);
        } else {
            file_put_contents($outputPath, "SURAT TAWARAN KEMASUKAN TADIKA AMAL ({$academicYear})\nNama Murid: {$student->name}\nKelas: " . ($student->cohort?->name ?? '-'));
        }

        return $outputPath;
    }
}
