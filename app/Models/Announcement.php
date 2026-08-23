<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'cohort_id',
        'title',
        'content',
        'category',
        'target_audience',
        'published_at',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate pre-formatted Malaysian WhatsApp broadcast text.
     */
    public function getFormattedWhatsAppTextAttribute(): string
    {
        $schoolName = strtoupper($this->school?->name ?? 'TADIKA ISLAM AMAL');
        $audience = match ($this->target_audience) {
            'parents' => 'Ibu Bapa & Penjaga',
            'teachers' => 'Semua Guru & Warga Tadika',
            'cohort' => 'Ibu Bapa Murid Kelas ' . ($this->cohort?->name ?? ''),
            default => 'Semua Ibu Bapa, Penjaga & Warga Tadika',
        };

        $categoryIcon = match ($this->category) {
            'urgent' => '🚨 *PERHATIAN / PENTING*',
            'event' => '🎉 *MAJLIS / ACARA TADIKA*',
            'holiday' => '🏖️ *MAKLUMAN CUTI PERISTIWA*',
            default => '📢 *PENGUMUMAN RASMI*',
        };

        $date = $this->published_at ? $this->published_at->format('d/m/Y') : date('d/m/Y');

        return "{$categoryIcon}\n" .
               "🏫 *{$schoolName}*\n" .
               "📅 Tarikh: {$date}\n" .
               "👥 Kepada: {$audience}\n\n" .
               "📌 *TAJUK: " . strtoupper($this->title) . "*\n\n" .
               "{$this->content}\n\n" .
               "Sekian, terima kasih.\n" .
               "_Pengurusan {$schoolName}_";
    }

    public function getWhatsAppUrlAttribute(): string
    {
        return 'https://wa.me/?text=' . rawurlencode($this->formatted_whats_app_text);
    }
}
