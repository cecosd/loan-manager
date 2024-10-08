<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'loan_id' => $this->formatted_loan_id, // Using the accessor for formatted loan ID
            'borrower_id' => $this->borrower_id,
            'borrower_name' => $this->borrower->name,
            'amount' => $this->amount,
            'amount_left' => $this->amount_left,
            'term_months' => $this->term_months,
            'monthly_payment' => $this->monthly_payment,
        ];
    }
}