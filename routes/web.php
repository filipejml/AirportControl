<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AeronaveController;
use App\Http\Controllers\CompanhiaAereaController;
use App\Http\Controllers\AeroportoController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\FabricanteController;

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

        // REMOVA esta linha duplicada:
        // Route::post('/fabricantes', [FabricanteController::class, 'store'])->name('fabricantes.store');

        // Use apenas os resources com os parâmetros corretos
        Route::resource('fabricantes', FabricanteController::class);
        Route::resource('aeronaves', AeronaveController::class, [
            'parameters' => [
                'aeronaves' => 'aeronave' // Força o nome correto
            ]
        ])->except(['show']);
        Route::resource('companhias', CompanhiaAereaController::class)->except(['show']);
        Route::resource('aeroportos', AeroportoController::class)->except(['show']);
    });
});