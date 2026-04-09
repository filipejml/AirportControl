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
use App\Http\Controllers\UserController;
use App\Http\Controllers\DepositoController;
use App\Http\Controllers\VeiculoController;

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
| ROTAS PÚBLICAS - RECUPERAÇÃO DE SENHA
|--------------------------------------------------------------------------
*/
Route::get('/esqueci-senha', [App\Http\Controllers\PasswordResetController::class, 'showForgotForm'])->name('password.request');
Route::post('/esqueci-senha', [App\Http\Controllers\PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/resetar-senha/{token}', [App\Http\Controllers\PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/resetar-senha', [App\Http\Controllers\PasswordResetController::class, 'resetPassword'])->name('password.update');

/*
|--------------------------------------------------------------------------
| ROTAS AUTENTICADAS (USUÁRIOS COMUNS)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | ROTAS DE CONSULTA PÚBLICA (USUÁRIOS COMUNS)
    |--------------------------------------------------------------------------
    */
    
    // Dashboard geral
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/graficos', [DashboardController::class, 'graficos'])->name('graficos');
    });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rotas de visualização de informações (usuário comum)
    Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios');
    
    // Rotas de informações de companhias aéreas (usuário comum)
    Route::get('/companhias/informacoes', [CompanhiaAereaController::class, 'informacoes'])->name('companhias.informacoes');
    Route::get('/companhias/{companhia}/dashboard', [CompanhiaAereaController::class, 'dashboard'])->name('companhias.dashboard');
    
    // Rotas de informações de aeronaves (usuário comum)
    Route::get('/aeronaves/informacoes', [AeronaveController::class, 'informacoes'])->name('aeronaves.informacoes');
    Route::get('/aeronaves/{aeronave}/dashboard', [AeronaveController::class, 'dashboard'])->name('aeronaves.dashboard');
    
    // Rotas de informações de aeroportos (usuário comum)
    Route::get('/aeroportos/informacoes', [AeroportoController::class, 'informacoes'])->name('aeroportos.informacoes');
    Route::get('/aeroportos/{aeroporto}/dashboard', [AeroportoController::class, 'dashboard'])->name('aeroportos.dashboard');

    /*
    |--------------------------------------------------------------------------
    | ROTAS DE CRUD ADMIN (ACESSO COMPLETO)
    |--------------------------------------------------------------------------
    */
    
    // Rotas para voos - CRUD completo
    Route::resource('voos', VooController::class);
    
    // Rotas para companhias aéreas - CRUD completo
    Route::resource('companhias', CompanhiaAereaController::class);
    
    // Rotas para aeronaves - CRUD completo
    Route::resource('aeronaves', AeronaveController::class, [
        'parameters' => [
            'aeronaves' => 'aeronave'
        ]
    ]);
    
    // Rotas para aeroportos - CRUD completo (ADMIN)
    Route::resource('aeroportos', AeroportoController::class);
    
    /*
    |--------------------------------------------------------------------------
    | ROTAS PARA DEPÓSITOS E VEÍCULOS (ADMIN)
    |--------------------------------------------------------------------------
    */
    Route::prefix('aeroportos/{aeroporto}')->name('aeroportos.')->group(function () {
        // Rotas de depósitos
        Route::get('/depositos', [DepositoController::class, 'index'])->name('depositos.index');
        Route::get('/depositos/create', [DepositoController::class, 'create'])->name('depositos.create');
        Route::post('/depositos', [DepositoController::class, 'store'])->name('depositos.store');
        Route::get('/depositos/{deposito}', [DepositoController::class, 'show'])->name('depositos.show');
        Route::get('/depositos/{deposito}/edit', [DepositoController::class, 'edit'])->name('depositos.edit');
        Route::put('/depositos/{deposito}', [DepositoController::class, 'update'])->name('depositos.update');
        Route::delete('/depositos/{deposito}', [DepositoController::class, 'destroy'])->name('depositos.destroy');
        Route::post('/depositos/check-codigo', [DepositoController::class, 'checkCodigo'])->name('depositos.check-codigo');
        
        // Rotas de veículos
        Route::get('/depositos/{deposito}/veiculos', [VeiculoController::class, 'index'])->name('depositos.veiculos.index');
        Route::get('/depositos/{deposito}/veiculos/create', [VeiculoController::class, 'create'])->name('depositos.veiculos.create');
        Route::post('/depositos/{deposito}/veiculos', [VeiculoController::class, 'store'])->name('depositos.veiculos.store');
        Route::get('/depositos/{deposito}/veiculos/{veiculo}', [VeiculoController::class, 'show'])->name('depositos.veiculos.show');
        Route::get('/depositos/{deposito}/veiculos/{veiculo}/edit', [VeiculoController::class, 'edit'])->name('depositos.veiculos.edit');
        Route::put('/depositos/{deposito}/veiculos/{veiculo}', [VeiculoController::class, 'update'])->name('depositos.veiculos.update');
        Route::delete('/depositos/{deposito}/veiculos/{veiculo}', [VeiculoController::class, 'destroy'])->name('depositos.veiculos.destroy');
        Route::post('/depositos/{deposito}/veiculos/check-placa', [VeiculoController::class, 'checkPlaca'])->name('depositos.veiculos.check-placa');
    });
    
    /*
    |--------------------------------------------------------------------------
    | ROTAS AJAX PARA VALIDAÇÕES
    |--------------------------------------------------------------------------
    */
    
    // Verificar código da companhia aérea
    Route::post('/companhias/check-code', [CompanhiaAereaController::class, 'checkCode'])->name('companhias.check-code');
    
    // Verificar nome da companhia aérea
    Route::post('/companhias/check-name', [CompanhiaAereaController::class, 'checkName'])->name('companhias.check-name');
    
    // Verificar nome do aeroporto
    Route::post('/aeroportos/check-name', [AeroportoController::class, 'checkName'])->name('aeroportos.check-name');
    
    // Atualizar disponibilidade da aeronave na companhia
    Route::post('/companhias/{companhia}/aeronaves/{aeronave}/disponibilidade', 
        [CompanhiaAereaController::class, 'atualizarDisponibilidade'])
        ->name('companhias.aeronaves.disponibilidade');
    
    // Buscar aeronaves por companhia (para voos)
    Route::get('/api/companhias/{companhiaId}/aeronaves', [VooController::class, 'getAeronavesByCompanhia'])
        ->name('api.companhias.aeronaves');
    
    // Verificar ID do voo (evitar duplicidade)
    Route::post('/verificar-id-voo', [VooController::class, 'verificarIdVoo'])->name('verificar.id.voo');
    
    // Buscar companhia pelo código do voo
    Route::get('/api/buscar-companhia/{codigo}', [VooController::class, 'buscarCompanhiaPorCodigo'])->name('buscar.companhia');
    
    /*
    |--------------------------------------------------------------------------
    | ROTAS DE EXPORTAÇÃO
    |--------------------------------------------------------------------------
    */
    
    // Exportar CSV de voos
    Route::get('/voos/export/csv', [VooController::class, 'exportCSV'])->name('voos.export.csv');
    
    // Exportar PDF de voos
    Route::get('/voos/export/pdf', [VooController::class, 'exportPDF'])->name('voos.export.pdf');
    
    // Exportar PDF de voos da companhia
    Route::get('/companhias/{companhia}/voos-pdf', [CompanhiaAereaController::class, 'exportVoosPdf'])->name('companhias.voos.pdf');

    /*
    |--------------------------------------------------------------------------
    | ROTAS ADMIN (ACESSO RESTRITO)
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->group(function () {
        // Registros de sistema
        Route::get('/registros', function () {
            return view('admin.registros.index');
        })->name('registros');

        // Relatórios administrativos
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/relatorios', [RelatorioController::class, 'adminIndex'])->name('relatorios.index');
            Route::get('/relatorios/create', [RelatorioController::class, 'create'])->name('relatorios.create');
            Route::post('/relatorios', [RelatorioController::class, 'store'])->name('relatorios.store');
            Route::get('/relatorios/{relatorio}/edit', [RelatorioController::class, 'edit'])->name('relatorios.edit');
            Route::put('/relatorios/{relatorio}', [RelatorioController::class, 'update'])->name('relatorios.update');
            Route::delete('/relatorios/{relatorio}', [RelatorioController::class, 'destroy'])->name('relatorios.destroy');

            // Gerenciamento de usuários
            Route::resource('users', UserController::class);
        });

        // Gerenciamento de fabricantes
        Route::resource('fabricantes', FabricanteController::class);

        // Verificar modelo de aeronave (AJAX)
        Route::get('/api/verificar-modelo', [AeronaveController::class, 'verificarModelo'])->name('verificar.modelo');
    });
});