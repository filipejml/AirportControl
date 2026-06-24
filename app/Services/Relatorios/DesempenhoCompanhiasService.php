<?php

namespace App\Services\Relatorios;

use App\Models\CompanhiaAerea;
use App\Services\VooMetricasService;
use Illuminate\Support\Collection;

class DesempenhoCompanhiasService
{
    public function gerar(array $filtros = []): array
    {
        $carregarVoos = function ($query) use ($filtros) {
            $query->with(['aeroporto', 'aeronave']);
            FiltrosRelatorioService::aplicar($query, $filtros);
        };

        $query = CompanhiaAerea::with(['voos' => $carregarVoos]);

        if (!empty($filtros['companhia_id'])) {
            $query->whereKey((int) $filtros['companhia_id']);
        }

        $companhias = $query->orderBy('nome')->get();
        $todosVoos = $companhias->flatMap->voos;

        $dados = $companhias->map(function (CompanhiaAerea $companhia) {
            $voos = $companhia->voos;
            $totalVoos = (int) $voos->sum('qtd_voos');
            $totalPassageiros = (int) $voos->sum('total_passageiros');

            return [
                'id' => $companhia->id,
                'nome' => $companhia->nome,
                'codigo' => $companhia->codigo,
                'total_voos' => $totalVoos,
                'total_passageiros' => $totalPassageiros,
                'media_passageiros_por_voo' => $totalVoos > 0
                    ? round($totalPassageiros / $totalVoos)
                    : 0,
                'total_aeroportos' => $voos->pluck('aeroporto_id')->filter()->unique()->count(),
                'total_aeronaves' => $voos->pluck('aeronave_id')->filter()->unique()->count(),
                'voos_regulares' => (int) $voos->where('tipo_voo', 'Regular')->sum('qtd_voos'),
                'voos_charter' => (int) $voos->where('tipo_voo', 'Charter')->sum('qtd_voos'),
                'nota_obj' => round(VooMetricasService::mediaPonderada($voos, 'nota_obj'), 2),
                'nota_pontualidade' => round(VooMetricasService::mediaPonderada($voos, 'nota_pontualidade'), 2),
                'nota_servicos' => round(VooMetricasService::mediaPonderada($voos, 'nota_servicos'), 2),
                'nota_patio' => round(VooMetricasService::mediaPonderada($voos, 'nota_patio'), 2),
                'media_geral' => round(VooMetricasService::mediaGeral($voos), 2),
                'aeroportos' => $voos->pluck('aeroporto.nome_aeroporto')->filter()->unique()->values(),
                'aeronaves' => $voos->pluck('aeronave.modelo')->filter()->unique()->values(),
            ];
        })->filter(fn (array $item) => $item['total_voos'] > 0)
            ->sortByDesc('total_voos')
            ->values();

        return [
            'data' => $dados,
            'totais' => $this->calcularTotais($dados, $todosVoos),
        ];
    }

    private function calcularTotais(Collection $dados, Collection $voos): array
    {
        $totalVoos = (int) $dados->sum('total_voos');
        $totalPassageiros = (int) $dados->sum('total_passageiros');

        return [
            'total_companhias' => $dados->count(),
            'total_voos' => $totalVoos,
            'total_passageiros' => $totalPassageiros,
            'media_passageiros_por_voo' => $totalVoos > 0
                ? round($totalPassageiros / $totalVoos)
                : 0,
            'media_geral' => round(VooMetricasService::mediaGeral($voos), 2),
        ];
    }

}
