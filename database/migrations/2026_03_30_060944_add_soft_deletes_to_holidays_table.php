<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::table('holidays', function (Blueprint $table) {
        $table->softDeletes(); // Ito ang magdadagdag ng 'deleted_at' column
        });
    }

    public function down(): void
    {
        Schema::table('holidays', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
