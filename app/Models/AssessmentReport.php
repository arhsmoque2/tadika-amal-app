<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_id',
        'cohort_id',
        'academic_year',
        'period',
        'teacher_comments',
        'headmaster_comments',
        'generated_pdf_path',
        'is_finalized',
        'finalized_by',
        'finalized_at',
    ];

    protected $casts = [
        'is_finalized' => 'boolean',
        'finalized_at' => 'datetime',
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
     * @return BelongsTo<User, $this>
     */
    public function finalizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }
}
