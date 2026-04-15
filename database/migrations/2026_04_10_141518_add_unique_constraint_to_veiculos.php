<?php
// database/migrations/2026_04_10_000003_add_unique_constraint_to_veiculos.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Primeiro, remover a unique antiga se existir
        Schema::table('veiculos', function (Blueprint $table) {
            // Remover a constraint unique antiga da coluna codigo
            $table->dropUnique(['codigo']);
        });
        
        // Adicionar nova constraint unique composta (deposito_id + codigo)
        Schema::table('veiculos', function (Blueprint $table) {
            $table->unique(['deposito_id', 'codigo'], 'veiculos_deposito_codigo_unique');
        });
    }

    public function down(): void
    {
        Schema::table('veiculos', function (Blueprint $table) {
            $table->dropUnique('veiculos_deposito_codigo_unique');
            $table->unique('codigo');
        });
    }
};