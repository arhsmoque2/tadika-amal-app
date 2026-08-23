<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_id',
        'cohort_id',
        'incident_date',
        'incident_time',
        'location',
        'severity',
        'incident_description',
        'injury_details',
        'first_aid_given',
        'witness_teacher_id',
        'parent_notified',
        'parent_notified_at',
        'followup_actions',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'parent_notified' => 'boolean',
        'parent_notified_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function witnessTeacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'witness_teacher_id');
    }
}
