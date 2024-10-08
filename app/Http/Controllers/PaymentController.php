<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest; // Create a request for validation
use App\Http\Resources\PaymentResource;
use App\Models\Borrower;
use App\Models\Loan;
use App\Models\Payment; // Import the Payment model
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Show the payment history for a loan.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $loan = Loan::findOrFail($id);
        $payments = $loan->payments; // Retrieve payments related to the loan

        return response()->json(PaymentResource::collection($payments), 200); // 200 OK status
    }

    /**
     * Process the loan payment.
     *
     * @param PaymentRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function store(PaymentRequest $request, $id): JsonResponse
    {
        $paymentExceedsLoan = false;
        $cancelPayment = false;
        $loan = Loan::findOrFail($id);
        $borrower = Borrower::findOrFail($loan->borrower_id);
        $paymentData = [
            'loan_id' => $loan->id,
            'payment_amount' => $request->payment_amount,
        ];

        $amountLeft = $loan->amount_left - $request->payment_amount;

        if($amountLeft < 0) {
            $paymentExceedsLoan = true;
            $loan->amount_left = $request->payment_amount - $amountLeft;
        }
        $loan->amount_left = $amountLeft;
        if($loan->amount_left < 0) {
            $loan->amount_left = 0;
        }
        $loan->save();

        $borrower->total_loans_amount -= $request->payment_amount;
        if($borrower->total_loans_amount < 0) {
            $borrower->total_loans_amount = 0;
        }
        $borrower->save();

        $responseMessage = 'Payment processed successfully!';
        if($paymentExceedsLoan) {
            $exceedAmount = abs($amountLeft);
            $responseMessage = "Loan met in full. Change from the payed amount is {$exceedAmount}";
        } else if($loan->amount_left === 0) {
            $responseMessage = 'The loan is met in full already.';
            $cancelPayment = true;
        }

        if(!$cancelPayment) {
            $payment = Payment::create($paymentData);
            // Return a success response
            return response()->json([
                'message' =>  $responseMessage,
                'payment' => new PaymentResource($payment),
            ], 201); // 201 Created status 
        }

        // Return a success response
        return response()->json([
            'message' =>  $responseMessage,
        ], 201); // 201 Created status

    }
}