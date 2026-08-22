<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'domain_category',
        'code',
        'name',
        'description',
        'sort_order',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(SkillEvaluation::class);
    }
}
