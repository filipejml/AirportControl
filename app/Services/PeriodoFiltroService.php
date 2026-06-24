<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;

class PeriodoFiltroService
{
    public const PERIODOS_RELATORIO = ['hoje', 'semana', 'mes', 'ano'];
    public const PERIODOS_DETALHADOS = ['geral', 'semanal', 'mensal', 'anual'];

    public static function filtrosDetalhadosFromRequest(Request $request): array
    {
        return [
            'periodo' => $request->get('periodo', 'geral'),
            'semana' => $request->get('semana'),
            'ano' => $request->get('ano'),
            'mes' => $request->get('mes'),
            'ano_selecionado' => $request->get('ano_selecionado'),
        ];
    }

    public static function aplicarPeriodoRelatorio(
        EloquentBuilder|QueryBuilder $query,
        ?string $periodo,
        string $campo = 'created_at'
    ): void {
        $intervalo = self::intervaloPeriodoRelatorio($periodo);

        if ($intervalo) {
            $query->whereBetween($campo, $intervalo);
        }
    }

    public static function aplicarPeriodoDetalhado(
        EloquentBuilder|QueryBuilder $query,
        array $filters,
        string $campo = 'created_at'
    ): void {
        $intervalo = self::intervaloPeriodoDetalhado($filters);

        if ($intervalo) {
            $query->whereBetween($campo, $intervalo);
        }
    }

    public static function intervaloPeriodoRelatorio(?string $periodo): ?array
    {
        return match ($periodo) {
            'hoje' => [now()->startOfDay(), now()->endOfDay()],
            'semana' => [now()->startOfWeek(), now()->endOfWeek()],
            'mes' => [now()->startOfMonth(), now()->endOfMonth()],
            'ano' => [now()->startOfYear(), now()->endOfYear()],
            default => null,
        };
    }

    public static function intervaloPeriodoDetalhado(array $filters): ?array
    {
        return match ($filters['periodo'] ?? 'geral') {
            'semanal' => self::intervaloSemana($filters['semana'] ?? null),
            'mensal' => self::intervaloMes($filters['ano'] ?? null, $filters['mes'] ?? null),
            'anual' => self::intervaloAno($filters['ano_selecionado'] ?? null),
            default => null,
        };
    }

    public static function semanasDisponiveis(int $quantidade = 52): \Illuminate\Support\Collection
    {
        $semanas = collect();

        for ($i = 0; $i < $quantidade; $i++) {
            $data = now()->subWeeks($i);
            $semanas->push((object) [
                'semana' => $data->format('Y-\WW'),
                'numero_semana' => $data->weekOfYear,
                'ano' => $data->year,
            ]);
        }

        return $semanas->unique('semana')->values();
    }

    private static function intervaloSemana(?string $semanaSelecionada): ?array
    {
        if (!$semanaSelecionada || !str_contains($semanaSelecionada, '-W')) {
            return null;
        }

        [$ano, $semana] = explode('-W', $semanaSelecionada, 2);

        if (!ctype_digit($ano) || !ctype_digit($semana)) {
            return null;
        }

        $inicio = Carbon::now()->setISODate((int) $ano, (int) $semana)->startOfWeek();

        return [$inicio, $inicio->copy()->endOfWeek()];
    }

    private static function intervaloMes(null|string|int $ano, null|string|int $mes): ?array
    {
        if (!$ano || !$mes || !ctype_digit((string) $ano) || !ctype_digit((string) $mes)) {
            return null;
        }

        $mes = (int) $mes;

        if ($mes < 1 || $mes > 12) {
            return null;
        }

        $inicio = Carbon::create((int) $ano, $mes, 1)->startOfDay();

        return [$inicio, $inicio->copy()->endOfMonth()];
    }

    private static function intervaloAno(null|string|int $ano): ?array
    {
        if (!$ano || !ctype_digit((string) $ano)) {
            return null;
        }

        $inicio = Carbon::create((int) $ano, 1, 1)->startOfYear();

        return [$inicio, $inicio->copy()->endOfYear()];
    }
}
