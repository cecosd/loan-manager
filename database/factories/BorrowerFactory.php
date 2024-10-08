<?php

namespace Database\Factories;

use App\Models\Borrower;
use Illuminate\Database\Eloquent\Factories\Factory;

class BorrowerFactory extends Factory
{
    protected $model = Borrower::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'total_loans_amount' => 0,
            'max_loans_amount' => '80000',
        ];
    }
}