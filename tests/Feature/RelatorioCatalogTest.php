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

        $this->assertSame('relatorios.companhias-por-aeroporto', $companhias->route);
        $this->assertSame('admin.relatorios.companhias-por-aeroporto', $companhias->admin_route);
        $this->assertSame('relatorios.voos-por-aeroporto', $voos->route);
        $this->assertSame('admin.relatorios.voos-por-aeroporto', $voos->admin_route);
    }
}
