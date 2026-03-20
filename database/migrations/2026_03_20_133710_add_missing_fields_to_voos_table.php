<?php
// database/migrations/2026_03_20_000000_add_missing_fields_to_voos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('voos', function (Blueprint $table) {
            // Adicionar campo tipo_aeronave se não existir
            if (!Schema::hasColumn('voos', 'tipo_aeronave')) {
                $table->enum('tipo_aeronave', ['PC', 'MC', 'LC'])->nullable()->after('tipo_voo');
            }
            
            // Adicionar campo media_notas se não existir
            if (!Schema::hasColumn('voos', 'media_notas')) {
                $table->decimal('media_notas', 4, 2)->nullable()->after('nota_patio')
                      ->comment('Média das notas');
            }
        });
    }

    public function down(): void
    {
        Schema::table('voos', function (Blueprint $table) {
            if (Schema::hasColumn('voos', 'tipo_aeronave')) {
                $table->dropColumn('tipo_aeronave');
            }
            if (Schema::hasColumn('voos', 'media_notas')) {
                $table->dropColumn('media_notas');
            }
        });
    }
};