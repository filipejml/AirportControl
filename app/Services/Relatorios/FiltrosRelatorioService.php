<?php

namespace App\Services\Relatorios;

use App\Services\PeriodoFiltroService;

class FiltrosRelatorioService
{
    public static function aplicar($query, array $filtros): void
    {
        self::aplicarPeriodo($query, $filtros['periodo'] ?? null);

        if (!empty($filtros['aeroporto_id'])) {
            $query->where('aeroporto_id', (int) $filtros['aeroporto_id']);
        }

        if (!empty($filtros['companhia_id'])) {
            $query->where('companhia_aerea_id', (int) $filtros['companhia_id']);
        }

        if (!empty($filtros['aeronave_id'])) {
            $query->where('aeronave_id', (int) $filtros['aeronave_id']);
        }
    }

    public static function aplicarPeriodo($query, ?string $periodo): void
    {
        PeriodoFiltroService::aplicarPeriodoRelatorio($query, $periodo);
    }
}
