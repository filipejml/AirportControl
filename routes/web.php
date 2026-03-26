<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AeronaveController;
use App\Http\Controllers\CompanhiaAereaController;
use App\Http\Controllers\AeroportoController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\FabricanteController;
use App\Http\Controllers\VooController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;

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
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Rotas para voos - CRUD completo
    Route::resource('voos', VooController::class);


    // Rota para exibir informações detalhadas de uma companhia aérea (incluindo aeronaves associadas)
    Route::get('/companhias/informacoes', [CompanhiaAereaController::class, 'informacoes'])->name('companhias.informacoes');
    // Rota para exibir dashboard de uma companhia aérea (com gráficos e estatísticas)
    Route::get('/companhias/{companhia}/dashboard', [CompanhiaAereaController::class, 'dashboard'])->name('companhias.dashboard');
    // Rotas para companhias aéreas - CRUD completo
    Route::resource('companhias', CompanhiaAereaController::class);
    
    // Rota para exibir informações detalhadas de uma aeronave (incluindo fabricante e companhias associadas)
    Route::get('/aeronaves/informacoes', [AeronaveController::class, 'informacoes'])->name('aeronaves.informacoes');
    // Rotas para aeronaves - CRUD completo
    Route::resource('aeronaves', AeronaveController::class, [
        'parameters' => [
            'aeronaves' => 'aeronave' // Corrige o nome do parâmetro
        ]
    ]);

    // Dashboard
    Route::prefix('dashboard')->name('dashboard.')->middleware('auth')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/graficos', [DashboardController::class, 'graficos'])->name('graficos');
    });

    // Para manter compatibilidade com a rota antiga (opcional)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rota para exibir informações detalhadas de um aeroporto (incluindo companhias associadas)
    Route::get('/aeroportos/informacoes', [AeroportoController::class, 'informacoes'])->name('aeroportos.informacoes');
    // Rotas para aeroportos - CRUD completo
    Route::resource('aeroportos', AeroportoController::class);
    
    // Rota AJAX para buscar aeronaves por companhia - página de cadastro de voo
    Route::get('/api/companhias/{companhiaId}/aeronaves', [VooController::class, 'getAeronavesByCompanhia'])
        ->name('api.companhias.aeronaves');

    // Rota AJAX para verificar ID do voo - página de cadastro de voo (para evitar duplicidade)
    Route::post('/verificar-id-voo', [VooController::class, 'verificarIdVoo'])
        ->name('verificar.id.voo');

    // Rota AJAX para buscar companhia pelo código do voo
    Route::get('/api/buscar-companhia/{codigo}', [VooController::class, 'buscarCompanhiaPorCodigo'])->name('buscar.companhia');

    // Rota para exportar CSV - página de listagem de voos
    Route::get('/voos/export/csv', [VooController::class, 'exportCSV'])->name('voos.export.csv');

    // Rota para exportar PDF - página de listagem de voos
    Route::get('/voos/export/pdf', [VooController::class, 'exportPDF'])->name('voos.export.pdf');

    // Rota AJAX para verificar nome da companhia aérea - página de cadastro de companhia (para evitar duplicidade)
    Route::post('/companhias/check-name', [CompanhiaAereaController::class, 'checkName'])->name('companhias.check-name');

    // Rota AJAX para verificar nome do aeroporto - página de cadastro de aeroporto (para evitar duplicidade)
    Route::post('/aeroportos/check-name', [AeroportoController::class, 'checkName'])->name('aeroportos.check-name');

    /*
    |--------------------------------------------------------------------------
    | ROTAS ADMIN
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->group(function () {
        Route::get('/registros', function () {
            return view('admin.registros.index');
        })->name('registros');

        // Rotas de relatórios para admin (controle)
        Route::prefix('admin')->name('admin.')->group(function () {
            // Rota para listagem de controle
            Route::get('/relatorios', [RelatorioController::class, 'adminIndex'])
                ->name('relatorios.index');
            
            // Rotas de CRUD para relatórios (exceto index)
            Route::get('/relatorios/create', [RelatorioController::class, 'create'])
                ->name('relatorios.create');
            Route::post('/relatorios', [RelatorioController::class, 'store'])
                ->name('relatorios.store');
            Route::get('/relatorios/{relatorio}/edit', [RelatorioController::class, 'edit'])
                ->name('relatorios.edit');
            Route::put('/relatorios/{relatorio}', [RelatorioController::class, 'update'])
                ->name('relatorios.update');
            Route::delete('/relatorios/{relatorio}', [RelatorioController::class, 'destroy'])
                ->name('relatorios.destroy');
        });

        Route::resource('fabricantes', FabricanteController::class);

        // Verifica se o modelo de aeronave já existe (usado para validação AJAX no formulário de cadastrado)
        Route::get('/api/verificar-modelo', [AeronaveController::class, 'verificarModelo'])->name('verificar.modelo');
    });
});