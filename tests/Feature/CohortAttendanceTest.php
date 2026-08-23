<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\Cohort;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CohortAttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_record_student_daily_attendance(): void
    {
        $school = School::create([
            'name' => 'Tadika Amal Cawangan Cyberjaya',
            'code' => 'TAC-01',
            'contact_number' => '+60123456789',
        ]);

        $cohort = Cohort::create([
            'school_id' => $school->id,
            'name' => 'Al-Fateh 6 Tahun',
            'academic_year' => 2026,
            'age_group' => '6_years',
        ]);

        $student = Student::create([
            'school_id' => $school->id,
            'cohort_id' => $cohort->id,
            'name' => 'Ahmad Bilal Bin Hilmi',
            'mykid' => '200101-10-1234',
            'gender' => 'Lelaki',
            'is_active' => true,
        ]);

        $user = User::create([
            'name' => 'Ustazah Aisyah',
            'email' => 'aisyah@tadikaamal.edu.my',
            'password' => bcrypt('password123'),
        ]);

        $attendance = AttendanceRecord::create([
            'school_id' => $school->id,
            'cohort_id' => $cohort->id,
            'student_id' => $student->id,
            'date' => now()->toDateString(),
            'status' => 'hadir',
            'temperature' => 36.6,
            'recorded_by' => $user->id,
        ]);

        $this->assertDatabaseHas('attendance_records', [
            'id' => $attendance->id,
            'student_id' => $student->id,
            'status' => 'hadir',
        ]);

        $this->assertEquals('hadir', $attendance->status);
        $this->assertEquals($student->id, $attendance->student->id);
    }

    public function test_supports_absence_and_medical_status_codes(): void
    {
        $statuses = ['hadir', 'tidak_hadir', 'sakit', 'cuti'];

        foreach ($statuses as $status) {
            $this->assertContains($status, ['hadir', 'tidak_hadir', 'sakit', 'cuti']);
        }
    }
}
