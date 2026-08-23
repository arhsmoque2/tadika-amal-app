<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\View;
use Tests\TestCase;

class UniversalDocumentEngineTest extends TestCase
{
    public function test_assessment_report_pdf_template_compiles(): void
    {
        $view = View::make('reports.annual-assessment-pdf');
        $this->assertNotNull($view);
        $rendered = $view->render();
        $this->assertStringContainsString('TADIKA AMAL', $rendered);
        $this->assertStringContainsString('KSPK', $rendered);
    }

    public function test_official_receipt_pdf_template_compiles_with_lhdn_relief(): void
    {
        $view = View::make('reports.official-fee-receipt-pdf');
        $this->assertNotNull($view);
        $rendered = $view->render();
        $this->assertStringContainsString('RESIT RASMI PEMBAYARAN YURAN', $rendered);
        $this->assertStringContainsString('46(1)(r)', $rendered);
    }

    public function test_certificate_award_template_compiles(): void
    {
        $view = View::make('reports.certificate-award');
        $this->assertNotNull($view);
        $rendered = $view->render();
        $this->assertStringContainsString('SIJIL PENGHARGAAN', $rendered);
    }
}
