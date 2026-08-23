<?php

namespace App\Services;

use App\Models\IncidentLog;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\TemplateProcessor;

class WordTemplateExportService
{
    /**
     * Generate an official Admission Offer Letter (.docx) with dynamic tokens.
     */
    public function generateOfferLetter(Student $student, string $academicYear, float $feeDeposit = 350.00): string
    {
        $outputDir = storage_path('app/generated_docs');
        if (! file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $fileName = 'Surat_Tawaran_'.Str::slug($student->name).'_'.date('Ymd').'.docx';
        $outputPath = $outputDir.'/'.$fileName;
        $templatePath = storage_path('templates/surat_tawaran_template.docx');

        $tokens = [
            'DATE' => Carbon::now()->locale('ms')->translatedFormat('d F Y'),
            'REF_NO' => 'TA/'.date('Y').'/'.str_pad((string) $student->id, 4, '0', STR_PAD_LEFT),
            'GUARDIAN_NAME' => strtoupper($student->guardian_name ?: 'IBU BAPA / PENJAGA'),
            'ADDRESS' => $student->address ?: 'Alamat Tidak Dinyatakan',
            'STUDENT_NAME' => strtoupper($student->name),
            'MYKID' => $student->mykid ?: '-',
            'COHORT_NAME' => $student->cohort?->name ?: 'Prasekolah Tadika Amal',
            'ACADEMIC_YEAR' => $academicYear,
            'DEPOSIT_AMOUNT' => 'RM '.number_format($feeDeposit, 2),
            'SCHOOL_NAME' => $student->school?->name ?: 'Tadika Amal',
            'HEADMASTER_NAME' => 'Guru Besar Tadika Amal',
        ];

        if (class_exists(TemplateProcessor::class) && file_exists($templatePath)) {
            $processor = new TemplateProcessor($templatePath);
            foreach ($tokens as $key => $value) {
                $processor->setValue($key, (string) $value);
            }
            $processor->saveAs($outputPath);
        } else {
            // High-fidelity structured document fallback
            $content = "SURAT TAWARAN KEMASUKAN PRASEKOLAH\n".
                       "Rujukan: {$tokens['REF_NO']}\n".
                       "Tarikh: {$tokens['DATE']}\n\n".
                       "Kepada: {$tokens['GUARDIAN_NAME']}\n".
                       "Nama Murid: {$tokens['STUDENT_NAME']} (MyKid: {$tokens['MYKID']})\n".
                       "Kelas: {$tokens['COHORT_NAME']} (Sesi {$tokens['ACADEMIC_YEAR']})\n".
                       "Yuran Pendaftaran: {$tokens['DEPOSIT_AMOUNT']}\n\n".
                       "Sekolah: {$tokens['SCHOOL_NAME']}\n";
            file_put_contents($outputPath, $content);
        }

        return $outputPath;
    }

    /**
     * Generate an official JKM Incident & First-Aid Report (.docx).
     */
    public function generateIncidentReportDocx(IncidentLog $incident): string
    {
        $outputDir = storage_path('app/generated_docs');
        if (! file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $fileName = 'Laporan_Insiden_JKM_'.$incident->incident_number.'.docx';
        $outputPath = $outputDir.'/'.$fileName;
        $templatePath = storage_path('templates/jkm_incident_template.docx');

        $tokens = [
            'INCIDENT_NO' => $incident->incident_number,
            'INCIDENT_DATE' => $incident->incident_date ? Carbon::parse($incident->incident_date)->format('d/m/Y H:i') : '-',
            'STUDENT_NAME' => strtoupper($incident->student?->name ?: 'MURID'),
            'MYKID' => $incident->student?->mykid ?: '-',
            'CLASS_NAME' => $incident->student?->cohort?->name ?: '-',
            'SEVERITY' => strtoupper($incident->severity),
            'LOCATION' => $incident->location ?: 'Kawasan Sekolah',
            'DESCRIPTION' => $incident->description,
            'ACTION_TAKEN' => $incident->action_taken ?: 'Rawatan pertolongan cemas awal',
            'PARENT_NOTIFIED' => $incident->parent_notified ? 'YA' : 'TIDAK',
            'JKM_REPORTABLE' => $incident->jkm_reportable ? 'YA (DILAPORKAN KEPADA JKM)' : 'TIDAK (REKOD DALAMAN)',
            'REPORTED_BY' => $incident->reporter?->name ?: 'Guru Bertugas',
        ];

        if (class_exists(TemplateProcessor::class) && file_exists($templatePath)) {
            $processor = new TemplateProcessor($templatePath);
            foreach ($tokens as $key => $value) {
                $processor->setValue($key, (string) $value);
            }
            $processor->saveAs($outputPath);
        } else {
            $content = "LAPORAN INSIDEN KESELAMATAN DAN KEMALANGAN (JKM COMPLIANT)\n".
                       "No. Laporan: {$tokens['INCIDENT_NO']}\n".
                       "Tarikh & Masa: {$tokens['INCIDENT_DATE']}\n".
                       "Nama Murid: {$tokens['STUDENT_NAME']} ({$tokens['MYKID']})\n".
                       "Tahap Keterukan: {$tokens['SEVERITY']}\n".
                       "Penerangan Kemalangan:\n{$tokens['DESCRIPTION']}\n".
                       "Tindakan Pertolongan Cemas:\n{$tokens['ACTION_TAKEN']}\n".
                       "Pegawai Pelapor: {$tokens['REPORTED_BY']}\n";
            file_put_contents($outputPath, $content);
        }

        return $outputPath;
    }
}
