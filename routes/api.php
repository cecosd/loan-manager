<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



// Routes for Loan operations
Route::get('/loans', [LoanController::class, 'index']); // Display a listing of loans
Route::post('/loans', [LoanController::class, 'store']); // Store a newly created loan
Route::get('/loans/{id}', [LoanController::class, 'show']); // Show a specific loan (if needed)
Route::put('/loans/{id}', [LoanController::class, 'update']); // Update a specific loan (if needed)
Route::delete('/loans/{id}', [LoanController::class, 'destroy']); // Delete a specific loan (if needed)

// Routes for Payment operations
Route::get('/loans/{id}/payments', [PaymentController::class, 'show']); // Show payment history for a specific loan
Route::post('/loans/{id}/payments', [PaymentController::class, 'store']); // Process a payment for a specific loan


