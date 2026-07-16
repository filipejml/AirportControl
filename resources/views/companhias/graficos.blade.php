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
        .chart-card {
            height: 100%;
            padding: 1.25rem;
            border: 1px solid #e4e7ec;
            border-radius: 1rem;
            background: #fff;
            box-shadow: 0 8px 22px rgba(16, 24, 40, .045);
        }
        .chart-area { position: relative; height: 300px; }
        .summary-card { padding: 1rem; border-radius: .9rem; background: #fff; border: 1px solid #e4e7ec; }
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
                    <p class="mb-0 text-white-50">Distribuição operacional de voos e passageiros.</p>
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

        <section class="row g-4">
            @foreach([
                ['id' => 'voosHorario', 'titulo' => 'Voos por horário'],
                ['id' => 'passageirosHorario', 'titulo' => 'Passageiros por horário'],
                ['id' => 'voosTipo', 'titulo' => 'Voos por tipo'],
                ['id' => 'passageirosTipo', 'titulo' => 'Passageiros por tipo'],
                ['id' => 'voosAeroporto', 'titulo' => 'Voos por aeroporto'],
                ['id' => 'passageirosAeroporto', 'titulo' => 'Passageiros por aeroporto'],
                ['id' => 'voosModelo', 'titulo' => 'Voos por modelo'],
                ['id' => 'passageirosModelo', 'titulo' => 'Passageiros por modelo'],
            ] as $grafico)
                <div class="col-xl-6">
                    <article class="chart-card">
                        <h2 class="h5 fw-bold mb-3"><i class="bi bi-bar-chart me-2 text-primary"></i>{{ $grafico['titulo'] }}</h2>
                        <div class="chart-area"><canvas id="{{ $grafico['id'] }}"></canvas></div>
                    </article>
                </div>
            @endforeach
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="module">
        const horarioCores = ['#0B3D91', '#4DA3FF', '#F97316', '#DC2626', '#7E22CE'];
        const tipoCores = ['#0d6efd', '#F97316'];
        const paleta = ['#0d6efd', '#198754', '#0dcaf0', '#F97316', '#7E22CE', '#DC2626', '#6c757d'];
        const conjuntos = {
            voosHorario: [@json($voosPorHorario), horarioCores],
            passageirosHorario: [@json($passageirosPorHorario), horarioCores],
            voosTipo: [@json($voosPorTipo), tipoCores],
            passageirosTipo: [@json($passageirosPorTipo), tipoCores],
            voosAeroporto: [@json($voosPorAeroporto), paleta],
            passageirosAeroporto: [@json($passageirosPorAeroporto), paleta],
            voosModelo: [@json($voosPorModelo), paleta],
            passageirosModelo: [@json($passageirosPorModelo), paleta],
        };

        Object.entries(conjuntos).forEach(([id, [dados, cores]]) => {
            AirportCharts.create(document.getElementById(id), {
                type: 'bar',
                data: {
                    labels: Object.keys(dados),
                    datasets: [{
                        data: Object.values(dados),
                        backgroundColor: Object.keys(dados).map((_, indice) => cores[indice % cores.length]),
                        borderColor: Object.keys(dados).map((_, indice) => cores[indice % cores.length]),
                        borderWidth: 1,
                        borderRadius: 7,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } },
                },
            });
        });
    </script>
</body>
</html>
