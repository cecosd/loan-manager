<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id(); // Auto-increment ID
            $table->integer('loan_id')->unique();
            $table->foreignId('borrower_id')->constrained()->onDelete('cascade'); // Foreign key
            $table->string('borrower_name')->nullable(true);
            $table->decimal('amount', 10, 2)->nullable(true);
            $table->decimal('amount_left', 10, 2)->nullable(true);
            $table->integer('term_months')->default(120);
            $table->decimal('monthly_payment', 10, 2)->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
