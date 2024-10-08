<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    // Determine if the user is authorized to make this request
    public function authorize()
    {
        return true;
    }

    // Validation rules
    public function rules()
    {
        return [
            'payment_amount' => 'required|numeric|min:1|max:80000', // Validate payment amount
        ];
    }
}