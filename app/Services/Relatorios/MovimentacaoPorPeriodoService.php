<?php

namespace App\Services\Relatorios;

use App\Models\Voo;
use App\Services\VooMetricasService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MovimentacaoPorPeriodoService
{
    public function gerar(
        string $agrupamento = 'mes',
        ?string $dataInicio = null,
        ?string $dataFim = null,
        array $filtros = []
    ): array {
        $query = Voo::query()->orderBy('created_at');

        if (!empty($filtros['periodo'])) {
            FiltrosRelatorioService::aplicarPeriodo($query, $filtros['periodo']);
        }

        if (!empty($filtros['aeroporto_id'])) {
            $query->where('aeroporto_id', (int) $filtros['aeroporto_id']);
        }

        if (!empty($filtros['companhia_id'])) {
            $query->where('companhia_aerea_id', (int) $filtros['companhia_id']);
        }

        if (!empty($filtros['aeronave_id'])) {
            $query->where('aeronave_id', (int) $filtros['aeronave_id']);
        }

        if ($dataInicio) {
            $query->where('created_at', '>=', Carbon::parse($dataInicio)->startOfDay());
        }

        if ($dataFim) {
            $query->where('created_at', '<=', Carbon::parse($dataFim)->endOfDay());
        }

        $voos = $query->get();

        $dados = $voos
            ->groupBy(fn (Voo $voo) => $this->chavePeriodo($voo->created_at, $agrupamento))
            ->map(fn (Collection $grupo, string $chave) => $this->resumirPeriodo(
                $grupo,
                $chave,
                $agrupamento
            ))
            ->sortBy('ordem')
            ->values();

        $dados = $this->adicionarVariacao($dados);

        return [
            'data' => $dados,
            'totais' => $this->calcularTotais($dados, $voos),
        ];
    }

    private function resumirPeriodo(
        Collection $voos,
        string $chave,
        string $agrupamento
    ): array {
        $totalVoos = (int) $voos->sum('qtd_voos');
        $totalPassageiros = (int) $voos->sum('total_passageiros');
        [$inicio, $fim, $label] = $this->descricaoPeriodo($chave, $agrupamento);

        return [
            'chave' => $chave,
            'label' => $label,
            'ordem' => $inicio->timestamp,
            'data_inicio' => $inicio->toDateString(),
            'data_fim' => $fim->toDateString(),
            'total_voos' => $totalVoos,
            'total_passageiros' => $totalPassageiros,
            'media_passageiros_por_voo' => $totalVoos > 0
                ? round($totalPassageiros / $totalVoos)
                : 0,
            'voos_regulares' => (int) $voos->where('tipo_voo', 'Regular')->sum('qtd_voos'),
            'voos_charter' => (int) $voos->where('tipo_voo', 'Charter')->sum('qtd_voos'),
            'media_geral' => round(VooMetricasService::mediaGeral($voos), 2),
            'total_aeroportos' => $voos->pluck('aeroporto_id')->unique()->count(),
            'total_companhias' => $voos->pluck('companhia_aerea_id')->unique()->count(),
        ];
    }

    private function adicionarVariacao(Collection $dados): Collection
    {
        return $dados->map(function (array $item, int $indice) use ($dados) {
            $anterior = $indice > 0 ? $dados[$indice - 1]['total_voos'] : null;

            $item['variacao_percentual'] = $anterior && $anterior > 0
                ? round((($item['total_voos'] - $anterior) / $anterior) * 100, 2)
                : null;

            return $item;
        });
    }

    private function calcularTotais(Collection $dados, Collection $voos): array
    {
        $totalVoos = (int) $dados->sum('total_voos');
        $totalPassageiros = (int) $dados->sum('total_passageiros');
        $maiorMovimento = $dados->sortByDesc('total_voos')->first();

        return [
            'total_periodos' => $dados->count(),
            'total_voos' => $totalVoos,
            'total_passageiros' => $totalPassageiros,
            'media_passageiros_por_voo' => $totalVoos > 0
                ? round($totalPassageiros / $totalVoos)
                : 0,
            'media_geral' => round(VooMetricasService::mediaGeral($voos), 2),
            'maior_movimento' => $maiorMovimento
                ? [
                    'label' => $maiorMovimento['label'],
                    'total_voos' => $maiorMovimento['total_voos'],
                ]
                : null,
        ];
    }

    private function chavePeriodo(Carbon $data, string $agrupamento): string
    {
        return match ($agrupamento) {
            'dia' => $data->format('Y-m-d'),
            'semana' => $data->format('o-\WW'),
            'ano' => $data->format('Y'),
            default => $data->format('Y-m'),
        };
    }

    private function descricaoPeriodo(
        string $chave,
        string $agrupamento
    ): array {
        return match ($agrupamento) {
            'dia' => $this->descricaoDia($chave),
            'semana' => $this->descricaoSemana($chave),
            'ano' => $this->descricaoAno($chave),
            default => $this->descricaoMes($chave),
        };
    }

    private function descricaoDia(string $chave): array
    {
        $data = Carbon::createFromFormat('Y-m-d', $chave)->startOfDay();
        return [$data, $data->copy()->endOfDay(), $data->format('d/m/Y')];
    }

    private function descricaoSemana(string $chave): array
    {
        [$ano, $semana] = explode('-W', $chave);
        $inicio = Carbon::now()->setISODate((int) $ano, (int) $semana)->startOfWeek();
        $fim = $inicio->copy()->endOfWeek();

        return [
            $inicio,
            $fim,
            "Semana {$semana} ({$inicio->format('d/m')}–{$fim->format('d/m')})",
        ];
    }

    private function descricaoMes(string $chave): array
    {
        $inicio = Carbon::createFromFormat('Y-m', $chave)->startOfMonth();
        $fim = $inicio->copy()->endOfMonth();

        return [$inicio, $fim, $inicio->format('m/Y')];
    }

    private function descricaoAno(string $chave): array
    {
        $inicio = Carbon::create((int) $chave)->startOfYear();
        return [$inicio, $inicio->copy()->endOfYear(), $chave];
    }
}
