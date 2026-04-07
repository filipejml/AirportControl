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
            height: 400px;
            margin-bottom: 30px;
        }
        
        .toggle-series-btn {
            transition: all 0.2s;
        }
        
        .toggle-series-btn:hover {
            transform: translateY(-2px);
        }
        
        .series-badge {
            cursor: pointer;
            transition: opacity 0.2s;
        }
        
        .series-badge:hover {
            opacity: 0.7;
        }
        
        .series-badge.hidden-series {
            opacity: 0.4;
            text-decoration: line-through;
        }
        
        .ano-filter {
            max-width: 150px;
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
                        <option value="{{ strtolower($aeroporto['nome']) }}">{{ $aeroporto['nome'] }}</option>
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
                                        <a href="{{ route('aeroportos.show', $aeroporto['id']) }}" 
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
        <div class="card shadow-sm mb-4 mt-4">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h5 class="mb-0">
                            <i class="bi bi-graph-up me-2 text-primary"></i>
                            Evolução Semanal de Passageiros por Aeroporto - Ano {{ $anoSelecionado }}
                        </h5>
                        <small class="text-muted">Semanas com registros de voos</small>
                    </div>
                    <div class="mt-2 mt-md-0">
                        <button class="btn btn-sm btn-outline-secondary toggle-series-btn" id="toggleAllBtn">
                            <i class="bi bi-eye"></i> Mostrar Todos
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                {{-- Legenda interativa --}}
                <div class="mb-3 d-flex flex-wrap gap-2" id="chartLegend">
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
                <div class="chart-container">
                    <canvas id="passageirosSemanalChart"></canvas>
                </div>
                
                {{-- Resumo estatístico --}}
                <div class="alert alert-info mt-3">
                    <div class="row">
                        <div class="col-md-6">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <strong>Como interpretar:</strong> O gráfico mostra a evolução do número de passageiros 
                            transportados por cada aeroporto nas semanas do ano de <strong>{{ $anoSelecionado }}</strong> 
                            que possuem registros de voos.
                        </div>
                        <div class="col-md-6 text-md-end">
                            <small class="text-muted">
                                Clique nos badges acima para mostrar/ocultar aeroportos no gráfico
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="alert alert-warning mb-4 mt-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            Não há dados suficientes para exibir o gráfico de evolução semanal de passageiros para o ano {{ $anoSelecionado }}.
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
                passageirosChart.update();
            }
            
            function toggleSeries(index) {
                seriesVisibility[index] = !seriesVisibility[index];
                const badge = document.querySelector(`.series-badge[data-series-index="${index}"]`);
                if (badge) {
                    if (seriesVisibility[index]) {
                        badge.classList.remove('hidden-series');
                        badge.innerHTML = '<i class="bi bi-eye-fill me-1"></i> ' + dadosSemanais.aeroportos[index].nome;
                    } else {
                        badge.classList.add('hidden-series');
                        badge.innerHTML = '<i class="bi bi-eye-slash-fill me-1"></i> ' + dadosSemanais.aeroportos[index].nome;
                    }
                }
                updateChartVisibility();
            }
            
            function showAllSeries() {
                for (let i = 0; i < dadosSemanais.aeroportos.length; i++) {
                    seriesVisibility[i] = true;
                    const badge = document.querySelector(`.series-badge[data-series-index="${i}"]`);
                    if (badge) {
                        badge.classList.remove('hidden-series');
                        badge.innerHTML = '<i class="bi bi-eye-fill me-1"></i> ' + dadosSemanais.aeroportos[i].nome;
                    }
                }
                updateChartVisibility();
            }
            
            function hideAllSeries() {
                for (let i = 0; i < dadosSemanais.aeroportos.length; i++) {
                    seriesVisibility[i] = false;
                    const badge = document.querySelector(`.series-badge[data-series-index="${i}"]`);
                    if (badge) {
                        badge.classList.add('hidden-series');
                        badge.innerHTML = '<i class="bi bi-eye-slash-fill me-1"></i> ' + dadosSemanais.aeroportos[i].nome;
                    }
                }
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
                        this.innerHTML = '<i class="bi bi-eye"></i> Mostrar Todos';
                    } else {
                        hideAllSeries();
                        this.innerHTML = '<i class="bi bi-eye-slash"></i> Ocultar Todos';
                    }
                });
            });
        @endif
        
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
            const searchTerm = document.getElementById('searchSelect') ? document.getElementById('searchSelect').value.toLowerCase() : '';
            const companhiaFilter = document.getElementById('filterCompanhia') ? document.getElementById('filterCompanhia').value : '';
            
            const cards = document.querySelectorAll('.aeroporto-card');
            
            cards.forEach(card => {
                const nome = card.dataset.nome;
                const companhias = JSON.parse(card.dataset.companhias);
                
                let showByNome = true;
                let showByCompanhia = true;
                
                if (searchTerm && !nome.includes(searchTerm)) {
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