<?php

namespace App\Services;

use App\Models\Cohort;
use App\Models\FeeInvoice;
use App\Models\Room;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TimetableSlot;
use Illuminate\Database\Eloquent\Model;

class UniversalImportExportService
{
    /**
     * Get the standardized import column mappings and validation rules for a given model.
     *
     * @return array<string, mixed>
     */
    public function getImportSchemaForModel(string $modelClass): array
    {
        return match ($modelClass) {
            Student::class => [
                'label' => 'Import Pendaftaran Murid',
                'upsert_key' => 'mykid',
                'required_fields' => ['name', 'gender'],
                'optional_fields' => ['mykid', 'birth_date', 'blood_type', 'allergies_medical', 'address', 'guardian_name', 'guardian_phone', 'guardian_email'],
                'relations' => [
                    'cohort_id' => [
                        'model' => Cohort::class,
                        'lookup_column' => 'name',
                        'label' => 'Kelas / Kohort',
                    ],
                ],
            ],
            Teacher::class => [
                'label' => 'Import Profil Guru & Staf',
                'upsert_key' => 'ic_number',
                'required_fields' => ['name', 'phone'],
                'optional_fields' => ['ic_number', 'staff_no', 'email', 'qualification', 'hire_date', 'role'],
                'relations' => [],
            ],
            TimetableSlot::class => [
                'label' => 'Import Jadual Waktu Kelas',
                'upsert_key' => null,
                'required_fields' => ['day_of_week', 'start_time', 'end_time', 'subject_title'],
                'optional_fields' => ['kspk_strand', 'color'],
                'relations' => [
                    'cohort_id' => [
                        'model' => Cohort::class,
                        'lookup_column' => 'name',
                        'label' => 'Kelas',
                    ],
                    'teacher_id' => [
                        'model' => Teacher::class,
                        'lookup_column' => 'name',
                        'label' => 'Guru Bertugas',
                    ],
                    'room_id' => [
                        'model' => Room::class,
                        'lookup_column' => 'name',
                        'label' => 'Bilik / Ruang',
                    ],
                ],
            ],
            FeeInvoice::class => [
                'label' => 'Import Yuran & Invois',
                'upsert_key' => 'invoice_no',
                'required_fields' => ['amount', 'month', 'status'],
                'optional_fields' => ['invoice_no', 'due_date', 'notes'],
                'relations' => [
                    'student_id' => [
                        'model' => Student::class,
                        'lookup_column' => 'mykid',
                        'label' => 'Murid (MyKid)',
                    ],
                ],
            ],
            default => [
                'label' => 'Universal Import',
                'upsert_key' => 'id',
                'required_fields' => [],
                'optional_fields' => [],
                'relations' => [],
            ],
        };
    }
}
