<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthScreening extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'cohort_id',
        'student_id',
        'date',
        'temperature',
        'symptoms',
        'status',
        'remarks',
        'screened_by',
    ];

    protected $casts = [
        'date' => 'date',
        'temperature' => 'float',
        'symptoms' => 'array',
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
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function screener(): BelongsTo
    {
        return $this->belongsTo(User::class, 'screened_by');
    }

    public function isFever(): bool
    {
        return $this->temperature >= 37.5;
    }
}
