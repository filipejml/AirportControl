<?php

namespace Tests\Unit;

use App\Services\VooMetricasService;
use PHPUnit\Framework\TestCase;

class VooMetricasServiceTest extends TestCase
{
    public function test_weighted_average_uses_flight_quantity_instead_of_record_count(): void
    {
        $voos = collect([
            (object) ['qtd_voos' => 1, 'nota_obj' => 10],
            (object) ['qtd_voos' => 9, 'nota_obj' => 2],
        ]);

        $this->assertSame(2.8, VooMetricasService::mediaPonderada($voos, 'nota_obj'));
    }

    public function test_general_average_ignores_categories_without_ratings(): void
    {
        $voos = collect([
            (object) [
                'qtd_voos' => 2,
                'nota_obj' => 8,
                'nota_pontualidade' => null,
                'nota_servicos' => 6,
                'nota_patio' => null,
            ],
        ]);

        $this->assertSame(7.0, VooMetricasService::mediaGeral($voos));
    }
}
