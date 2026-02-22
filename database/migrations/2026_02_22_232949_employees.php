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
        //
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            $table->string('employee_id')->unique(); // prevent duplicates

            $table->string('first_name');
            $table->string('middle_name')->nullable(); // not everyone has one
            $table->string('last_name');

            $table->string('position');
            $table->string('division');

            // Money field
            $table->decimal('salary', 12, 2);

            // Employment type (better name than status)
            $table->enum('employment_type', ['permanent', 'contractual', 'job_order']);

            // Active flag
            $table->boolean('is_active')->default(true);

            $table->timestamps(); // very important
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
