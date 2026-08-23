<?php

namespace Tests\Feature;

use App\Models\Cohort;
use App\Models\IncidentLog;
use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncidentLogComplianceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_record_incident_with_parent_notification(): void
    {
        $school = School::create([
            'name' => 'Tadika Amal Cawangan Cyberjaya',
            'code' => 'TAC-01',
        ]);

        $cohort = Cohort::create([
            'school_id' => $school->id,
            'name' => 'Al-Farabi 4 Tahun',
            'academic_year' => 2026,
            'age_group' => '4_years',
        ]);

        $student = Student::create([
            'school_id' => $school->id,
            'cohort_id' => $cohort->id,
            'name' => 'Harith Bin Danial',
            'gender' => 'Lelaki',
            'is_active' => true,
        ]);

        $teacherUser = User::create([
            'name' => 'Cikgu Huda',
            'email' => 'huda@tadikaamal.edu.my',
            'password' => bcrypt('password123'),
        ]);

        $teacher = Teacher::create([
            'school_id' => $school->id,
            'user_id' => $teacherUser->id,
            'full_name' => 'Cikgu Huda',
            'role' => 'guru_kelas',
            'status' => 'aktif',
        ]);

        $incident = IncidentLog::create([
            'school_id' => $school->id,
            'student_id' => $student->id,
            'cohort_id' => $cohort->id,
            'incident_date' => now()->toDateString(),
            'incident_time' => '10:15:00',
            'location' => 'Taman Permainan Prasekolah',
            'severity' => 'ringan',
            'incident_description' => 'Tergelincir semasa aktiviti bermain pasir dan luka kecil di lutut.',
            'injury_details' => 'Luka calar ringan di lutut kanan.',
            'first_aid_given' => 'Dicuci dengan cecair antiseptik dan dipasang plaster.',
            'witness_teacher_id' => $teacher->id,
            'parent_notified' => true,
            'parent_notified_at' => now(),
            'followup_actions' => 'Ibu bapa dimaklumkan melalui panggilan telefon dan WhatsApp.',
        ]);

        $this->assertTrue($incident->parent_notified);
        $this->assertEquals('ringan', $incident->severity);
        $this->assertEquals($student->id, $incident->student->id);
        $this->assertDatabaseHas('incident_logs', [
            'id' => $incident->id,
            'severity' => 'ringan',
            'parent_notified' => true,
        ]);
    }
}
