<?php
// database/migrations/2026_05_13_000000_add_tipo_to_relatorios_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('relatorios', function (Blueprint $table) {
            if (!Schema::hasColumn('relatorios', 'tipo')) {
                $table->string('tipo')->nullable()->after('descricao');
            }
        });
        
        // Inserir o relatório padrão se não existir
        $relatorioExistente = DB::table('relatorios')
            ->where('tipo', 'companhias_por_aeroporto')
            ->first();
            
        if (!$relatorioExistente) {
            DB::table('relatorios')->insert([
                'nome' => 'Companhias por Aeroporto',
                'descricao' => 'Lista de todas as companhias aéreas organizadas por aeroporto',
                'tipo' => 'companhias_por_aeroporto',
                'visivel_usuario' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('relatorios', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
};