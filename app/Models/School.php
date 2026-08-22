<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'phone',
        'email',
        'address',
        'logo_path',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    public function cohorts(): HasMany
    {
        return $this->hasMany(Cohort::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
