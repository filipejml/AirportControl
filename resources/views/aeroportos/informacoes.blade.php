{{-- resources/views/aeroportos/informacoes.blade.php --}}
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aeroportos - Informações Gerais</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Ícones -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        body {
            background-color: white;
        }
        
        .container {
            margin-top: 20px;
        }
        
        .aeroporto-card {
            transition: transform 0.2s;
        }
        
        .aeroporto-card:hover {
            transform: translateY(-5px);
        }
        
        .card {
            transition: box-shadow 0.3s;
        }
        
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        
        .btn-dashboard {
            transition: all 0.2s;
        }
        
        .btn-dashboard:hover {
            transform: scale(1.02);
        }
        
        .chart-container {
            position: relative;
            height: 420px;
        }

        .weekly-chart-card {
            overflow: hidden;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(23, 52, 88, 0.1) !important;
        }

        .weekly-chart-header {
            padding: 1.15rem 1.35rem;
            color: #fff;
            border: 0;
            background: linear-gradient(135deg, #0d5c8b, #1674ac);
        }

        .weekly-chart-header .text-muted {
            color: rgba(255, 255, 255, 0.72) !important;
        }

        .weekly-chart-header .header-icon {
            display: grid;
            width: 42px;
            height: 42px;
            place-items: center;
            border-radius: 11px;
            background: rgba(255, 255, 255, 0.14);
            font-size: 1.2rem;
        }

        .chart-filter-panel {
            padding: 1rem;
            border: 1px solid #e3eaf2;
            border-radius: 10px;
            background: #f7f9fc;
        }

        .chart-legend-panel {
            padding: .8rem 1rem;
            border: 1px solid #e7edf4;
            border-radius: 10px;
            background: #fff;
        }

        .chart-surface {
            padding: 1rem;
            border: 1px solid #edf1f5;
            border-radius: 12px;
            background: linear-gradient(180deg, #fff, #fbfcfe);
        }
        
        .toggle-series-btn {
            transition: all 0.2s;
        }
        
        .toggle-series-btn:hover {
            transform: translateY(-2px);
        }
        
        .series-badge {
            cursor: pointer;
            border: 1px solid rgba(255, 255, 255, .35);
            box-shadow: 0 2px 6px rgba(23, 52, 88, .12);
            transition: opacity 0.2s, transform 0.2s;
        }
        
        .series-badge:hover {
            opacity: 0.85;
            transform: translateY(-1px);
        }
        
        .series-badge.hidden-series {
            opacity: 0.4;
            text-decoration: line-through;
        }
        
        .ano-filter {
            max-width: 150px;
        }

        @media (max-width: 767.98px) {
            .chart-container {
                height: 340px;
            }

            .weekly-chart-header {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    @include('components.navbar')

    <!-- Conteúdo -->
    <div class="container">
        {{-- Linha do título --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">Informações Gerais dos Aeroportos</h1>
        </div>

        {{-- Estatísticas Gerais --}}
        <div class="row mb-4">
            <div class="col-md-3 col-6 mb-3 mb-md-0">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0d6efd !important;">
                    <div class="card-body">
                        <div>
                            <h6 class="text-muted mb-2">Total de Aeroportos</h6>
                            <h3 class="mb-0 fw-bold text-primary">{{ number_format($totalAeroportos) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-6 mb-3 mb-md-0">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #198754 !important;">
                    <div class="card-body">
                        <div>
                            <h6 class="text-muted mb-2">Total de Voos</h6>
                            <h3 class="mb-0 fw-bold text-success">{{ number_format($totalVoos) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-6 mb-3 mb-md-0">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0dcaf0 !important;">
                    <div class="card-body">
                        <div>
                            <h6 class="text-muted mb-2">Total de Passageiros</h6>
                            <h3 class="mb-0 fw-bold text-info">{{ number_format($totalPassageiros) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-6 mb-3 mb-md-0">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important;">
                    <div class="card-body">
                        <div>
                            <h6 class="text-muted mb-2">Média de Pax por Voo</h6>
                            <h3 class="mb-0 fw-bold text-warning">{{ number_format($mediaPassageirosPorVoo, 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <select id="searchSelect" class="form-select">
                    <option value="">Todos os aeroportos</option>
                    @foreach($aeroportosData as $aeroporto)
                        <option value="{{ $aeroporto['id'] }}">{{ $aeroporto['nome'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select id="filterCompanhia" class="form-select">
                    <option value="">Todas as companhias</option>
                    @foreach($companhias as $companhia)
                        <option value="{{ $companhia->id }}">{{ $companhia->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select id="sortSelect" class="form-select">
                    <option value="nome">Ordenar por Nome (A-Z)</option>
                    <option value="nome-desc">Ordenar por Nome (Z-A)</option>
                    <option value="voos">Mais Voos</option>
                    <option value="passageiros">Mais Passageiros</option>
                    <option value="objetivo">Melhor Nota Objetivo</option>
                    <option value="pontualidade">Melhor Nota Pontualidade</option>
                    <option value="servicos">Melhor Nota Serviços</option>
                    <option value="patio">Melhor Nota Pátio</option>
                </select>
            </div>
            <div class="col-md-2">
                <select id="anoFilter" class="form-select ano-filter">
                    @if(isset($anosDisponiveis) && count($anosDisponiveis) > 0)
                        @foreach($anosDisponiveis as $ano)
                            <option value="{{ $ano }}" {{ $ano == $anoSelecionado ? 'selected' : '' }}>Ano {{ $ano }}</option>
                        @endforeach
                    @else
                        <option value="{{ date('Y') }}" selected>Ano {{ date('Y') }}</option>
                    @endif
                </select>
            </div>
        </div>

        {{-- CARDS DOS AEROPORTOS (ACIMA DO GRÁFICO) --}}
        <div class="row" id="aeroportosContainer">
            @foreach($aeroportosData as $aeroporto)
                @php
                    $borderColor = '#0d6efd';
                    $mediaGeral = $aeroporto['media_notas'] ?? 0;
                    $temDados = ($aeroporto['total_voos'] ?? 0) > 0;
                @endphp
                
                <div class="col-md-6 col-lg-4 mb-4 aeroporto-card" 
                     data-id="{{ $aeroporto['id'] }}"
                     data-nome="{{ strtolower($aeroporto['nome']) }}"
                     data-voos="{{ $aeroporto['total_voos'] ?? 0 }}"
                     data-passageiros="{{ $aeroporto['total_passageiros'] ?? 0 }}"
                     data-objetivo="{{ $aeroporto['nota_obj'] ?? 0 }}"
                     data-pontualidade="{{ $aeroporto['nota_pontualidade'] ?? 0 }}"
                     data-servicos="{{ $aeroporto['nota_servicos'] ?? 0 }}"
                     data-patio="{{ $aeroporto['nota_patio'] ?? 0 }}"
                     data-media="{{ $mediaGeral }}"
                     data-companhias='@json($aeroporto['companhias'] ?? [])'>
                    <div class="card h-100 shadow-sm" style="border-left:5px solid {{ $borderColor }};">
                        
                        {{-- Cabeçalho --}}
                        <div class="card-header bg-transparent border-0 pb-0 pt-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title mb-1 text-primary">
                                        <i class="bi bi-building me-2"></i>{{ $aeroporto['nome'] }}
                                    </h5>
                                    <p class="card-text text-muted small mb-0">
                                        <i class="bi bi-airplane me-1"></i>{{ $aeroporto['companhias_count'] ?? 0 }} companhias operando
                                    </p>
                                </div>
                                @if($temDados)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle-fill"></i> Ativo
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-dash-circle"></i> Sem voos
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Corpo do card --}}
                        <div class="card-body pt-3">
                            {{-- Informações principais --}}
                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-airplane-fill text-primary me-2"></i>
                                        <div>
                                            <p class="mb-0 fw-bold">{{ number_format($aeroporto['total_voos'] ?? 0) }}</p>
                                            <small class="text-muted">Voos</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-people-fill text-success me-2"></i>
                                        <div>
                                            <p class="mb-0 fw-bold">{{ number_format($aeroporto['total_passageiros'] ?? 0) }}</p>
                                            <small class="text-muted">Passageiros</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Médias das notas --}}
                            <div class="border-top pt-3">
                                <p class="small text-muted mb-2">Médias de Avaliação:</p>
                                <div class="row text-center">
                                    <div class="col-3">
                                        <div class="border rounded p-2 bg-light">
                                            <i class="bi bi-flag-fill text-primary d-block mb-1"></i>
                                            <p class="mb-0 fw-bold small">{{ number_format($aeroporto['nota_obj'] ?? 0, 1) }}</p>
                                            <small class="text-muted">Obj</small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="border rounded p-2 bg-light">
                                            <i class="bi bi-clock-fill text-success d-block mb-1"></i>
                                            <p class="mb-0 fw-bold small">{{ number_format($aeroporto['nota_pontualidade'] ?? 0, 1) }}</p>
                                            <small class="text-muted">Pont</small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="border rounded p-2 bg-light">
                                            <i class="bi bi-gear-fill text-info d-block mb-1"></i>
                                            <p class="mb-0 fw-bold small">{{ number_format($aeroporto['nota_servicos'] ?? 0, 1) }}</p>
                                            <small class="text-muted">Serv</small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="border rounded p-2 bg-light">
                                            <i class="bi bi-pin-fill text-warning d-block mb-1"></i>
                                            <p class="mb-0 fw-bold small">{{ number_format($aeroporto['nota_patio'] ?? 0, 1) }}</p>
                                            <small class="text-muted">Pátio</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Melhores Companhias por Categoria --}}
                            @if(isset($aeroporto['melhores_companhias']) && count(array_filter($aeroporto['melhores_companhias'])) > 0)
                                <div class="border-top pt-3 mt-2">
                                    <p class="small text-muted mb-2">
                                        <i class="bi bi-trophy-fill text-warning me-1"></i>Melhores por Categoria:
                                    </p>
                                    <div class="small">
                                        @foreach($aeroporto['melhores_companhias'] as $categoria => $companhia)
                                            @if($companhia)
                                                @php
                                                    $iconClass = match($categoria) {
                                                        'Objetivo' => 'bi-flag-fill text-primary',
                                                        'Pontualidade' => 'bi-clock-fill text-success',
                                                        'Servicos' => 'bi-gear-fill text-info',
                                                        'Patio' => 'bi-pin-fill text-warning',
                                                        default => 'bi-star-fill text-secondary'
                                                    };
                                                @endphp
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span>
                                                        <i class="{{ $iconClass }} me-1"></i>
                                                        <small>{{ $categoria }}:</small>
                                                    </span>
                                                    <span class="text-truncate ms-2" style="max-width: 150px;">
                                                        <small>{{ $companhia['nome'] }}</small>
                                                    </span>
                                                    <span class="badge bg-secondary ms-2">{{ number_format($companhia['media'], 1) }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Botão Dashboard --}}
                            <div class="mt-3">
                                @if($temDados)
                                    <div class="d-grid">
                                        <a href="{{ route('aeroportos.dashboard', $aeroporto['id']) }}" 
                                        class="btn btn-primary btn-dashboard">
                                            <i class="bi bi-graph-up me-1"></i> Ver Dashboard do Aeroporto
                                        </a>
                                    </div>
                                @else
                                    <div class="d-grid">
                                        <button class="btn btn-outline-secondary btn-sm" disabled>
                                            <i class="bi bi-eye-slash me-1"></i> Dashboard Indisponível
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if(count($aeroportosData) === 0)
            <div class="text-center py-5">
                <i class="bi bi-building display-1 text-muted"></i>
                <p class="text-muted mt-3">Nenhum aeroporto encontrado no banco de dados.</p>
            </div>
        @endif

        {{-- GRÁFICO DE PASSAGEIROS POR SEMANA (ABAIXO DOS CARDS) --}}
        @if(isset($dadosSemanais) && count($dadosSemanais['aeroportos']) > 0 && count($dadosSemanais['semanas']) > 0)
        <div class="card weekly-chart-card border-0 mb-4 mt-4">
            <div class="card-header weekly-chart-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center gap-3">
                        <span class="header-icon"><i class="bi bi-graph-up-arrow"></i></span>
                        <div>
                            <h5 class="mb-1">Evolução Semanal de Passageiros por Aeroporto</h5>
                            <small class="text-muted">
                                {{ $dadosSemanais['periodo_label'] ?? "Ano de {$anoSelecionado}" }} · semanas com registros
                            </small>
                        </div>
                    </div>
                    <div class="mt-2 mt-md-0">
                        <button class="btn btn-sm btn-light toggle-series-btn" id="toggleAllBtn">
                            <i class="bi bi-eye-slash"></i> Ocultar Todos
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <form method="GET" action="{{ route('aeroportos.informacoes') }}" class="row g-2 align-items-end mb-4 chart-filter-panel" id="graficoPeriodoForm">
                    <input type="hidden" name="ano" value="{{ $anoSelecionado }}">
                    <div class="col-md-4">
                        <label for="graficoPeriodo" class="form-label small fw-semibold text-muted">Intervalo do gráfico</label>
                        <select name="grafico_periodo" id="graficoPeriodo" class="form-select form-select-sm" onchange="atualizarFiltroPeriodoGrafico()">
                            <option value="ano" @selected(($filtrosGrafico['periodo'] ?? 'ano') === 'ano')>Ano inteiro</option>
                            <option value="semestre" @selected(($filtrosGrafico['periodo'] ?? '') === 'semestre')>Por semestre</option>
                            <option value="trimestre" @selected(($filtrosGrafico['periodo'] ?? '') === 'trimestre')>Por trimestre</option>
                            <option value="mes" @selected(($filtrosGrafico['periodo'] ?? '') === 'mes')>Por mês</option>
                        </select>
                    </div>
                    <div class="col-md-3 grafico-filtro-detalhe" id="graficoFiltroSemestre" hidden>
                        <label for="graficoSemestre" class="form-label small fw-semibold text-muted">Semestre</label>
                        <select name="grafico_semestre" id="graficoSemestre" class="form-select form-select-sm">
                            <option value="1" @selected(($filtrosGrafico['semestre'] ?? 1) == 1)>1º semestre</option>
                            <option value="2" @selected(($filtrosGrafico['semestre'] ?? 1) == 2)>2º semestre</option>
                        </select>
                    </div>
                    <div class="col-md-3 grafico-filtro-detalhe" id="graficoFiltroTrimestre" hidden>
                        <label for="graficoTrimestre" class="form-label small fw-semibold text-muted">Trimestre</label>
                        <select name="grafico_trimestre" id="graficoTrimestre" class="form-select form-select-sm">
                            @for($trimestre = 1; $trimestre <= 4; $trimestre++)
                                <option value="{{ $trimestre }}" @selected(($filtrosGrafico['trimestre'] ?? 1) == $trimestre)>{{ $trimestre }}º trimestre</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3 grafico-filtro-detalhe" id="graficoFiltroMes" hidden>
                        <label for="graficoMes" class="form-label small fw-semibold text-muted">Mês</label>
                        <select name="grafico_mes" id="graficoMes" class="form-select form-select-sm">
                            @foreach(['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'] as $numero => $nomeMes)
                                <option value="{{ $numero + 1 }}" @selected(($filtrosGrafico['mes'] ?? 1) == $numero + 1)>{{ $nomeMes }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-funnel me-1"></i>Aplicar
                        </button>
                    </div>
                </form>

                {{-- Legenda interativa --}}
                <div class="mb-3 d-flex flex-wrap align-items-center gap-2 chart-legend-panel" id="chartLegend">
                    @foreach($dadosSemanais['aeroportos'] as $index => $aeroporto)
                        <span class="badge series-badge p-2" 
                              data-series-index="{{ $index }}"
                              style="background-color: {{ $aeroporto['cor'] }}; cursor: pointer;">
                            <i class="bi bi-eye-fill me-1"></i>
                            {{ $aeroporto['nome'] }}
                        </span>
                    @endforeach
                </div>
                
                {{-- Container do gráfico --}}
                <div class="chart-container chart-surface">
                    <canvas id="passageirosSemanalChart"></canvas>
                    <div id="chartEmptyMessage" class="h-100 d-none align-items-center justify-content-center text-muted">
                        <div class="text-center">
                            <i class="bi bi-funnel fs-2 d-block mb-2"></i>
                            Nenhum aeroporto do gráfico corresponde aos filtros selecionados.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="card shadow-sm border-0 mt-4 mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('aeroportos.informacoes') }}" class="row g-2 align-items-end" id="graficoPeriodoForm">
                    <input type="hidden" name="ano" value="{{ $anoSelecionado }}">
                    <div class="col-md-4">
                        <label for="graficoPeriodo" class="form-label small fw-semibold text-muted">Intervalo do gráfico</label>
                        <select name="grafico_periodo" id="graficoPeriodo" class="form-select form-select-sm" onchange="atualizarFiltroPeriodoGrafico()">
                            <option value="ano" @selected(($filtrosGrafico['periodo'] ?? 'ano') === 'ano')>Ano inteiro</option>
                            <option value="semestre" @selected(($filtrosGrafico['periodo'] ?? '') === 'semestre')>Por semestre</option>
                            <option value="trimestre" @selected(($filtrosGrafico['periodo'] ?? '') === 'trimestre')>Por trimestre</option>
                            <option value="mes" @selected(($filtrosGrafico['periodo'] ?? '') === 'mes')>Por mês</option>
                        </select>
                    </div>
                    <div class="col-md-3 grafico-filtro-detalhe" id="graficoFiltroSemestre" hidden>
                        <label class="form-label small fw-semibold text-muted">Semestre</label>
                        <select name="grafico_semestre" class="form-select form-select-sm">
                            <option value="1" @selected(($filtrosGrafico['semestre'] ?? 1) == 1)>1º semestre</option>
                            <option value="2" @selected(($filtrosGrafico['semestre'] ?? 1) == 2)>2º semestre</option>
                        </select>
                    </div>
                    <div class="col-md-3 grafico-filtro-detalhe" id="graficoFiltroTrimestre" hidden>
                        <label class="form-label small fw-semibold text-muted">Trimestre</label>
                        <select name="grafico_trimestre" class="form-select form-select-sm">
                            @for($trimestre = 1; $trimestre <= 4; $trimestre++)
                                <option value="{{ $trimestre }}" @selected(($filtrosGrafico['trimestre'] ?? 1) == $trimestre)>{{ $trimestre }}º trimestre</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3 grafico-filtro-detalhe" id="graficoFiltroMes" hidden>
                        <label class="form-label small fw-semibold text-muted">Mês</label>
                        <select name="grafico_mes" class="form-select form-select-sm">
                            @foreach(['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'] as $numero => $nomeMes)
                                <option value="{{ $numero + 1 }}" @selected(($filtrosGrafico['mes'] ?? 1) == $numero + 1)>{{ $nomeMes }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-funnel me-1"></i>Aplicar</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="alert alert-warning mb-4 mt-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            Não há dados para exibir o gráfico no período selecionado de {{ $anoSelecionado }}.
        </div>
        @endif

        <p class="text-center text-muted mt-4">
            Desenvolvido por <strong>Filipe Lopes</strong>
        </p>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Inicializar gráfico de passageiros por semana
        let passageirosChart = null;
        const seriesVisibility = {};
        
        @if(isset($dadosSemanais) && count($dadosSemanais['aeroportos']) > 0 && count($dadosSemanais['semanas']) > 0)
            const dadosSemanais = @json($dadosSemanais);
            
            function initPassageirosChart() {
                const ctx = document.getElementById('passageirosSemanalChart').getContext('2d');
                
                // Preparar datasets - SEM sombreado (fill: false)
                const datasets = dadosSemanais.aeroportos.map((aeroporto, index) => {
                    seriesVisibility[index] = true;
                    return {
                        label: aeroporto.nome,
                        data: aeroporto.dados,
                        borderColor: aeroporto.cor,
                        backgroundColor: 'transparent', // Sem cor de fundo
                        borderWidth: 2.5,
                        fill: false, // SEM SOMBREADO
                        tension: 0.3,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: aeroporto.cor,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    };
                });
                
                passageirosChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: dadosSemanais.semanas,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        let value = context.parsed.y;
                                        return label + ': ' + new Intl.NumberFormat('pt-BR').format(value) + ' passageiros';
                                    }
                                }
                            },
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Número de Passageiros',
                                    font: {
                                        weight: 'bold'
                                    }
                                },
                                ticks: {
                                    callback: function(value) {
                                        return new Intl.NumberFormat('pt-BR').format(value);
                                    }
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Semanas do Ano (com dados)',
                                    font: {
                                        weight: 'bold'
                                    }
                                },
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45,
                                    autoSkip: true,
                                    maxTicksLimit: 15
                                }
                            }
                        }
                    }
                });
            }
            
            function updateChartVisibility() {
                if (!passageirosChart) return;
                
                passageirosChart.data.datasets.forEach((dataset, index) => {
                    dataset.hidden = !seriesVisibility[index];
                });

                const possuiSerieVisivel = Object.values(seriesVisibility).some(Boolean);
                const canvas = document.getElementById('passageirosSemanalChart');
                const emptyMessage = document.getElementById('chartEmptyMessage');
                canvas.classList.toggle('d-none', !possuiSerieVisivel);
                emptyMessage.classList.toggle('d-none', possuiSerieVisivel);
                emptyMessage.classList.toggle('d-flex', !possuiSerieVisivel);

                passageirosChart.update();
            }

            function updateSeriesBadge(index) {
                const badge = document.querySelector(`.series-badge[data-series-index="${index}"]`);
                if (!badge) return;

                const visivel = seriesVisibility[index];
                badge.classList.toggle('hidden-series', !visivel);
                badge.innerHTML = `<i class="bi bi-eye-${visivel ? 'fill' : 'slash-fill'} me-1"></i> ${dadosSemanais.aeroportos[index].nome}`;
            }
            
            function toggleSeries(index) {
                seriesVisibility[index] = !seriesVisibility[index];
                updateSeriesBadge(index);
                updateChartVisibility();
            }
            
            function showAllSeries() {
                for (let i = 0; i < dadosSemanais.aeroportos.length; i++) {
                    seriesVisibility[i] = true;
                    updateSeriesBadge(i);
                }
                updateChartVisibility();
            }
            
            function hideAllSeries() {
                for (let i = 0; i < dadosSemanais.aeroportos.length; i++) {
                    seriesVisibility[i] = false;
                    updateSeriesBadge(i);
                }
                updateChartVisibility();
            }

            function filterChartSeries() {
                const aeroportoId = document.getElementById('searchSelect')?.value || '';
                const companhiaId = document.getElementById('filterCompanhia')?.value || '';

                dadosSemanais.aeroportos.forEach((aeroporto, index) => {
                    const correspondeAeroporto = !aeroportoId || String(aeroporto.id) === String(aeroportoId);
                    const correspondeCompanhia = !companhiaId
                        || aeroporto.companhias_ids.some(id => String(id) === String(companhiaId));

                    seriesVisibility[index] = correspondeAeroporto && correspondeCompanhia;
                    updateSeriesBadge(index);
                });

                updateChartVisibility();
            }
            
            // Inicializar quando o DOM estiver pronto
            document.addEventListener('DOMContentLoaded', function() {
                initPassageirosChart();
                
                // Adicionar eventos aos badges
                document.querySelectorAll('.series-badge').forEach(badge => {
                    badge.addEventListener('click', function() {
                        const index = parseInt(this.dataset.seriesIndex);
                        toggleSeries(index);
                    });
                });
                
                // Evento do botão toggle all
                const toggleAllBtn = document.getElementById('toggleAllBtn');
                
                toggleAllBtn.addEventListener('click', function() {
                    const someHidden = Object.values(seriesVisibility).some(v => v === false);
                    if (someHidden) {
                        showAllSeries();
                        this.innerHTML = '<i class="bi bi-eye-slash"></i> Ocultar Todos';
                    } else {
                        hideAllSeries();
                        this.innerHTML = '<i class="bi bi-eye"></i> Mostrar Todos';
                    }
                });
            });
        @endif
        
        // Filtro por ano - redireciona ao selecionar
        function atualizarFiltroPeriodoGrafico() {
            const periodo = document.getElementById('graficoPeriodo')?.value || 'ano';
            document.querySelectorAll('.grafico-filtro-detalhe').forEach(elemento => elemento.hidden = true);

            const filtros = {
                semestre: 'graficoFiltroSemestre',
                trimestre: 'graficoFiltroTrimestre',
                mes: 'graficoFiltroMes'
            };

            if (filtros[periodo]) {
                document.getElementById(filtros[periodo]).hidden = false;
            }
        }

        document.addEventListener('DOMContentLoaded', atualizarFiltroPeriodoGrafico);

        // Filtro por ano - redireciona ao selecionar
        const anoFilter = document.getElementById('anoFilter');
        if (anoFilter) {
            anoFilter.addEventListener('change', function() {
                const ano = this.value;
                const url = new URL(window.location.href);
                url.searchParams.set('ano', ano);
                window.location.href = url.toString();
            });
        }
        
        // Filtro por nome do aeroporto
        const searchSelect = document.getElementById('searchSelect');
        if (searchSelect) {
            searchSelect.addEventListener('change', function() {
                const searchTerm = this.value;
                filterCards();
            });
        }

        // Filtro por companhia
        const filterCompanhia = document.getElementById('filterCompanhia');
        if (filterCompanhia) {
            filterCompanhia.addEventListener('change', function() {
                const companhiaId = this.value;
                filterCards();
            });
        }

        // Ordenação
        const sortSelect = document.getElementById('sortSelect');
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                const sortType = this.value;
                sortCards(sortType);
            });
        }

        function filterCards() {
            const aeroportoId = document.getElementById('searchSelect') ? document.getElementById('searchSelect').value : '';
            const companhiaFilter = document.getElementById('filterCompanhia') ? document.getElementById('filterCompanhia').value : '';
            
            const cards = document.querySelectorAll('.aeroporto-card');
            
            cards.forEach(card => {
                const companhias = JSON.parse(card.dataset.companhias);
                
                let showByNome = true;
                let showByCompanhia = true;
                
                if (aeroportoId && card.dataset.id !== aeroportoId) {
                    showByNome = false;
                }
                
                if (companhiaFilter && companhiaFilter !== '') {
                    const companhiaExiste = companhias.some(c => c.id == companhiaFilter);
                    if (!companhiaExiste) {
                        showByCompanhia = false;
                    }
                }
                
                if (showByNome && showByCompanhia) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });

            if (typeof filterChartSeries === 'function') {
                filterChartSeries();
            }
        }

        function sortCards(sortType) {
            const container = document.getElementById('aeroportosContainer');
            const cards = Array.from(document.querySelectorAll('.aeroporto-card'));
            
            cards.sort((a, b) => {
                switch(sortType) {
                    case 'nome':
                        return a.dataset.nome.localeCompare(b.dataset.nome);
                    case 'nome-desc':
                        return b.dataset.nome.localeCompare(a.dataset.nome);
                    case 'voos':
                        return parseInt(b.dataset.voos) - parseInt(a.dataset.voos);
                    case 'passageiros':
                        return parseInt(b.dataset.passageiros) - parseInt(a.dataset.passageiros);
                    case 'objetivo':
                        return parseFloat(b.dataset.objetivo) - parseFloat(a.dataset.objetivo);
                    case 'pontualidade':
                        return parseFloat(b.dataset.pontualidade) - parseFloat(a.dataset.pontualidade);
                    case 'servicos':
                        return parseFloat(b.dataset.servicos) - parseFloat(a.dataset.servicos);
                    case 'patio':
                        return parseFloat(b.dataset.patio) - parseFloat(a.dataset.patio);
                    default:
                        return 0;
                }
            });
            
            // Reordenar os cards no DOM
            cards.forEach(card => {
                container.appendChild(card);
            });
        }
        
        // Inicializar ordenação padrão
        if (typeof sortCards === 'function') {
            sortCards('nome');
        }
    </script>
</body>
</html>
