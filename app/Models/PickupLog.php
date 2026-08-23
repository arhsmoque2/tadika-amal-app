<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PickupLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_id',
        'cohort_id',
        'date',
        'pickup_time',
        'guardian_type',
        'guardian_name',
        'guardian_phone',
        'is_late_pickup',
        'late_minutes',
        'notes',
        'logged_by',
    ];

    protected $casts = [
        'date' => 'date',
        'is_late_pickup' => 'boolean',
        'late_minutes' => 'integer',
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
    public function logger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'logged_by');
    }
}
