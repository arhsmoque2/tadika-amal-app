<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'user_id',
        'staff_number',
        'full_name',
        'ic_number',
        'phone',
        'email',
        'qualification',
        'joined_date',
        'is_active',
    ];

    protected $casts = [
        'joined_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cohorts(): HasMany
    {
        return $this->hasMany(Cohort::class);
    }

    public function timetableSlots(): HasMany
    {
        return $this->hasMany(TimetableSlot::class);
    }
}
