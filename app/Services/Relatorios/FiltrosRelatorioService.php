<?php

namespace App\Services\Relatorios;

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
