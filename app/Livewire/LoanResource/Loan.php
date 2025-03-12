<?php

namespace App\Livewire\LoanResource;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Loan as ModelsLoan;

class Loan extends Component
{

    public $customer_name;
    public $car_price;
    public $installment_months;
    public $contract_no = "AGR00001";

    public $down_payment;
    public $monthly_installment;
    public $schedule = [];

    public $interest_rate;
    public $total_interest;
    public $total_loan;
    public $loan_amount;

    public function calculate()
    {
        // **Validasi Input**
        $this->validate([
            'customer_name' => 'required',
            'car_price' => 'required|numeric|min:1000000',
            'installment_months' => 'required|integer|min:1|max:60',
        ]);

        // **1. Hitung DP (20%)**
        $this->down_payment = $this->car_price * 0.2;

        // **2. Hitung Pokok Utang**
        $this->loan_amount = $this->car_price - $this->down_payment;

        // **3. Tentukan Bunga Sesuai Jangka Waktu**
        if ($this->installment_months <= 12) {
            $this->interest_rate = 0.12; // 12%
        } elseif ($this->installment_months <= 24) {
            $this->interest_rate = 0.14; // 14%
        } else {
            $this->interest_rate = 0.165; // 16.5%
        }

        // **4. Hitung Total Bunga**
        $this->total_interest = $this->loan_amount * $this->interest_rate;

        // **5. Hitung Total Hutang**
        $this->total_loan = $this->loan_amount + $this->total_interest;

        // **6. Hitung Angsuran Per Bulan**
        $this->monthly_installment = round($this->total_loan / $this->installment_months, -3);

        // **7. Buat Jadwal Angsuran**
        $this->generateSchedule();
    }

    private function generateSchedule()
    {
        $this->schedule = [];
        $due_date = Carbon::now()->addMonth(); // Angsuran pertama bulan depan

        for ($i = 1; $i <= $this->installment_months; $i++) {
            $this->schedule[] = [
                'contract_no' => $this->contract_no,
                'installment_no' => $i,
                'monthly_installment' => $this->monthly_installment,
                'due_date' => $due_date->format('Y-m-d'),
            ];
            $due_date->addMonth(); // Tambah 1 bulan untuk angsuran berikutnya
        }
    }


    public function render()
    {
        return view('livewire.loan-resource.loan')->layout('components.layouts.app');
    }
}
