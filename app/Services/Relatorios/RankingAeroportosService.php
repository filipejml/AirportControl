<?php

namespace App\Services\Relatorios;

use App\Models\Aeroporto;
use App\Services\VooMetricasService;
use Illuminate\Support\Collection;

class RankingAeroportosService
{
    public function gerar(
        ?string $periodo = null,
        string $ordenacao = 'total_voos'
    ): array {
        $carregarVoos = function ($query) use ($periodo) {
            $this->aplicarPeriodo($query, $periodo);
        };

        $aeroportos = Aeroporto::with(['voos' => $carregarVoos])
            ->orderBy('nome_aeroporto')
            ->get();
        $todosVoos = $aeroportos->flatMap->voos;

        $dados = $aeroportos->map(function (Aeroporto $aeroporto) {
            $voos = $aeroporto->voos;
            $totalVoos = (int) $voos->sum('qtd_voos');
            $totalPassageiros = (int) $voos->sum('total_passageiros');

            return [
                'id' => $aeroporto->id,
                'nome' => $aeroporto->nome_aeroporto,
                'total_voos' => $totalVoos,
                'total_passageiros' => $totalPassageiros,
                'media_passageiros_por_voo' => $totalVoos > 0
                    ? round($totalPassageiros / $totalVoos)
                    : 0,
                'total_companhias' => $voos->pluck('companhia_aerea_id')
                    ->filter()
                    ->unique()
                    ->count(),
                'total_aeronaves' => $voos->pluck('aeronave_id')
                    ->filter()
                    ->unique()
                    ->count(),
                'voos_regulares' => (int) $voos->where('tipo_voo', 'Regular')->sum('qtd_voos'),
                'voos_charter' => (int) $voos->where('tipo_voo', 'Charter')->sum('qtd_voos'),
                'nota_obj' => round(VooMetricasService::mediaPonderada($voos, 'nota_obj'), 2),
                'nota_pontualidade' => round(VooMetricasService::mediaPonderada($voos, 'nota_pontualidade'), 2),
                'nota_servicos' => round(VooMetricasService::mediaPonderada($voos, 'nota_servicos'), 2),
                'nota_patio' => round(VooMetricasService::mediaPonderada($voos, 'nota_patio'), 2),
                'media_geral' => round(VooMetricasService::mediaGeral($voos), 2),
            ];
        })->filter(fn (array $item) => $item['total_voos'] > 0)
            ->sortByDesc($ordenacao)
            ->values()
            ->map(function (array $item, int $indice) {
                $item['posicao'] = $indice + 1;
                return $item;
            });

        return [
            'data' => $dados,
            'totais' => $this->calcularTotais($dados, $todosVoos, $ordenacao),
        ];
    }

    private function calcularTotais(
        Collection $dados,
        Collection $voos,
        string $ordenacao
    ): array {
        $totalVoos = (int) $dados->sum('total_voos');
        $totalPassageiros = (int) $dados->sum('total_passageiros');

        return [
            'total_aeroportos' => $dados->count(),
            'total_voos' => $totalVoos,
            'total_passageiros' => $totalPassageiros,
            'media_passageiros_por_voo' => $totalVoos > 0
                ? round($totalPassageiros / $totalVoos)
                : 0,
            'media_geral' => round(VooMetricasService::mediaGeral($voos), 2),
            'lider' => $dados->first()
                ? [
                    'nome' => $dados->first()['nome'],
                    'valor' => $dados->first()[$ordenacao],
                    'criterio' => $ordenacao,
                ]
                : null,
        ];
    }

    private function aplicarPeriodo($query, ?string $periodo): void
    {
        match ($periodo) {
            'hoje' => $query->whereDate('created_at', today()),
            'semana' => $query->whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ]),
            'mes' => $query->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year),
            'ano' => $query->whereYear('created_at', now()->year),
            default => null,
        };
    }
}
