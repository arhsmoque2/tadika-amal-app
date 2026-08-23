<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
            \App\Models\Student::class => [
                'label' => 'Import Pendaftaran Murid',
                'upsert_key' => 'mykid',
                'required_fields' => ['name', 'gender'],
                'optional_fields' => ['mykid', 'birth_date', 'blood_type', 'allergies_medical', 'address', 'guardian_name', 'guardian_phone', 'guardian_email'],
                'relations' => [
                    'cohort_id' => [
                        'model' => \App\Models\Cohort::class,
                        'lookup_column' => 'name',
                        'label' => 'Kelas / Kohort',
                    ],
                ],
            ],
            \App\Models\Teacher::class => [
                'label' => 'Import Profil Guru & Staf',
                'upsert_key' => 'ic_number',
                'required_fields' => ['name', 'phone'],
                'optional_fields' => ['ic_number', 'staff_no', 'email', 'qualification', 'hire_date', 'role'],
                'relations' => [],
            ],
            \App\Models\TimetableSlot::class => [
                'label' => 'Import Jadual Waktu Kelas',
                'upsert_key' => null,
                'required_fields' => ['day_of_week', 'start_time', 'end_time', 'subject_title'],
                'optional_fields' => ['kspk_strand', 'color'],
                'relations' => [
                    'cohort_id' => [
                        'model' => \App\Models\Cohort::class,
                        'lookup_column' => 'name',
                        'label' => 'Kelas',
                    ],
                    'teacher_id' => [
                        'model' => \App\Models\Teacher::class,
                        'lookup_column' => 'name',
                        'label' => 'Guru Bertugas',
                    ],
                    'room_id' => [
                        'model' => \App\Models\Room::class,
                        'lookup_column' => 'name',
                        'label' => 'Bilik / Ruang',
                    ],
                ],
            ],
            \App\Models\FeeInvoice::class => [
                'label' => 'Import Yuran & Invois',
                'upsert_key' => 'invoice_no',
                'required_fields' => ['amount', 'month', 'status'],
                'optional_fields' => ['invoice_no', 'due_date', 'notes'],
                'relations' => [
                    'student_id' => [
                        'model' => \App\Models\Student::class,
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
