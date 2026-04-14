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
        Schema::create('leave_ledgers', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            
            // Detalye ng transaction (e.g., "Monthly Earned", "Late/Undertime Deduction")
            $table->string('description'); 
            
            // Dito papasok ang value. 
            // Positive (1.250) kung dagdag, Negative (-0.125) kung bawas.
            $table->decimal('amount', 8, 3); 
            
            // Para madaling ma-filter: 'credit' (dagdag) o 'debit' (bawas)
            $table->enum('type', ['credit', 'debit']); 
            
            // Mahalaga ito para sa historical tracking
            $table->date('transaction_date'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_ledgers');
    }
};
