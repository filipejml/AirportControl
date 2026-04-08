<?php
// database/migrations/2026_04_08_000000_recalculate_media_notas_for_existing_voos.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Garantir que a coluna media_notas existe
        if (!Schema::hasColumn('voos', 'media_notas')) {
            Schema::table('voos', function (Blueprint $table) {
                $table->decimal('media_notas', 4, 2)->nullable()->after('nota_patio')
                      ->comment('Média das notas (A=10, B=9, C=8, D=6, E=4, F=2)');
            });
        }

        // Recalcular média das notas para todos os voos existentes
        $this->recalculateAllMediaNotas();
    }

    public function down(): void
    {
        // Não remover a coluna, apenas reverter os cálculos se necessário
        // Os valores originais serão mantidos
    }

    private function recalculateAllMediaNotas(): void
    {
        $mapaNotas = [
            'A' => 10,
            'B' => 9,
            'C' => 8,
            'D' => 6,
            'E' => 4,
            'F' => 2
        ];

        // Buscar todos os voos
        $voos = DB::table('voos')->get();

        foreach ($voos as $voo) {
            $notas = [];
            
            // Converter notas de letra para número (se armazenadas como letra)
            if ($voo->nota_obj && is_string($voo->nota_obj) && isset($mapaNotas[$voo->nota_obj])) {
                $notas[] = $mapaNotas[$voo->nota_obj];
            } elseif ($voo->nota_obj && is_numeric($voo->nota_obj)) {
                $notas[] = (int) $voo->nota_obj;
            }
            
            if ($voo->nota_pontualidade && is_string($voo->nota_pontualidade) && isset($mapaNotas[$voo->nota_pontualidade])) {
                $notas[] = $mapaNotas[$voo->nota_pontualidade];
            } elseif ($voo->nota_pontualidade && is_numeric($voo->nota_pontualidade)) {
                $notas[] = (int) $voo->nota_pontualidade;
            }
            
            if ($voo->nota_servicos && is_string($voo->nota_servicos) && isset($mapaNotas[$voo->nota_servicos])) {
                $notas[] = $mapaNotas[$voo->nota_servicos];
            } elseif ($voo->nota_servicos && is_numeric($voo->nota_servicos)) {
                $notas[] = (int) $voo->nota_servicos;
            }
            
            if ($voo->nota_patio && is_string($voo->nota_patio) && isset($mapaNotas[$voo->nota_patio])) {
                $notas[] = $mapaNotas[$voo->nota_patio];
            } elseif ($voo->nota_patio && is_numeric($voo->nota_patio)) {
                $notas[] = (int) $voo->nota_patio;
            }
            
            // Calcular média se houver notas
            $mediaNotas = null;
            if (count($notas) > 0) {
                $mediaNotas = array_sum($notas) / count($notas);
                $mediaNotas = round($mediaNotas, 2);
            }
            
            // Atualizar o registro
            DB::table('voos')
                ->where('id', $voo->id)
                ->update(['media_notas' => $mediaNotas]);
        }
    }
};