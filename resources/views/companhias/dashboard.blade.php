<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $companhia->nome }} - Dashboard</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Ícones -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .stat-card {
            transition: transform 0.2s;
            cursor: pointer;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .rating-badge {
            font-size: 1.2rem;
            font-weight: bold;
        }
        
        .progress {
            height: 8px;
        }
        
        .card-header {
            background-color: rgba(0,0,0,0.02);
            border-bottom: 1px solid rgba(0,0,0,0.125);
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    @include('components.navbar')

    <div class="container mt-4">
        <!-- Cabeçalho -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="display-5 fw-bold">{{ $companhia->nome }}</h1>
                        @if($companhia->codigo)
                            <p class="text-muted">Código: {{ $companhia->codigo }}</p>
                        @endif
                    </div>
                    <a href="{{ route('companhias.informacoes') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stat-card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-airplane-fill text-primary fs-1"></i>
                        <h3 class="mt-2">{{ number_format($totalVoos, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0">Voos Realizados</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-people-fill text-success fs-1"></i>
                        <h3 class="mt-2">{{ number_format($totalPassageiros, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0">Passageiros Transportados</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-building fs-1 text-info"></i>
                        <h3 class="mt-2">{{ number_format($totalAeronaves, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0">Aeronaves na Frota</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-geo-alt-fill text-warning fs-1"></i>
                        <h3 class="mt-2">{{ number_format($totalAeroportos, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0">Aeroportos Operados</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notas e Avaliações -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-star-fill text-warning me-2"></i>Avaliações e Desempenho</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="text-center">
                                    <i class="bi bi-flag-fill text-primary fs-3"></i>
                                    <h6 class="mt-2">Objetivo</h6>
                                    <div class="progress mb-2">
                                        <div class="progress-bar bg-primary" style="width: {{ ($notaObj / 10) * 100 }}%"></div>
                                    </div>
                                    <span class="rating-badge">{{ number_format($notaObj, 1) }}/10</span>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="text-center">
                                    <i class="bi bi-clock-fill text-success fs-3"></i>
                                    <h6 class="mt-2">Pontualidade</h6>
                                    <div class="progress mb-2">
                                        <div class="progress-bar bg-success" style="width: {{ ($notaPontualidade / 10) * 100 }}%"></div>
                                    </div>
                                    <span class="rating-badge">{{ number_format($notaPontualidade, 1) }}/10</span>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="text-center">
                                    <i class="bi bi-gear-fill text-info fs-3"></i>
                                    <h6 class="mt-2">Serviços</h6>
                                    <div class="progress mb-2">
                                        <div class="progress-bar bg-info" style="width: {{ ($notaServicos / 10) * 100 }}%"></div>
                                    </div>
                                    <span class="rating-badge">{{ number_format($notaServicos, 1) }}/10</span>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="text-center">
                                    <i class="bi bi-pin-fill text-warning fs-3"></i>
                                    <h6 class="mt-2">Pátio</h6>
                                    <div class="progress mb-2">
                                        <div class="progress-bar bg-warning" style="width: {{ ($notaPatio / 10) * 100 }}%"></div>
                                    </div>
                                    <span class="rating-badge">{{ number_format($notaPatio, 1) }}/10</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <h4 class="text-primary">Média Geral: {{ number_format($mediaGeral, 1) }}/10</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        @if($dadosMensais->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Evolução Mensal (Últimos 12 Meses)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="voosChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Últimos Voos -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Últimos Voos</h5>
                    </div>
                    <div class="card-body">
                        @if($ultimosVoos->count() > 0)
                            <div class="list-group">
                                @foreach($ultimosVoos as $voo)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $voo->aeroportoOrigem->codigo ?? 'N/A' }} → {{ $voo->aeroportoDestino->codigo ?? 'N/A' }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar3"></i> {{ $voo->created_at->format('d/m/Y H:i') }}
                                                    <i class="bi bi-people ms-2"></i> {{ number_format($voo->total_passageiros, 0, ',', '.') }} passageiros
                                                </small>
                                            </div>
                                            <span class="badge bg-primary">{{ $voo->id_voo }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center">Nenhum voo registrado ainda.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Principais Aeroportos -->
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Principais Aeroportos Operados</h5>
                    </div>
                    <div class="card-body">
                        @if($voosPorAeroporto->count() > 0)
                            <div class="list-group">
                                @foreach($voosPorAeroporto as $aeroporto)
                                    <div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>{{ $aeroporto->nome_aeroporto }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $aeroporto->cidade ?? 'Localização não informada' }}</small>
                                            </div>
                                            <span class="badge bg-success">{{ number_format($aeroporto->voos_count, 0, ',', '.') }} voos</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center">Nenhum aeroporto registrado ainda.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Frota de Aeronaves -->
        @if($aeronaves->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-building me-2"></i>Frota de Aeronaves</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Modelo</th>
                                        <th>Fabricante</th>
                                        <th>Capacidade</th>
                                        <th>Ano</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($aeronaves as $aeronave)
                                        <tr>
                                            <td><strong>{{ $aeronave->modelo }}</strong></td>
                                            <td>{{ $aeronave->fabricante->nome ?? 'N/A' }}</td>
                                            <td>{{ number_format($aeronave->capacidade, 0, ',', '.') }} passageiros</td>
                                            <td>{{ $aeronave->ano_fabricacao ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @if($dadosMensais->count() > 0)
    <script>
        // Gráfico de voos
        const ctx = document.getElementById('voosChart').getContext('2d');
        const dados = @json($dadosMensais);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dados.map(item => {
                    const [ano, mes] = item.mes.split('-');
                    return `${mes}/${ano}`;
                }),
                datasets: [
                    {
                        label: 'Número de Voos',
                        data: dados.map(item => item.total_voos),
                        borderColor: 'rgb(13, 110, 253)',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        tension: 0.1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Passageiros Transportados',
                        data: dados.map(item => item.total_passageiros),
                        borderColor: 'rgb(25, 135, 84)',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        tension: 0.1,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('pt-BR').format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Número de Voos'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Passageiros'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });
    </script>
    @endif
</body>
</html>