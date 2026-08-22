<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    use HasFactory;

    public const STATUS_HADIR = 'hadir';
    public const STATUS_TIDAK_HADIR = 'tidak_hadir';
    public const STATUS_SAKIT = 'sakit';
    public const STATUS_CUTI = 'cuti';

    protected $fillable = [
        'school_id',
        'cohort_id',
        'student_id',
        'date',
        'status',
        'reason',
        'remarks',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            self::STATUS_HADIR => ['label' => 'Hadir', 'color' => 'emerald', 'code' => 'H'],
            self::STATUS_TIDAK_HADIR => ['label' => 'Tidak Hadir', 'color' => 'rose', 'code' => 'TH'],
            self::STATUS_SAKIT => ['label' => 'Sakit', 'color' => 'amber', 'code' => 'S'],
            self::STATUS_CUTI => ['label' => 'Cuti', 'color' => 'blue', 'code' => 'C'],
            default => ['label' => '-', 'color' => 'gray', 'code' => '-'],
        };
    }
}
