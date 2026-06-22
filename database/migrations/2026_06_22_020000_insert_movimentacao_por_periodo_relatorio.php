<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('relatorios')->updateOrInsert(
            ['tipo' => 'movimentacao_por_periodo'],
            [
                'nome' => 'Movimentação por Período',
                'descricao' => 'Evolução de voos, passageiros e avaliações agrupada por dia, semana, mês ou ano',
                'visivel_usuario' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('relatorios')
            ->where('tipo', 'movimentacao_por_periodo')
            ->delete();
    }
};
