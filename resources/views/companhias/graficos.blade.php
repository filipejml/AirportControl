<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráficos - {{ $companhia->nome }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    @vite('resources/js/app.js')
    <style>
        body { background: #f8fafc; }
        .charts-hero {
            padding: clamp(1.5rem, 4vw, 2.5rem);
            border-radius: 1.5rem;
            color: #fff;
            background: radial-gradient(circle at 88% 15%, rgba(255,255,255,.16), transparent 24%),
                        linear-gradient(125deg, #101828 0%, #1849a9 60%, #2e90fa 100%);
            box-shadow: 0 20px 45px rgba(16, 24, 40, .15);
        }
        .summary-card { padding: 1rem; border-radius: .9rem; background: #fff; border: 1px solid #e4e7ec; }
        .chart-card { padding: 1.25rem; border-radius: 1rem; background: #fff; border: 1px solid #e4e7ec; }
        .chart-area { position: relative; height: 420px; }
    </style>
</head>
<body>
    @include('components.navbar')

    <main class="container py-4">
        <header class="charts-hero mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="small text-white-50 fw-semibold text-uppercase mb-1">Análise visual</div>
                    <h1 class="h2 fw-bold mb-1">Gráficos da {{ $companhia->nome }}</h1>
                    <p class="mb-0 text-white-50">Resumo operacional de voos e passageiros.</p>
                </div>
                <a href="{{ route('companhias.dashboard', $companhia) }}" class="btn btn-outline-light">
                    <i class="bi bi-arrow-left me-1"></i> Voltar ao dashboard
                </a>
            </div>
        </header>

        <section class="row g-3 mb-4">
            <div class="col-md-6"><div class="summary-card"><small class="text-muted">Total de voos</small><div class="h3 fw-bold text-primary mb-0">{{ number_format($totalVoos, 0, ',', '.') }}</div></div></div>
            <div class="col-md-6"><div class="summary-card"><small class="text-muted">Total de passageiros</small><div class="h3 fw-bold text-info mb-0">{{ number_format($totalPassageiros, 0, ',', '.') }}</div></div></div>
        </section>

        <section class="chart-card mb-4">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h2 class="h5 fw-bold mb-1"><i class="bi bi-graph-up me-2 text-primary"></i>Voos por modelo por semana</h2>
                    <p class="small text-muted mb-0">Evolução semanal da quantidade de voos de cada modelo da companhia.</p>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-export-csv="voos-modelo" @disabled($voosPorModeloSemana->isEmpty())><i class="bi bi-filetype-csv me-1"></i>CSV</button>
            </div>
            @if($semanasGrafico->isEmpty() || $voosPorModeloSemana->isEmpty())
                <div class="py-5 text-center text-muted">
                    <i class="bi bi-bar-chart fs-2 d-block mb-2"></i>
                    Não há dados suficientes para gerar o gráfico.
                </div>
            @else
                <div class="chart-area"><canvas id="voosModeloSemanaChart"></canvas></div>
            @endif
        </section>

        <section class="chart-card">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h2 class="h5 fw-bold mb-1"><i class="bi bi-people me-2 text-info"></i>Passageiros por modelo por semana</h2>
                    <p class="small text-muted mb-0">Evolução semanal dos passageiros transportados por cada modelo da companhia.</p>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-export-csv="passageiros-modelo" @disabled($passageirosPorModeloSemana->isEmpty())><i class="bi bi-filetype-csv me-1"></i>CSV</button>
            </div>
            @if($semanasGrafico->isEmpty() || $passageirosPorModeloSemana->isEmpty())
                <div class="py-5 text-center text-muted">
                    <i class="bi bi-bar-chart fs-2 d-block mb-2"></i>
                    Não há dados suficientes para gerar o gráfico.
                </div>
            @else
                <div class="chart-area"><canvas id="passageirosModeloSemanaChart"></canvas></div>
            @endif
        </section>

        <section class="chart-card mt-4">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h2 class="h5 fw-bold mb-1"><i class="bi bi-building me-2 text-success"></i>Voos por aeroporto por semana</h2>
                    <p class="small text-muted mb-0">Evolução semanal da quantidade de voos realizados em cada aeroporto.</p>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-export-csv="voos-aeroporto" @disabled($voosPorAeroportoSemana->isEmpty())><i class="bi bi-filetype-csv me-1"></i>CSV</button>
            </div>
            @if($semanasGrafico->isEmpty() || $voosPorAeroportoSemana->isEmpty())
                <div class="py-5 text-center text-muted">
                    <i class="bi bi-bar-chart fs-2 d-block mb-2"></i>
                    Não há dados suficientes para gerar o gráfico.
                </div>
            @else
                <div class="chart-area"><canvas id="voosAeroportoSemanaChart"></canvas></div>
            @endif
        </section>

        <section class="chart-card mt-4">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h2 class="h5 fw-bold mb-1"><i class="bi bi-people me-2 text-info"></i>Passageiros por aeroporto por semana</h2>
                    <p class="small text-muted mb-0">Evolução semanal dos passageiros transportados em cada aeroporto.</p>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary" data-export-csv="passageiros-aeroporto" @disabled($passageirosPorAeroportoSemana->isEmpty())><i class="bi bi-filetype-csv me-1"></i>CSV</button>
            </div>
            @if($semanasGrafico->isEmpty() || $passageirosPorAeroportoSemana->isEmpty())
                <div class="py-5 text-center text-muted">
                    <i class="bi bi-bar-chart fs-2 d-block mb-2"></i>
                    Não há dados suficientes para gerar o gráfico.
                </div>
            @else
                <div class="chart-area"><canvas id="passageirosAeroportoSemanaChart"></canvas></div>
            @endif
        </section>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @if($semanasGrafico->isNotEmpty() && $voosPorModeloSemana->isNotEmpty())
        <script type="module">
            const semanas = @json($semanasGrafico);
            const seriesVoos = @json($voosPorModeloSemana);
            const seriesPassageiros = @json($passageirosPorModeloSemana);
            const seriesAeroportos = @json($voosPorAeroportoSemana);
            const seriesPassageirosAeroportos = @json($passageirosPorAeroportoSemana);
            const cores = ['#0d6efd', '#198754', '#F97316', '#7E22CE', '#DC2626', '#0dcaf0', '#6c757d', '#d63384'];
            const nomeCompanhia = @json($companhia->nome);

            const exportacoes = {
                'voos-modelo': { series: seriesVoos, chave: 'modelo', arquivo: 'voos_por_modelo_por_semana' },
                'passageiros-modelo': { series: seriesPassageiros, chave: 'modelo', arquivo: 'passageiros_por_modelo_por_semana' },
                'voos-aeroporto': { series: seriesAeroportos, chave: 'aeroporto', arquivo: 'voos_por_aeroporto_por_semana' },
                'passageiros-aeroporto': { series: seriesPassageirosAeroportos, chave: 'aeroporto', arquivo: 'passageiros_por_aeroporto_por_semana' },
            };

            const campoCsv = (valor) => `"${String(valor ?? '').replaceAll('"', '""')}"`;

            document.querySelectorAll('[data-export-csv]').forEach((botao) => {
                botao.addEventListener('click', () => {
                    const exportacao = exportacoes[botao.dataset.exportCsv];
                    if (!exportacao) return;

                    const cabecalho = ['Semana', ...exportacao.series.map((serie) => serie[exportacao.chave])];
                    const linhas = semanas.map((semana, indice) => [
                        semana.replace('-W', '/S'),
                        ...exportacao.series.map((serie) => serie.dados[indice] ?? 0),
                    ]);
                    const csv = '\uFEFFsep=;\r\n' + [cabecalho, ...linhas]
                        .map((linha) => linha.map(campoCsv).join(';'))
                        .join('\r\n');
                    const url = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8;' }));
                    const link = document.createElement('a');
                    const companhia = nomeCompanhia.toLowerCase().normalize('NFD')
                        .replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '');
                    link.href = url;
                    link.download = `${exportacao.arquivo}_${companhia}.csv`;
                    link.click();
                    URL.revokeObjectURL(url);
                });
            });

            AirportCharts.create(document.getElementById('voosModeloSemanaChart'), {
                type: 'line',
                data: {
                    labels: semanas.map((semana) => semana.replace('-W', '/S')),
                    datasets: seriesVoos.map((serie, indice) => ({
                        label: serie.modelo,
                        data: serie.dados,
                        borderColor: cores[indice % cores.length],
                        backgroundColor: cores[indice % cores.length],
                        borderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        tension: .25,
                        fill: false,
                    })),
                },
                options: {
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { position: 'bottom' } },
                    scales: {
                        x: { title: { display: true, text: 'Semana' } },
                        y: { beginAtZero: true, title: { display: true, text: 'Quantidade de voos' } },
                    },
                },
            });

            AirportCharts.create(document.getElementById('passageirosModeloSemanaChart'), {
                type: 'line',
                data: {
                    labels: semanas.map((semana) => semana.replace('-W', '/S')),
                    datasets: seriesPassageiros.map((serie, indice) => ({
                        label: serie.modelo,
                        data: serie.dados,
                        borderColor: cores[indice % cores.length],
                        backgroundColor: cores[indice % cores.length],
                        borderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        tension: .25,
                        fill: false,
                    })),
                },
                options: {
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: (context) => `${context.dataset.label}: ${context.parsed.y.toLocaleString('pt-BR')} passageiros`,
                            },
                        },
                    },
                    scales: {
                        x: { title: { display: true, text: 'Semana' } },
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Quantidade de passageiros' },
                            ticks: { callback: (valor) => valor.toLocaleString('pt-BR') },
                        },
                    },
                },
            });

            const voosAeroportoCanvas = document.getElementById('voosAeroportoSemanaChart');
            if (voosAeroportoCanvas) {
                AirportCharts.create(voosAeroportoCanvas, {
                    type: 'line',
                    data: {
                        labels: semanas.map((semana) => semana.replace('-W', '/S')),
                        datasets: seriesAeroportos.map((serie, indice) => ({
                            label: serie.aeroporto,
                            data: serie.dados,
                            borderColor: cores[indice % cores.length],
                            backgroundColor: cores[indice % cores.length],
                            borderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            tension: .25,
                            fill: false,
                        })),
                    },
                    options: {
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: { legend: { position: 'bottom' } },
                        scales: {
                            x: { title: { display: true, text: 'Semana' } },
                            y: { beginAtZero: true, title: { display: true, text: 'Quantidade de voos' } },
                        },
                    },
                });
            }

            const passageirosAeroportoCanvas = document.getElementById('passageirosAeroportoSemanaChart');
            if (passageirosAeroportoCanvas) {
                AirportCharts.create(passageirosAeroportoCanvas, {
                    type: 'line',
                    data: {
                        labels: semanas.map((semana) => semana.replace('-W', '/S')),
                        datasets: seriesPassageirosAeroportos.map((serie, indice) => ({
                            label: serie.aeroporto,
                            data: serie.dados,
                            borderColor: cores[indice % cores.length],
                            backgroundColor: cores[indice % cores.length],
                            borderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            tension: .25,
                            fill: false,
                        })),
                    },
                    options: {
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    label: (context) => `${context.dataset.label}: ${context.parsed.y.toLocaleString('pt-BR')} passageiros`,
                                },
                            },
                        },
                        scales: {
                            x: { title: { display: true, text: 'Semana' } },
                            y: {
                                beginAtZero: true,
                                title: { display: true, text: 'Quantidade de passageiros' },
                                ticks: { callback: (valor) => valor.toLocaleString('pt-BR') },
                            },
                        },
                    },
                });
            }
        </script>
    @endif
</body>
</html>
