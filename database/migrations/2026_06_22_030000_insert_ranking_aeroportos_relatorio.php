<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('relatorios')->updateOrInsert(
            ['tipo' => 'ranking_aeroportos'],
            [
                'nome' => 'Ranking de Aeroportos',
                'descricao' => 'Classificação dos aeroportos por voos, passageiros, cobertura e avaliação operacional',
                'visivel_usuario' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('relatorios')
            ->where('tipo', 'ranking_aeroportos')
            ->delete();
    }
};
