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
         Schema::create('leave_credit_ledger', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            
            $table->date('transaction_date');
            $table->string('period', 7); // '2026-03' format
            
            $table->enum('type', [
                'INITIAL',      // Yung manual na initial balance ng HR
                'ACCRUAL',      // Monthly +1.25
                'LATE',         // Deduction dahil sa late
                'UNDERTIME',    // Deduction dahil sa undertime
                'ABSENT',       // Deduction dahil absent
                'LEAVE_USED',   // Nag-file ng leave (approved)
                'ADJUSTMENT',   // Manual correction ng HR
            ]);
            
            // Positive = credit, Negative = deduction
            $table->decimal('amount', 8, 4); 
            
            $table->string('description')->nullable();
            
            // Optional reference sa attendance record
            $table->unsignedBigInteger('reference_id')->nullable();
            
            $table->timestamps();
            
            // Indexes para sa speed
            $table->index(['employee_id', 'period']);
            $table->index(['employee_id', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_credit_ledger');
    }
};
