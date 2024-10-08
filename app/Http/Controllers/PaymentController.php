<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest; // Create a request for validation
use App\Http\Resources\PaymentResource;
use App\Models\Loan;
use App\Models\Payment; // Import the Payment model
use Illuminate\Http\JsonResponse;

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
        $responseMessage = null;
        $finalPaymentAmount = null;
        // find the loan
        $loan = Loan::findOrFail($id);
        // check the if the loan is met in full
        if($loan->amount_left === "0.00") {
            return response()->json([
                'message' => 'The loan is paid in full. No need for additional payment.',
            ], 200);
        }

        // does the payment exceed the needed amount
        $amountLeftAfterPayment = $loan->amount_left - $request->payment_amount;

        if($amountLeftAfterPayment < 0) { // the payment is more than enough
            $amountLeftAfterPayment *= -1;
            $finalPaymentAmount = $request->payment_amount - $amountLeftAfterPayment; // the final amount is the amountLeft substracted from the payment request amount
            $loan->amount_left = 0;
            $responseMessage = "Loan met in full. Please return to the customer this amount {$amountLeftAfterPayment}.";
        } else {
            $responseMessage = 'Payment is processed successfully!';
            $loan->amount_left = $amountLeftAfterPayment;
            $finalPaymentAmount = $request->payment_amount;
        }

        $loan->save();

        // substract from the total loans amount from the borrower
        $loan->borrower->total_loans_amount -= $request->payment_amount;
        $loan->borrower->total_loans_amount = $loan->borrower->total_loans_amount < 0 ? 0 : $loan->borrower->total_loans_amount;
        $loan->borrower->save();


        $payment = Payment::create([
            'loan_id' => $loan->id,
            'payment_amount' => $finalPaymentAmount,
        ]);
        // Return a success response
        return response()->json([
            'message' =>  $responseMessage,
            'payment' => new PaymentResource($payment),
        ], 201); // 201 Created status 
    }
}