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
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->date('date'); 
            
            $table->enum('type', ['Regular', 'Special Working', 'Special Non-Working', 'Local'])->default('Regular');
            
            $table->string('reference')->nullable(); 
            $table->text('remarks')->nullable();
            
            $table->timestamps();
            $table->index(['date', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};