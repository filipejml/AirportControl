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

    // Rotas para voos - CRUD completo
    Route::resource('voos', VooController::class);

    // Rota AJAX para buscar aeronaves por companhia - página de cadastro de voo
    Route::get('/api/companhias/{companhia}/aeronaves', [VooController::class, 'getAeronavesByCompanhia']);

    // Rota AJAX para verificar ID do voo - página de cadastro de voo (para evitar duplicidade)
    Route::post('/api/verificar-id-voo', [VooController::class, 'verificarIdVoo'])->name('verificar.id.voo');

    // Rota para exportar CSV - página de listagem de voos
    Route::get('/voos/export/csv', [VooController::class, 'exportCSV'])->name('voos.export.csv');

    // Rota AJAX para verificar nome da companhia aérea - página de cadastro de companhia (para evitar duplicidade)
    Route::post('/companhias/check-name', [CompanhiaAereaController::class, 'checkName'])->name('companhias.check-name');

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

        // Verifica se o modelo de aeronave já existe (usado para validação AJAX no formulário de cadastrado)
        Route::get('/api/verificar-modelo', [AeronaveController::class, 'verificarModelo'])->name('verificar.modelo');
    });
});