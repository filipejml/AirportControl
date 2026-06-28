<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminCrudRoutesTest extends TestCase
{
    public function test_crud_routes_require_admin_middleware(): void
    {
        $adminRoutes = [
            'voos.index',
            'voos.create',
            'voos.store',
            'voos.edit',
            'voos.update',
            'voos.destroy',
            'companhias.index',
            'companhias.create',
            'companhias.store',
            'companhias.edit',
            'companhias.update',
            'companhias.destroy',
            'aeronaves.index',
            'aeronaves.create',
            'aeronaves.store',
            'aeronaves.edit',
            'aeronaves.update',
            'aeronaves.destroy',
            'aeroportos.index',
            'aeroportos.create.step1',
            'aeroportos.edit',
            'aeroportos.update',
            'aeroportos.destroy',
            'aeroportos.depositos.index',
            'aeroportos.depositos.create',
            'aeroportos.depositos.store',
            'aeroportos.depositos.edit',
            'aeroportos.depositos.update',
            'aeroportos.depositos.destroy',
            'aeroportos.depositos.veiculos.index',
            'aeroportos.depositos.veiculos.create',
            'aeroportos.depositos.veiculos.store',
            'aeroportos.depositos.veiculos.edit',
            'aeroportos.depositos.veiculos.update',
            'aeroportos.depositos.veiculos.destroy',
            'fabricantes.index',
            'fabricantes.create',
            'fabricantes.store',
            'fabricantes.edit',
            'fabricantes.update',
            'fabricantes.destroy',
            'admin.users.index',
            'admin.users.create',
            'admin.users.store',
            'admin.users.edit',
            'admin.users.update',
            'admin.users.destroy',
        ];

        foreach ($adminRoutes as $routeName) {
            $route = Route::getRoutes()->getByName($routeName);

            $this->assertNotNull($route, "A rota {$routeName} não foi encontrada.");
            $this->assertContains('auth', $route->gatherMiddleware());
            $this->assertContains('admin', $route->gatherMiddleware());
        }
    }

    public function test_common_information_routes_remain_available_to_authenticated_users(): void
    {
        $commonRoutes = [
            'dashboard',
            'relatorios',
            'companhias.informacoes',
            'companhias.ranking',
            'companhias.dashboard',
            'aeronaves.informacoes',
            'aeronaves.dashboard',
            'aeronaves.ranking',
            'aeroportos.informacoes',
            'aeroportos.dashboard',
        ];

        foreach ($commonRoutes as $routeName) {
            $route = Route::getRoutes()->getByName($routeName);

            $this->assertNotNull($route, "A rota {$routeName} não foi encontrada.");
            $this->assertContains('auth', $route->gatherMiddleware());
            $this->assertNotContains('admin', $route->gatherMiddleware());
        }
    }
}
