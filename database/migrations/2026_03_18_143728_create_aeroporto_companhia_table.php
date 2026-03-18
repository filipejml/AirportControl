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
        Schema::create('aeroporto_companhia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aeroporto_id')->constrained()->cascadeOnDelete();
            $table->foreignId('companhia_aerea_id')->constrained('companhias_aereas')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aeroporto_companhia');
    }
};