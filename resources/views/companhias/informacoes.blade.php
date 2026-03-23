{{-- resources/views/companhias/informacoes.blade.php --}}
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Companhias Aéreas - Informações Gerais</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Ícones -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Chart.js para gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            background-color: #f5f7fa;
        }

        .card-stats {
            border-radius: 12px;
            transition: 0.3s;
            height: 100%;
        }

        .card-stats:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #0d5c8b;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .company-card {
            cursor: pointer;
            transition: all 0.3s;
            border-left: 4px solid #0d5c8b;
        }

        .company-card:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }

        .company-card.active {
            background-color: #e3f2fd;
            border-left-color: #0d5c8b;
        }

        .rating-bar {
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }

        .rating-fill {
            height: 100%;
            background-color: #0d5c8b;
            border-radius: 4px;
        }

        .rating-fill.excellent {
            background-color: #28a745;
        }

        .rating-fill.good {
            background-color: #ffc107;
        }

        .rating-fill.poor {
            background-color: #dc3545;
        }

        .performance-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .performance-card .card-title {
            color: rgba(255,255,255,0.9);
        }

        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.7;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    @include('components.navbar')

    <!-- Conteúdo -->
    <div class="container mt-4">
        
        <!-- Cabeçalho -->
        <div class="mb-4">
            <h3 class="fw-bold">
                <i class="bi bi-building me-2"></i>Catálogo de Companhias Aéreas
            </h3>
            <p class="text-muted">
                Visão geral das companhias aéreas, estatísticas e desempenho
            </p>
        </div>

        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title fw-bold mb-3">
                            <i class="bi bi-funnel me-2"></i>Filtrar por Companhia
                        </h6>
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-outline-primary btn-sm filter-company" data-company="all">
                                Todas as Companhias
                            </button>
                            @foreach($companhias as $companhia)
                                <button class="btn btn-outline-secondary btn-sm filter-company" data-company="{{ $companhia->id }}">
                                    {{ $companhia->nome }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title fw-bold mb-3">
                            <i class="bi bi-geo-alt me-2"></i>Filtrar por Aeroporto
                        </h6>
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-outline-primary btn-sm filter-airport" data-airport="all">
                                Todos os Aeroportos
                            </button>
                            @foreach($aeroportos as $aeroporto)
                                <button class="btn btn-outline-secondary btn-sm filter-airport" data-airport="{{ $aeroporto->id }}">
                                    {{ $aeroporto->nome_aeroporto }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de Estatísticas Gerais -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card card-stats shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-number">{{ $totalCompanhias }}</div>
                                <div class="stat-label">Companhias Aéreas</div>
                            </div>
                            <i class="bi bi-building stat-icon text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-number">{{ number_format($totalVoos) }}</div>
                                <div class="stat-label">Total de Voos</div>
                            </div>
                            <i class="bi bi-airplane stat-icon text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-number">{{ number_format($totalPassageiros) }}</div>
                                <div class="stat-label">Total de Passageiros</div>
                            </div>
                            <i class="bi bi-people stat-icon text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-number">{{ number_format($mediaGeralNotas, 1) }}</div>
                                <div class="stat-label">Média Geral de Notas</div>
                            </div>
                            <i class="bi bi-star-fill stat-icon text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Companhias e Desempenho -->
        <div class="row">
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-building me-2"></i>Companhias Aéreas
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush" id="companiesList">
                            @foreach($companhias as $companhia)
                                <div class="list-group-item company-card" data-company-id="{{ $companhia->id }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 fw-bold">{{ $companhia->nome }}</h6>
                                            <small class="text-muted">
                                                <i class="bi bi-airplane me-1"></i>{{ $companhia->aeronaves_count }} aeronaves
                                                <span class="mx-1">•</span>
                                                <i class="bi bi-calendar me-1"></i>{{ number_format($companhia->voos_count) }} voos
                                                <span class="mx-1">•</span>
                                                <i class="bi bi-people me-1"></i>{{ number_format($companhia->total_passageiros) }} passageiros
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            @if($companhia->media_notas)
                                                <span class="badge bg-primary">{{ number_format($companhia->media_notas, 1) }}/10</span>
                                            @endif
                                            <div class="mt-1">
                                                <small class="text-muted">
                                                    <i class="bi bi-geo-alt me-1"></i>{{ $companhia->aeroportos->count() }} aeroportos
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <!-- Card de Desempenho da Companhia Selecionada -->
                <div class="card shadow-sm mb-4" id="performanceCard">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-graph-up me-2"></i>Desempenho
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="defaultPerformance">
                            <p class="text-muted text-center my-5">
                                Selecione uma companhia aérea para visualizar o desempenho detalhado
                            </p>
                        </div>
                        <div id="companyPerformance" style="display: none;">
                            <h4 id="companyName" class="mb-3"></h4>
                            
                            <!-- Notas por Categoria -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">Notas por Categoria</h6>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Objetivo</span>
                                        <span id="notaObj" class="fw-bold"></span>
                                    </div>
                                    <div class="rating-bar">
                                        <div id="objBar" class="rating-fill" style="width: 0%"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Pontualidade</span>
                                        <span id="notaPontualidade" class="fw-bold"></span>
                                    </div>
                                    <div class="rating-bar">
                                        <div id="pontualidadeBar" class="rating-fill" style="width: 0%"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Serviços</span>
                                        <span id="notaServicos" class="fw-bold"></span>
                                    </div>
                                    <div class="rating-bar">
                                        <div id="servicosBar" class="rating-fill" style="width: 0%"></div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span>Pátio</span>
                                        <span id="notaPatio" class="fw-bold"></span>
                                    </div>
                                    <div class="rating-bar">
                                        <div id="patioBar" class="rating-fill" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Aeroportos Operados -->
                            <div>
                                <h6 class="fw-bold mb-3">
                                    <i class="bi bi-geo-alt me-2"></i>Aeroportos Operados
                                </h6>
                                <div id="aeroportosList" class="list-group list-group-flush"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Voos por Companhia -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-bar-chart me-2"></i>Voos por Companhia
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="voosChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Dados das companhias (já preparados no controller)
        const companiesData = @json($companiesData);

        // Gráfico de voos
        const ctx = document.getElementById('voosChart').getContext('2d');
        const voosChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: companiesData.map(c => c.nome),
                datasets: [{
                    label: 'Número de Voos',
                    data: companiesData.map(c => c.voos_count),
                    backgroundColor: 'rgba(13, 92, 139, 0.8)',
                    borderColor: 'rgba(13, 92, 139, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Quantidade de Voos'
                        }
                    },
                    x: {
                        ticks: {
                            rotation: -45,
                            autoSkip: true,
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });

        // Função para atualizar o card de desempenho
        function updatePerformance(companyId) {
            const company = companiesData.find(c => c.id == companyId);
            
            if (!company) {
                document.getElementById('defaultPerformance').style.display = 'block';
                document.getElementById('companyPerformance').style.display = 'none';
                return;
            }

            document.getElementById('defaultPerformance').style.display = 'none';
            document.getElementById('companyPerformance').style.display = 'block';
            
            // Atualizar nome
            document.getElementById('companyName').innerHTML = company.nome;
            
            // Atualizar notas
            const notaObj = parseFloat(company.nota_obj) || 0;
            const notaPontualidade = parseFloat(company.nota_pontualidade) || 0;
            const notaServicos = parseFloat(company.nota_servicos) || 0;
            const notaPatio = parseFloat(company.nota_patio) || 0;
            
            document.getElementById('notaObj').innerHTML = notaObj.toFixed(1) + '/10';
            document.getElementById('notaPontualidade').innerHTML = notaPontualidade.toFixed(1) + '/10';
            document.getElementById('notaServicos').innerHTML = notaServicos.toFixed(1) + '/10';
            document.getElementById('notaPatio').innerHTML = notaPatio.toFixed(1) + '/10';
            
            document.getElementById('objBar').style.width = (notaObj * 10) + '%';
            document.getElementById('pontualidadeBar').style.width = (notaPontualidade * 10) + '%';
            document.getElementById('servicosBar').style.width = (notaServicos * 10) + '%';
            document.getElementById('patioBar').style.width = (notaPatio * 10) + '%';
            
            // Atualizar aeroportos
            const aeroportosList = document.getElementById('aeroportosList');
            aeroportosList.innerHTML = '';
            
            if (company.aeroportos.length === 0) {
                aeroportosList.innerHTML = '<div class="list-group-item text-muted">Nenhum aeroporto operado</div>';
            } else {
                company.aeroportos.forEach(aeroporto => {
                    const div = document.createElement('div');
                    div.className = 'list-group-item';
                    div.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-geo-alt-fill me-2 text-primary"></i>
                                <strong>${aeroporto.nome}</strong>
                            </div>
                            <span class="badge bg-secondary">${aeroporto.voos_count} voos</span>
                        </div>
                    `;
                    aeroportosList.appendChild(div);
                });
            }
        }

        // Eventos para seleção de companhia
        document.querySelectorAll('.company-card').forEach(card => {
            card.addEventListener('click', function() {
                // Remove active class de todos
                document.querySelectorAll('.company-card').forEach(c => {
                    c.classList.remove('active');
                });
                // Adiciona active class ao clicado
                this.classList.add('active');
                
                const companyId = this.dataset.companyId;
                updatePerformance(companyId);
            });
        });

        // Filtros por companhia
        document.querySelectorAll('.filter-company').forEach(btn => {
            btn.addEventListener('click', function() {
                const companyId = this.dataset.company;
                
                if (companyId === 'all') {
                    document.querySelectorAll('.company-card').forEach(card => {
                        card.style.display = '';
                    });
                } else {
                    document.querySelectorAll('.company-card').forEach(card => {
                        if (card.dataset.companyId === companyId) {
                            card.style.display = '';
                            card.click();
                        } else {
                            card.style.display = 'none';
                        }
                    });
                }
            });
        });

        // Filtros por aeroporto (implementação básica)
        document.querySelectorAll('.filter-airport').forEach(btn => {
            btn.addEventListener('click', function() {
                const airportId = this.dataset.airport;
                
                if (airportId === 'all') {
                    document.querySelectorAll('.company-card').forEach(card => {
                        card.style.display = '';
                    });
                } else {
                    // Filtrar companhias que operam no aeroporto selecionado
                    document.querySelectorAll('.company-card').forEach(card => {
                        const companyId = card.dataset.companyId;
                        const company = companiesData.find(c => c.id == companyId);
                        
                        const operatesAtAirport = company && company.aeroportos.some(a => a.id == airportId);
                        
                        if (operatesAtAirport) {
                            card.style.display = '';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>