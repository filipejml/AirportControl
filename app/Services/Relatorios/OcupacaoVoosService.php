<?php

namespace App\Services\Relatorios;

use App\Models\Voo;
use App\Services\VooMetricasService;
use Illuminate\Support\Collection;

class OcupacaoVoosService
{
    public function gerar(
        array $filtros = [],
        ?string $faixa = null
    ): array {
        $query = Voo::with(['companhiaAerea', 'aeroporto', 'aeronave'])
            ->orderByDesc('created_at');

        FiltrosRelatorioService::aplicar($query, $filtros);

        $voos = $query->get();

        $dados = $voos->map(function (Voo $voo) {
            $capacidade = (int) ($voo->aeronave?->capacidade ?? 0);
            $assentosOfertados = $capacidade * (int) $voo->qtd_voos;
            $totalPassageiros = (int) $voo->total_passageiros;
            $taxaOcupacao = $assentosOfertados > 0
                ? round(($totalPassageiros / $assentosOfertados) * 100, 2)
                : 0;

            return [
                'id' => $voo->id,
                'id_voo' => $voo->id_voo,
                'data' => $voo->created_at?->toDateString(),
                'companhia' => $voo->companhiaAerea?->nome ?? 'Não informada',
                'companhia_codigo' => $voo->companhiaAerea?->codigo,
                'aeroporto' => $voo->aeroporto?->nome_aeroporto ?? 'Não informado',
                'aeronave' => $voo->aeronave?->modelo ?? 'Não informada',
                'capacidade' => $capacidade,
                'qtd_voos' => (int) $voo->qtd_voos,
                'passageiros_por_voo' => (int) $voo->qtd_passageiros,
                'total_passageiros' => $totalPassageiros,
                'assentos_ofertados' => $assentosOfertados,
                'taxa_ocupacao' => $taxaOcupacao,
                'faixa_ocupacao' => $this->classificarOcupacao($taxaOcupacao),
                'tipo_voo' => $voo->tipo_voo,
                'media_geral' => round(VooMetricasService::mediaGeral(collect([$voo])), 2),
            ];
        });

        if ($faixa) {
            $dados = $dados->where('faixa_ocupacao', $faixa);
            $ids = $dados->pluck('id');
            $voos = $voos->whereIn('id', $ids);
        }

        $dados = $dados->sortByDesc('taxa_ocupacao')->values();

        return [
            'data' => $dados,
            'totais' => $this->calcularTotais($dados, $voos),
            'distribuicao' => $this->calcularDistribuicao($dados),
        ];
    }

    private function calcularTotais(Collection $dados, Collection $voos): array
    {
        $totalVoos = (int) $dados->sum('qtd_voos');
        $totalPassageiros = (int) $dados->sum('total_passageiros');
        $assentosOfertados = (int) $dados->sum('assentos_ofertados');
        $taxaGeral = $assentosOfertados > 0
            ? round(($totalPassageiros / $assentosOfertados) * 100, 2)
            : 0;

        return [
            'total_registros' => $dados->count(),
            'total_voos' => $totalVoos,
            'total_passageiros' => $totalPassageiros,
            'assentos_ofertados' => $assentosOfertados,
            'taxa_ocupacao_geral' => $taxaGeral,
            'media_geral' => round(VooMetricasService::mediaGeral($voos), 2),
            'maior_ocupacao' => $dados->first()
                ? [
                    'id_voo' => $dados->first()['id_voo'],
                    'taxa_ocupacao' => $dados->first()['taxa_ocupacao'],
                ]
                : null,
        ];
    }

    private function calcularDistribuicao(Collection $dados): array
    {
        return [
            'baixa' => $dados->where('faixa_ocupacao', 'baixa')->count(),
            'media' => $dados->where('faixa_ocupacao', 'media')->count(),
            'alta' => $dados->where('faixa_ocupacao', 'alta')->count(),
            'lotado' => $dados->where('faixa_ocupacao', 'lotado')->count(),
        ];
    }

    private function classificarOcupacao(float $taxa): string
    {
        return match (true) {
            $taxa < 50 => 'baixa',
            $taxa < 75 => 'media',
            $taxa < 100 => 'alta',
            default => 'lotado',
        };
    }

}
