<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('relatorios')->updateOrInsert(
            ['tipo' => 'ocupacao_voos'],
            [
                'nome' => 'Ocupação dos Voos',
                'descricao' => 'Taxa estimada de ocupação com base em passageiros transportados e capacidade das aeronaves',
                'visivel_usuario' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('relatorios')
            ->where('tipo', 'ocupacao_voos')
            ->delete();
    }
};
