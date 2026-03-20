<?php
// database/migrations/2026_03_19_122000_add_timestamps_to_aeroporto_companhia_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aeroporto_companhia', function (Blueprint $table) {
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('aeroporto_companhia', function (Blueprint $table) {
            $table->dropColumn(['created_at', 'updated_at']);
        });
    }
};