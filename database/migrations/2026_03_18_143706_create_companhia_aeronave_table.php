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
        Schema::create('companhia_aeronave', function (Blueprint $table) {
            $table->id();
            $table->foreignId('companhia_aerea_id')->constrained('companhias_aereas')->cascadeOnDelete();
            $table->foreignId('aeronave_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companhia_aeronave');
    }
};