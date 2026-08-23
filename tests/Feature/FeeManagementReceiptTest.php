<?php

namespace Tests\Feature;

use App\Models\Cohort;
use App\Models\FeeInvoice;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeeManagementReceiptTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_generate_and_pay_fee_invoice_with_receipt(): void
    {
        $school = School::create([
            'name' => 'Tadika Amal Cawangan Melawati',
            'code' => 'TAM-04',
        ]);

        $cohort = Cohort::create([
            'school_id' => $school->id,
            'name' => 'Al-Ghazali 6 Tahun',
            'academic_year' => 2026,
            'age_group' => '6_years',
        ]);

        $student = Student::create([
            'school_id' => $school->id,
            'cohort_id' => $cohort->id,
            'name' => 'Fatimah Az-Zahra Binti Luqman',
            'gender' => 'Perempuan',
            'is_active' => true,
        ]);

        $admin = User::create([
            'name' => 'Kerani Kewangan',
            'email' => 'finance@tadikaamal.edu.my',
            'password' => bcrypt('password123'),
        ]);

        $invoice = FeeInvoice::create([
            'school_id' => $school->id,
            'student_id' => $student->id,
            'cohort_id' => $cohort->id,
            'invoice_number' => 'INV-2026-0001',
            'receipt_number' => 'RCT-2026-0001',
            'academic_year' => 2026,
            'month' => 8,
            'fee_category' => 'yuran_bulanan',
            'amount' => 450.00,
            'status' => 'paid',
            'payment_method' => 'fpx_online',
            'paid_at' => now(),
            'processed_by' => $admin->id,
        ]);

        $this->assertEquals('paid', $invoice->status);
        $this->assertEquals(450.00, (float) $invoice->amount);
        $this->assertEquals('Yuran Pengajian Bulanan', $invoice->category_label);
        $this->assertDatabaseHas('fee_invoices', [
            'invoice_number' => 'INV-2026-0001',
            'receipt_number' => 'RCT-2026-0001',
            'status' => 'paid',
        ]);
    }
}
