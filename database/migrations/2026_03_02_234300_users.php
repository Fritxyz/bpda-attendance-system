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
        Schema::create('users', function (Blueprint $table) {

            $table->id();
            $table->string('employee_id')->nullable()->unique(); // prevent duplicates
            $table->string('password')->nullable();

            // Idagdag ito para sa Web Auth (Sessions)
            $table->rememberToken();

            $table->enum('role', ['Admin', 'Employee'])->default('Employee');

            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
