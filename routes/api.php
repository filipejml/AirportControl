<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

use App\Http\Controllers\AeronaveController;
use App\Http\Controllers\AeroportoController;
use App\Http\Controllers\CompanhiaAereaController;
use App\Http\Controllers\DepositoController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\VeiculoController;
use App\Http\Controllers\VooController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Seu projeto não usa API, então mantenha apenas esta rota básica
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Data endpoints consumed by the authenticated Blade frontend.
Route::middleware(['web', 'auth', 'throttle:internal-api'])->group(function () {
    Route::middleware('cache.relatorios')->prefix('relatorios')->name('api.relatorios.')->group(function () {
        Route::get('/companhias-por-aeroporto', [RelatorioController::class, 'apiCompanhiasPorAeroporto'])->name('companhias-por-aeroporto');
        Route::get('/voos-por-aeroporto', [RelatorioController::class, 'apiVoosPorAeroporto'])->name('voos-por-aeroporto');
        Route::get('/desempenho-companhias', [RelatorioController::class, 'apiDesempenhoCompanhias'])->name('desempenho-companhias');
        Route::get('/movimentacao-por-periodo', [RelatorioController::class, 'apiMovimentacaoPorPeriodo'])->name('movimentacao-por-periodo');
        Route::get('/ranking-aeroportos', [RelatorioController::class, 'apiRankingAeroportos'])->name('ranking-aeroportos');
        Route::get('/ocupacao-voos', [RelatorioController::class, 'apiOcupacaoVoos'])->name('ocupacao-voos');
    });

    Route::middleware('admin')->group(function () {
        Route::post('/companhias/check-code', [CompanhiaAereaController::class, 'checkCode'])->name('companhias.check-code');
        Route::post('/companhias/check-name', [CompanhiaAereaController::class, 'checkName'])->name('companhias.check-name');
        Route::post('/companhias/{companhia}/aeronaves/{aeronave}/disponibilidade', [CompanhiaAereaController::class, 'atualizarDisponibilidade'])->name('companhias.aeronaves.disponibilidade');
        Route::get('/companhias/{companhiaId}/aeronaves', [VooController::class, 'getAeronavesByCompanhia'])->name('api.companhias.aeronaves');
        Route::get('/buscar-companhia/{codigo}', [VooController::class, 'buscarCompanhiaPorCodigo'])->name('buscar.companhia');
        Route::post('/verificar-id-voo', [VooController::class, 'verificarIdVoo'])->name('verificar.id.voo');
        Route::get('/verificar-modelo', [AeronaveController::class, 'verificarModelo'])->name('verificar.modelo');
        Route::post('/aeroportos/check-name', [AeroportoController::class, 'checkName'])->name('aeroportos.check-name');
        Route::post('/aeroportos/veiculos/template', [AeroportoController::class, 'getVeiculoTemplate'])->name('aeroportos.veiculos.template');
        Route::post('/aeroportos/veiculos/check-codigo', [AeroportoController::class, 'checkVeiculoCodigo'])->name('aeroportos.veiculos.check-codigo');
        Route::post('/aeroportos/{aeroporto}/depositos/check-codigo', [DepositoController::class, 'checkCodigo'])->name('aeroportos.depositos.check-codigo');
        Route::post('/aeroportos/{aeroporto}/depositos/{deposito}/veiculos/check-codigo', [VeiculoController::class, 'checkCodigo'])->name('aeroportos.depositos.veiculos.check-codigo');
    });
});
