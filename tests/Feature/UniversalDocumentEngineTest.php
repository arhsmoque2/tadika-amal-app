<?php

namespace Tests\Feature;

use App\Models\Cohort;
use App\Models\FeeInvoice;
use App\Models\School;
use App\Models\Student;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class UniversalDocumentEngineTest extends TestCase
{
    public function test_assessment_report_pdf_template_compiles(): void
    {
        $school = new School(['name' => 'Tadika Amal Cawangan Cyberjaya']);
        $cohort = new Cohort(['name' => 'Al-Fateh 6 Tahun']);
        $cohort->setRelation('teacher', null);
        $student = new Student([
            'name' => 'Ahmad Bilal Bin Hilmi',
            'gender' => 'Lelaki',
            'mykid' => '200101-10-1234',
            'birth_date' => '2020-01-01',
        ]);
        $student->setRelation('cohort', $cohort);
        $student->setRelation('school', $school);

        $view = View::make('reports.annual-assessment-pdf', [
            'student' => $student,
            'cohort' => $cohort,
            'school' => $school,
            'period' => 'Akhir Tahun',
            'academicYear' => '2026',
            'skillsByDomain' => collect(),
            'evaluations' => collect(),
            'reportMeta' => null,
            'printedDate' => now()->format('d/m/Y'),
        ]);
        $this->assertNotNull($view);
        $rendered = $view->render();
        $this->assertStringContainsString('TADIKA AMAL', $rendered);
        $this->assertStringContainsString('KSPK', $rendered);
    }

    public function test_official_receipt_pdf_template_compiles_with_lhdn_relief(): void
    {
        $school = new School([
            'name' => 'Tadika Amal Cawangan Cyberjaya',
            'code' => 'TAC-01',
            'address' => 'No. 1, Jalan Tadika, Cyberjaya',
            'phone' => '+60123456789',
        ]);
        $cohort = new Cohort(['name' => 'Al-Fateh 6 Tahun']);
        $student = new Student([
            'name' => 'Fatimah Az-Zahra Binti Luqman',
            'guardian_name' => 'Encik Luqman',
            'mykid' => '200101-10-5678',
        ]);
        $invoice = new FeeInvoice([
            'invoice_number' => 'INV-2026-01-001-Ogo',
            'fee_category' => 'yuran_bulanan',
            'amount' => 350.00,
            'month' => 'Ogos',
            'academic_year' => 2026,
            'payment_method' => 'fpx_online',
            'paid_at' => now(),
        ]);

        $view = View::make('reports.official-fee-receipt-pdf', [
            'invoice' => $invoice,
            'student' => $student,
            'cohort' => $cohort,
            'school' => $school,
            'receiptNo' => 'REC-202608-0001',
            'printedDate' => now()->format('d/m/Y H:i'),
        ]);
        $this->assertNotNull($view);
        $rendered = $view->render();
        $this->assertStringContainsString('RESIT RASMI PEMBAYARAN YURAN', $rendered);
        $this->assertStringContainsString('46(1)(r)', $rendered);
    }

    public function test_certificate_award_template_compiles(): void
    {
        $school = new School(['name' => 'Tadika Amal Cawangan Cyberjaya']);
        $cohort = new Cohort(['name' => 'Al-Fateh 6 Tahun']);
        $student = new Student([
            'name' => 'Ahmad Bilal Bin Hilmi',
            'mykid' => '200101-10-1234',
        ]);
        $student->id = 1;
        $student->setRelation('cohort', $cohort);

        $view = View::make('reports.certificate-award', [
            'student' => $student,
            'awardTitle' => 'Murid Cemerlang Iqra',
            'academicYear' => '2026',
            'school' => $school,
            'issueDate' => now()->locale('ms')->translatedFormat('d F Y'),
        ]);
        $this->assertNotNull($view);
        $rendered = $view->render();
        $this->assertStringContainsString('SIJIL PENGHARGAAN', $rendered);
    }
}
