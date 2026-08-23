<?php

namespace App\Filament\App\Pages;

use App\Models\Cohort;
use App\Models\HealthScreening;
use App\Models\Student;
use BackedEnum;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class MorningHealthScreening extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Saringan Kesihatan Pagi';
    protected static ?string $title = 'Saringan Kesihatan Pagi (Pencegahan HFMD & Demam)';
    protected static ?int $navigationSort = 6;
    protected string $view = 'filament.pages.morning-health-screening';

    public ?int $selectedCohortId = null;
    public string $selectedDate = '';
    public array $screeningData = []; // [studentId => ['temp' => 36.5, 'symptoms' => [], 'status' => 'lulus', 'remarks' => '']]
    public array $students = [];
    public array $cohortOptions = [];

    public function mount(): void
    {
        $this->selectedDate = Carbon::now()->format('Y-m-d');
        $this->cohortOptions = Cohort::where('is_active', true)->pluck('name', 'id')->toArray();

        if (! empty($this->cohortOptions)) {
            $this->selectedCohortId = array_key_first($this->cohortOptions);
            $this->loadScreeningGrid();
        }
    }

    public function updatedSelectedCohortId(): void
    {
        $this->loadScreeningGrid();
    }

    public function updatedSelectedDate(): void
    {
        $this->loadScreeningGrid();
    }

    public function loadScreeningGrid(): void
    {
        if (! $this->selectedCohortId) {
            return;
        }

        $studentsList = Student::where('cohort_id', $this->selectedCohortId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $this->students = $studentsList->toArray();

        $existingRecords = HealthScreening::where('cohort_id', $this->selectedCohortId)
            ->where('date', $this->selectedDate)
            ->get()
            ->keyBy('student_id');

        $this->screeningData = [];
        foreach ($studentsList as $student) {
            $record = $existingRecords->get($student->id);
            $this->screeningData[$student->id] = [
                'temp' => $record ? (float) $record->temperature : 36.5,
                'symptoms' => $record && $record->symptoms ? $record->symptoms : [],
                'status' => $record ? $record->status : 'lulus',
                'remarks' => $record ? ($record->remarks ?? '') : '',
            ];
        }
    }

    public function toggleSymptom(int $studentId, string $symptom): void
    {
        if (! isset($this->screeningData[$studentId])) {
            return;
        }

        $symptoms = $this->screeningData[$studentId]['symptoms'] ?? [];
        if (in_array($symptom, $symptoms)) {
            $this->screeningData[$studentId]['symptoms'] = array_values(array_diff($symptoms, [$symptom]));
        } else {
            $this->screeningData[$studentId]['symptoms'][] = $symptom;
        }

        // Auto-flag quarantine if HFMD symptoms selected or temp >= 37.5
        if (in_array('hfmd_rash', $this->screeningData[$studentId]['symptoms']) || $this->screeningData[$studentId]['temp'] >= 37.5) {
            $this->screeningData[$studentId]['status'] = 'kuarantin';
        }
    }

    public function saveAllScreenings(): void
    {
        if (! $this->selectedCohortId) {
            return;
        }

        $cohort = Cohort::find($this->selectedCohortId);
        if (! $cohort) {
            return;
        }

        $userId = Auth::id();

        foreach ($this->screeningData as $studentId => $data) {
            HealthScreening::updateOrCreate(
                [
                    'school_id' => $cohort->school_id,
                    'cohort_id' => $cohort->id,
                    'student_id' => $studentId,
                    'date' => $this->selectedDate,
                ],
                [
                    'temperature' => $data['temp'],
                    'symptoms' => $data['symptoms'],
                    'status' => $data['status'],
                    'remarks' => $data['remarks'] ?: null,
                    'screened_by' => $userId,
                ]
            );
        }

        Notification::make()
            ->title('Rekod saringan kesihatan pagi berjaya disimpan!')
            ->success()
            ->send();
    }
}
