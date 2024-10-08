<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrower extends Model
{
    use HasFactory;

    // Fillable properties for mass assignment
    protected $fillable = [
        'name',
        'total_loans_amount',
        'max_loans_amount',
    ];

    // Relationship with the Loan model
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}