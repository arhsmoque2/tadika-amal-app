<?php

namespace App\Filament\App\Pages;

use App\Models\Cohort;
use App\Models\LessonPlan;
use App\Models\Teacher;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class LessonPlanner extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'RPH & Tema Mingguan';
    protected static ?string $title = 'Rancangan Pengajaran Harian (RPH) KSPK';
    protected static ?int $navigationSort = 7;
    protected string $view = 'filament.pages.lesson-planner';

    public ?int $selectedCohortId = null;
    public int $selectedWeek = 1;
    public string $theme_input = '';
    public string $tunjang_input = 'Tunjang Komunikasi (Bahasa Melayu)';
    public string $objectives_input = '';
    public string $activities_input = '';
    public string $materials_input = '';
    public string $reflection_input = '';
    public string $plan_date_input = '';

    public array $lessonPlans = [];
    public array $cohortOptions = [];

    public array $kspkTunjangOptions = [
        'Tunjang Komunikasi (Bahasa Melayu)' => 'Tunjang Komunikasi (Bahasa Melayu)',
        'Tunjang Komunikasi (Bahasa Inggeris)' => 'Tunjang Komunikasi (Bahasa Inggeris)',
        'Tunjang Kerohanian, Sikap & Nilai (Pendidikan Islam)' => 'Tunjang Kerohanian, Sikap & Nilai (Pendidikan Islam)',
        'Tunjang Keterampilan Diri & Sosioemosi' => 'Tunjang Keterampilan Diri & Sosioemosi',
        'Tunjang Perkembangan Fizikal & Estetika' => 'Tunjang Perkembangan Fizikal & Estetika',
        'Tunjang Sains & Teknologi (Awal Matematik)' => 'Tunjang Sains & Teknologi (Awal Matematik)',
        'Tunjang Kemanusiaan' => 'Tunjang Kemanusiaan',
    ];

    public function mount(): void
    {
        $this->plan_date_input = Carbon::now()->format('Y-m-d');
        $this->cohortOptions = Cohort::where('is_active', true)->pluck('name', 'id')->toArray();

        if (! empty($this->cohortOptions)) {
            $this->selectedCohortId = array_key_first($this->cohortOptions);
            $this->loadPlans();
        }
    }

    public function updatedSelectedCohortId(): void
    {
        $this->loadPlans();
    }

    public function loadPlans(): void
    {
        if (! $this->selectedCohortId) {
            return;
        }

        $this->lessonPlans = LessonPlan::where('cohort_id', $this->selectedCohortId)
            ->with(['teacher'])
            ->orderBy('plan_date', 'desc')
            ->get()
            ->toArray();
    }

    public function saveLessonPlan(): void
    {
        if (! $this->selectedCohortId || empty($this->theme_input) || empty($this->objectives_input)) {
            Notification::make()
                ->title('Sila lengkapkan maklumat tema dan objektif pembelajaran')
                ->danger()
                ->send();
            return;
        }

        $cohort = Cohort::find($this->selectedCohortId);
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user?->id)->first() ?? Teacher::first();

        LessonPlan::create([
            'school_id' => $cohort->school_id,
            'cohort_id' => $cohort->id,
            'teacher_id' => $teacher?->id ?? 1,
            'week_number' => $this->selectedWeek,
            'theme' => $this->theme_input,
            'tunjang_kspk' => $this->tunjang_input,
            'learning_objectives' => $this->objectives_input,
            'activities' => $this->activities_input,
            'materials_needed' => $this->materials_input,
            'reflection_notes' => $this->reflection_input,
            'plan_date' => $this->plan_date_input,
        ]);

        $this->reset(['theme_input', 'objectives_input', 'activities_input', 'materials_input', 'reflection_input']);
        $this->loadPlans();

        Notification::make()
            ->title('RPH berjaya disimpan!')
            ->success()
            ->send();
    }
}
