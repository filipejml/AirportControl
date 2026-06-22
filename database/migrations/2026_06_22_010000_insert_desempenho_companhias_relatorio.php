<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('relatorios')->updateOrInsert(
            ['tipo' => 'desempenho_companhias'],
            [
                'nome' => 'Desempenho das Companhias',
                'descricao' => 'Comparativo de voos, passageiros, cobertura operacional e avaliações das companhias aéreas',
                'visivel_usuario' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('relatorios')
            ->where('tipo', 'desempenho_companhias')
            ->delete();
    }
};
