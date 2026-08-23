<?php

namespace App\Services;

use App\Models\FeeInvoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class FeeReceiptPdfService
{
    /**
     * Generate official tax-relief (LHDN) receipt for preschool fees.
     */
    public function generateReceiptPdf(FeeInvoice $invoice): string
    {
        $outputDir = storage_path('app/fee_receipts');
        if (! file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $receiptNo = $invoice->receipt_number ?: 'REC-'.date('Ym').'-'.str_pad($invoice->id, 4, '0', STR_PAD_LEFT);
        $fileName = 'Resit_Yuran_'.Str::slug($receiptNo).'_'.Str::slug($invoice->student->name ?? 'Murid').'.pdf';
        $outputPath = $outputDir.'/'.$fileName;

        $viewData = [
            'invoice' => $invoice,
            'student' => $invoice->student,
            'cohort' => $invoice->cohort,
            'school' => $invoice->school,
            'receiptNo' => $receiptNo,
            'printedDate' => Carbon::now()->format('d/m/Y H:i'),
        ];

        $html = View::make('reports.official-fee-receipt-pdf', $viewData)->render();

        if (class_exists(Mpdf::class)) {
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A5',
                'orientation' => 'L',
                'margin_left' => 12,
                'margin_right' => 12,
                'margin_top' => 12,
                'margin_bottom' => 12,
            ]);
            $mpdf->WriteHTML($html);
            $mpdf->Output($outputPath, Destination::FILE);
        } else {
            file_put_contents(str_replace('.pdf', '.html', $outputPath), $html);
            file_put_contents($outputPath, "%PDF-1.4 Mock Receipt for {$receiptNo}");
        }

        return $outputPath;
    }
}
