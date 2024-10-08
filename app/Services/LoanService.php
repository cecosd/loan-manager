<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\Borrower;
use App\Http\Resources\LoanResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LoanService
{
    /**
     * Retrieve all loans as LoanResources.
     *
     * @return AnonymousResourceCollection
     */
    public function getAllLoans(): AnonymousResourceCollection
    {
        // Eager load borrowers with loans
        $loans = Loan::with('borrower')->get(); // Use eager loading

        // Return the loans as a JSON response, formatted with LoanResource
        return LoanResource::collection($loans);
    }

    /**
     * Create a new loan.
     *
     * @param array $data
     */
    public function createLoan(array $data)
    {
        // I know we can have many John Does on this planet.
        // For the sake of this task, please ignore.
        // Thank you!
        if(isset($data['borrower_name'])) {
            $borrowerExists = Borrower::where("name", '=', $data['borrower_name'])->first(); 
        } else if(isset($data['borrower_id'])) {
            $borrowerExists = Borrower::findOrFail($data['borrower_id']);
        }

        $testNewLoanExceedingLinits = $borrowerExists->total_loans_amount + $data['amount'] > $borrowerExists->max_loans_amount;

        if($testNewLoanExceedingLinits) {
            // Will be Custom Exception
            // Soon ;)
            // Famous last words...
            return [
                'error' => "Exceeding user limits: {$borrowerExists->max_loans_amount}"
            ];
        }

        $borrowerExists->total_loans_amount += $data['amount'];
        $borrowerExists->save();

        
        $loanData = [
            'amount' => $data['amount'],
            'term_months' => $data['term_months'],
            'borrower_id' => $borrowerExists ? $borrowerExists->id : null,
            'borrower_name' => $borrowerExists ? $borrowerExists->name : null
        ];

        if(isset($data['borrower_name']) && !$borrowerExists) {
            $loanData['borrower_name'] = $data['borrower_name'];
            $borrowerExists = Borrower::factory()->create([
                'name' => $data['borrower_name']
            ]);
            $loanData['borrower_id'] = $borrowerExists->id;
        }
        
        $loan = Loan::make($loanData);

        $loan->amount_left = $loan->amount;
        $loan->borrower_name = $borrowerExists->name;
        $loan->monthly_payment = $loan->calculateMonthlyPayment();
        $loan->save();

        return new LoanResource($loan);
    }

    /**
     * Fetch all borrowers for dropdown.
     *
     * @return Collection
     */
    public function getAllBorrowers(): Collection
    {
        return Borrower::all();
    }

    /**
     * Find a loan by its ID.
     *
     * @param int $id
     * @return Loan
     */
    public function findLoanById(int $id): Loan
    {
        return Loan::findOrFail($id);
    }

    /**
     * Handle the loan payment process.
     *
     * @param Loan $loan
     * @param float $paymentAmount
     * @return void
     */
    public function makePayment(Loan $loan, float $paymentAmount): void
    {
        $borrower = $loan->borrower;
        $borrower->handleLoanPayment($loan, $paymentAmount);
    }
}