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

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * @return BelongsTo<Cohort, $this>
     */
    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    /**
     * @return BelongsTo<Teacher, $this>
     */
    public function witnessTeacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'witness_teacher_id');
    }

    /**
     * Official JKM logbook reference number, e.g. 'JKM-2026-0001'.
     * Generated from the incident year and record id — there is no
     * separate stored column for this.
     */
    public function getIncidentNumberAttribute(): string
    {
        $year = $this->incident_date->format('Y');

        return 'JKM-'.$year.'-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Whether this incident meets the threshold for mandatory reporting to
     * Jabatan Kebajikan Masyarakat, per AGENTS.md's JKM compliance directive.
     * 'ringan' (minor) incidents are handled as internal records only.
     */
    public function getJkmReportableAttribute(): bool
    {
        return $this->severity !== 'ringan';
    }
}
