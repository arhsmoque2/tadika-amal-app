<?php

namespace Tests\Feature;

use App\Models\Cohort;
use App\Models\LessonPlan;
use App\Models\School;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonPlannerRphTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_and_manage_weekly_rph_lesson_plan(): void
    {
        $school = School::create([
            'name' => 'Tadika Amal Cawangan Cyberjaya',
            'code' => 'TAC-01',
        ]);

        $cohort = Cohort::create([
            'school_id' => $school->id,
            'name' => 'Al-Fateh 6 Tahun',
            'academic_year' => 2026,
            'age_group' => '6_years',
        ]);

        $teacherUser = User::create([
            'name' => 'Ustazah Khadijah',
            'email' => 'khadijah@tadikaamal.edu.my',
            'password' => bcrypt('password123'),
        ]);

        $teacher = Teacher::create([
            'school_id' => $school->id,
            'user_id' => $teacherUser->id,
            'full_name' => 'Ustazah Khadijah',
            'role' => 'guru_kelas',
            'status' => 'aktif',
        ]);

        $rph = LessonPlan::create([
            'school_id' => $school->id,
            'cohort_id' => $cohort->id,
            'teacher_id' => $teacher->id,
            'week_number' => 32,
            'theme' => 'Alam Hidupan - Haiwan Jinak',
            'tunjang_kspk' => 'Sains dan Teknologi & Kerohanian Sikap dan Nilai',
            'learning_objectives' => 'Mengenal ciptaan Allah dan menamakan haiwan jinak serta bunyinya.',
            'activities' => 'Aktiviti bercerita, simulasi bunyi haiwan, dan mewarna lembaran kerja.',
            'materials_needed' => 'Kad imbasan haiwan, pensel warna, pembesar suara audio.',
            'reflection_notes' => '18 daripada 20 murid berjaya meniru dan menamakan haiwan dengan betul.',
            'plan_date' => now()->toDateString(),
        ]);

        $this->assertEquals(32, $rph->week_number);
        $this->assertStringContainsString('Sains dan Teknologi', $rph->tunjang_kspk);
        $this->assertDatabaseHas('lesson_plans', [
            'id' => $rph->id,
            'week_number' => 32,
            'theme' => 'Alam Hidupan - Haiwan Jinak',
        ]);
    }
}
