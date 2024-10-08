<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    // Fillable attributes for mass assignment
    protected $fillable = [
        'loan_id',
        'borrower_id',
        'borrower_name',
        'amount',
        'term_months',
        'monthly_payment',
    ];

    // Boot method to auto-increment loan_id on creation
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Get the last loan ID from the database, or start from 1 if none exists
            $lastLoanId = self::max('loan_id') ?? 0; 
            $model->loan_id = $lastLoanId + 1; // Increment by 1 for the new loan ID
        });
    }

    // Calculate monthly payment
    public function calculateMonthlyPayment()
    {
        $annualInterestRate = 0.079; // 7.9%
        $monthlyInterestRate = $annualInterestRate / 12;
        $termInMonths = $this->term_months;

        return ($this->amount * $monthlyInterestRate) / (1 - pow(1 + $monthlyInterestRate, -$termInMonths));
    }

    // Accessor to format the loan ID with leading zeros
    public function getFormattedLoanIdAttribute()
    {
        return str_pad($this->loan_id, 7, '0', STR_PAD_LEFT); // Formats to 7 digits with leading zeros
    }

    /**
     * Define the relationship with the Payment model.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Define relationships
    public function borrower()
    {
        return $this->belongsTo(Borrower::class); // Assuming you have a Borrower model
    }

    // Example: Scope to filter loans by amount
    public function scopeAmountGreaterThan($query, $amount)
    {
        return $query->where('amount', '>', $amount);
    }

    // Example: Scope to filter loans by borrower
    public function scopeForBorrower($query, $borrowerId)
    {
        return $query->where('borrower_id', $borrowerId);
    }

    // Mutator to ensure the amount is always stored as a float
    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = floatval($value);
    }

    public function loanDetails()
    {
        return [
            'loan_id' => $this->formatted_loan_id,
            'borrower_id' => $this->borrower_id,
            'amount' => $this->amount,
            'amount_left' => $this->amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}