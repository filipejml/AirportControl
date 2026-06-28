<?php

namespace App\Services;

use App\Models\CompanhiaAerea;
use Illuminate\Support\Collection;

class CompanhiaRankingService
{
    public function generateRankings(array $filters = []): array
    {
        $companhias = CompanhiaAerea::query()
            ->with(['voos' => function ($query) use ($filters) {
                PeriodoFiltroService::aplicarPeriodoDetalhado($query, $filters);
            }])
            ->orderBy('nome')
            ->get();

        $rankings = $companhias->map(function (CompanhiaAerea $companhia) {
            $voos = $companhia->voos;
            $medias = [
                'media_objetivo' => VooMetricasService::mediaPonderada($voos, 'nota_obj'),
                'media_pontualidade' => VooMetricasService::mediaPonderada($voos, 'nota_pontualidade'),
                'media_servicos' => VooMetricasService::mediaPonderada($voos, 'nota_servicos'),
                'media_patio' => VooMetricasService::mediaPonderada($voos, 'nota_patio'),
            ];

            return [
                'id' => $companhia->id,
                'nome' => $companhia->nome,
                'codigo' => $companhia->codigo,
                'total_voos' => (int) $voos->sum('qtd_voos'),
                'total_passageiros' => (int) $voos->sum('total_passageiros'),
                'total_aeroportos' => $voos->pluck('aeroporto_id')->filter()->unique()->count(),
                'total_aeronaves' => $voos->pluck('aeronave_id')->filter()->unique()->count(),
                'media_objetivo' => round($medias['media_objetivo'], 1),
                'media_pontualidade' => round($medias['media_pontualidade'], 1),
                'media_servicos' => round($medias['media_servicos'], 1),
                'media_patio' => round($medias['media_patio'], 1),
                'nota_geral' => round($this->mediaGeralDasCategorias($medias), 1),
            ];
        });

        $comAvaliacoes = $rankings
            ->filter(fn (array $item) => $item['nota_geral'] > 0)
            ->values();

        return [
            'rankings_por_nota' => $this->ordenar($comAvaliacoes, 'nota_geral'),
            'rankings_objetivo' => $this->ordenarCategoria($rankings, 'media_objetivo'),
            'rankings_pontualidade' => $this->ordenarCategoria($rankings, 'media_pontualidade'),
            'rankings_servicos' => $this->ordenarCategoria($rankings, 'media_servicos'),
            'rankings_patio' => $this->ordenarCategoria($rankings, 'media_patio'),
            'rankings_por_voos' => $this->ordenar($rankings, 'total_voos'),
            'rankings_por_passageiros' => $this->ordenar($rankings, 'total_passageiros'),
            'estatisticas' => $this->estatisticas($rankings, $comAvaliacoes),
        ];
    }

    private function mediaGeralDasCategorias(array $medias): float
    {
        $avaliadas = collect($medias)->filter(fn (float $media) => $media > 0);

        return $avaliadas->isEmpty() ? 0 : (float) $avaliadas->avg();
    }

    private function ordenarCategoria(Collection $rankings, string $campo): Collection
    {
        return $this->ordenar(
            $rankings->filter(fn (array $item) => $item[$campo] > 0),
            $campo
        );
    }

    private function ordenar(Collection $rankings, string $campo): Collection
    {
        return $rankings
            ->sort(function (array $a, array $b) use ($campo) {
                return $b[$campo] <=> $a[$campo]
                    ?: $b['total_voos'] <=> $a['total_voos']
                    ?: strcasecmp($a['nome'], $b['nome']);
            })
            ->values();
    }

    private function estatisticas(Collection $rankings, Collection $comAvaliacoes): array
    {
        $voosAvaliados = $comAvaliacoes->sum('total_voos');
        $mediaGeral = $voosAvaliados > 0
            ? $comAvaliacoes->sum(
                fn (array $item) => $item['nota_geral'] * $item['total_voos']
            ) / $voosAvaliados
            : 0;

        return [
            'total_companhias' => $rankings->count(),
            'companhias_ativas' => $rankings->where('total_voos', '>', 0)->count(),
            'companhias_avaliadas' => $comAvaliacoes->count(),
            'total_voos' => $rankings->sum('total_voos'),
            'total_passageiros' => $rankings->sum('total_passageiros'),
            'media_geral' => round($mediaGeral, 1),
        ];
    }
}
