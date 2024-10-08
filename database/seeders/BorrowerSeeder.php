<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Borrower;
use App\Models\Loan;

class BorrowerSeeder extends Seeder
{
    public function run()
    {
        // Create 10 borrowers
        for ($i = 0; $i < 10; $i++) {
            $borrower = Borrower::factory()->create(); // Create a borrower

            // Create a loan with a random even number for this borrower
            $amount = $this->generateRandomEvenAmount();
            $loan = Loan::make([
                'borrower_id' => $borrower->id,
                'borrower_name' => $borrower->name,
                'amount' => $amount,
                'amount_left' => $amount,
                'term_months' => 120,
            ]);

            $loan->monthly_payment = $loan->calculateMonthlyPayment();
            $loan->save();


        }
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