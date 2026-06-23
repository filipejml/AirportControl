<?php

namespace Tests\Feature;

use App\Models\Relatorio;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RelatorioCatalogTest extends TestCase
{
    public function test_admin_routes_only_expose_catalog_visibility_actions(): void
    {
        $this->assertTrue(Route::has('admin.relatorios.index'));
        $this->assertTrue(Route::has('admin.relatorios.toggle-visibilidade'));
        $this->assertTrue(Route::has('admin.relatorios.companhias-por-aeroporto'));
        $this->assertTrue(Route::has('admin.relatorios.voos-por-aeroporto'));
        $this->assertTrue(Route::has('admin.relatorios.desempenho-companhias'));
        $this->assertTrue(Route::has('admin.relatorios.movimentacao-por-periodo'));
        $this->assertTrue(Route::has('admin.relatorios.ranking-aeroportos'));
        $this->assertTrue(Route::has('admin.relatorios.ocupacao-voos'));

        $this->assertFalse(Route::has('admin.relatorios.create'));
        $this->assertFalse(Route::has('admin.relatorios.store'));
        $this->assertFalse(Route::has('admin.relatorios.edit'));
        $this->assertFalse(Route::has('admin.relatorios.update'));
        $this->assertFalse(Route::has('admin.relatorios.destroy'));
    }

    public function test_report_types_resolve_to_their_application_routes(): void
    {
        $companhias = new Relatorio([
            'tipo' => Relatorio::TIPO_COMPANHIAS_POR_AEROPORTO,
        ]);
        $voos = new Relatorio([
            'tipo' => Relatorio::TIPO_VOOS_POR_AEROPORTO,
        ]);
        $desempenho = new Relatorio([
            'tipo' => Relatorio::TIPO_DESEMPENHO_COMPANHIAS,
        ]);
        $movimentacao = new Relatorio([
            'tipo' => Relatorio::TIPO_MOVIMENTACAO_POR_PERIODO,
        ]);
        $rankingAeroportos = new Relatorio([
            'tipo' => Relatorio::TIPO_RANKING_AEROPORTOS,
        ]);
        $ocupacaoVoos = new Relatorio([
            'tipo' => Relatorio::TIPO_OCUPACAO_VOOS,
        ]);

        $this->assertSame('relatorios.companhias-por-aeroporto', $companhias->route);
        $this->assertSame('admin.relatorios.companhias-por-aeroporto', $companhias->admin_route);
        $this->assertSame('relatorios.voos-por-aeroporto', $voos->route);
        $this->assertSame('admin.relatorios.voos-por-aeroporto', $voos->admin_route);
        $this->assertSame('relatorios.desempenho-companhias', $desempenho->route);
        $this->assertSame('admin.relatorios.desempenho-companhias', $desempenho->admin_route);
        $this->assertSame('relatorios.movimentacao-por-periodo', $movimentacao->route);
        $this->assertSame('admin.relatorios.movimentacao-por-periodo', $movimentacao->admin_route);
        $this->assertSame('relatorios.ranking-aeroportos', $rankingAeroportos->route);
        $this->assertSame('admin.relatorios.ranking-aeroportos', $rankingAeroportos->admin_route);
        $this->assertSame('relatorios.ocupacao-voos', $ocupacaoVoos->route);
        $this->assertSame('admin.relatorios.ocupacao-voos', $ocupacaoVoos->admin_route);
    }
}
