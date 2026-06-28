@extends('layouts.app')

@section('title', 'Ranking de Companhias Aéreas - Airport Manager')

@section('content')
@php
    $categorias = [
        ['titulo' => 'Objetivo', 'icone' => 'bullseye', 'cor' => 'primary', 'campo' => 'media_objetivo', 'dados' => $rankingsObjetivo],
        ['titulo' => 'Pontualidade', 'icone' => 'stopwatch', 'cor' => 'success', 'campo' => 'media_pontualidade', 'dados' => $rankingsPontualidade],
        ['titulo' => 'Serviços', 'icone' => 'bell', 'cor' => 'info', 'campo' => 'media_servicos', 'dados' => $rankingsServicos],
        ['titulo' => 'Pátio', 'icone' => 'buildings', 'cor' => 'warning', 'campo' => 'media_patio', 'dados' => $rankingsPatio],
    ];
@endphp

<div class="ranking-page py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <span class="text-uppercase text-primary fw-bold small tracking-wide">Desempenho operacional</span>
            <h1 class="h2 fw-bold mb-1 mt-1">
                <i class="bi bi-trophy-fill text-warning me-2"></i>Ranking de Companhias Aéreas
            </h1>
            <p class="text-muted mb-0">Comparativo por médias ponderadas e volume de operação.</p>
        </div>
        <a href="{{ route('companhias.informacoes') }}" class="btn btn-outline-primary">
            <i class="bi bi-info-circle me-1"></i> Informações gerais
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('companhias.ranking') }}" id="companhiaRankingForm" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="periodo" class="form-label fw-semibold small text-muted">Período</label>
                    <select name="periodo" id="periodo" class="form-select" onchange="atualizarFiltrosCompanhia()">
                        <option value="geral" @selected(($periodoSelecionado ?? 'geral') === 'geral')>Todos os dados</option>
                        <option value="semanal" @selected(($periodoSelecionado ?? '') === 'semanal')>Semanal</option>
                        <option value="mensal" @selected(($periodoSelecionado ?? '') === 'mensal')>Mensal</option>
                        <option value="anual" @selected(($periodoSelecionado ?? '') === 'anual')>Anual</option>
                    </select>
                </div>

                <div class="col-md-3 filtro-periodo" id="filtroSemanal" hidden>
                    <label for="semana" class="form-label fw-semibold small text-muted">Semana</label>
                    <select name="semana" id="semana" class="form-select" onchange="this.form.submit()">
                        <option value="">Selecione</option>
                        @foreach($semanasDisponiveis as $semana)
                            <option value="{{ $semana->semana }}" @selected(($semanaSelecionada ?? '') === $semana->semana)>
                                Semana {{ $semana->numero_semana }} — {{ $semana->ano }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 filtro-periodo" id="filtroMensal" hidden>
                    <div class="row g-2">
                        <div class="col-6">
                            <label for="ano" class="form-label fw-semibold small text-muted">Ano</label>
                            <select name="ano" id="ano" class="form-select" onchange="habilitarMes(this.value)">
                                <option value="">Selecione</option>
                                @foreach($anosDisponiveis as $anoOption)
                                    <option value="{{ $anoOption }}" @selected((string) ($anoFiltro ?? '') === (string) $anoOption)>{{ $anoOption }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="mes" class="form-label fw-semibold small text-muted">Mês</label>
                            <select name="mes" id="mes" class="form-select" onchange="this.form.submit()" @disabled(!($anoFiltro ?? null))>
                                <option value="">Selecione</option>
                                @for($mes = 1; $mes <= 12; $mes++)
                                    <option value="{{ $mes }}" @selected((string) ($mesSelecionado ?? '') === (string) $mes)>
                                        {{ str_pad($mes, 2, '0', STR_PAD_LEFT) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 filtro-periodo" id="filtroAnual" hidden>
                    <label for="ano_selecionado" class="form-label fw-semibold small text-muted">Ano</label>
                    <select name="ano_selecionado" id="ano_selecionado" class="form-select" onchange="this.form.submit()">
                        <option value="">Selecione</option>
                        @foreach($anosDisponiveis as $anoOption)
                            <option value="{{ $anoOption }}" @selected((string) ($anoSelecionado ?? '') === (string) $anoOption)>{{ $anoOption }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 ms-auto">
                    @if(($periodoSelecionado ?? 'geral') !== 'geral')
                        <a href="{{ route('companhias.ranking') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-x-circle me-1"></i>Limpar
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card bg-primary text-white">
                <i class="bi bi-buildings"></i><span>Companhias</span>
                <strong>{{ $estatisticas['total_companhias'] }}</strong>
                <small>{{ $estatisticas['companhias_ativas'] }} com voos no período</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card bg-success text-white">
                <i class="bi bi-airplane"></i><span>Total de voos</span>
                <strong>{{ number_format($estatisticas['total_voos'], 0, ',', '.') }}</strong>
                <small>operações contabilizadas</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card bg-info text-white">
                <i class="bi bi-people-fill"></i><span>Passageiros</span>
                <strong>{{ number_format($estatisticas['total_passageiros'], 0, ',', '.') }}</strong>
                <small>transportados no período</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card stat-dark text-white">
                <i class="bi bi-star-fill"></i><span>Média geral</span>
                <strong>{{ number_format($estatisticas['media_geral'], 1, ',', '.') }}</strong>
                <small>{{ $estatisticas['companhias_avaliadas'] }} companhias avaliadas</small>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4 overflow-hidden">
        <div class="card-header border-0 ranking-header text-white p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="trophy-icon"><i class="bi bi-trophy-fill"></i></div>
                <div>
                    <h2 class="h5 mb-1">Classificação geral</h2>
                    <p class="mb-0 opacity-75">Média das categorias em que a companhia possui avaliação</p>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th class="ps-4">Posição</th><th>Companhia</th><th class="text-center">Nota geral</th><th class="text-end pe-4">Voos</th></tr>
                    </thead>
                    <tbody>
                        @forelse($rankingsPorNota as $index => $companhia)
                            <tr>
                                <td class="ps-4"><span class="position-badge position-{{ $index + 1 }}">{{ $index + 1 }}º</span></td>
                                <td>
                                    <a href="{{ route('companhias.dashboard', $companhia['id']) }}" class="fw-bold text-decoration-none">{{ $companhia['nome'] }}</a>
                                    <small class="text-muted d-block">{{ $companhia['codigo'] ?: 'Sem código' }}</small>
                                </td>
                                <td class="text-center"><span class="score-badge">{{ number_format($companhia['nota_geral'], 1, ',', '.') }}</span></td>
                                <td class="text-end pe-4">{{ number_format($companhia['total_voos'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-5">Nenhuma companhia avaliada neste período.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        @foreach($categorias as $categoria)
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100 category-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h2 class="h5 mb-0"><i class="bi bi-{{ $categoria['icone'] }} text-{{ $categoria['cor'] }} me-2"></i>{{ $categoria['titulo'] }}</h2>
                            <span class="small text-muted">Top 10</span>
                        </div>
                        <div class="list-group list-group-flush">
                            @forelse($categoria['dados']->take(10) as $index => $companhia)
                                <div class="list-group-item px-0 d-flex align-items-center gap-3">
                                    <span class="rank-number">{{ $index + 1 }}</span>
                                    <div class="flex-grow-1 min-width-0">
                                        <a href="{{ route('companhias.dashboard', $companhia['id']) }}" class="fw-semibold text-decoration-none text-truncate d-block">{{ $companhia['nome'] }}</a>
                                        <small class="text-muted">{{ number_format($companhia['total_voos'], 0, ',', '.') }} voos</small>
                                    </div>
                                    <span class="badge bg-{{ $categoria['cor'] }} rounded-pill fs-6">{{ number_format($companhia[$categoria['campo']], 1, ',', '.') }}</span>
                                </div>
                            @empty
                                <p class="text-center text-muted py-4 mb-0">Sem avaliações nesta categoria.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        @foreach([
            ['titulo' => 'Maior volume de voos', 'icone' => 'graph-up-arrow', 'cor' => 'primary', 'campo' => 'total_voos', 'sufixo' => 'voos', 'dados' => $rankingsPorVoos],
            ['titulo' => 'Mais passageiros transportados', 'icone' => 'people-fill', 'cor' => 'danger', 'campo' => 'total_passageiros', 'sufixo' => 'passageiros', 'dados' => $rankingsPorPassageiros],
        ] as $rankingVolume)
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h2 class="h5 mb-3"><i class="bi bi-{{ $rankingVolume['icone'] }} text-{{ $rankingVolume['cor'] }} me-2"></i>{{ $rankingVolume['titulo'] }}</h2>
                        @forelse($rankingVolume['dados']->take(10) as $index => $companhia)
                            <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                                <span class="rank-number">{{ $index + 1 }}</span>
                                <span class="flex-grow-1 fw-semibold">{{ $companhia['nome'] }}</span>
                                <span class="small fw-bold text-{{ $rankingVolume['cor'] }}">
                                    {{ number_format($companhia[$rankingVolume['campo']], 0, ',', '.') }} {{ $rankingVolume['sufixo'] }}
                                </span>
                            </div>
                        @empty
                            <p class="text-center text-muted py-4">Nenhuma operação no período.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($rankingsPorNota->isNotEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3"><h2 class="h5 mb-0"><i class="bi bi-table me-2"></i>Detalhamento por companhia</h2></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Companhia</th><th class="text-center">Objetivo</th><th class="text-center">Pontualidade</th>
                            <th class="text-center">Serviços</th><th class="text-center">Pátio</th><th class="text-center">Geral</th>
                            <th class="text-end pe-3">Voos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rankingsPorNota as $companhia)
                            <tr>
                                <td class="ps-3 fw-semibold">{{ $companhia['nome'] }}</td>
                                @foreach(['media_objetivo', 'media_pontualidade', 'media_servicos', 'media_patio', 'nota_geral'] as $campo)
                                    <td class="text-center">{{ $companhia[$campo] > 0 ? number_format($companhia[$campo], 1, ',', '.') : '—' }}</td>
                                @endforeach
                                <td class="text-end pe-3">{{ number_format($companhia['total_voos'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function atualizarFiltrosCompanhia() {
        const periodo = document.getElementById('periodo').value;
        exibirFiltro(periodo);
        if (periodo === 'geral') document.getElementById('companhiaRankingForm').submit();
    }

    function exibirFiltro(periodo) {
        document.querySelectorAll('.filtro-periodo').forEach(elemento => elemento.hidden = true);
        const filtros = { semanal: 'filtroSemanal', mensal: 'filtroMensal', anual: 'filtroAnual' };
        if (filtros[periodo]) document.getElementById(filtros[periodo]).hidden = false;
    }

    function habilitarMes(ano) {
        const mes = document.getElementById('mes');
        mes.disabled = !ano;
        if (!ano) mes.value = '';
    }

    document.addEventListener('DOMContentLoaded', () => exibirFiltro(document.getElementById('periodo').value));
</script>
@endpush

@push('styles')
<style>
    .ranking-page { --ranking-navy: #102452; }
    .tracking-wide { letter-spacing: .12em; }
    .stat-card { position: relative; min-height: 145px; padding: 1.25rem; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 24px rgba(25,45,80,.12); }
    .stat-card > i { position: absolute; right: 1rem; top: .75rem; font-size: 3rem; opacity: .18; }
    .stat-card span, .stat-card small { display: block; opacity: .85; }
    .stat-card strong { display: block; margin: .35rem 0; font-size: 2rem; line-height: 1; }
    .stat-dark { background: var(--ranking-navy); }
    .ranking-header { background: linear-gradient(135deg, #102452, #1767aa); }
    .trophy-icon { display: grid; width: 48px; height: 48px; place-items: center; color: #102452; border-radius: 12px; background: #ffc107; font-size: 1.4rem; }
    .position-badge, .rank-number { display: inline-grid; width: 32px; height: 32px; place-items: center; border-radius: 50%; color: #57657a; background: #eef2f7; font-weight: 800; }
    .position-1 { color: #6c5200; background: #ffe69c; }
    .position-2 { color: #46505d; background: #dce1e7; }
    .position-3 { color: #754320; background: #efc3a4; }
    .score-badge { display: inline-block; min-width: 52px; padding: .4rem .75rem; color: #fff; border-radius: 20px; background: #198754; font-weight: 800; }
    .category-card { border-top: 3px solid #e5eaf0 !important; }
    .min-width-0 { min-width: 0; }
    .list-group-item:last-child, .border-bottom:last-child { border-bottom: 0 !important; }
    @media (max-width: 767.98px) {
        .ranking-page h1 { font-size: 1.65rem; }
        .stat-card { min-height: 125px; }
    }
</style>
@endpush
