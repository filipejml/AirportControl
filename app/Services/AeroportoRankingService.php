<?php

namespace App\Services;

use App\Models\Aeroporto;
use Illuminate\Support\Collection;

class AeroportoRankingService
{
    public function generateRankings(array $filters = []): array
    {
        $rankings = Aeroporto::with(['voos' => function ($query) use ($filters) {
            PeriodoFiltroService::aplicarPeriodoDetalhado($query, $filters);
        }])->orderBy('nome_aeroporto')->get()->map(function (Aeroporto $aeroporto) {
            $voos = $aeroporto->voos;
            $medias = [
                'media_objetivo' => VooMetricasService::mediaPonderada($voos, 'nota_obj'),
                'media_pontualidade' => VooMetricasService::mediaPonderada($voos, 'nota_pontualidade'),
                'media_servicos' => VooMetricasService::mediaPonderada($voos, 'nota_servicos'),
                'media_patio' => VooMetricasService::mediaPonderada($voos, 'nota_patio'),
            ];

            return [
                'id' => $aeroporto->id,
                'nome' => $aeroporto->nome_aeroporto,
                'total_voos' => (int) $voos->sum('qtd_voos'),
                'total_passageiros' => (int) $voos->sum('total_passageiros'),
                'total_companhias' => $voos->pluck('companhia_aerea_id')->filter()->unique()->count(),
                'total_aeronaves' => $voos->pluck('aeronave_id')->filter()->unique()->count(),
                'media_objetivo' => round($medias['media_objetivo'], 1),
                'media_pontualidade' => round($medias['media_pontualidade'], 1),
                'media_servicos' => round($medias['media_servicos'], 1),
                'media_patio' => round($medias['media_patio'], 1),
                'nota_geral' => round($this->mediaGeral($medias), 1),
            ];
        });

        $avaliados = $rankings->filter(fn (array $item) => $item['nota_geral'] > 0)->values();

        return [
            'rankings_por_nota' => $this->ordenar($avaliados, 'nota_geral'),
            'rankings_objetivo' => $this->ordenarCategoria($rankings, 'media_objetivo'),
            'rankings_pontualidade' => $this->ordenarCategoria($rankings, 'media_pontualidade'),
            'rankings_servicos' => $this->ordenarCategoria($rankings, 'media_servicos'),
            'rankings_patio' => $this->ordenarCategoria($rankings, 'media_patio'),
            'rankings_por_voos' => $this->ordenar($rankings, 'total_voos'),
            'rankings_por_passageiros' => $this->ordenar($rankings, 'total_passageiros'),
            'estatisticas' => $this->estatisticas($rankings, $avaliados),
        ];
    }

    private function mediaGeral(array $medias): float
    {
        $avaliadas = collect($medias)->filter(fn (float $media) => $media > 0);
        return $avaliadas->isEmpty() ? 0 : (float) $avaliadas->avg();
    }

    private function ordenarCategoria(Collection $rankings, string $campo): Collection
    {
        return $this->ordenar($rankings->filter(fn (array $item) => $item[$campo] > 0), $campo);
    }

    private function ordenar(Collection $rankings, string $campo): Collection
    {
        return $rankings->sort(fn (array $a, array $b) =>
            $b[$campo] <=> $a[$campo]
            ?: $b['total_voos'] <=> $a['total_voos']
            ?: strcasecmp($a['nome'], $b['nome'])
        )->values();
    }

    private function estatisticas(Collection $rankings, Collection $avaliados): array
    {
        $voosAvaliados = $avaliados->sum('total_voos');
        $mediaGeral = $voosAvaliados > 0
            ? $avaliados->sum(fn (array $item) => $item['nota_geral'] * $item['total_voos']) / $voosAvaliados
            : 0;

        return [
            'total_aeroportos' => $rankings->count(),
            'aeroportos_ativos' => $rankings->where('total_voos', '>', 0)->count(),
            'aeroportos_avaliados' => $avaliados->count(),
            'total_voos' => $rankings->sum('total_voos'),
            'total_passageiros' => $rankings->sum('total_passageiros'),
            'media_geral' => round($mediaGeral, 1),
        ];
    }
}
