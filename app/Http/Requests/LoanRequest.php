<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoanRequest extends FormRequest
{
    // Determine if the user is authorized to make this request
    public function authorize()
    {
        return true; // Change this to your authorization logic if needed
    }

    // Validation rules
    public function rules()
    {
        return [
            'borrower_id' => 'nullable|exists:borrowers,id', // Ensure borrower_id exists if provided
            'borrower_name' => 'required_if:borrower_id,null|string|max:255', // Required if borrower_id is not present
            'amount' => 'required|numeric|min:1|max:80000', // Ensure amount is a positive number
            'term_months' => 'required|integer|between:3,120', // This ensures the term is between 3 and 120 months
        ];
    }

    // Custom messages (optional)
    public function messages()
    {
        return [
            'borrower_id.required' => 'The borrower field is required.',
            'borrower_id.exists' => 'The selected borrower does not exist.',
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.min' => 'The amount must be at least 1.',
            'amount.min' => 'The amount must be max 80000.',
        ];
    }
}