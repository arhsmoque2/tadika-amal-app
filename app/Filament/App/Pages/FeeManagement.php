<?php

namespace App\Filament\App\Pages;

use App\Models\Cohort;
use App\Models\FeeInvoice;
use App\Models\Student;
use App\Services\FeeReceiptPdfService;
use BackedEnum;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FeeManagement extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Yuran & Resit LHDN';
    protected static ?string $title = 'Pengurusan Yuran Tadika & Resit Rasmi Pelepasan Cukai LHDN';
    protected static ?int $navigationSort = 8;
    protected string $view = 'filament.pages.fee-management';

    public ?int $selectedCohortId = null;
    public string $selectedMonth = 'Ogos';
    public array $cohortOptions = [];
    public array $invoices = [];

    public array $monthOptions = [
        'Januari' => 'Januari',
        'Februari' => 'Februari',
        'Mac' => 'Mac',
        'April' => 'April',
        'Mei' => 'Mei',
        'Jun' => 'Jun',
        'Julai' => 'Julai',
        'Ogos' => 'Ogos',
        'September' => 'September',
        'Oktober' => 'Oktober',
        'November' => 'November',
        'Disember' => 'Disember',
    ];

    public function mount(): void
    {
        $this->cohortOptions = Cohort::where('is_active', true)->pluck('name', 'id')->toArray();
        if (! empty($this->cohortOptions)) {
            $this->selectedCohortId = array_key_first($this->cohortOptions);
            $this->loadInvoices();
        }
    }

    public function updatedSelectedCohortId(): void
    {
        $this->loadInvoices();
    }

    public function updatedSelectedMonth(): void
    {
        $this->loadInvoices();
    }

    public function loadInvoices(): void
    {
        if (! $this->selectedCohortId) {
            return;
        }

        $this->invoices = FeeInvoice::where('cohort_id', $this->selectedCohortId)
            ->where('month', $this->selectedMonth)
            ->with(['student'])
            ->get()
            ->toArray();

        // If no invoices exist for this month, auto-generate from active student roster
        if (empty($this->invoices)) {
            $students = Student::where('cohort_id', $this->selectedCohortId)->where('is_active', true)->get();
            $cohort = Cohort::find($this->selectedCohortId);

            foreach ($students as $index => $student) {
                $invNo = 'INV-' . date('Y') . '-' . str_pad($cohort->id, 2, '0', STR_PAD_LEFT) . '-' . str_pad($student->id, 3, '0', STR_PAD_LEFT) . '-' . substr($this->selectedMonth, 0, 3);
                FeeInvoice::firstOrCreate(
                    [
                        'school_id' => $cohort->school_id,
                        'student_id' => $student->id,
                        'cohort_id' => $cohort->id,
                        'month' => $this->selectedMonth,
                        'academic_year' => $cohort->academic_year,
                    ],
                    [
                        'invoice_number' => $invNo,
                        'fee_category' => 'yuran_bulanan',
                        'amount' => 350.00, // Standard preschool monthly fee
                        'status' => 'pending',
                    ]
                );
            }

            $this->invoices = FeeInvoice::where('cohort_id', $this->selectedCohortId)
                ->where('month', $this->selectedMonth)
                ->with(['student'])
                ->get()
                ->toArray();
        }
    }

    public function markAsPaid(int $invoiceId, string $method = 'fpx_online'): void
    {
        $invoice = FeeInvoice::findOrFail($invoiceId);
        $receiptNo = 'REC-' . date('Ym') . '-' . str_pad($invoice->id, 4, '0', STR_PAD_LEFT);

        $invoice->update([
            'status' => 'paid',
            'receipt_number' => $receiptNo,
            'payment_method' => $method,
            'paid_at' => now(),
            'processed_by' => Auth::id(),
        ]);

        $this->loadInvoices();

        Notification::make()
            ->title('Bayaran berjaya disahkan!')
            ->body('No. Resit: ' . $receiptNo)
            ->success()
            ->send();
    }

    public function downloadReceiptPdf(int $invoiceId): BinaryFileResponse
    {
        $invoice = FeeInvoice::with(['student', 'cohort', 'school'])->findOrFail($invoiceId);
        $pdfService = new FeeReceiptPdfService();
        $filePath = $pdfService->generateReceiptPdf($invoice);

        return response()->download($filePath);
    }
}
