<?php

namespace Database\Factories;

use App\Models\Loan;
use App\Models\Borrower;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition()
    {
        $amount = $this->generateRandomEvenAmount();
        $borrower = Borrower::factory()->make();
        $borrower->total_loans_amount = $amount;
        $borrower->save();
        $protoLoan = Loan::make([
            'amount' => $amount,
            'term_months' => 120,
        ]);
        return [
            'borrower_id' => $borrower->id, 
            'borrower_name' => $borrower->name,
            'amount' => $amount,
            'amount_left' => $amount,
            'term_months' => 120,
            'monthly_payment' => $protoLoan->calculateMonthlyPayment()
        ];
    }

    /**
     * Generate a random even number for the loan amount.
     *
     * @return int
     */
    private function generateRandomEvenAmount(): int
    {
        return rand(5000, 40000) * 2; // Generates a random even number between 10000 and 100000
    }
}