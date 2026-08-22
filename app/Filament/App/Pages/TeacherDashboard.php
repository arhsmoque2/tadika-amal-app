<?php

namespace App\Filament\App\Pages;

use App\Models\AttendanceRecord;
use App\Models\Cohort;
use App\Models\Teacher;
use App\Models\TimetableSlot;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class TeacherDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Papan Pemuka Guru';
    protected static ?string $title = 'Papan Pemuka Guru & Kelas';
    protected static ?int $navigationSort = 1;
    protected string $view = 'filament.pages.teacher-dashboard';

    public ?Teacher $teacher = null;
    public array $cohorts = [];
    public array $todaySlots = [];
    public array $pendingAttendanceCohorts = [];
    public int $totalStudents = 0;

    public function mount(): void
    {
        $user = Auth::user();
        $this->teacher = Teacher::where('user_id', $user?->id)->first();

        $todayDayOfWeek = Carbon::now()->dayOfWeekIso; // 1 = Monday, 7 = Sunday
        $todayDate = Carbon::now()->format('Y-m-d');

        if ($this->teacher) {
            $cohortModels = Cohort::where('teacher_id', $this->teacher->id)
                ->withCount('students')
                ->get();

            $this->cohorts = $cohortModels->toArray();
            $this->totalStudents = $cohortModels->sum('students_count');

            // Today's schedule slots
            $this->todaySlots = TimetableSlot::where('teacher_id', $this->teacher->id)
                ->where('day_of_week', $todayDayOfWeek)
                ->with(['cohort', 'room'])
                ->orderBy('start_time')
                ->get()
                ->toArray();

            // Check if attendance is taken today for each assigned cohort
            foreach ($cohortModels as $cohort) {
                $hasTakenAttendance = AttendanceRecord::where('cohort_id', $cohort->id)
                    ->where('date', $todayDate)
                    ->exists();

                if (! $hasTakenAttendance) {
                    $this->pendingAttendanceCohorts[] = [
                        'id' => $cohort->id,
                        'name' => $cohort->name,
                        'students_count' => $cohort->students_count,
                    ];
                }
            }
        } else {
            // Fallback for admins viewing the page
            $allCohorts = Cohort::withCount('students')->get();
            $this->cohorts = $allCohorts->toArray();
            $this->totalStudents = $allCohorts->sum('students_count');
        }
    }
}
