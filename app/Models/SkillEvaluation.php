<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkillEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'cohort_id',
        'student_id',
        'skill_id',
        'skill_scale_id',
        'evaluation_period',
        'evaluated_by',
        'remarks',
        'evaluated_at',
    ];

    protected $casts = [
        'evaluated_at' => 'date',
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

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    public function skillScale(): BelongsTo
    {
        return $this->belongsTo(SkillScale::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }
}
