<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'capacity',
        'description',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function cohorts(): HasMany
    {
        return $this->hasMany(Cohort::class);
    }
}
