<?php

namespace App\Filament\App\Pages;

use App\Models\Cohort;
use App\Models\Teacher;
use App\Models\TimetableSlot;
use Filament\Pages\Page;

class ClassTimetable extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Jadual Waktu Kelas & Guru';
    protected static ?string $title = 'Jadual Waktu Prasekolah';
    protected static ?int $navigationSort = 3;
    protected string $view = 'filament.pages.class-timetable';

    public string $viewMode = 'cohort'; // 'cohort' or 'teacher'
    public ?int $selectedCohortId = null;
    public ?int $selectedTeacherId = null;
    public array $cohortOptions = [];
    public array $teacherOptions = [];
    public array $timetableGrid = []; // [day_of_week => [slots]]

    public array $days = [
        1 => 'Isnin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Khamis',
        5 => 'Jumaat',
    ];

    public function mount(): void
    {
        $this->cohortOptions = Cohort::where('is_active', true)->pluck('name', 'id')->toArray();
        $this->teacherOptions = Teacher::where('is_active', true)->pluck('full_name', 'id')->toArray();

        if (! empty($this->cohortOptions)) {
            $this->selectedCohortId = array_key_first($this->cohortOptions);
        }
        if (! empty($this->teacherOptions)) {
            $this->selectedTeacherId = array_key_first($this->teacherOptions);
        }

        $this->loadTimetable();
    }

    public function updatedViewMode(): void
    {
        $this->loadTimetable();
    }

    public function updatedSelectedCohortId(): void
    {
        $this->loadTimetable();
    }

    public function updatedSelectedTeacherId(): void
    {
        $this->loadTimetable();
    }

    public function loadTimetable(): void
    {
        $query = TimetableSlot::with(['cohort', 'teacher', 'room']);

        if ($this->viewMode === 'cohort' && $this->selectedCohortId) {
            $query->where('cohort_id', $this->selectedCohortId);
        } elseif ($this->viewMode === 'teacher' && $this->selectedTeacherId) {
            $query->where('teacher_id', $this->selectedTeacherId);
        }

        $slots = $query->orderBy('day_of_week')->orderBy('start_time')->get();

        $this->timetableGrid = [];
        foreach ($this->days as $dayNum => $dayName) {
            $this->timetableGrid[$dayNum] = $slots->where('day_of_week', $dayNum)->values()->toArray();
        }
    }
}
