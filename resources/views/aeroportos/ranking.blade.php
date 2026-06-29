@extends('layouts.app')

@section('title', 'Ranking de Aeroportos - Airport Manager')

@section('content')
@php
    $categorias = [
        ['titulo' => 'Objetivo', 'icone' => 'bullseye', 'cor' => 'primary', 'campo' => 'media_objetivo', 'dados' => $rankingsObjetivo],
        ['titulo' => 'Pontualidade', 'icone' => 'stopwatch', 'cor' => 'success', 'campo' => 'media_pontualidade', 'dados' => $rankingsPontualidade],
        ['titulo' => 'Serviços', 'icone' => 'bell', 'cor' => 'info', 'campo' => 'media_servicos', 'dados' => $rankingsServicos],
        ['titulo' => 'Pátio', 'icone' => 'buildings', 'cor' => 'warning', 'campo' => 'media_patio', 'dados' => $rankingsPatio],
    ];
@endphp

<div class="airport-ranking py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <span class="text-uppercase text-primary fw-bold small tracking-wide">Desempenho operacional</span>
            <h1 class="h2 fw-bold mt-1 mb-1"><i class="bi bi-trophy-fill text-warning me-2"></i>Ranking de Aeroportos</h1>
            <p class="text-muted mb-0">Comparativo por avaliações e volume de operação.</p>
        </div>
        <a href="{{ route('aeroportos.informacoes') }}" class="btn btn-outline-primary">
            <i class="bi bi-info-circle me-1"></i> Informações gerais
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('aeroportos.ranking') }}" id="aeroportoRankingForm" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="periodo" class="form-label fw-semibold small text-muted">Período</label>
                    <select name="periodo" id="periodo" class="form-select" onchange="atualizarFiltros()">
                        <option value="geral" @selected($periodoSelecionado === 'geral')>Todos os dados</option>
                        <option value="semanal" @selected($periodoSelecionado === 'semanal')>Semanal</option>
                        <option value="mensal" @selected($periodoSelecionado === 'mensal')>Mensal</option>
                        <option value="anual" @selected($periodoSelecionado === 'anual')>Anual</option>
                    </select>
                </div>
                <div class="col-md-3 filtro-periodo" id="filtroSemanal" hidden>
                    <label for="semana" class="form-label fw-semibold small text-muted">Semana</label>
                    <select name="semana" id="semana" class="form-select" onchange="this.form.submit()">
                        <option value="">Selecione</option>
                        @foreach($semanasDisponiveis as $semana)
                            <option value="{{ $semana->semana }}" @selected($semanaSelecionada === $semana->semana)>
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
                                @foreach($anosDisponiveis as $ano)
                                    <option value="{{ $ano }}" @selected((string) $anoFiltro === (string) $ano)>{{ $ano }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="mes" class="form-label fw-semibold small text-muted">Mês</label>
                            <select name="mes" id="mes" class="form-select" onchange="this.form.submit()" @disabled(!$anoFiltro)>
                                <option value="">Selecione</option>
                                @for($mes = 1; $mes <= 12; $mes++)
                                    <option value="{{ $mes }}" @selected((string) $mesSelecionado === (string) $mes)>{{ str_pad($mes, 2, '0', STR_PAD_LEFT) }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 filtro-periodo" id="filtroAnual" hidden>
                    <label for="ano_selecionado" class="form-label fw-semibold small text-muted">Ano</label>
                    <select name="ano_selecionado" id="ano_selecionado" class="form-select" onchange="this.form.submit()">
                        <option value="">Selecione</option>
                        @foreach($anosDisponiveis as $ano)
                            <option value="{{ $ano }}" @selected((string) $anoSelecionado === (string) $ano)>{{ $ano }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 ms-auto">
                    @if($periodoSelecionado !== 'geral')
                        <a href="{{ route('aeroportos.ranking') }}" class="btn btn-outline-secondary w-100"><i class="bi bi-x-circle me-1"></i>Limpar</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @foreach([
            ['Aeroportos', $estatisticas['total_aeroportos'], $estatisticas['aeroportos_ativos'].' com voos', 'geo-alt-fill', 'primary'],
            ['Total de voos', number_format($estatisticas['total_voos'], 0, ',', '.'), 'operações contabilizadas', 'airplane', 'success'],
            ['Passageiros', number_format($estatisticas['total_passageiros'], 0, ',', '.'), 'no período', 'people-fill', 'info'],
            ['Média geral', number_format($estatisticas['media_geral'], 1, ',', '.'), $estatisticas['aeroportos_avaliados'].' avaliados', 'star-fill', 'dark'],
        ] as [$rotulo, $valor, $detalhe, $icone, $cor])
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card bg-{{ $cor }} text-white">
                    <i class="bi bi-{{ $icone }}"></i><span>{{ $rotulo }}</span><strong>{{ $valor }}</strong><small>{{ $detalhe }}</small>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm mb-4 overflow-hidden">
        <div class="card-header ranking-header text-white p-4">
            <h2 class="h5 mb-1"><i class="bi bi-trophy-fill text-warning me-2"></i>Classificação geral</h2>
            <p class="mb-0 opacity-75">Média das categorias avaliadas</p>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light"><tr><th class="ps-4">Posição</th><th>Aeroporto</th><th class="text-center">Nota geral</th><th class="text-end pe-4">Voos</th></tr></thead>
                <tbody>
                    @forelse($rankingsPorNota as $index => $aeroporto)
                        <tr>
                            <td class="ps-4"><span class="position-badge position-{{ $index + 1 }}">{{ $index + 1 }}º</span></td>
                            <td><a href="{{ route('aeroportos.dashboard', $aeroporto['id']) }}" class="fw-bold text-decoration-none">{{ $aeroporto['nome'] }}</a></td>
                            <td class="text-center"><span class="score-badge">{{ number_format($aeroporto['nota_geral'], 1, ',', '.') }}</span></td>
                            <td class="text-end pe-4">{{ number_format($aeroporto['total_voos'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-5">Nenhum aeroporto avaliado neste período.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="row g-4 mb-4">
        @foreach($categorias as $categoria)
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h2 class="h5 mb-3"><i class="bi bi-{{ $categoria['icone'] }} text-{{ $categoria['cor'] }} me-2"></i>{{ $categoria['titulo'] }} <small class="text-muted float-end">Top 10</small></h2>
                        @forelse($categoria['dados']->take(10) as $index => $aeroporto)
                            <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                                <span class="rank-number">{{ $index + 1 }}</span>
                                <a href="{{ route('aeroportos.dashboard', $aeroporto['id']) }}" class="flex-grow-1 fw-semibold text-decoration-none">{{ $aeroporto['nome'] }}</a>
                                <span class="badge bg-{{ $categoria['cor'] }} rounded-pill fs-6">{{ number_format($aeroporto[$categoria['campo']], 1, ',', '.') }}</span>
                            </div>
                        @empty
                            <p class="text-center text-muted py-4">Sem avaliações nesta categoria.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4">
        @foreach([
            ['Maior volume de voos', 'total_voos', 'voos', $rankingsPorVoos, 'primary'],
            ['Mais passageiros', 'total_passageiros', 'passageiros', $rankingsPorPassageiros, 'danger'],
        ] as [$titulo, $campo, $sufixo, $dados, $cor])
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100"><div class="card-body">
                    <h2 class="h5 mb-3">{{ $titulo }}</h2>
                    @forelse($dados->take(10) as $index => $aeroporto)
                        <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                            <span class="rank-number">{{ $index + 1 }}</span><span class="flex-grow-1 fw-semibold">{{ $aeroporto['nome'] }}</span>
                            <span class="small fw-bold text-{{ $cor }}">{{ number_format($aeroporto[$campo], 0, ',', '.') }} {{ $sufixo }}</span>
                        </div>
                    @empty
                        <p class="text-center text-muted py-4">Nenhuma operação no período.</p>
                    @endforelse
                </div></div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
function exibirFiltro(periodo) {
    document.querySelectorAll('.filtro-periodo').forEach(item => item.hidden = true);
    const filtros = { semanal: 'filtroSemanal', mensal: 'filtroMensal', anual: 'filtroAnual' };
    if (filtros[periodo]) document.getElementById(filtros[periodo]).hidden = false;
}
function atualizarFiltros() {
    const periodo = document.getElementById('periodo').value;
    exibirFiltro(periodo);
    if (periodo === 'geral') document.getElementById('aeroportoRankingForm').submit();
}
function habilitarMes(ano) {
    document.getElementById('mes').disabled = !ano;
}
document.addEventListener('DOMContentLoaded', () => exibirFiltro(document.getElementById('periodo').value));
</script>
@endpush

@push('styles')
<style>
.tracking-wide { letter-spacing: .12em; }
.stat-card { position: relative; min-height: 140px; padding: 1.25rem; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 24px rgba(25,45,80,.12); }
.stat-card > i { position: absolute; right: 1rem; top: .75rem; font-size: 3rem; opacity: .18; }
.stat-card span, .stat-card small { display: block; opacity: .85; }
.stat-card strong { display: block; margin: .35rem 0; font-size: 2rem; line-height: 1; }
.ranking-header { background: linear-gradient(135deg, #102452, #1767aa); }
.position-badge, .rank-number { display: inline-grid; width: 32px; height: 32px; place-items: center; border-radius: 50%; color: #57657a; background: #eef2f7; font-weight: 800; }
.position-1 { color: #6c5200; background: #ffe69c; }.position-2 { background: #dce1e7; }.position-3 { color: #754320; background: #efc3a4; }
.score-badge { display: inline-block; min-width: 52px; padding: .4rem .75rem; color: #fff; border-radius: 20px; background: #198754; font-weight: 800; }
.border-bottom:last-child { border-bottom: 0 !important; }
</style>
@endpush
