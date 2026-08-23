<?php

namespace App\Services;

use App\Models\HealthScreening;
use App\Models\School;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PosterDocumentService
{
    /**
     * Render a high-impact Daily Morning Health Bulletin poster (HTML).
     */
    public function renderHealthBulletinPosterHtml(School $school, string $date = null): string
    {
        $targetDate = $date ?: Carbon::today()->format('Y-m-d');

        $screenings = HealthScreening::with('student.cohort')
            ->where('screening_date', $targetDate)
            ->get();

        $totalChecked = $screenings->count();
        $normalCount = $screenings->where('temperature', '<', 37.5)->where('has_hfmd_symptoms', false)->count();
        $flaggedCount = $screenings->where('temperature', '>=', 37.5)->count();

        return view('reports.poster-health-bulletin', [
            'school' => $school,
            'date' => Carbon::parse($targetDate)->locale('ms')->translatedFormat('l, d F Y'),
            'totalChecked' => $totalChecked,
            'normalCount' => $normalCount,
            'flaggedCount' => $flaggedCount,
            'screenings' => $screenings,
        ])->render();
    }

    /**
     * Render an official Student Certificate of Achievement / Sijil Penghargaan (HTML).
     */
    public function renderStudentCertificateHtml(Student $student, string $awardTitle, string $academicYear): string
    {
        return view('reports.certificate-award', [
            'student' => $student,
            'awardTitle' => $awardTitle,
            'academicYear' => $academicYear,
            'school' => $student->school,
            'issueDate' => Carbon::now()->locale('ms')->translatedFormat('d F Y'),
        ])->render();
    }

    /**
     * Export the poster HTML to PDF via mPDF.
     */
    public function exportPosterToPdf(string $htmlContent, string $fileName, string $orientation = 'P'): string
    {
        $outputDir = storage_path('app/posters');
        if (! file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $outputPath = $outputDir . '/' . Str::slug($fileName) . '_' . date('Ymd_His') . '.pdf';

        if (class_exists(\Mpdf\Mpdf::class)) {
            $mpdf = new \Mpdf\Mpdf([
                'format' => 'A4',
                'orientation' => $orientation,
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
            ]);

            $mpdf->WriteHTML($htmlContent);
            $mpdf->Output($outputPath, \Mpdf\Output\Destination::FILE);
        } else {
            file_put_contents($outputPath, $htmlContent);
        }

        return $outputPath;
    }
}
