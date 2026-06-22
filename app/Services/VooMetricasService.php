<?php

namespace App\Services;

use Illuminate\Support\Collection;

class VooMetricasService
{
    private const CAMPOS_NOTA = [
        'nota_obj',
        'nota_pontualidade',
        'nota_servicos',
        'nota_patio',
        'media_notas',
    ];

    public static function mediaPonderada(Collection $voos, string $campo): float
    {
        self::validarCampoNota($campo);

        $voosComNota = $voos->whereNotNull($campo);
        $pesoTotal = $voosComNota->sum('qtd_voos');

        if ($pesoTotal <= 0) {
            return 0;
        }

        return (float) ($voosComNota->sum(
            fn ($voo) => (float) $voo->{$campo} * (int) $voo->qtd_voos
        ) / $pesoTotal);
    }

    public static function mediaGeral(Collection $voos): float
    {
        $medias = collect([
            self::mediaPonderada($voos, 'nota_obj'),
            self::mediaPonderada($voos, 'nota_pontualidade'),
            self::mediaPonderada($voos, 'nota_servicos'),
            self::mediaPonderada($voos, 'nota_patio'),
        ])->filter(fn ($nota) => $nota > 0);

        return $medias->isEmpty() ? 0 : (float) $medias->avg();
    }

    public static function mediaPonderadaQuery($query, string $campo): float
    {
        self::validarCampoNota($campo);

        return (float) ($query
            ->whereNotNull($campo)
            ->selectRaw(
                "COALESCE(SUM(qtd_voos * {$campo}) / NULLIF(SUM(qtd_voos), 0), 0) as media"
            )
            ->value('media') ?? 0);
    }

    private static function validarCampoNota(string $campo): void
    {
        if (!in_array($campo, self::CAMPOS_NOTA, true)) {
            throw new \InvalidArgumentException("Campo de nota inválido: {$campo}");
        }
    }
}
