<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\Cohort;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AttendanceSpreadsheetService
{
    /**
     * Export attendance matrix for a cohort within a given date range as CSV.
     */
    public function exportCohortAttendanceCsv(Cohort $cohort, string $startDate, string $endDate): string
    {
        $outputDir = storage_path('app/attendance_exports');
        if (! file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $fileName = 'Kehadiran_' . str_replace(' ', '_', $cohort->name) . '_' . $startDate . '_to_' . $endDate . '.csv';
        $outputPath = $outputDir . '/' . $fileName;

        $period = CarbonPeriod::create($startDate, $endDate);
        $dates = [];
        foreach ($period as $date) {
            // Filter only weekdays (Monday - Friday)
            if ($date->isWeekday()) {
                $dates[] = $date->format('Y-m-d');
            }
        }

        $students = $cohort->students()->where('is_active', true)->orderBy('name')->get();
        $records = AttendanceRecord::where('cohort_id', $cohort->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy(fn ($r) => $r->student_id . '_' . $r->date->format('Y-m-d'));

        $handle = fopen($outputPath, 'w');
        // UTF-8 BOM for Excel compatibility
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

        // Header Row
        $header = array_merge(['Bil', 'Nama Murid', 'MyKid'], array_map(fn ($d) => Carbon::parse($d)->format('d/m (D)'), $dates), ['Jumlah Hadir', 'Jumlah Hari', 'Peratus (%)']);
        fputcsv($handle, $header);

        $bil = 1;
        foreach ($students as $student) {
            $row = [
                $bil++,
                $student->name,
                $student->mykid ?: '-',
            ];

            $presentCount = 0;
            $totalDays = count($dates);

            foreach ($dates as $date) {
                $key = $student->id . '_' . $date;
                $record = $records->get($key)?->first();
                $status = $record ? $record->status_badge['code'] : '-';
                
                if ($record && $record->status === AttendanceRecord::STATUS_HADIR) {
                    $presentCount++;
                }

                $row[] = $status;
            }

            $percentage = $totalDays > 0 ? round(($presentCount / $totalDays) * 100, 1) : 0;
            $row[] = $presentCount;
            $row[] = $totalDays;
            $row[] = $percentage . '%';

            fputcsv($handle, $row);
        }

        fclose($handle);
        return $outputPath;
    }
}
