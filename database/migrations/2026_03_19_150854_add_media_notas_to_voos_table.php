<?php
// database/migrations/2026_03_19_150854_add_media_notas_to_voos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('voos', function (Blueprint $table) {
            // Check if column doesn't exist before adding
            if (!Schema::hasColumn('voos', 'media_notas')) {
                $table->decimal('media_notas', 4, 2)->nullable()->after('nota_patio')
                      ->comment('Média das notas (A=10, B=9, C=8, D=6, E=4, F=2)');
            }
        });
    }

    public function down(): void
    {
        Schema::table('voos', function (Blueprint $table) {
            $table->dropColumn('media_notas');
        });
    }
};