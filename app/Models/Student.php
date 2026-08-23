<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'cohort_id',
        'name',
        'mykid',
        'gender',
        'birth_date',
        'photo_path',
        'guardian_name',
        'guardian_phone',
        'guardian_email',
        'address',
        'emergency_contact',
        'allergies_medical',
        'dynamic_data',
        'is_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'dynamic_data' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * @return BelongsTo<Cohort, $this>
     */
    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    /**
     * @return HasMany<AttendanceRecord, $this>
     */
    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    /**
     * @return HasMany<SkillEvaluation, $this>
     */
    public function skillEvaluations(): HasMany
    {
        return $this->hasMany(SkillEvaluation::class);
    }

    /**
     * @return HasMany<AssessmentReport, $this>
     */
    public function assessmentReports(): HasMany
    {
        return $this->hasMany(AssessmentReport::class);
    }
}
