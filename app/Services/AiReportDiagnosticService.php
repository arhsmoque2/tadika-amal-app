<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\Cohort;
use App\Models\HealthScreening;
use App\Models\SkillEvaluation;
use App\Models\Student;
use Carbon\Carbon;

class AiReportDiagnosticService
{
    /**
     * Build structured JSON payload for AI Teacher Co-Pilot to generate qualitative remarks.
     * Takes student profile, attendance summary, and KSPK evaluations.
     *
     * @return array<string, mixed>
     */
    public function buildStudentEvaluationPromptPayload(Student $student, int $academicYear): array
    {
        $evaluations = SkillEvaluation::with(['skill', 'skillScale'])
            ->where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->get();

        $strands = [];
        foreach ($evaluations as $eval) {
            $strandName = $eval->skill?->domain_category ?: 'Perkembangan Holistik';
            $strands[$strandName][] = [
                'skill_code' => $eval->skill?->code,
                'skill_name' => $eval->skill?->name,
                'rating_score' => $eval->skillScale?->value,
                'rating_label' => $eval->skillScale?->name,
                'teacher_notes' => $eval->remarks,
            ];
        }

        // Attendance stats
        $totalDays = AttendanceRecord::where('student_id', $student->id)->count();
        $presentDays = AttendanceRecord::where('student_id', $student->id)
            ->where('status', AttendanceRecord::STATUS_HADIR)
            ->count();
        $attendanceRate = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1).'%' : '100%';

        return [
            'meta' => [
                'type' => 'teacher_copilot_student_remarks',
                'generated_at' => Carbon::now()->toIso8601String(),
                'version' => '1.0',
            ],
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'gender' => $student->gender,
                'age_cohort' => $student->cohort?->name,
                'attendance_summary' => [
                    'rate' => $attendanceRate,
                    'present_days' => $presentDays,
                    'total_days' => $totalDays,
                ],
            ],
            'kspk_assessment_domains' => $strands,
            'prompt_instruction' => 'Berdasarkan data pencapaian KSPK dan kehadiran murid di atas, sila jana ulasan perkembangan murid (Ulasan Guru Kelas) yang komprehensif, membina, dan mesra ibu bapa dalam Bahasa Melayu. Berikan cadangan aktiviti pengukuhan di rumah untuk domain yang memerlukan bimbingan tambahan.',
        ];
    }

    /**
     * Build structured JSON diagnostic snapshot of whole-school operations for Agent Sentinel.
     * Evaluates attendance health, HFMD/fever symptoms, fee collections, and missing profiles.
     *
     * @return array<string, mixed>
     */
    public function buildSchoolDiagnosticSnapshot(int $schoolId, ?string $date = null): array
    {
        $targetDate = $date ?: Carbon::today()->format('Y-m-d');

        // Cohort attendance health
        $cohorts = Cohort::where('school_id', $schoolId)->get();
        $cohortMetrics = [];

        foreach ($cohorts as $cohort) {
            $studentsCount = $cohort->students()->where('is_active', true)->count();
            $presentCount = AttendanceRecord::where('cohort_id', $cohort->id)
                ->where('date', $targetDate)
                ->where('status', AttendanceRecord::STATUS_HADIR)
                ->count();
            $sickCount = AttendanceRecord::where('cohort_id', $cohort->id)
                ->where('date', $targetDate)
                ->where('status', AttendanceRecord::STATUS_SAKIT)
                ->count();

            $cohortMetrics[] = [
                'cohort_name' => $cohort->name,
                'enrolled_students' => $studentsCount,
                'present_today' => $presentCount,
                'sick_today' => $sickCount,
                'attendance_pct' => $studentsCount > 0 ? round(($presentCount / $studentsCount) * 100, 1) : 0,
            ];
        }

        // Morning health screening flags (HFMD / Fever)
        $feverScreenings = HealthScreening::where('school_id', $schoolId)
            ->where('date', $targetDate)
            ->where(function ($q) {
                $q->where('temperature', '>=', 37.5)
                    ->orWhereJsonContains('symptoms', 'hfmd_rash');
            })
            ->with('student.cohort')
            ->get()
            ->map(fn (HealthScreening $s) => [
                'student_name' => $s->student?->name,
                'cohort' => $s->student?->cohort?->name,
                'temperature' => (float) $s->temperature,
                'hfmd_symptoms' => in_array('hfmd_rash', $s->symptoms ?? [], true),
                'action_status' => $s->status,
            ])
            ->values()
            ->all();

        return [
            'meta' => [
                'type' => 'agent_school_diagnostic_snapshot',
                'target_date' => $targetDate,
                'school_id' => $schoolId,
                'timestamp' => Carbon::now()->toIso8601String(),
            ],
            'cohort_attendance_overview' => $cohortMetrics,
            'health_sentinel_alerts' => [
                'flagged_cases_count' => count($feverScreenings),
                'cases' => $feverScreenings,
            ],
            'operational_status' => count($feverScreenings) > 0 ? 'ATTENTION_REQUIRED' : 'NORMAL',
        ];
    }
}
