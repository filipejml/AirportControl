<?php
// database/migrations/YYYY_MM_DD_HHMMSS_insert_voos_por_aeroporto_relatorio.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('relatorios')->updateOrInsert(
            ['tipo' => 'voos_por_aeroporto'],
            [
                'nome' => 'Voos por Aeroporto',
                'descricao' => 'Estatísticas de voos, passageiros e notas organizadas por aeroporto',
                'visivel_usuario' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('relatorios')
            ->where('tipo', 'voos_por_aeroporto')
            ->delete();
    }
};