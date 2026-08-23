<?php

namespace App\Filament\App\Pages;

use App\Models\Cohort;
use App\Models\IncidentLog;
use App\Models\Student;
use App\Models\Teacher;
use BackedEnum;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class IncidentLogPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-circle';
    protected static ?string $navigationLabel = 'Buku Log Kemalangan (JKM)';
    protected static ?string $title = 'Buku Rekod Kemalangan & Rawatan Awal (Pematuhan JKM)';
    protected static ?int $navigationSort = 9;
    protected string $view = 'filament.pages.incident-log-page';

    public ?int $selectedCohortId = null;
    public ?int $selectedStudentId = null;
    public string $incident_date = '';
    public string $incident_time = '';
    public string $location = 'Taman Permainan';
    public string $severity = 'ringan';
    public string $incident_description = '';
    public string $injury_details = '';
    public string $first_aid_given = '';
    public bool $parent_notified = true;

    public array $incidentLogs = [];
    public array $cohortOptions = [];
    public array $studentOptions = [];

    public function mount(): void
    {
        $this->incident_date = Carbon::now()->format('Y-m-d');
        $this->incident_time = Carbon::now()->format('H:i');
        $this->cohortOptions = Cohort::where('is_active', true)->pluck('name', 'id')->toArray();

        if (! empty($this->cohortOptions)) {
            $this->selectedCohortId = array_key_first($this->cohortOptions);
            $this->loadStudents();
        }

        $this->loadIncidentLogs();
    }

    public function updatedSelectedCohortId(): void
    {
        $this->loadStudents();
    }

    public function loadStudents(): void
    {
        if ($this->selectedCohortId) {
            $this->studentOptions = Student::where('cohort_id', $this->selectedCohortId)
                ->where('is_active', true)
                ->pluck('name', 'id')
                ->toArray();
            $this->selectedStudentId = ! empty($this->studentOptions) ? array_key_first($this->studentOptions) : null;
        }
    }

    public function loadIncidentLogs(): void
    {
        $this->incidentLogs = IncidentLog::with(['student', 'cohort', 'witnessTeacher'])
            ->orderBy('incident_date', 'desc')
            ->orderBy('incident_time', 'desc')
            ->limit(20)
            ->get()
            ->toArray();
    }

    public function saveIncident(): void
    {
        if (! $this->selectedStudentId || empty($this->incident_description) || empty($this->injury_details)) {
            Notification::make()
                ->title('Sila lengkapkan butiran murid, penerangan kejadian, dan jenis kecederaan')
                ->danger()
                ->send();
            return;
        }

        $student = Student::find($this->selectedStudentId);
        $cohort = Cohort::find($this->selectedCohortId);
        $teacher = Teacher::where('user_id', Auth::id())->first() ?? Teacher::first();

        IncidentLog::create([
            'school_id' => $cohort->school_id,
            'cohort_id' => $cohort->id,
            'student_id' => $student->id,
            'incident_date' => $this->incident_date,
            'incident_time' => $this->incident_time,
            'location' => $this->location,
            'severity' => $this->severity,
            'incident_description' => $this->incident_description,
            'injury_details' => $this->injury_details,
            'first_aid_given' => $this->first_aid_given,
            'witness_teacher_id' => $teacher?->id,
            'parent_notified' => $this->parent_notified,
            'parent_notified_at' => $this->parent_notified ? now() : null,
        ]);

        $this->reset(['incident_description', 'injury_details', 'first_aid_given']);
        $this->loadIncidentLogs();

        Notification::make()
            ->title('Rekod insiden berjaya didaftarkan ke dalam Buku Log JKM!')
            ->success()
            ->send();
    }
}
