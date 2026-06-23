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
    
    // ==================== RELATÓRIOS DISPONÍVEIS ====================
    // Relatório: Companhias por Aeroporto (usuário comum)
    Route::get('/relatorios/companhias-por-aeroporto', 
        [RelatorioController::class, 'userCompanhiasPorAeroporto']
    )->name('relatorios.companhias-por-aeroporto');
    
    // Relatório: Voos por Aeroporto (usuário comum)
    Route::get('/relatorios/voos-por-aeroporto', 
        [RelatorioController::class, 'userVoosPorAeroporto']
    )->name('relatorios.voos-por-aeroporto');
    Route::get('/relatorios/desempenho-companhias',
        [RelatorioController::class, 'userDesempenhoCompanhias']
    )->name('relatorios.desempenho-companhias');
    Route::get('/relatorios/movimentacao-por-periodo',
        [RelatorioController::class, 'userMovimentacaoPorPeriodo']
    )->name('relatorios.movimentacao-por-periodo');
    Route::get('/relatorios/ranking-aeroportos',
        [RelatorioController::class, 'userRankingAeroportos']
    )->name('relatorios.ranking-aeroportos');
    Route::get('/relatorios/ocupacao-voos',
        [RelatorioController::class, 'userOcupacaoVoos']
    )->name('relatorios.ocupacao-voos');
    // ================================================================
    
    // Rotas de informações de companhias aéreas (usuário comum)
    Route::get('/companhias/informacoes', [CompanhiaAereaController::class, 'informacoes'])->name('companhias.informacoes');
    Route::get('/companhias/{companhia}/dashboard', [CompanhiaAereaController::class, 'dashboard'])->name('companhias.dashboard');
    
    // Rotas de informações de aeronaves (usuário comum)
    Route::get('/aeronaves/informacoes', [AeronaveController::class, 'informacoes'])->name('aeronaves.informacoes');
    Route::get('/aeronaves/{aeronave}/dashboard', [AeronaveController::class, 'dashboard'])->name('aeronaves.dashboard');    
    Route::get('/aeronaves/ranking', [AeronaveController::class, 'ranking'])->name('aeronaves.ranking');

    // Rotas de informações de aeroportos (usuário comum)
    Route::get('/aeroportos/informacoes', [AeroportoController::class, 'informacoes'])->name('aeroportos.informacoes');
    Route::get('/aeroportos/{aeroporto}/dashboard', [AeroportoController::class, 'dashboard'])->name('aeroportos.dashboard');

    /*
    |--------------------------------------------------------------------------
    | ROTAS DE CRUD ADMIN (ACESSO COMPLETO)
    |--------------------------------------------------------------------------
    */

    Route::middleware('admin')->group(function () {
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
    
    /*
    |--------------------------------------------------------------------------
    | ROTAS PARA AEROPORTOS - WIZARD DE CRIAÇÃO (3 ETAPAS)
    |--------------------------------------------------------------------------
    */
    // Rotas do wizard (substituem o create padrão)
    Route::get('/aeroportos/create-step1', [AeroportoController::class, 'createStep1'])->name('aeroportos.create.step1');
    Route::post('/aeroportos/store-step1', [AeroportoController::class, 'storeStep1'])->name('aeroportos.store.step1');
    Route::get('/aeroportos/create-step2/{aeroporto}', [AeroportoController::class, 'createStep2'])->name('aeroportos.create.step2');
    Route::post('/aeroportos/store-step2/{aeroporto}', [AeroportoController::class, 'storeStep2'])->name('aeroportos.store.step2');
    Route::get('/aeroportos/create-step3/{aeroporto}', [AeroportoController::class, 'createStep3'])->name('aeroportos.create.step3');
    Route::post('/aeroportos/store-step3/{aeroporto}', [AeroportoController::class, 'storeStep3'])->name('aeroportos.store.step3');
    
    // Rotas AJAX para o wizard
    Route::post('/aeroportos/veiculos/template', [AeroportoController::class, 'getVeiculoTemplate'])->name('aeroportos.veiculos.template');
    Route::post('/aeroportos/veiculos/check-codigo', [AeroportoController::class, 'checkVeiculoCodigo'])->name('aeroportos.veiculos.check-codigo');
    
    // CRUD padrão para aeroportos (edit, update, destroy, show, index)
    Route::get('/aeroportos', [AeroportoController::class, 'index'])->name('aeroportos.index');
    Route::get('/aeroportos/{aeroporto}', [AeroportoController::class, 'show'])->name('aeroportos.show');
    Route::get('/aeroportos/{aeroporto}/edit', [AeroportoController::class, 'edit'])->name('aeroportos.edit');
    Route::put('/aeroportos/{aeroporto}', [AeroportoController::class, 'update'])->name('aeroportos.update');
    Route::delete('/aeroportos/{aeroporto}', [AeroportoController::class, 'destroy'])->name('aeroportos.destroy');
    
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
        Route::post('/depositos/{deposito}/veiculos/finalizar', [VeiculoController::class, 'finalizar'])->name('depositos.veiculos.finalizar');
        Route::delete('/depositos/{deposito}/veiculos/remover-carrinho', [VeiculoController::class, 'removerDoCarrinho'])->name('depositos.veiculos.remover-carrinho');
        Route::delete('/depositos/{deposito}/veiculos/limpar-carrinho', [VeiculoController::class, 'limparCarrinho'])->name('depositos.veiculos.limpar-carrinho');
        Route::get('/depositos/{deposito}/veiculos/{veiculo}', [VeiculoController::class, 'show'])->name('depositos.veiculos.show');
        Route::get('/depositos/{deposito}/veiculos/{veiculo}/edit', [VeiculoController::class, 'edit'])->name('depositos.veiculos.edit');
        Route::put('/depositos/{deposito}/veiculos/{veiculo}', [VeiculoController::class, 'update'])->name('depositos.veiculos.update');
        Route::delete('/depositos/{deposito}/veiculos/{veiculo}', [VeiculoController::class, 'destroy'])->name('depositos.veiculos.destroy');
        Route::post('/depositos/{deposito}/veiculos/check-codigo', [VeiculoController::class, 'checkCodigo'])->name('depositos.veiculos.check-codigo');
        
        // Rota AJAX para verificar código do veículo
        Route::post('/depositos/{deposito}/veiculos/check-codigo', [VeiculoController::class, 'checkCodigo'])
            ->name('depositos.veiculos.check-codigo');
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
    });

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

            // Rota para toggle de visibilidade
            Route::patch('/relatorios/{relatorio}/toggle-visibilidade', [RelatorioController::class, 'toggleVisibilidade'])
                ->name('relatorios.toggle-visibilidade');
            
            // ==================== RELATÓRIOS ADMIN ====================
            // Relatório: Companhias por Aeroporto (admin)
            Route::get('/relatorios/companhias-por-aeroporto-admin', 
                [RelatorioController::class, 'adminCompanhiasPorAeroporto']
            )->name('relatorios.companhias-por-aeroporto');
            
            // Relatório: Voos por Aeroporto (admin)
            Route::get('/relatorios/voos-por-aeroporto-admin', 
                [RelatorioController::class, 'adminVoosPorAeroporto']
            )->name('relatorios.voos-por-aeroporto');

            Route::get('/relatorios/desempenho-companhias-admin',
                [RelatorioController::class, 'adminDesempenhoCompanhias']
            )->name('relatorios.desempenho-companhias');
            Route::get('/relatorios/movimentacao-por-periodo-admin',
                [RelatorioController::class, 'adminMovimentacaoPorPeriodo']
            )->name('relatorios.movimentacao-por-periodo');
            Route::get('/relatorios/ranking-aeroportos-admin',
                [RelatorioController::class, 'adminRankingAeroportos']
            )->name('relatorios.ranking-aeroportos');
            Route::get('/relatorios/ocupacao-voos-admin',
                [RelatorioController::class, 'adminOcupacaoVoos']
            )->name('relatorios.ocupacao-voos');
            // ========================================================
            
            // Gerenciamento de usuários
            Route::resource('users', UserController::class);
        });

        // Gerenciamento de fabricantes
        Route::resource('fabricantes', FabricanteController::class);

        // Verificar modelo de aeronave (AJAX)
        Route::get('/api/verificar-modelo', [AeronaveController::class, 'verificarModelo'])->name('verificar.modelo');
    });
    
    /*
    |--------------------------------------------------------------------------
    | ROTAS DE API PARA RELATÓRIOS (FORA DO GRUPO ADMIN)
    |--------------------------------------------------------------------------
    */
    // ==================== APIS DOS RELATÓRIOS ====================
    // API para dados do relatório de Companhias por Aeroporto
    Route::get('/api/relatorios/companhias-por-aeroporto', 
        [RelatorioController::class, 'apiCompanhiasPorAeroporto']
    )->name('api.relatorios.companhias-por-aeroporto');
    
    // API para dados do relatório de Voos por Aeroporto
    Route::get('/api/relatorios/voos-por-aeroporto', 
        [RelatorioController::class, 'apiVoosPorAeroporto']
    )->name('api.relatorios.voos-por-aeroporto');

    Route::get('/api/relatorios/desempenho-companhias',
        [RelatorioController::class, 'apiDesempenhoCompanhias']
    )->name('api.relatorios.desempenho-companhias');
    Route::get('/api/relatorios/movimentacao-por-periodo',
        [RelatorioController::class, 'apiMovimentacaoPorPeriodo']
    )->name('api.relatorios.movimentacao-por-periodo');
    Route::get('/api/relatorios/ranking-aeroportos',
        [RelatorioController::class, 'apiRankingAeroportos']
    )->name('api.relatorios.ranking-aeroportos');
    Route::get('/api/relatorios/ocupacao-voos',
        [RelatorioController::class, 'apiOcupacaoVoos']
    )->name('api.relatorios.ocupacao-voos');
    // ============================================================
});
