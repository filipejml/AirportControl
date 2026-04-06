@extends('layouts.app')

@section('title', 'Gráficos Gerais')

@section('content')
<div class="container mt-4">

    {{-- Botão para voltar ao painel --}}
    <div class="mb-4">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-2"></i>Voltar para o Painel
        </a>
    </div>

    <h1 class="mb-4 text-center fw-bold text-primary">Gráficos Gerais</h1>

    {{-- Linha 1: Gráfico Total de Voos por Companhia --}}
    <div class="row mb-4">
        <div class="col-md-12 mb-3">
            <div class="card" style="border-left:5px solid #0d6efd;">
                <div class="card-header text-center py-3 rounded-top-3 bg-transparent border-0">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-airplane me-2"></i>Total de Voos por Companhia</h5>
                </div>
                <div class="card-body">
                    <canvas id="voosPorCompanhiaChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Linha 2: Gráfico Total de Passageiros por Companhia --}}
    <div class="row mb-4">
        <div class="col-md-12 mb-3">
            <div class="card" style="border-left:5px solid #198754;">
                <div class="card-header text-center py-3 rounded-top-3 bg-transparent border-0">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-people me-2"></i>Total de Passageiros por Companhia</h5>
                </div>
                <div class="card-body">
                    <canvas id="passageirosPorCompanhiaChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Linha 3: Todos os gráficos de Voos na mesma linha --}}
    <div class="row mb-4">
        {{-- Gráfico: Total de Voos por Horário --}}
        <div class="col-md-4 mb-3">
            <div class="card h-100" style="border-left:5px solid #fd7e14;">
                <div class="card-header text-center py-3 rounded-top-3 bg-transparent border-0">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-clock me-2"></i>Total de Voos por Horário</h5>
                </div>
                <div class="card-body d-flex align-items-center">
                    <canvas id="voosPorHorarioChart" height="200"></canvas>
                </div>
            </div>
        </div>

        {{-- Gráfico: Total de Voos por Tipo de Voo --}}
        <div class="col-md-4 mb-3">
            <div class="card h-100" style="border-left:5px solid #0dcaf0;">
                <div class="card-header text-center py-3 rounded-top-3 bg-transparent border-0">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-diagram-3 me-2"></i>Total de Voos por Tipo de Voo</h5>
                </div>
                <div class="card-body d-flex align-items-center" style="height: 300px;">
                    <canvas id="voosPorTipoChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Gráfico: Total de Voos por Tipo de Aeronave --}}
        <div class="col-md-4 mb-3">
            <div class="card h-100" style="border-left:5px solid #6f42c1;">
                <div class="card-header text-center py-3 rounded-top-3 bg-transparent border-0">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-airplane-fill me-2"></i>Total de Voos por Tipo de Aeronave</h5>
                </div>
                <div class="card-body d-flex align-items-center" style="height: 300px;">
                    <canvas id="voosPorTipoAeronaveChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Linha 4: Todos os gráficos de Passageiros na mesma linha --}}
    <div class="row mb-4">
        {{-- Gráfico: Total de Passageiros por Horário --}}
        <div class="col-md-4 mb-3">
            <div class="card h-100" style="border-left:5px solid #20c997;">
                <div class="card-header text-center py-3 rounded-top-3 bg-transparent border-0">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-people me-2"></i>Total de Passageiros por Horário</h5>
                </div>
                <div class="card-body d-flex align-items-center">
                    <canvas id="passageirosPorHorarioChart" height="200"></canvas>
                </div>
            </div>
        </div>

        {{-- Gráfico: Total de Passageiros por Tipo de Voo --}}
        <div class="col-md-4 mb-3">
            <div class="card h-100" style="border-left:5px solid #ffc107;">
                <div class="card-header text-center py-3 rounded-top-3 bg-transparent border-0">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-pie-chart me-2"></i>Total de Passageiros por Tipo de Voo</h5>
                </div>
                <div class="card-body d-flex align-items-center" style="height: 300px;">
                    <canvas id="passageirosPorTipoVooChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Gráfico: Total de Passageiros por Tipo de Aeronave --}}
        <div class="col-md-4 mb-3">
            <div class="card h-100" style="border-left:5px solid #e83e8c;">
                <div class="card-header text-center py-3 rounded-top-3 bg-transparent border-0">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-graph-up me-2"></i>Total de Pass. por Tipo de Aeronave</h5>
                </div>
                <div class="card-body d-flex align-items-center" style="height: 300px;">
                    <canvas id="passageirosPorTipoAeronaveChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Botão Voltar ao Topo --}}
    <div class="mt-4 text-center">
        <a href="#" class="btn btn-outline-secondary" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;">
            <i class="bi bi-arrow-up me-2"></i>Voltar ao Topo
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js"></script>
<script>
    // Registrar o plugin de datalabels
    Chart.register(ChartDataLabels);
    
    // Configuração global para os datalabels - desabilitado por padrão
    Chart.defaults.set('plugins.datalabels', {
        display: false  // Desabilita todos os datalabels globalmente
    });

    // Helper para arredondamento no JavaScript
    function round(value, decimals) {
        return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
    }

    // -------------------------------
    // Gráfico 1: Voos por Companhia com MEDIANA
    // -------------------------------

    // Dados para Voos por Companhia
    var labelsCompanhia = @json($labelsCompanhia ?? []);
    var dataCompanhia = @json($dataCompanhia ?? []);
    var coresCompanhia = @json($coresCompanhia ?? []);
    var medianaGeralVoos = {{ $medianaGeralVoos ?? 0 }};
    var totalGeralVoos = {{ $totalGeralVoos ?? 0 }};
    var medianasGerais = @json($medianasGerais ?? []);

    if (labelsCompanhia.length > 0 && dataCompanhia.length > 0) {
        new Chart(document.getElementById('voosPorCompanhiaChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labelsCompanhia,
                datasets: [
                    {
                        label: 'Total de Voos',
                        data: dataCompanhia,
                        backgroundColor: coresCompanhia,
                        borderColor: coresCompanhia,
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Mediana Geral de Voos',
                        data: medianasGerais,
                        type: 'line',
                        borderColor: 'rgba(0, 128, 0, 0.8)',
                        backgroundColor: 'rgba(0, 128, 0, 0.1)',
                        borderWidth: 3,
                        borderDash: [5, 5],
                        pointRadius: 0,
                        fill: false,
                        yAxisID: 'y'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { 
                        display: true,
                        position: 'top'
                    },
                    tooltip: { 
                        enabled: true,
                        callbacks: {
                            afterBody: function(context) {
                                var dataIndex = context[0].dataIndex;
                                var totalVoos = dataCompanhia[dataIndex];
                                var percentual = totalGeralVoos > 0 ? round((totalVoos / totalGeralVoos) * 100, 2) : 0;
                                return 'Voos: ' + totalVoos.toLocaleString('pt-BR') + ' / ' + totalGeralVoos.toLocaleString('pt-BR') + '\nPercentual: ' + percentual + '%\nMediana Geral: ' + medianaGeralVoos.toLocaleString('pt-BR') + ' voos';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Total de Voos' },
                        position: 'left',
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('pt-BR');
                            }
                        }
                    },
                    x: { 
                        title: { display: true, text: 'Companhia Aérea' }
                    }
                }
            }
        });
    }

    // -------------------------------
    // Gráfico 2: Passageiros por Companhia com MEDIANA
    // -------------------------------

    var labelsPassageirosCompanhia = @json($labelsPassageirosCompanhia ?? []);
    var dataPassageirosCompanhia = @json($dataPassageirosCompanhia ?? []);
    var coresPassageirosCompanhia = @json($coresPassageirosCompanhia ?? []);
    var medianaGeralPassageiros = {{ $medianaGeralPassageiros ?? 0 }};
    var totalPassageiros = {{ $totalPassageiros ?? 0 }};

    if (labelsPassageirosCompanhia.length > 0 && dataPassageirosCompanhia.length > 0) {
        var medianaPassageirosDataset = {
            label: 'Mediana Geral de Passageiros',
            data: labelsPassageirosCompanhia.map(function() { return medianaGeralPassageiros; }),
            type: 'line',
            borderColor: 'rgba(0, 128, 0, 0.8)',
            backgroundColor: 'rgba(0, 128, 0, 0.1)',
            borderWidth: 2,
            borderDash: [5, 5],
            pointRadius: 0,
            fill: false,
            yAxisID: 'y',
            tension: 0,
            spanGaps: true
        };

        new Chart(document.getElementById('passageirosPorCompanhiaChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: labelsPassageirosCompanhia,
                datasets: [
                    {
                        label: 'Total de Passageiros',
                        data: dataPassageirosCompanhia,
                        backgroundColor: coresPassageirosCompanhia,
                        borderColor: coresPassageirosCompanhia,
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                        yAxisID: 'y'
                    },
                    medianaPassageirosDataset
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { 
                        display: true,
                        position: 'top'
                    },
                    tooltip: { 
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                if (context.dataset.label === 'Total de Passageiros') {
                                    var totalPassageirosValor = context.parsed.y;
                                    var percentual = totalPassageiros > 0 ? round((totalPassageirosValor / totalPassageiros) * 100, 2) : 0;
                                    return 'Passageiros: ' + totalPassageirosValor.toLocaleString('pt-BR') + ' (' + percentual + '%)';
                                } else if (context.dataset.label === 'Mediana Geral de Passageiros') {
                                    return 'Mediana Geral: ' + medianaGeralPassageiros.toLocaleString('pt-BR') + ' passageiros';
                                }
                                return context.dataset.label + ': ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Total de Passageiros' },
                        position: 'left',
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('pt-BR');
                            }
                        }
                    },
                    x: { 
                        title: { display: true, text: 'Companhia Aérea' },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }

    // -------------------------------
    // Gráfico 3: Voos por Horário com MEDIANA
    // -------------------------------
    var horarios = @json($horarios);
    var dataHorario = @json($dataHorario);
    var medianaHorarioDataset = @json($medianaHorarioDataset ?? []);

    new Chart(document.getElementById('voosPorHorarioChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: horarios,
            datasets: [
                {
                    label: 'Total de Voos',
                    data: dataHorario,
                    backgroundColor: [
                        'rgba(0, 51, 102, 0.8)',
                        'rgba(0, 102, 204, 0.8)',
                        'rgba(255, 165, 0, 0.8)',
                        'rgba(255, 0, 0, 0.8)',
                        'rgba(128, 0, 128, 0.8)'
                    ],
                    borderColor: [
                        'rgb(0, 51, 102)',
                        'rgb(0, 102, 204)',
                        'rgb(255, 165, 0)',
                        'rgb(255, 0, 0)',
                        'rgb(128, 0, 128)'
                    ],
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    yAxisID: 'y'
                },
                {
                    label: 'Mediana',
                    data: medianaHorarioDataset,
                    type: 'line',
                    borderColor: 'rgba(0, 128, 0, 0.8)',
                    backgroundColor: 'rgba(0, 128, 0, 0.1)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    pointRadius: 0,
                    fill: false,
                    yAxisID: 'y'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.label === 'Total de Voos') {
                                var total = {{ $totalGeralVoos ?? 0 }};
                                var percentual = total > 0 ? ((context.parsed.y / total) * 100).toFixed(1) : 0;
                                return 'Voos: ' + context.parsed.y.toLocaleString('pt-BR') + ' (' + percentual + '%)';
                            } else if (context.dataset.label === 'Mediana') {
                                return 'Mediana: ' + context.parsed.y.toLocaleString('pt-BR') + ' voos';
                            }
                            return context.dataset.label + ': ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Quantidade de Voos' },
                    ticks: { callback: function(value) { return value.toLocaleString('pt-BR'); } }
                },
                x: { title: { display: true, text: 'Horário do Voo' } }
            }
        }
    });

    // -------------------------------
    // Gráfico 4: Passageiros por Horário com MEDIANA
    // -------------------------------
    var dataPassageirosHorario = @json($dataPassageirosHorario);
    var medianaPassageirosHorarioDataset = @json($medianaPassageirosHorarioDataset ?? []);

    new Chart(document.getElementById('passageirosPorHorarioChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: horarios,
            datasets: [
                {
                    label: 'Total de Passageiros',
                    data: dataPassageirosHorario,
                    backgroundColor: [
                        'rgba(0, 51, 102, 0.8)',
                        'rgba(0, 102, 204, 0.8)',
                        'rgba(255, 165, 0, 0.8)',
                        'rgba(255, 0, 0, 0.8)',
                        'rgba(128, 0, 128, 0.8)'
                    ],
                    borderColor: [
                        'rgb(0, 51, 102)',
                        'rgb(0, 102, 204)',
                        'rgb(255, 165, 0)',
                        'rgb(255, 0, 0)',
                        'rgb(128, 0, 128)'
                    ],
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    yAxisID: 'y'
                },
                {
                    label: 'Mediana',
                    data: medianaPassageirosHorarioDataset,
                    type: 'line',
                    borderColor: 'rgba(0, 128, 0, 0.8)',
                    backgroundColor: 'rgba(0, 128, 0, 0.1)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    pointRadius: 0,
                    fill: false,
                    yAxisID: 'y'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.label === 'Total de Passageiros') {
                                var total = {{ $totalPassageiros ?? 0 }};
                                var percentual = total > 0 ? ((context.parsed.y / total) * 100).toFixed(1) : 0;
                                return 'Passageiros: ' + context.parsed.y.toLocaleString('pt-BR') + ' (' + percentual + '%)';
                            } else if (context.dataset.label === 'Mediana') {
                                return 'Mediana: ' + context.parsed.y.toLocaleString('pt-BR') + ' passageiros';
                            }
                            return context.dataset.label + ': ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Quantidade de Passageiros' },
                    ticks: { callback: function(value) { return value.toLocaleString('pt-BR'); } }
                },
                x: { title: { display: true, text: 'Horário do Voo' } }
            }
        }
    });

    // -------------------------------
    // Gráfico 5: Voos por Tipo de Voo com MEDIANA
    // -------------------------------
    var labelsTipoVoo = @json($labelsTipoVoo);
    var dataTipoVoo = @json($dataTipoVoo);
    var coresTipoVoo = @json($coresTipoVoo);
    var totalTipoVoo = {{ $totalTipoVoo ?? 0 }};
    var medianaTipoVooDataset = @json($medianaTipoVooDataset ?? []);

    new Chart(document.getElementById('voosPorTipoChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: labelsTipoVoo,
            datasets: [
                {
                    label: 'Total de Voos',
                    data: dataTipoVoo,
                    backgroundColor: coresTipoVoo,
                    borderColor: coresTipoVoo,
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    yAxisID: 'y'
                },
                {
                    label: 'Mediana',
                    data: medianaTipoVooDataset,
                    type: 'line',
                    borderColor: 'rgba(0, 128, 0, 0.8)',
                    backgroundColor: 'rgba(0, 128, 0, 0.1)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    pointRadius: 0,
                    fill: false,
                    yAxisID: 'y'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.label === 'Total de Voos') {
                                var label = context.label || '';
                                var value = context.raw || 0;
                                var percentage = totalTipoVoo > 0 ? Math.round((value / totalTipoVoo) * 100) : 0;
                                return label + ': ' + value.toLocaleString('pt-BR') + ' voos (' + percentage + '%)';
                            } else if (context.dataset.label === 'Mediana') {
                                return 'Mediana: ' + context.parsed.y.toLocaleString('pt-BR') + ' voos';
                            }
                            return context.dataset.label + ': ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Quantidade de Voos' },
                    ticks: { callback: function(value) { return value.toLocaleString('pt-BR'); } }
                },
                x: { title: { display: true, text: 'Tipo de Voo' } }
            }
        }
    });

    // -------------------------------
    // Gráfico 6: Voos por Tipo de Aeronave com MEDIANA
    // -------------------------------
    var labelsTipoAeronave = @json($labelsTipoAeronave);
    var dataTipoAeronave = @json($dataTipoAeronave);
    var coresTipoAeronave = @json($coresTipoAeronave);
    var totalTipoAeronave = {{ $totalTipoAeronave ?? 0 }};
    var medianaTipoAeronaveDataset = @json($medianaTipoAeronaveDataset ?? []);

    new Chart(document.getElementById('voosPorTipoAeronaveChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: labelsTipoAeronave,
            datasets: [
                {
                    label: 'Total de Voos',
                    data: dataTipoAeronave,
                    backgroundColor: coresTipoAeronave,
                    borderColor: coresTipoAeronave,
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    yAxisID: 'y'
                },
                {
                    label: 'Mediana',
                    data: medianaTipoAeronaveDataset,
                    type: 'line',
                    borderColor: 'rgba(0, 128, 0, 0.8)',
                    backgroundColor: 'rgba(0, 128, 0, 0.1)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    pointRadius: 0,
                    fill: false,
                    yAxisID: 'y'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.label === 'Total de Voos') {
                                var label = context.label || '';
                                var value = context.raw || 0;
                                var percentage = totalTipoAeronave > 0 ? Math.round((value / totalTipoAeronave) * 100) : 0;
                                return label + ': ' + value.toLocaleString('pt-BR') + ' voos (' + percentage + '%)';
                            } else if (context.dataset.label === 'Mediana') {
                                return 'Mediana: ' + context.parsed.y.toLocaleString('pt-BR') + ' voos';
                            }
                            return context.dataset.label + ': ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Quantidade de Voos' },
                    ticks: { callback: function(value) { return value.toLocaleString('pt-BR'); } }
                },
                x: { title: { display: true, text: 'Tipo de Aeronave' } }
            }
        }
    });

    // -------------------------------
    // Gráfico 7: Passageiros por Tipo de Voo com MEDIANA
    // -------------------------------
    var labelsPassageirosTipoVoo = @json($labelsPassageirosTipoVoo);
    var dataPassageirosTipoVoo = @json($dataPassageirosTipoVoo);
    var coresPassageirosTipoVoo = @json($coresPassageirosTipoVoo);
    var totalPassageirosTipoVoo = {{ $totalPassageirosTipoVoo ?? 0 }};
    var medianaPassageirosTipoVooDataset = @json($medianaPassageirosTipoVooDataset ?? []);

    new Chart(document.getElementById('passageirosPorTipoVooChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: labelsPassageirosTipoVoo,
            datasets: [
                {
                    label: 'Total de Passageiros',
                    data: dataPassageirosTipoVoo,
                    backgroundColor: coresPassageirosTipoVoo,
                    borderColor: coresPassageirosTipoVoo,
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    yAxisID: 'y'
                },
                {
                    label: 'Mediana',
                    data: medianaPassageirosTipoVooDataset,
                    type: 'line',
                    borderColor: 'rgba(0, 128, 0, 0.8)',
                    backgroundColor: 'rgba(0, 128, 0, 0.1)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    pointRadius: 0,
                    fill: false,
                    yAxisID: 'y'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.label === 'Total de Passageiros') {
                                var label = context.label || '';
                                var value = context.raw || 0;
                                var percentage = totalPassageirosTipoVoo > 0 ? Math.round((value / totalPassageirosTipoVoo) * 100) : 0;
                                return label + ': ' + value.toLocaleString('pt-BR') + ' passageiros (' + percentage + '%)';
                            } else if (context.dataset.label === 'Mediana') {
                                return 'Mediana: ' + context.parsed.y.toLocaleString('pt-BR') + ' passageiros';
                            }
                            return context.dataset.label + ': ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Quantidade de Passageiros' },
                    ticks: { callback: function(value) { return value.toLocaleString('pt-BR'); } }
                },
                x: { title: { display: true, text: 'Tipo de Voo' } }
            }
        }
    });

    // -------------------------------
    // Gráfico 8: Passageiros por Tipo de Aeronave com MEDIANA
    // -------------------------------
    var labelsPassageirosTipoAeronave = @json($labelsPassageirosTipoAeronave);
    var dataPassageirosTipoAeronave = @json($dataPassageirosTipoAeronave);
    var coresPassageirosTipoAeronave = @json($coresPassageirosTipoAeronave);
    var totalPassageirosTipoAeronave = {{ $totalPassageirosTipoAeronave ?? 0 }};
    var medianaPassageirosTipoAeronaveDataset = @json($medianaPassageirosTipoAeronaveDataset ?? []);

    new Chart(document.getElementById('passageirosPorTipoAeronaveChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: labelsPassageirosTipoAeronave,
            datasets: [
                {
                    label: 'Total de Passageiros',
                    data: dataPassageirosTipoAeronave,
                    backgroundColor: coresPassageirosTipoAeronave,
                    borderColor: coresPassageirosTipoAeronave,
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    yAxisID: 'y'
                },
                {
                    label: 'Mediana',
                    data: medianaPassageirosTipoAeronaveDataset,
                    type: 'line',
                    borderColor: 'rgba(0, 128, 0, 0.8)',
                    backgroundColor: 'rgba(0, 128, 0, 0.1)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    pointRadius: 0,
                    fill: false,
                    yAxisID: 'y'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.label === 'Total de Passageiros') {
                                var label = context.label || '';
                                var value = context.raw || 0;
                                var percentage = totalPassageirosTipoAeronave > 0 ? Math.round((value / totalPassageirosTipoAeronave) * 100) : 0;
                                return label + ': ' + value.toLocaleString('pt-BR') + ' passageiros (' + percentage + '%)';
                            } else if (context.dataset.label === 'Mediana') {
                                return 'Mediana: ' + context.parsed.y.toLocaleString('pt-BR') + ' passageiros';
                            }
                            return context.dataset.label + ': ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Quantidade de Passageiros' },
                    ticks: { callback: function(value) { return value.toLocaleString('pt-BR'); } }
                },
                x: { title: { display: true, text: 'Tipo de Aeronave' } }
            }
        }
    });
</script>

<style>
    /* Ajuste responsivo para telas menores */
    @media (max-width: 768px) {
        .card-body {
            padding: 1rem;
        }
    }
</style>

{{-- Bootstrap Icons --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<hr class="mt-5">
<p class="text-center text-muted mb-0">
    Desenvolvido por <strong>Filipe Lopes</strong>
</p>
@endsection