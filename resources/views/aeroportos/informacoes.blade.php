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

        .airport-card {
            cursor: pointer;
            transition: all 0.3s;
            border-left: 4px solid #0d5c8b;
        }

        .airport-card:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }

        .airport-card.active {
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

        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.7;
        }

        .badge-stat {
            font-size: 0.85rem;
            padding: 5px 10px;
            border-radius: 8px;
        }

        .badge-voos {
            background-color: #0d5c8b;
            color: white;
        }

        .badge-passageiros {
            background-color: #28a745;
            color: white;
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
                <i class="bi bi-geo-alt me-2"></i>Catálogo de Aeroportos
            </h3>
            <p class="text-muted">
                Visão geral dos aeroportos, estatísticas e movimentação
            </p>
        </div>

        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title fw-bold mb-3">
                            <i class="bi bi-funnel me-2"></i>Filtrar por Companhia Aérea
                        </h6>
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-outline-primary btn-sm filter-companhia" data-companhia="all">
                                Todos os Aeroportos
                            </button>
                            @foreach($companhias as $companhia)
                                <button class="btn btn-outline-secondary btn-sm filter-companhia" data-companhia="{{ $companhia->id }}">
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
                            <i class="bi bi-search me-2"></i>Buscar por Aeroporto
                        </h6>
                        <input type="text" id="searchAirport" class="form-control" placeholder="Digite o nome do aeroporto...">
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
                                <div class="stat-number">{{ $totalAeroportos }}</div>
                                <div class="stat-label">Total de Aeroportos</div>
                            </div>
                            <i class="bi bi-geo-alt stat-icon text-primary"></i>
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
                                <div class="stat-number">{{ number_format($mediaPassageirosPorVoo, 0) }}</div>
                                <div class="stat-label">Média de Pax por Voo</div>
                            </div>
                            <i class="bi bi-calculator stat-icon text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Aeroportos -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-trophy me-2"></i>Top 5 Aeroportos Mais Movimentados
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="topVoosChart" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-people me-2"></i>Top 5 Aeroportos por Passageiros
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="topPassageirosChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Aeroportos e Detalhes -->
        <div class="row">
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-list-ul me-2"></i>Aeroportos Cadastrados
                            <span class="badge bg-primary ms-2" id="totalFiltrado">{{ $totalAeroportos }}</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush" id="airportsList">
                            @foreach($aeroportos as $aeroporto)
                                <div class="list-group-item airport-card" 
                                     data-airport-id="{{ $aeroporto->id }}"
                                     data-nome="{{ strtolower($aeroporto->nome_aeroporto) }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 fw-bold">{{ $aeroporto->nome_aeroporto }}</h6>
                                            <small class="text-muted">
                                                <i class="bi bi-building me-1"></i>{{ $aeroporto->companhias_count ?? 0 }} companhias
                                                <span class="mx-1">•</span>
                                                <i class="bi bi-airplane me-1"></i>{{ number_format($aeroporto->total_voos ?? 0) }} voos
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge badge-voos">
                                                <i class="bi bi-people me-1"></i>{{ number_format($aeroporto->total_passageiros ?? 0) }}
                                            </span>
                                            @if(($aeroporto->media_notas ?? 0) > 0)
                                                <div class="mt-1">
                                                    <small class="text-muted">
                                                        <i class="bi bi-star-fill text-warning me-1"></i>
                                                        {{ number_format($aeroporto->media_notas, 1) }}/10
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <!-- Card de Detalhes do Aeroporto Selecionado -->
                <div class="card shadow-sm mb-4" id="detailsCard">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-info-circle me-2"></i>Detalhes do Aeroporto
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="defaultDetails">
                            <p class="text-muted text-center my-5">
                                Selecione um aeroporto para visualizar os detalhes completos
                            </p>
                        </div>
                        <div id="airportDetails" style="display: none;">
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h4 id="airportNome" class="mb-2 fw-bold"></h4>
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <i class="bi bi-building fs-1 text-primary"></i>
                                            <h3 id="airportCompanhias" class="mt-2 mb-0"></h3>
                                            <small class="text-muted">Companhias que Operam</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <i class="bi bi-airplane fs-1 text-primary"></i>
                                            <h3 id="airportVoos" class="mt-2 mb-0"></h3>
                                            <small class="text-muted">Total de Voos</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <i class="bi bi-people fs-1 text-primary"></i>
                                            <h3 id="airportPassageiros" class="mt-2 mb-0"></h3>
                                            <small class="text-muted">Total de Passageiros</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notas por Categoria -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">
                                    <i class="bi bi-star me-2"></i>Avaliações do Aeroporto
                                </h6>
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

                            <!-- Companhias que operam no aeroporto -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">
                                    <i class="bi bi-building me-2"></i>Companhias Aéreas que Operam
                                </h6>
                                <div id="companhiasList" class="list-group list-group-flush"></div>
                            </div>

                            <!-- Últimos voos -->
                            <div>
                                <h6 class="fw-bold mb-3">
                                    <i class="bi bi-calendar-check me-2"></i>Últimos Voos Realizados
                                </h6>
                                <div id="voosList" class="list-group list-group-flush"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Distribuição de Voos por Horário -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-clock me-2"></i>Distribuição de Voos por Horário
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="horarioChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Dados dos aeroportos
        const airportsData = @json($aeroportos->map(function($a) {
            return [
                'id' => $a->id,
                'nome' => $a->nome_aeroporto,
                'companhias_count' => $a->companhias_count ?? 0,
                'total_voos' => $a->total_voos ?? 0,
                'total_passageiros' => $a->total_passageiros ?? 0,
                'media_passageiros_por_voo' => $a->media_passageiros_por_voo ?? 0,
                'media_notas' => $a->media_notas ?? 0,
                'nota_obj' => $a->voos->avg('nota_obj') ?? 0,
                'nota_pontualidade' => $a->voos->avg('nota_pontualidade') ?? 0,
                'nota_servicos' => $a->voos->avg('nota_servicos') ?? 0,
                'nota_patio' => $a->voos->avg('nota_patio') ?? 0,
                'companhias' => $a->companhias->map(function($c) use ($a) {
                    return [
                        'id' => $c->id,
                        'nome' => $c->nome,
                        'voos_count' => $a->voos()->where('companhia_aerea_id', $c->id)->count()
                    ];
                }),
                'voos' => $a->voos->sortByDesc('created_at')->take(5)->map(function($v) {
                    return [
                        'id' => $v->id,
                        'data' => new Date('{{ $v->created_at }}'),
                        'total_passageiros' => $v->total_passageiros,
                        'horario' => $v->horario_voo,
                        'companhia_nome' => $v->companhia->nome ?? 'N/A'
                    ];
                })
            ];
        }));

        // Dados para os gráficos de top
        const topVoos = airportsData
            .sort((a, b) => b.total_voos - a.total_voos)
            .slice(0, 5);
        
        const topPassageiros = airportsData
            .sort((a, b) => b.total_passageiros - a.total_passageiros)
            .slice(0, 5);

        // Gráfico Top 5 por Voos
        const ctxTopVoos = document.getElementById('topVoosChart').getContext('2d');
        new Chart(ctxTopVoos, {
            type: 'bar',
            data: {
                labels: topVoos.map(a => a.nome.length > 20 ? a.nome.substring(0, 20) + '...' : a.nome),
                datasets: [{
                    label: 'Número de Voos',
                    data: topVoos.map(a => a.total_voos),
                    backgroundColor: 'rgba(13, 92, 139, 0.8)',
                    borderColor: 'rgba(13, 92, 139, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
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

        // Gráfico Top 5 por Passageiros
        const ctxTopPassageiros = document.getElementById('topPassageirosChart').getContext('2d');
        new Chart(ctxTopPassageiros, {
            type: 'bar',
            data: {
                labels: topPassageiros.map(a => a.nome.length > 20 ? a.nome.substring(0, 20) + '...' : a.nome),
                datasets: [{
                    label: 'Total de Passageiros',
                    data: topPassageiros.map(a => a.total_passageiros),
                    backgroundColor: 'rgba(40, 167, 69, 0.8)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Quantidade de Passageiros'
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

        // Gráfico de distribuição por horário
        const horarioData = @json($horarioStats);
        const ctxHorario = document.getElementById('horarioChart').getContext('2d');
        new Chart(ctxHorario, {
            type: 'pie',
            data: {
                labels: ['Madrugada (00-06)', 'Manhã (06-12)', 'Tarde (12-18)', 'Noite (18-00)'],
                datasets: [{
                    data: [horarioData.EAM || 0, horarioData.AM || 0, horarioData.AN || 0, horarioData.PM || 0],
                    backgroundColor: ['#6c757d', '#ffc107', '#28a745', '#0d5c8b'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // Função para atualizar os detalhes do aeroporto
        function updateDetails(airportId) {
            const airport = airportsData.find(a => a.id == airportId);
            
            if (!airport) {
                document.getElementById('defaultDetails').style.display = 'block';
                document.getElementById('airportDetails').style.display = 'none';
                return;
            }

            document.getElementById('defaultDetails').style.display = 'none';
            document.getElementById('airportDetails').style.display = 'block';
            
            // Atualizar informações básicas
            document.getElementById('airportNome').innerHTML = airport.nome;
            document.getElementById('airportCompanhias').innerHTML = airport.companhias_count || 0;
            document.getElementById('airportVoos').innerHTML = Number(airport.total_voos).toLocaleString();
            document.getElementById('airportPassageiros').innerHTML = Number(airport.total_passageiros).toLocaleString();
            
            // Atualizar notas
            const notaObj = parseFloat(airport.nota_obj) || 0;
            const notaPontualidade = parseFloat(airport.nota_pontualidade) || 0;
            const notaServicos = parseFloat(airport.nota_servicos) || 0;
            const notaPatio = parseFloat(airport.nota_patio) || 0;
            
            document.getElementById('notaObj').innerHTML = notaObj.toFixed(1) + '/10';
            document.getElementById('notaPontualidade').innerHTML = notaPontualidade.toFixed(1) + '/10';
            document.getElementById('notaServicos').innerHTML = notaServicos.toFixed(1) + '/10';
            document.getElementById('notaPatio').innerHTML = notaPatio.toFixed(1) + '/10';
            
            document.getElementById('objBar').style.width = (notaObj * 10) + '%';
            document.getElementById('pontualidadeBar').style.width = (notaPontualidade * 10) + '%';
            document.getElementById('servicosBar').style.width = (notaServicos * 10) + '%';
            document.getElementById('patioBar').style.width = (notaPatio * 10) + '%';
            
            // Atualizar lista de companhias
            const companhiasList = document.getElementById('companhiasList');
            companhiasList.innerHTML = '';
            
            if (!airport.companhias || airport.companhias.length === 0) {
                companhiasList.innerHTML = '<div class="list-group-item text-muted">Nenhuma companhia opera neste aeroporto</div>';
            } else {
                airport.companhias.forEach(companhia => {
                    const div = document.createElement('div');
                    div.className = 'list-group-item';
                    div.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-building me-2 text-primary"></i>
                                <strong>${companhia.nome}</strong>
                            </div>
                            <span class="badge bg-secondary">${companhia.voos_count} voos</span>
                        </div>
                    `;
                    companhiasList.appendChild(div);
                });
            }
            
            // Atualizar lista de voos
            const voosList = document.getElementById('voosList');
            voosList.innerHTML = '';
            
            if (!airport.voos || airport.voos.length === 0) {
                voosList.innerHTML = '<div class="list-group-item text-muted">Nenhum voo registrado neste aeroporto</div>';
            } else {
                airport.voos.forEach(voo => {
                    const dataFormatada = new Date(voo.data).toLocaleDateString('pt-BR');
                    const div = document.createElement('div');
                    div.className = 'list-group-item';
                    div.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-calendar me-2 text-primary"></i>
                                <strong>${dataFormatada}</strong>
                                <br>
                                <small class="text-muted">
                                    <i class="bi bi-building me-1"></i>${voo.companhia_nome}
                                    <span class="mx-1">•</span>
                                    <i class="bi bi-clock me-1"></i>${voo.horario}
                                </small>
                            </div>
                            <span class="badge badge-voos">
                                <i class="bi bi-people me-1"></i>${Number(voo.total_passageiros).toLocaleString()} pax
                            </span>
                        </div>
                    `;
                    voosList.appendChild(div);
                });
            }
        }

        // Eventos para seleção de aeroporto
        document.querySelectorAll('.airport-card').forEach(card => {
            card.addEventListener('click', function() {
                // Remove active class de todos
                document.querySelectorAll('.airport-card').forEach(c => {
                    c.classList.remove('active');
                });
                // Adiciona active class ao clicado
                this.classList.add('active');
                
                const airportId = this.dataset.airportId;
                updateDetails(airportId);
            });
        });

        // Filtro por companhia
        document.querySelectorAll('.filter-companhia').forEach(btn => {
            btn.addEventListener('click', function() {
                const companhiaId = this.dataset.companhia;
                let visibleCount = 0;
                
                if (companhiaId === 'all') {
                    document.querySelectorAll('.airport-card').forEach(card => {
                        card.style.display = '';
                        visibleCount++;
                    });
                } else {
                    document.querySelectorAll('.airport-card').forEach(card => {
                        const airportId = card.dataset.airportId;
                        const airport = airportsData.find(a => a.id == airportId);
                        
                        if (airport && airport.companhias && airport.companhias.some(c => c.id == companhiaId)) {
                            card.style.display = '';
                            visibleCount++;
                        } else {
                            card.style.display = 'none';
                        }
                    });
                }
                
                document.getElementById('totalFiltrado').innerText = visibleCount;
                
                // Atualizar botões ativos
                document.querySelectorAll('.filter-companhia').forEach(b => {
                    b.classList.remove('btn-primary');
                    b.classList.add('btn-outline-secondary');
                });
                this.classList.remove('btn-outline-secondary');
                this.classList.add('btn-primary');
            });
        });

        // Busca por nome do aeroporto
        document.getElementById('searchAirport').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            let visibleCount = 0;
            
            document.querySelectorAll('.airport-card').forEach(card => {
                const nome = card.dataset.nome;
                if (nome.includes(searchTerm)) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            document.getElementById('totalFiltrado').innerText = visibleCount;
        });
    </script>
</body>
</html>