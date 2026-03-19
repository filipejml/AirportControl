<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AeronaveController;
use App\Http\Controllers\CompanhiaAereaController;
use App\Http\Controllers\AeroportoController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\FabricanteController;
use App\Http\Controllers\VooController;

/*
|--------------------------------------------------------------------------
| ROTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

/*
|--------------------------------------------------------------------------
| ROTAS AUTENTICADAS
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/home', [AuthController::class, 'home'])->name('home');
    Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // NOVO: Rotas para voos (acessível para todos usuários autenticados)
    Route::resource('voos', VooController::class);

    // NOVO: Rota AJAX para buscar aeronaves por companhia
    Route::get('/api/companhias/{companhia}/aeronaves', [VooController::class, 'getAeronavesByCompanhia']);

    /*
    |--------------------------------------------------------------------------
    | ROTAS ADMIN
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->group(function () {
        Route::get('/registros', function () {
            return view('admin.registros.index');
        })->name('registros');

        Route::resource('relatorios.admin', RelatorioController::class)
            ->except(['index', 'show']);

        Route::resource('fabricantes', FabricanteController::class);
        Route::resource('aeronaves', AeronaveController::class, [
            'parameters' => [
                'aeronaves' => 'aeronave' // Força o nome correto
            ]
        ]);
        
        Route::resource('companhias', CompanhiaAereaController::class);
        Route::resource('aeroportos', AeroportoController::class);;
    });
});