<?php

namespace Tests\Feature;

use App\Models\Cohort;
use App\Models\HealthScreening;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MorningHealthScreeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_log_normal_morning_health_screening(): void
    {
        $school = School::create([
            'name' => 'Tadika Amal Cawangan Shah Alam',
            'code' => 'TASA-02',
        ]);

        $cohort = Cohort::create([
            'school_id' => $school->id,
            'name' => 'Al-Kindi 5 Tahun',
            'academic_year' => 2026,
            'age_group' => '5_years',
        ]);

        $student = Student::create([
            'school_id' => $school->id,
            'cohort_id' => $cohort->id,
            'name' => 'Nur Maryam Binti Farhan',
            'gender' => 'Perempuan',
            'is_active' => true,
        ]);

        $teacher = User::create([
            'name' => 'Cikgu Hafizah',
            'email' => 'hafizah@tadikaamal.edu.my',
            'password' => bcrypt('password123'),
        ]);

        $screening = HealthScreening::create([
            'school_id' => $school->id,
            'cohort_id' => $cohort->id,
            'student_id' => $student->id,
            'date' => now()->toDateString(),
            'temperature' => 36.5,
            'symptoms' => [],
            'status' => 'admit',
            'screened_by' => $teacher->id,
        ]);

        $this->assertFalse($screening->isFever());
        $this->assertEquals('admit', $screening->status);
        $this->assertDatabaseHas('health_screenings', [
            'id' => $screening->id,
            'status' => 'admit',
        ]);
    }

    public function test_detects_fever_and_hfmd_symptoms_for_isolation(): void
    {
        $school = School::create([
            'name' => 'Tadika Amal Cawangan Bangi',
            'code' => 'TAB-03',
        ]);

        $cohort = Cohort::create([
            'school_id' => $school->id,
            'name' => 'Ibnu Sina 6 Tahun',
            'academic_year' => 2026,
            'age_group' => '6_years',
        ]);

        $student = Student::create([
            'school_id' => $school->id,
            'cohort_id' => $cohort->id,
            'name' => 'Umar Bin Zulkifli',
            'gender' => 'Lelaki',
            'is_active' => true,
        ]);

        $screening = HealthScreening::create([
            'school_id' => $school->id,
            'cohort_id' => $cohort->id,
            'student_id' => $student->id,
            'date' => now()->toDateString(),
            'temperature' => 38.2,
            'symptoms' => ['ulser_mulut', 'ruam_tangan_kaki'],
            'status' => 'isolate_refer',
            'remarks' => 'Disyaki simptom HFMD. Ibu bapa dihubungi.',
        ]);

        $this->assertTrue($screening->isFever());
        $this->assertEquals('isolate_refer', $screening->status);
        $this->assertContains('ulser_mulut', $screening->symptoms);
    }
}
