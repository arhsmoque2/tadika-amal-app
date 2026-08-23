<?php

namespace App\Filament\App\Pages;

use App\Models\AssessmentReport;
use App\Models\Cohort;
use App\Models\Skill;
use App\Models\SkillEvaluation;
use App\Models\SkillScale;
use App\Models\Student;
use App\Services\AssessmentReportPdfService;
use BackedEnum;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SkillEvaluationPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationLabel = 'Penilaian Perkembangan Murid';
    protected static ?string $title = 'Matriks Penilaian Perkembangan Prasekolah (KSPK)';
    protected static ?int $navigationSort = 4;
    protected string $view = 'filament.pages.skill-evaluation';

    public ?int $selectedCohortId = null;
    public string $selectedPeriod = 'Akhir Tahun';
    public array $cohortOptions = [];
    public array $periodOptions = [
        'Penggal 1' => 'Penggal 1 (Pertengahan Tahun)',
        'Penggal 2' => 'Penggal 2',
        'Akhir Tahun' => 'Penilaian Akhir Tahun (KSPK)',
    ];

    public array $students = [];
    public array $skills = [];
    public array $scales = [];
    public array $evaluations = []; // [ "studentId_skillId" => scale_id ]
    public array $remarks = []; // [ "studentId_skillId" => string ]

    public function mount(): void
    {
        $cohorts = Cohort::where('is_active', true)->get();
        $this->cohortOptions = $cohorts->pluck('name', 'id')->toArray();

        if (! empty($this->cohortOptions)) {
            $this->selectedCohortId = array_key_first($this->cohortOptions);
        }

        $this->loadScales();
        $this->loadMatrixData();
    }

    public function updatedSelectedCohortId(): void
    {
        $this->loadMatrixData();
    }

    public function updatedSelectedPeriod(): void
    {
        $this->loadMatrixData();
    }

    public function loadScales(): void
    {
        $this->scales = SkillScale::orderBy('value', 'desc')->get()->toArray();
        if (empty($this->scales)) {
            // Seed default scales if empty
            $this->scales = [
                ['id' => 3, 'name' => 'Menguasai', 'shortname' => 'TM', 'value' => 3, 'color' => 'emerald'],
                ['id' => 2, 'name' => 'Sedang Menguasai', 'shortname' => 'SM', 'value' => 2, 'color' => 'amber'],
                ['id' => 1, 'name' => 'Belum Menguasai', 'shortname' => 'BM', 'value' => 1, 'color' => 'rose'],
            ];
        }
    }

    public function loadMatrixData(): void
    {
        if (! $this->selectedCohortId) {
            return;
        }

        $cohort = Cohort::find($this->selectedCohortId);
        if (! $cohort) {
            return;
        }

        $this->students = Student::where('cohort_id', $this->selectedCohortId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->toArray();

        $this->skills = Skill::where('school_id', $cohort->school_id)
            ->orderBy('domain_category')
            ->orderBy('sort_order')
            ->get()
            ->toArray();

        // Load existing evaluations
        $evalRecords = SkillEvaluation::where('cohort_id', $this->selectedCohortId)
            ->where('evaluation_period', $this->selectedPeriod)
            ->get();

        $this->evaluations = [];
        $this->remarks = [];

        foreach ($evalRecords as $rec) {
            $key = $rec->student_id . '_' . $rec->skill_id;
            $this->evaluations[$key] = $rec->skill_scale_id;
            $this->remarks[$key] = $rec->remarks ?? '';
        }
    }

    public function setEvaluation(int $studentId, int $skillId, int $scaleId): void
    {
        $key = $studentId . '_' . $skillId;
        $this->evaluations[$key] = $scaleId;
    }

    public function saveEvaluations(): void
    {
        if (! $this->selectedCohortId) {
            return;
        }

        $cohort = Cohort::find($this->selectedCohortId);
        if (! $cohort) {
            return;
        }

        $userId = Auth::id();
        $today = Carbon::now()->format('Y-m-d');

        foreach ($this->evaluations as $key => $scaleId) {
            if (! $scaleId) {
                continue;
            }

            [$studentId, $skillId] = explode('_', $key);

            SkillEvaluation::updateOrCreate(
                [
                    'school_id' => $cohort->school_id,
                    'cohort_id' => $cohort->id,
                    'student_id' => (int) $studentId,
                    'skill_id' => (int) $skillId,
                    'evaluation_period' => $this->selectedPeriod,
                ],
                [
                    'skill_scale_id' => (int) $scaleId,
                    'remarks' => $this->remarks[$key] ?? null,
                    'evaluated_by' => $userId,
                    'evaluated_at' => $today,
                ]
            );
        }

        Notification::make()
            ->title('Semua penilaian berjaya disimpan!')
            ->success()
            ->send();
    }

    public function generateStudentPdf(int $studentId): BinaryFileResponse
    {
        $student = Student::findOrFail($studentId);
        $cohort = Cohort::findOrFail($this->selectedCohortId);

        $pdfService = new AssessmentReportPdfService();
        $pdfPath = $pdfService->generateReportPdf($student, $this->selectedPeriod, $cohort->academic_year);

        return response()->download($pdfPath);
    }
}
