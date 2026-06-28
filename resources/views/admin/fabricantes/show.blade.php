@extends('layouts.app')

@section('title', $fabricante->nome . ' - Fabricante')

@section('content')
@php
    $totalModelos = $fabricante->aeronaves->count();
    $capacidadeTotal = (int) $fabricante->aeronaves->sum('capacidade');
    $capacidadeMedia = $totalModelos > 0 ? round($fabricante->aeronaves->avg('capacidade')) : 0;
    $totalCompanhias = $fabricante->aeronaves->flatMap->companhias->unique('id')->count();
    $portes = [
        'PC' => $fabricante->aeronaves->where('porte', 'PC')->count(),
        'MC' => $fabricante->aeronaves->where('porte', 'MC')->count(),
        'LC' => $fabricante->aeronaves->where('porte', 'LC')->count(),
    ];
@endphp

<div class="manufacturer-show container py-4">
    <div class="hero-card mb-4">
        <div class="hero-pattern"></div>
        <div class="position-relative d-flex flex-wrap justify-content-between align-items-center gap-4">
            <div class="d-flex align-items-center gap-3">
                <div class="hero-icon"><i class="bi bi-tools"></i></div>
                <div>
                    <span class="text-uppercase small fw-bold opacity-75 tracking-wide">Fabricante de aeronaves</span>
                    <h1 class="display-6 fw-bold mb-1">{{ $fabricante->nome }}</h1>
                    <div class="opacity-75">
                        <i class="bi bi-geo-alt me-1"></i>{{ $fabricante->pais_origem ?: 'País de origem não informado' }}
                    </div>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('fabricantes.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left me-1"></i>Voltar
                </a>
                <a href="{{ route('fabricantes.edit', $fabricante) }}" class="btn btn-warning">
                    <i class="bi bi-pencil me-1"></i>Editar fabricante
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach([
            ['Modelos', $totalModelos, 'airplane', 'primary', 'aeronaves cadastradas'],
            ['Capacidade total', $capacidadeTotal, 'people-fill', 'success', 'assentos entre os modelos'],
            ['Capacidade média', $capacidadeMedia, 'bar-chart-fill', 'info', 'assentos por modelo'],
            ['Companhias', $totalCompanhias, 'buildings', 'warning', 'operam estes modelos'],
        ] as [$rotulo, $valor, $icone, $cor, $detalhe])
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card h-100">
                    <div class="stat-icon text-{{ $cor }} bg-{{ $cor }} bg-opacity-10">
                        <i class="bi bi-{{ $icone }}"></i>
                    </div>
                    <div>
                        <span class="text-muted small">{{ $rotulo }}</span>
                        <strong>{{ number_format($valor, 0, ',', '.') }}</strong>
                        <small class="text-muted">{{ $detalhe }}</small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 px-4 pt-4 pb-2">
            <h2 class="h5 fw-bold mb-1">Desempenho operacional</h2>
            <p class="small text-muted mb-0">Dados acumulados dos modelos deste fabricante</p>
        </div>
        <div class="card-body p-4">
            <div class="row g-3 mb-4">
                @foreach([
                    ['Total de voos', $estatisticasOperacionais['total_voos'], 'airplane-engines', 'primary', 'operações realizadas'],
                    ['Passageiros', $estatisticasOperacionais['total_passageiros'], 'people-fill', 'success', 'transportados'],
                    ['Passageiros por voo', $estatisticasOperacionais['passageiros_por_voo'], 'person-check-fill', 'info', 'média por operação'],
                    ['Aeroportos atendidos', $estatisticasOperacionais['total_aeroportos'], 'geo-alt-fill', 'warning', 'destinos com registros'],
                ] as [$rotulo, $valor, $icone, $cor, $detalhe])
                    <div class="col-sm-6 col-xl-3">
                        <div class="operational-metric">
                            <i class="bi bi-{{ $icone }} text-{{ $cor }}"></i>
                            <div>
                                <span>{{ $rotulo }}</span>
                                <strong>{{ number_format($valor, 0, ',', '.') }}</strong>
                                <small>{{ $detalhe }}</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row g-3 align-items-stretch">
                <div class="col-md-4">
                    <div class="overall-score h-100">
                        <span>Nota geral</span>
                        <strong>
                            {{ $estatisticasOperacionais['nota_geral'] > 0
                                ? number_format($estatisticasOperacionais['nota_geral'], 1, ',', '.')
                                : '—' }}
                        </strong>
                        <small>Média das categorias avaliadas</small>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="row g-2 h-100">
                        @foreach([
                            ['Objetivo', 'objetivo', 'bullseye', 'primary'],
                            ['Pontualidade', 'pontualidade', 'stopwatch', 'success'],
                            ['Serviços', 'servicos', 'bell', 'info'],
                            ['Pátio', 'patio', 'buildings', 'warning'],
                        ] as [$rotulo, $campo, $icone, $cor])
                            <div class="col-6 col-lg-3">
                                <div class="category-score h-100">
                                    <i class="bi bi-{{ $icone }} text-{{ $cor }}"></i>
                                    <span>{{ $rotulo }}</span>
                                    <strong>
                                        {{ $estatisticasOperacionais['medias'][$campo] > 0
                                            ? number_format($estatisticasOperacionais['medias'][$campo], 1, ',', '.')
                                            : '—' }}
                                    </strong>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 p-4 pb-2">
                    <div class="d-flex justify-content-between align-items-center gap-3">
                        <div>
                            <h2 class="h5 fw-bold mb-1">Modelos de aeronaves</h2>
                            <p class="text-muted small mb-0">Aeronaves produzidas por {{ $fabricante->nome }}</p>
                        </div>
                        <a href="{{ route('aeronaves.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i>Novo modelo
                        </a>
                    </div>
                </div>
                <div class="card-body p-0 pt-3">
                    @if($totalModelos > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Modelo</th>
                                        <th>Capacidade</th>
                                        <th>Porte</th>
                                        <th>Companhias</th>
                                        <th>Cadastro</th>
                                        <th class="text-end pe-4">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($fabricante->aeronaves->sortBy('modelo') as $aeronave)
                                        @php
                                            $porte = match($aeronave->porte) {
                                                'PC' => ['Pequeno', 'info'],
                                                'MC' => ['Médio', 'warning'],
                                                'LC' => ['Grande', 'danger'],
                                                default => ['Não classificado', 'secondary'],
                                            };
                                        @endphp
                                        <tr>
                                            <td class="ps-4">
                                                <a href="{{ route('aeronaves.show', $aeronave) }}" class="model-link">
                                                    <span class="model-icon"><i class="bi bi-airplane"></i></span>
                                                    <span>
                                                        <strong>{{ $aeronave->modelo }}</strong>
                                                        <small>ID #{{ $aeronave->id }}</small>
                                                    </span>
                                                </a>
                                            </td>
                                            <td>
                                                <strong>{{ number_format($aeronave->capacidade, 0, ',', '.') }}</strong>
                                                <small class="text-muted"> assentos</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $porte[1] }}{{ $porte[1] === 'warning' ? ' text-dark' : '' }}">
                                                    {{ $aeronave->porte ?: '—' }} · {{ $porte[0] }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    {{ $aeronave->companhias->count() }}
                                                    {{ $aeronave->companhias->count() === 1 ? 'companhia' : 'companhias' }}
                                                </span>
                                            </td>
                                            <td class="text-muted small">
                                                {{ $aeronave->created_at?->format('d/m/Y') ?? '—' }}
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('aeronaves.show', $aeronave) }}" class="btn btn-outline-primary" title="Visualizar">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('aeronaves.edit', $aeronave) }}" class="btn btn-outline-secondary" title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon"><i class="bi bi-airplane"></i></div>
                            <h3 class="h5">Nenhum modelo cadastrado</h3>
                            <p class="text-muted">Cadastre a primeira aeronave deste fabricante.</p>
                            <a href="{{ route('aeronaves.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>Cadastrar aeronave
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-4">Distribuição por porte</h2>
                    @foreach([
                        ['PC', 'Pequeno porte', 'info'],
                        ['MC', 'Médio porte', 'warning'],
                        ['LC', 'Grande porte', 'danger'],
                    ] as [$codigo, $nome, $cor])
                        @php
                            $percentual = $totalModelos > 0 ? ($portes[$codigo] / $totalModelos) * 100 : 0;
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small"><strong>{{ $codigo }}</strong> · {{ $nome }}</span>
                                <span class="fw-bold">{{ $portes[$codigo] }}</span>
                            </div>
                            <div class="progress" style="height: 7px;">
                                <div class="progress-bar bg-{{ $cor }}" style="width: {{ $percentual }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">Informações do cadastro</h2>
                    <div class="info-row">
                        <span><i class="bi bi-hash me-2"></i>Identificador</span>
                        <strong>#{{ $fabricante->id }}</strong>
                    </div>
                    <div class="info-row">
                        <span><i class="bi bi-calendar-plus me-2"></i>Cadastrado em</span>
                        <strong>{{ $fabricante->created_at?->format('d/m/Y') ?? '—' }}</strong>
                    </div>
                    <div class="info-row">
                        <span><i class="bi bi-clock-history me-2"></i>Atualizado em</span>
                        <strong>{{ $fabricante->updated_at?->format('d/m/Y') ?? '—' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.manufacturer-show { --brand-navy: #102452; }
.tracking-wide { letter-spacing: .12em; }
.hero-card { position: relative; padding: 2rem; overflow: hidden; color: #fff; border-radius: 16px; background: linear-gradient(135deg, #102452, #1767aa); box-shadow: 0 12px 30px rgba(16,36,82,.22); }
.hero-pattern { position: absolute; width: 280px; height: 280px; right: -80px; top: -130px; border: 45px solid rgba(255,255,255,.07); border-radius: 50%; }
.hero-icon { display: grid; width: 64px; height: 64px; flex: 0 0 auto; place-items: center; color: var(--brand-navy); border-radius: 16px; background: #ffc107; font-size: 1.75rem; }
.stat-card { display: flex; padding: 1.25rem; align-items: center; gap: 1rem; border-radius: 12px; background: #fff; box-shadow: 0 .125rem .75rem rgba(0,0,0,.08); }
.stat-icon { display: grid; width: 48px; height: 48px; flex: 0 0 auto; place-items: center; border-radius: 12px; font-size: 1.35rem; }
.stat-card strong, .stat-card small { display: block; }
.stat-card strong { font-size: 1.55rem; line-height: 1.2; }
.operational-metric { display: flex; height: 100%; padding: 1rem; gap: .8rem; align-items: center; border: 1px solid #edf0f2; border-radius: 10px; }
.operational-metric > i { font-size: 1.6rem; }
.operational-metric span, .operational-metric small, .operational-metric strong { display: block; }
.operational-metric span, .operational-metric small { color: #6c757d; font-size: .78rem; }
.operational-metric strong { font-size: 1.35rem; }
.overall-score { display: flex; min-height: 120px; flex-direction: column; align-items: center; justify-content: center; color: #fff; border-radius: 12px; background: linear-gradient(135deg, #102452, #1767aa); text-align: center; }
.overall-score strong { font-size: 2.5rem; line-height: 1.1; }
.overall-score small { opacity: .7; }
.category-score { display: flex; min-height: 120px; padding: .8rem; flex-direction: column; align-items: center; justify-content: center; border-radius: 10px; background: #f8f9fa; text-align: center; }
.category-score i { margin-bottom: .25rem; font-size: 1.25rem; }
.category-score span { color: #6c757d; font-size: .78rem; }
.category-score strong { font-size: 1.4rem; }
.model-link { display: flex; align-items: center; gap: .65rem; color: #212529; text-decoration: none; }
.model-link:hover strong { color: #0d6efd; }
.model-link small { display: block; color: #6c757d; font-weight: 400; }
.model-icon { display: grid; width: 36px; height: 36px; place-items: center; color: #0d6efd; border-radius: 9px; background: #e7f1ff; }
.empty-state { padding: 4rem 1rem; text-align: center; }
.empty-icon { display: grid; width: 72px; height: 72px; margin: 0 auto 1rem; place-items: center; color: #6c757d; border-radius: 50%; background: #f1f3f5; font-size: 2rem; }
.info-row { display: flex; padding: .8rem 0; justify-content: space-between; gap: 1rem; border-bottom: 1px solid #edf0f2; font-size: .9rem; }
.info-row:last-child { border-bottom: 0; }
.info-row span { color: #6c757d; }
@media (max-width: 767.98px) {
    .hero-card { padding: 1.5rem; }
    .hero-icon { width: 52px; height: 52px; }
    .hero-card h1 { font-size: 1.75rem; }
}
</style>
@endpush
