{{-- resources/views/aeroportos/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ $aeroporto->nome_aeroporto }}</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Ícones -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        body {
            background-color: #f8f9fc;
        }
        
        .container {
            margin-top: 20px;
        }
        
        .stat-card {
            transition: transform 0.2s;
            border: none;
            border-radius: 10px;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 20px;
        }
        
        .chart-container canvas {
            max-height: 300px;
        }
        
        .rating-badge {
            font-size: 1.2rem;
            font-weight: bold;
        }
        
        .back-button {
            transition: all 0.2s;
        }
        
        .back-button:hover {
            transform: translateX(-3px);
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    @include('components.navbar')

    <div class="container">
        <!-- Botão Voltar e Título -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="{{ route('aeroportos.informacoes') }}" class="btn btn-outline-secondary back-button">
                    <i class="bi bi-arrow-left"></i> Voltar para Informações
                </a>
            </div>
            <div class="text-center">
                <h1 class="mb-0">
                    <i class="bi bi-building text-primary"></i> {{ $aeroporto->nome_aeroporto }}
                </h1>
                <p class="text-muted mt-2">
                    <i class="bi bi-airplane"></i> Dashboard completo do aeroporto
                </p>
            </div>
            <div style="width: 120px;"></div>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="row mb-4">
            <div class="col-md-3 col-6 mb-3">
                <div class="card stat-card shadow-sm" style="border-left: 4px solid #0d6efd;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total de Voos</h6>
                                <h3 class="mb-0 fw-bold text-primary">{{ number_format($totalVoos) }}</h3>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                                <i class="bi bi-airplane-fill text-primary fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-6 mb-3">
                <div class="card stat-card shadow-sm" style="border-left: 4px solid #198754;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total de Passageiros</h6>
                                <h3 class="mb-0 fw-bold text-success">{{ number_format($totalPassageiros) }}</h3>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                                <i class="bi bi-people-fill text-success fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-6 mb-3">
                <div class="card stat-card shadow-sm" style="border-left: 4px solid #0dcaf0;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Média Pax / Voo</h6>
                                <h3 class="mb-0 fw-bold text-info">{{ number_format($mediaPassageirosPorVoo, 0) }}</h3>
                            </div>
                            <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                                <i class="bi bi-graph-up text-info fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-6 mb-3">
                <div class="card stat-card shadow-sm" style="border-left: 4px solid #ffc107;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Companhias</h6>
                                <h3 class="mb-0 fw-bold text-warning">{{ number_format($totalCompanhias) }}</h3>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                                <i class="bi bi-building text-warning fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notas de Avaliação -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="bi bi-star-fill text-warning"></i> Avaliações do Aeroporto
                    </h5>
                    <div class="row text-center">
                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 border rounded bg-light">
                                <i class="bi bi-flag-fill text-primary fs-3"></i>
                                <h6 class="mt-2 text-muted">Objetivo</h6>
                                <h4 class="mb-0 fw-bold">{{ number_format($notaObj, 1) }}</h4>
                                <small class="text-muted">/ 10</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 border rounded bg-light">
                                <i class="bi bi-clock-fill text-success fs-3"></i>
                                <h6 class="mt-2 text-muted">Pontualidade</h6>
                                <h4 class="mb-0 fw-bold">{{ number_format($notaPontualidade, 1) }}</h4>
                                <small class="text-muted">/ 10</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 border rounded bg-light">
                                <i class="bi bi-gear-fill text-info fs-3"></i>
                                <h6 class="mt-2 text-muted">Serviços</h6>
                                <h4 class="mb-0 fw-bold">{{ number_format($notaServicos, 1) }}</h4>
                                <small class="text-muted">/ 10</small>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="p-3 border rounded bg-light">
                                <i class="bi bi-pin-fill text-warning fs-3"></i>
                                <h6 class="mt-2 text-muted">Pátio</h6>
                                <h4 class="mb-0 fw-bold">{{ number_format($notaPatio, 1) }}</h4>
                                <small class="text-muted">/ 10</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="row">
            <div class="col-md-6">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="bi bi-building"></i> Voos por Companhia
                    </h5>
                    <canvas id="voosPorCompanhiaChart"></canvas>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="bi bi-people-fill"></i> Passageiros por Companhia
                    </h5>
                    <canvas id="passageirosPorCompanhiaChart"></canvas>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="bi bi-clock-history"></i> Voos por Horário
                    </h5>
                    <canvas id="voosPorHorarioChart"></canvas>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="bi bi-tags"></i> Voos por Tipo
                    </h5>
                    <canvas id="voosPorTipoChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Companhias Melhores Avaliadas -->
        @if(isset($topCompanhiasNotas) && $topCompanhiasNotas->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="bi bi-trophy-fill text-warning"></i> Top 5 Companhias Melhores Avaliadas
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Companhia</th>
                                    <th class="text-center">Objetivo</th>
                                    <th class="text-center">Pontualidade</th>
                                    <th class="text-center">Serviços</th>
                                    <th class="text-center">Pátio</th>
                                    <th class="text-center">Média Geral</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topCompanhiasNotas as $companhia)
                                <tr>
                                    <td><strong>{{ $companhia->companhia }}</strong></td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ number_format($companhia->nota_obj, 1) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ number_format($companhia->nota_pontualidade, 1) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ number_format($companhia->nota_servicos, 1) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning">{{ number_format($companhia->nota_patio, 1) }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-dark">{{ number_format($companhia->media_geral, 1) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Evolução Mensal -->
        @if(isset($evolucaoMensal) && $evolucaoMensal->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="chart-container">
                    <h5 class="mb-3">
                        <i class="bi bi-calendar-month"></i> Evolução Mensal (Últimos 12 meses)
                    </h5>
                    <canvas id="evolucaoMensalChart"></canvas>
                </div>
            </div>
        </div>
        @endif

        <p class="text-center text-muted mt-4">
            Desenvolvido por <strong>Filipe Lopes</strong>
        </p>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Dados para os gráficos
        const voosPorCompanhiaData = @json($voosPorCompanhia);
        const passageirosPorCompanhiaData = @json($passageirosPorCompanhia);
        const horariosData = @json($horariosData);
        const tiposData = @json($tiposData);
        const evolucaoMensalData = @json($evolucaoMensal);
        const topCompanhiasNotas = @json($topCompanhiasNotas);

        // Gráfico: Voos por Companhia
        if (voosPorCompanhiaData.length > 0) {
            const ctx1 = document.getElementById('voosPorCompanhiaChart').getContext('2d');
            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: voosPorCompanhiaData.map(item => item.companhia),
                    datasets: [{
                        label: 'Quantidade de Voos',
                        data: voosPorCompanhiaData.map(item => item.total_voos),
                        backgroundColor: 'rgba(13, 110, 253, 0.7)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'top' }
                    }
                }
            });
        }

        // Gráfico: Passageiros por Companhia
        if (passageirosPorCompanhiaData.length > 0) {
            const ctx2 = document.getElementById('passageirosPorCompanhiaChart').getContext('2d');
            new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: passageirosPorCompanhiaData.map(item => item.companhia),
                    datasets: [{
                        label: 'Total de Passageiros',
                        data: passageirosPorCompanhiaData.map(item => item.total_passageiros),
                        backgroundColor: 'rgba(25, 135, 84, 0.7)',
                        borderColor: 'rgba(25, 135, 84, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'top' }
                    }
                }
            });
        }

        // Gráfico: Voos por Horário
        const ctx3 = document.getElementById('voosPorHorarioChart').getContext('2d');
        new Chart(ctx3, {
            type: 'pie',
            data: {
                labels: Object.keys(horariosData),
                datasets: [{
                    data: Object.values(horariosData),
                    backgroundColor: [
                        'rgba(13, 110, 253, 0.8)',
                        'rgba(25, 135, 84, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(13, 202, 240, 0.8)',
                        'rgba(111, 66, 193, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Gráfico: Voos por Tipo
        const ctx4 = document.getElementById('voosPorTipoChart').getContext('2d');
        new Chart(ctx4, {
            type: 'doughnut',
            data: {
                labels: Object.keys(tiposData),
                datasets: [{
                    data: Object.values(tiposData),
                    backgroundColor: [
                        'rgba(13, 110, 253, 0.8)',
                        'rgba(255, 193, 7, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Gráfico: Evolução Mensal
        if (evolucaoMensalData.length > 0) {
            const ctx5 = document.getElementById('evolucaoMensalChart').getContext('2d');
            new Chart(ctx5, {
                type: 'line',
                data: {
                    labels: evolucaoMensalData.map(item => item.mes),
                    datasets: [
                        {
                            label: 'Total de Voos',
                            data: evolucaoMensalData.map(item => item.total_voos),
                            borderColor: 'rgba(13, 110, 253, 1)',
                            backgroundColor: 'rgba(13, 110, 253, 0.1)',
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Total de Passageiros',
                            data: evolucaoMensalData.map(item => item.total_passageiros),
                            borderColor: 'rgba(25, 135, 84, 1)',
                            backgroundColor: 'rgba(25, 135, 84, 0.1)',
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { position: 'top' }
                    }
                }
            });
        }
    </script>
</body>
</html>