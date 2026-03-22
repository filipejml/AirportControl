{{-- resources/views/aeronaves/informacoes.blade.php --}}
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aeronaves - Informações Gerais</title>

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

        .aircraft-card {
            cursor: pointer;
            transition: all 0.3s;
            border-left: 4px solid #0d5c8b;
        }

        .aircraft-card:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }

        .aircraft-card.active {
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

        .rating-fill.pc {
            background-color: #28a745;
        }

        .rating-fill.mc {
            background-color: #ffc107;
        }

        .rating-fill.lc {
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

        .porte-badge {
            font-size: 0.8rem;
            padding: 4px 8px;
            border-radius: 6px;
        }

        .porte-pc {
            background-color: #d4edda;
            color: #155724;
        }

        .porte-mc {
            background-color: #fff3cd;
            color: #856404;
        }

        .porte-lc {
            background-color: #f8d7da;
            color: #721c24;
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
                <i class="bi bi-airplane me-2"></i>Catálogo de Aeronaves
            </h3>
            <p class="text-muted">
                Visão geral das aeronaves, estatísticas e desempenho
            </p>
        </div>

        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title fw-bold mb-3">
                            <i class="bi bi-funnel me-2"></i>Filtrar por Porte
                        </h6>
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-outline-primary btn-sm filter-porte" data-porte="all">
                                Todas
                            </button>
                            <button class="btn btn-outline-secondary btn-sm filter-porte" data-porte="PC">
                                Pequeno Porte (≤100)
                            </button>
                            <button class="btn btn-outline-secondary btn-sm filter-porte" data-porte="MC">
                                Médio Porte (101-299)
                            </button>
                            <button class="btn btn-outline-secondary btn-sm filter-porte" data-porte="LC">
                                Grande Porte (≥300)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title fw-bold mb-3">
                            <i class="bi bi-building me-2"></i>Filtrar por Fabricante
                        </h6>
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-outline-primary btn-sm filter-fabricante" data-fabricante="all">
                                Todos
                            </button>
                            @foreach($fabricantes as $fabricante)
                                <button class="btn btn-outline-secondary btn-sm filter-fabricante" data-fabricante="{{ $fabricante->id }}">
                                    {{ $fabricante->nome }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h6 class="card-title fw-bold mb-3">
                            <i class="bi bi-search me-2"></i>Buscar por Modelo
                        </h6>
                        <input type="text" id="searchModelo" class="form-control" placeholder="Digite o modelo da aeronave...">
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
                                <div class="stat-number">{{ $totalAeronaves }}</div>
                                <div class="stat-label">Total de Aeronaves</div>
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
                                <div class="stat-number">{{ $totalFabricantes }}</div>
                                <div class="stat-label">Fabricantes</div>
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
                                <div class="stat-number">{{ number_format($capacidadeTotal) }}</div>
                                <div class="stat-label">Capacidade Total (pax)</div>
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
                                <div class="stat-number">{{ number_format($capacidadeMedia, 0) }}</div>
                                <div class="stat-label">Capacidade Média (pax)</div>
                            </div>
                            <i class="bi bi-calculator stat-icon text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribuição por Porte -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-pie-chart me-2"></i>Distribuição por Porte
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <canvas id="porteChart" height="200"></canvas>
                            </div>
                            <div class="col-md-6">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Porte</th>
                                                <th>Quantidade</th>
                                                <th>Percentual</th>
                                                <th>Capacidade Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge porte-pc">Pequeno Porte (≤100)</span></td>
                                                <td><strong>{{ $porteStats['PC']['quantidade'] }}</strong></td>
                                                <td>{{ number_format($porteStats['PC']['percentual'], 1) }}%</td>
                                                <td>{{ number_format($porteStats['PC']['capacidade']) }} pax</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge porte-mc">Médio Porte (101-299)</span></td>
                                                <td><strong>{{ $porteStats['MC']['quantidade'] }}</strong></td>
                                                <td>{{ number_format($porteStats['MC']['percentual'], 1) }}%</td>
                                                <td>{{ number_format($porteStats['MC']['capacidade']) }} pax</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge porte-lc">Grande Porte (≥300)</span></td>
                                                <td><strong>{{ $porteStats['LC']['quantidade'] }}</strong></td>
                                                <td>{{ number_format($porteStats['LC']['percentual'], 1) }}%</td>
                                                <td>{{ number_format($porteStats['LC']['capacidade']) }} pax</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Aeronaves e Detalhes -->
        <div class="row">
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-list-ul me-2"></i>Aeronaves Cadastradas
                            <span class="badge bg-primary ms-2" id="totalFiltrado">{{ $totalAeronaves }}</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush" id="aircraftsList">
                            @foreach($aeronaves as $aeronave)
                                <div class="list-group-item aircraft-card" 
                                     data-aircraft-id="{{ $aeronave->id }}"
                                     data-porte="{{ $aeronave->porte }}"
                                     data-fabricante="{{ $aeronave->fabricante_id }}"
                                     data-modelo="{{ strtolower($aeronave->modelo) }}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 fw-bold">{{ $aeronave->modelo }}</h6>
                                            <small class="text-muted">
                                                <i class="bi bi-building me-1"></i>{{ $aeronave->fabricante->nome }}
                                                <span class="mx-1">•</span>
                                                <i class="bi bi-people me-1"></i>{{ number_format($aeronave->capacidade) }} passageiros
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge 
                                                @if($aeronave->porte == 'PC') porte-pc
                                                @elseif($aeronave->porte == 'MC') porte-mc
                                                @else porte-lc
                                                @endif">
                                                @if($aeronave->porte == 'PC') Pequeno Porte
                                                @elseif($aeronave->porte == 'MC') Médio Porte
                                                @else Grande Porte
                                                @endif
                                            </span>
                                            <div class="mt-1">
                                                <small class="text-muted">
                                                    <i class="bi bi-building me-1"></i>{{ $aeronave->companhias_count ?? 0 }} companhias
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
                <!-- Card de Detalhes da Aeronave Selecionada -->
                <div class="card shadow-sm mb-4" id="detailsCard">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-info-circle me-2"></i>Detalhes da Aeronave
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="defaultDetails">
                            <p class="text-muted text-center my-5">
                                Selecione uma aeronave para visualizar os detalhes completos
                            </p>
                        </div>
                        <div id="aircraftDetails" style="display: none;">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 id="aircraftModelo" class="mb-2 fw-bold"></h4>
                                    <p class="text-muted mb-3" id="aircraftFabricante"></p>
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <i class="bi bi-people fs-1 text-primary"></i>
                                            <h3 id="aircraftCapacidade" class="mt-2 mb-0"></h3>
                                            <small class="text-muted">Capacidade de Passageiros</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <i class="bi bi-building fs-1 text-primary"></i>
                                            <h3 id="aircraftCompanhias" class="mt-2 mb-0"></h3>
                                            <small class="text-muted">Companhias que Operam</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Companhias que utilizam esta aeronave -->
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3">
                                    <i class="bi bi-building me-2"></i>Companhias Aéreas que Utilizam
                                </h6>
                                <div id="companhiasList" class="list-group list-group-flush"></div>
                            </div>

                            <!-- Voos realizados -->
                            <div>
                                <h6 class="fw-bold mb-3">
                                    <i class="bi bi-calendar-check me-2"></i>Últimos Voos Realizados
                                </h6>
                                <div id="voosList" class="list-group list-group-flush"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Voos por Aeronave -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-bar-chart me-2"></i>Top 10 Aeronaves Mais Utilizadas
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="voosChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Dados das aeronaves
        const aircraftsData = @json($aeronaves->map(function($a) {
            return [
                'id' => $a->id,
                'modelo' => $a->modelo,
                'capacidade' => $a->capacidade,
                'porte' => $a->porte,
                'porte_descricao' => $a->porte_descricao,
                'fabricante_id' => $a->fabricante_id,
                'fabricante_nome' => $a->fabricante->nome,
                'companhias_count' => $a->companhias_count,
                'companhias' => $a->companhias->map(function($c) {
                    return [
                        'id' => $c->id,
                        'nome' => $c->nome,
                        'voos_count' => $c->voos()->where('aeronave_id', $a->id)->count()
                    ];
                }),
                'voos' => $a->voos->sortByDesc('created_at')->take(5)->map(function($v) {
                    return [
                        'id' => $v->id,
                        'data' => new Date('{{ $v->created_at }}'),
                        'total_passageiros' => $v->total_passageiros,
                        'companhia_nome' => $v->companhia->nome ?? 'N/A'
                    ];
                })
            ];
        }));

        // Dados para o gráfico de voos
        const voosCount = aircraftsData.map(a => a.voos ? a.voos.length : 0);
        const topAircrafts = aircraftsData
            .map(a => ({ modelo: a.modelo, total_voos: a.voos ? a.voos.length : 0 }))
            .sort((a, b) => b.total_voos - a.total_voos)
            .slice(0, 10);

        // Gráfico de voos
        const ctxVoos = document.getElementById('voosChart').getContext('2d');
        const voosChart = new Chart(ctxVoos, {
            type: 'bar',
            data: {
                labels: topAircrafts.map(a => a.modelo),
                datasets: [{
                    label: 'Número de Voos',
                    data: topAircrafts.map(a => a.total_voos),
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

        // Gráfico de distribuição por porte
        const porteData = @json($porteStats);
        const ctxPorte = document.getElementById('porteChart').getContext('2d');
        const porteChart = new Chart(ctxPorte, {
            type: 'doughnut',
            data: {
                labels: ['Pequeno Porte (≤100)', 'Médio Porte (101-299)', 'Grande Porte (≥300)'],
                datasets: [{
                    data: [porteData.PC.quantidade, porteData.MC.quantidade, porteData.LC.quantidade],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
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

        // Função para atualizar os detalhes da aeronave
        function updateDetails(aircraftId) {
            const aircraft = aircraftsData.find(a => a.id == aircraftId);
            
            if (!aircraft) {
                document.getElementById('defaultDetails').style.display = 'block';
                document.getElementById('aircraftDetails').style.display = 'none';
                return;
            }

            document.getElementById('defaultDetails').style.display = 'none';
            document.getElementById('aircraftDetails').style.display = 'block';
            
            // Atualizar informações básicas
            document.getElementById('aircraftModelo').innerHTML = aircraft.modelo;
            document.getElementById('aircraftFabricante').innerHTML = 
                `<i class="bi bi-building me-1"></i>Fabricante: ${aircraft.fabricante_nome}`;
            document.getElementById('aircraftCapacidade').innerHTML = 
                Number(aircraft.capacidade).toLocaleString();
            document.getElementById('aircraftCompanhias').innerHTML = 
                aircraft.companhias_count || 0;
            
            // Atualizar lista de companhias
            const companhiasList = document.getElementById('companhiasList');
            companhiasList.innerHTML = '';
            
            if (!aircraft.companhias || aircraft.companhias.length === 0) {
                companhiasList.innerHTML = '<div class="list-group-item text-muted">Nenhuma companhia opera esta aeronave</div>';
            } else {
                aircraft.companhias.forEach(companhia => {
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
            
            if (!aircraft.voos || aircraft.voos.length === 0) {
                voosList.innerHTML = '<div class="list-group-item text-muted">Nenhum voo registrado com esta aeronave</div>';
            } else {
                aircraft.voos.forEach(voo => {
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
                                </small>
                            </div>
                            <span class="badge bg-info">
                                <i class="bi bi-people me-1"></i>${Number(voo.total_passageiros).toLocaleString()} pax
                            </span>
                        </div>
                    `;
                    voosList.appendChild(div);
                });
            }
        }

        // Eventos para seleção de aeronave
        document.querySelectorAll('.aircraft-card').forEach(card => {
            card.addEventListener('click', function() {
                // Remove active class de todos
                document.querySelectorAll('.aircraft-card').forEach(c => {
                    c.classList.remove('active');
                });
                // Adiciona active class ao clicado
                this.classList.add('active');
                
                const aircraftId = this.dataset.aircraftId;
                updateDetails(aircraftId);
            });
        });

        // Filtro por porte
        document.querySelectorAll('.filter-porte').forEach(btn => {
            btn.addEventListener('click', function() {
                const porte = this.dataset.porte;
                let visibleCount = 0;
                
                document.querySelectorAll('.aircraft-card').forEach(card => {
                    if (porte === 'all' || card.dataset.porte === porte) {
                        card.style.display = '';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });
                
                document.getElementById('totalFiltrado').innerText = visibleCount;
                
                // Atualizar botões ativos
                document.querySelectorAll('.filter-porte').forEach(b => {
                    b.classList.remove('btn-primary');
                    b.classList.add('btn-outline-secondary');
                });
                this.classList.remove('btn-outline-secondary');
                this.classList.add('btn-primary');
            });
        });

        // Filtro por fabricante
        document.querySelectorAll('.filter-fabricante').forEach(btn => {
            btn.addEventListener('click', function() {
                const fabricanteId = this.dataset.fabricante;
                let visibleCount = 0;
                
                document.querySelectorAll('.aircraft-card').forEach(card => {
                    if (fabricanteId === 'all' || card.dataset.fabricante === fabricanteId) {
                        card.style.display = '';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });
                
                document.getElementById('totalFiltrado').innerText = visibleCount;
                
                // Atualizar botões ativos
                document.querySelectorAll('.filter-fabricante').forEach(b => {
                    b.classList.remove('btn-primary');
                    b.classList.add('btn-outline-secondary');
                });
                this.classList.remove('btn-outline-secondary');
                this.classList.add('btn-primary');
            });
        });

        // Busca por modelo
        document.getElementById('searchModelo').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            let visibleCount = 0;
            
            document.querySelectorAll('.aircraft-card').forEach(card => {
                const modelo = card.dataset.modelo;
                if (modelo.includes(searchTerm)) {
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