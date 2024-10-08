<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoanRequest; // Ensure this request class has the necessary validation rules
use App\Http\Resources\LoanResource;
use App\Services\LoanService; // Import the LoanService class
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    protected $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService; // Inject the LoanService
    }

    // Display a listing of loans
    public function index(): JsonResponse
    {
        $loans = $this->loanService->getAllLoans(); // Use the service to get loans
        return response()->json($loans, 200); // Return the LoanResource collection as JSON
    }

    // Show the form for creating a new loan
    public function create(): JsonResponse
    {
        $borrowers = $this->loanService->getAllBorrowers(); // Use the service to get borrowers
        return response()->json($borrowers, 200); // 200 OK status
    }

    // Store a newly created loan in storage
    public function store(LoanRequest $request): JsonResponse
    {
        $loanResource = $this->loanService->createLoan($request->validated());

        // Yes, I know. Magically the code knows what is the error.
        if ($loanResource['error']) {
            return response()->json([
                'message' => $loanResource['error']
            ], 400); // 400 Bad Request
        }

        return response()->json([
            'loan' => $loanResource,
            'message' => 'Loan created successfully!'
        ], 201);
    }

    // Destroy a specific loan
    public function destroy($id): JsonResponse
    {
        $loan = $this->loanService->findLoanById($id); // Find the loan by ID

        if(!$loan) {
            return response()->json([
                'message' => 'The loan you are trying to delete does not exist.'
            ], 400); // 200 OK status
        }
        
        $loan->delete(); // Delete the loan

        return response()->json([
            'message' => 'Loan deleted successfully!'
        ], 200); // 200 OK status
    }
}