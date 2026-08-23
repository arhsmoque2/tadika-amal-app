<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Cohort;
use App\Models\FeeInvoice;
use App\Models\HealthScreening;
use App\Models\IncidentLog;
use App\Models\LessonPlan;
use App\Models\Room;
use App\Models\School;
use App\Models\Skill;
use App\Models\SkillScale;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TimetableSlot;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TadikaAmalKspkSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create School Tenant
        $school = School::firstOrCreate(
            ['code' => 'TADIKA-AMAL-01'],
            [
                'name' => 'Tadika Islam Amal Bestari',
                'phone' => '03-89210001',
                'email' => 'admin@tadikaamal.edu.my',
                'address' => 'No 12, Jalan Bestari 2, Bandar Baru Bangi, 43650 Selangor',
            ]
        );

        // 2. Create Rooms
        $roomBiruni = Room::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'Bilik Al-Biruni'],
            ['capacity' => 20, 'description' => 'Bilik Darjah 6 Tahun']
        );
        $roomFarabi = Room::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'Bilik Al-Farabi'],
            ['capacity' => 20, 'description' => 'Bilik Darjah 5 Tahun']
        );

        // 3. Create Teacher Users & Profile
        $teacherUser = User::firstOrCreate(
            ['email' => 'ustazah.aminah@tadikaamal.edu.my'],
            [
                'name' => 'Ustazah Aminah Binti Yusof',
                'password' => Hash::make('password123'),
            ]
        );

        $teacher = Teacher::firstOrCreate(
            ['school_id' => $school->id, 'full_name' => 'Ustazah Aminah Binti Yusof'],
            [
                'user_id' => $teacherUser->id,
                'staff_number' => 'AMAL-T-001',
                'phone' => '012-3456789',
                'email' => 'ustazah.aminah@tadikaamal.edu.my',
                'qualification' => 'Diploma Pendidikan Awal Kanak-kanak (KUIS)',
                'is_active' => true,
            ]
        );

        // 4. Create Cohorts
        $cohort6 = Cohort::firstOrCreate(
            ['school_id' => $school->id, 'name' => '6 Tahun - Umar Al-Khattab'],
            [
                'teacher_id' => $teacher->id,
                'room_id' => $roomBiruni->id,
                'age_group' => '6',
                'academic_year' => '2026',
                'capacity' => 20,
                'color' => '#10b981',
                'is_active' => true,
            ]
        );

        $cohort5 = Cohort::firstOrCreate(
            ['school_id' => $school->id, 'name' => '5 Tahun - Fatimah Az-Zahra'],
            [
                'teacher_id' => $teacher->id,
                'room_id' => $roomFarabi->id,
                'age_group' => '5',
                'academic_year' => '2026',
                'capacity' => 20,
                'color' => '#3b82f6',
                'is_active' => true,
            ]
        );

        // 5. Seed Sample Students
        $studentsData = [
            ['name' => 'Ahmad Rayyan Bin Mohd Zulkifli', 'mykid' => '200115-10-1233', 'gender' => 'Lelaki', 'guardian_phone' => '019-2233445'],
            ['name' => 'Nur Aina Batrisyia Binti Khairul', 'mykid' => '200322-10-4552', 'gender' => 'Perempuan', 'guardian_phone' => '012-9988771'],
            ['name' => 'Muhammad Harith Bin Abdullah', 'mykid' => '200511-10-8891', 'gender' => 'Lelaki', 'guardian_phone' => '013-4455667'],
            ['name' => 'Siti Khadijah Binti Azman', 'mykid' => '200719-10-3344', 'gender' => 'Perempuan', 'guardian_phone' => '017-8899001'],
            ['name' => 'Umar Fayyadh Bin Abdul Rahman', 'mykid' => '200905-10-5567', 'gender' => 'Lelaki', 'guardian_phone' => '018-7766554'],
        ];

        foreach ($studentsData as $data) {
            Student::firstOrCreate(
                ['school_id' => $school->id, 'name' => $data['name']],
                [
                    'cohort_id' => $cohort6->id,
                    'mykid' => $data['mykid'],
                    'gender' => $data['gender'],
                    'guardian_name' => 'Waris '.$data['name'],
                    'guardian_phone' => $data['guardian_phone'],
                    'is_active' => true,
                ]
            );
        }

        // 6. Skill Scales
        $scales = [
            ['name' => 'Menguasai', 'shortname' => 'TM', 'value' => 3, 'color' => 'emerald'],
            ['name' => 'Sedang Menguasai', 'shortname' => 'SM', 'value' => 2, 'color' => 'amber'],
            ['name' => 'Belum Menguasai', 'shortname' => 'BM', 'value' => 1, 'color' => 'rose'],
        ];

        foreach ($scales as $scale) {
            SkillScale::firstOrCreate(
                ['school_id' => $school->id, 'shortname' => $scale['shortname']],
                $scale
            );
        }

        // 7. KSPK Preschool Standards / Skills
        $kspkSkills = [
            // Tunjang Komunikasi BM
            ['domain' => 'Tunjang Komunikasi (BM)', 'code' => 'BM 1.1', 'name' => 'Mendengar dan memberi respons dengan bertatasusila', 'order' => 1],
            ['domain' => 'Tunjang Komunikasi (BM)', 'code' => 'BM 2.1', 'name' => 'Mengecam dan membunyikan huruf abjad (A-Z)', 'order' => 2],
            ['domain' => 'Tunjang Komunikasi (BM)', 'code' => 'BM 3.1', 'name' => 'Menguasai kemahiran pramenulis dan menulis perkataan mudah', 'order' => 3],
            // Tunjang Kerohanian & Pendidikan Islam
            ['domain' => 'Tunjang Kerohanian & Nilai', 'code' => 'PI 1.1', 'name' => 'Mengetahui Rukun Iman dan Rukun Islam', 'order' => 4],
            ['domain' => 'Tunjang Kerohanian & Nilai', 'code' => 'PI 2.1', 'name' => 'Melafazkan bacaan doa harian dan surah Al-Fatihah', 'order' => 5],
            ['domain' => 'Tunjang Kerohanian & Nilai', 'code' => 'PI 3.1', 'name' => 'Melakukan amali wuduk dan perlakuan solat dengan tertib', 'order' => 6],
            // Tunjang Sains & Matematik Awal
            ['domain' => 'Tunjang Sains & Matematik', 'code' => 'MA 1.1', 'name' => 'Memadankan objek dan membilang nombor 1 hingga 20', 'order' => 7],
            ['domain' => 'Tunjang Sains & Matematik', 'code' => 'SA 1.1', 'name' => 'Meneroka alam sekitar menggunakan deria penglihatan dan sentuhan', 'order' => 8],
            // Tunjang Perkembangan Fizikal & Sosioemosi
            ['domain' => 'Tunjang Fizikal & Sosioemosi', 'code' => 'FK 1.1', 'name' => 'Melakukan kemahiran motor halus (memegang pensel, menggunting)', 'order' => 9],
            ['domain' => 'Tunjang Fizikal & Sosioemosi', 'code' => 'KD 1.1', 'name' => 'Mengenali emosi diri dan berinteraksi mesra dengan rakan sebaya', 'order' => 10],
        ];

        foreach ($kspkSkills as $skill) {
            Skill::firstOrCreate(
                ['school_id' => $school->id, 'code' => $skill['code']],
                [
                    'domain_category' => $skill['domain'],
                    'name' => $skill['name'],
                    'sort_order' => $skill['order'],
                ]
            );
        }

        // 8. Timetable Slots
        $subjects = [
            ['time' => ['08:00:00', '08:30:00'], 'subject' => 'Perhimpunan Pagi & Bacaan Doa', 'color' => '#6366f1'],
            ['time' => ['08:30:00', '09:30:00'], 'subject' => 'Pendidikan Islam & Amali Solat', 'color' => '#10b981'],
            ['time' => ['09:30:00', '10:00:00'], 'subject' => 'Rehat & Adab Makan', 'color' => '#f59e0b'],
            ['time' => ['10:00:00', '11:00:00'], 'subject' => 'Literasi Bahasa Melayu / English', 'color' => '#3b82f6'],
            ['time' => ['11:00:00', '12:00:00'], 'subject' => 'Sains Awal & Matematik Cilik', 'color' => '#8b5cf6'],
        ];

        for ($day = 1; $day <= 5; $day++) {
            foreach ($subjects as $s) {
                TimetableSlot::firstOrCreate(
                    [
                        'school_id' => $school->id,
                        'cohort_id' => $cohort6->id,
                        'day_of_week' => $day,
                        'start_time' => $s['time'][0],
                    ],
                    [
                        'teacher_id' => $teacher->id,
                        'room_id' => $roomBiruni->id,
                        'end_time' => $s['time'][1],
                        'subject_name' => $s['subject'],
                        'color' => $s['color'],
                    ]
                );
            }
        }

        // 9. Sample Announcements
        Announcement::firstOrCreate(
            ['school_id' => $school->id, 'title' => 'Cuti Peristiwa Sempena Sambutan Maulidur Rasul'],
            [
                'content' => "Assalamu'alaikum wbt kepada semua ibu bapa & penjaga yang dihormati,\n\nDimaklumkan bahawa Tadika Islam Amal Bestari akan bercuti sempena Sambutan Maulidur Rasul pada hari Isnin ini. Sesi persekolahan akan bersambung seperti biasa pada hari Selasa.\n\nSekian, terima kasih.",
                'category' => 'holiday',
                'target_audience' => 'all',
                'published_at' => now(),
                'created_by' => $teacherUser->id,
                'is_active' => true,
            ]
        );

        // 10. Sample Lesson Plan (RPH)
        LessonPlan::firstOrCreate(
            ['school_id' => $school->id, 'cohort_id' => $cohort6->id, 'week_number' => 1],
            [
                'teacher_id' => $teacher->id,
                'theme' => 'Diri Saya & Anggota Badan',
                'tunjang_kspk' => 'Tunjang Komunikasi (Bahasa Melayu)',
                'learning_objectives' => 'Murid dapat mengenal dan menyebut 5 bahagian anggota badan utama dalam Bahasa Melayu dan Bahasa Arab.',
                'activities' => "1. Nyanyian lagu 'Anggota Badan'.\n2. Murid memadankan kad imbasan anggota badan pada poster peraga.\n3. Lembaran kerja mewarna dan menyurih perkataan.",
                'materials_needed' => 'Kad imbasan, poster anggota badan, pensel warna.',
                'plan_date' => now()->format('Y-m-d'),
            ]
        );

        // 11. Sample Health Screening
        $allStudents = Student::where('school_id', $school->id)->get();
        foreach ($allStudents as $st) {
            HealthScreening::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'cohort_id' => $cohort6->id,
                    'student_id' => $st->id,
                    'date' => now()->format('Y-m-d'),
                ],
                [
                    'temperature' => 36.6,
                    'symptoms' => [],
                    'status' => 'lulus',
                    'remarks' => 'Murid ceria dan sihat.',
                    'screened_by' => $teacherUser->id,
                ]
            );
        }

        // 12. Sample Fee Invoices & Paid Receipt
        foreach ($allStudents as $idx => $st) {
            $inv = FeeInvoice::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'student_id' => $st->id,
                    'month' => 'Ogos',
                    'academic_year' => '2026',
                ],
                [
                    'cohort_id' => $cohort6->id,
                    'invoice_number' => 'INV-2026-06-'.str_pad($st->id, 3, '0', STR_PAD_LEFT).'-AUG',
                    'receipt_number' => $idx === 0 ? 'REC-202608-0001' : null,
                    'fee_category' => 'yuran_bulanan',
                    'amount' => 350.00,
                    'status' => $idx === 0 ? 'paid' : 'pending',
                    'payment_method' => $idx === 0 ? 'fpx_online' : null,
                    'paid_at' => $idx === 0 ? now() : null,
                    'processed_by' => $teacherUser->id,
                ]
            );
        }

        // 13. Sample Incident Log
        if ($allStudents->isNotEmpty()) {
            IncidentLog::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'student_id' => $allStudents->first()->id,
                    'incident_date' => now()->format('Y-m-d'),
                ],
                [
                    'cohort_id' => $cohort6->id,
                    'incident_time' => '10:15:00',
                    'location' => 'Taman Permainan Tadika',
                    'severity' => 'ringan',
                    'incident_description' => 'Murid tersadung ketika bermain kejar-kejar di atas rumput.',
                    'injury_details' => 'Luka calar kecil pada lutut kanan.',
                    'first_aid_given' => 'Dicuci dengan cecair antiseptik Dettol dan ditampal plaster Hansaplast.',
                    'witness_teacher_id' => $teacher->id,
                    'parent_notified' => true,
                    'parent_notified_at' => now(),
                ]
            );
        }
    }
}
