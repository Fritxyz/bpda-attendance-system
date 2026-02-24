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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            $table->string('employee_id')->unique(); // prevent duplicates

            $table->string('first_name');
            $table->string('middle_name')->nullable(); // not everyone has one
            $table->string('last_name');

            $table->string('bureau')->nullable();
            $table->string('division')->nullable();
            $table->string('position')->nullable();

            // Money field
            $table->decimal('salary', 12, 2)->nullable();

            // Employment type (better name than status)
            $table->enum('employment_type', ['Permanent', 'Contractual', 'Job Order'])->default('Permanent');
            
            // Employee role
            $table->enum('role', ['Admin', 'Employee'])->default('Employee');

            // Active flag
            $table->boolean('is_active')->default(true);

            // username and password
            $table->string('username')->unique()->nullable();
            $table->string('password')->nullable();

            $table->timestamps();

        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
