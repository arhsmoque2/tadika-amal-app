<?php

namespace App\Filament\App\Pages;

use App\Models\AttendanceRecord;
use App\Models\Cohort;
use App\Models\Student;
use App\Services\AttendanceSpreadsheetService;
use BackedEnum;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CohortAttendance extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Rekod Kehadiran Harian';

    protected static ?string $title = 'Rekod Kehadiran Murid (Matriks)';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.cohort-attendance';

    public ?int $selectedCohortId = null;

    public string $selectedDate = '';

    public array $attendanceData = []; // [student_id => ['status' => 'hadir', 'reason' => '']]

    public array $students = [];

    public array $cohortOptions = [];

    public function mount(): void
    {
        $this->selectedDate = Carbon::now()->format('Y-m-d');
        $cohorts = Cohort::where('is_active', true)->get();
        $this->cohortOptions = $cohorts->pluck('name', 'id')->toArray();

        if (! empty($this->cohortOptions)) {
            $this->selectedCohortId = array_key_first($this->cohortOptions);
            $this->loadAttendanceGrid();
        }
    }

    public function updatedSelectedCohortId(): void
    {
        $this->loadAttendanceGrid();
    }

    public function updatedSelectedDate(): void
    {
        $this->loadAttendanceGrid();
    }

    public function loadAttendanceGrid(): void
    {
        if (! $this->selectedCohortId) {
            $this->students = [];
            $this->attendanceData = [];

            return;
        }

        $cohort = Cohort::find($this->selectedCohortId);
        if (! $cohort) {
            return;
        }

        $studentsList = Student::where('cohort_id', $this->selectedCohortId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $this->students = $studentsList->toArray();

        // Load existing attendance for date
        $existingRecords = AttendanceRecord::where('cohort_id', $this->selectedCohortId)
            ->where('date', $this->selectedDate)
            ->get()
            ->keyBy('student_id');

        $this->attendanceData = [];
        foreach ($studentsList as $student) {
            $record = $existingRecords->get($student->id);
            $this->attendanceData[$student->id] = [
                'status' => $record ? $record->status : AttendanceRecord::STATUS_HADIR,
                'reason' => $record ? ($record->reason ?? '') : '',
            ];
        }
    }

    public function setStatus(int $studentId, string $status): void
    {
        if (isset($this->attendanceData[$studentId])) {
            $this->attendanceData[$studentId]['status'] = $status;
        }
    }

    public function markAllPresent(): void
    {
        foreach ($this->attendanceData as $studentId => $data) {
            $this->attendanceData[$studentId]['status'] = AttendanceRecord::STATUS_HADIR;
        }

        Notification::make()
            ->title('Semua murid ditanda Hadir')
            ->success()
            ->send();
    }

    public function saveAttendance(): void
    {
        if (! $this->selectedCohortId || empty($this->selectedDate)) {
            return;
        }

        $cohort = Cohort::find($this->selectedCohortId);
        if (! $cohort) {
            return;
        }

        $userId = Auth::id();

        foreach ($this->attendanceData as $studentId => $data) {
            AttendanceRecord::updateOrCreate(
                [
                    'school_id' => $cohort->school_id,
                    'cohort_id' => $cohort->id,
                    'student_id' => $studentId,
                    'date' => $this->selectedDate,
                ],
                [
                    'status' => $data['status'],
                    'reason' => ! empty($data['reason']) ? $data['reason'] : null,
                    'recorded_by' => $userId,
                ]
            );
        }

        Notification::make()
            ->title('Rekod kehadiran berjaya disimpan!')
            ->body('Tarikh: '.Carbon::parse($this->selectedDate)->format('d/m/Y'))
            ->success()
            ->send();
    }

    public function exportCsv(): BinaryFileResponse
    {
        $cohort = Cohort::findOrFail($this->selectedCohortId);
        $startOfMonth = Carbon::parse($this->selectedDate)->startOfMonth()->format('Y-m-d');
        $endOfMonth = Carbon::parse($this->selectedDate)->endOfMonth()->format('Y-m-d');

        $exporter = new AttendanceSpreadsheetService;
        $filePath = $exporter->exportCohortAttendanceCsv($cohort, $startOfMonth, $endOfMonth);

        return response()->download($filePath);
    }
}
