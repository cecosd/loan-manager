<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'payment_amount',
    ];

    /**
     * Define the relationship with the Loan model.
     */
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}