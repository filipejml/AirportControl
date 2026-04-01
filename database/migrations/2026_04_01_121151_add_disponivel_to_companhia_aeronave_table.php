// database/migrations/2026_04_01_000000_add_disponivel_to_companhia_aeronave_table.php
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
        Schema::table('companhia_aeronave', function (Blueprint $table) {
            $table->boolean('disponivel')->default(true)->after('aeronave_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companhia_aeronave', function (Blueprint $table) {
            $table->dropColumn('disponivel');
        });
    }
};