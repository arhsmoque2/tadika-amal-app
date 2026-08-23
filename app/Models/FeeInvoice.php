<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_id',
        'cohort_id',
        'invoice_number',
        'receipt_number',
        'academic_year',
        'month',
        'fee_category',
        'amount',
        'status',
        'payment_method',
        'paid_at',
        'receipt_pdf_path',
        'processed_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
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
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->fee_category) {
            'yuran_bulanan' => 'Yuran Pengajian Bulanan',
            'yuran_pendaftaran' => 'Yuran Pendaftaran Tahunan',
            'yuran_transit' => 'Yuran Transit & Asuhan Petang',
            // @phpstan-ignore match.alreadyNarrowedType (defensive fallback for unexpected/legacy category values, kept for data integrity)
            'yuran_makan_tuisyen' => 'Yuran Makan & Tuisyen Tambahan',
            default => 'Yuran Tadika',
        };
    }
}
