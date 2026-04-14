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
        Schema::create('travel_orders', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            
            // Document Info
            $table->string('to_number')->unique(); // Travel Order Number (e.g., TO-2026-001)
            $table->string('destination');
            $table->text('purpose');
            
            // Dates (Important for Attendance logic)
            $table->date('date_from');
            $table->date('date_to');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_orders');
    }
};
