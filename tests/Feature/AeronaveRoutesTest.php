<?php

namespace Tests\Feature;

use App\Http\Controllers\AeronaveController;
use Illuminate\Support\Facades\Route;
use ReflectionMethod;
use Tests\TestCase;

class AeronaveRoutesTest extends TestCase
{
    public function test_aircraft_resource_routes_point_to_existing_controller_methods(): void
    {
        $routes = [
            'aeronaves.index' => 'index',
            'aeronaves.create' => 'create',
            'aeronaves.store' => 'store',
            'aeronaves.show' => 'show',
            'aeronaves.edit' => 'edit',
            'aeronaves.update' => 'update',
            'aeronaves.destroy' => 'destroy',
            'verificar.modelo' => 'verificarModelo',
        ];

        foreach ($routes as $routeName => $method) {
            $this->assertTrue(Route::has($routeName));
            $this->assertTrue(method_exists(AeronaveController::class, $method));
            $this->assertTrue((new ReflectionMethod(AeronaveController::class, $method))->isPublic());
        }
    }
}
