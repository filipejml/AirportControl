@extends('layouts.app')

@section('title', 'Ranking de Aeronaves - Airport Manager')

@section('content')
<div class="container mt-5">
    {{-- Cabeçalho com botões --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="bi bi-trophy-fill text-warning"></i> 
            Ranking de Aeronaves
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('aeronaves.informacoes') }}" class="btn btn-outline-info">
                <i class="bi bi-info-circle"></i> Informações Gerais
            </a>
            @if(auth()->user()->tipo == 0)
            <a href="{{ route('aeronaves.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-list-ul"></i> Gerenciar Aeronaves
            </a>
            @endif
        </div>
    </div>

    {{-- Estatísticas Gerais --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title mb-2">Total de Aeronaves</h6>
                    <h2 class="mb-0">{{ $estatisticas['total_aeronaves'] }}</h2>
                    <small>{{ $estatisticas['total_fabricantes'] }} fabricantes diferentes</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title mb-2">Total de Voos</h6>
                    <h2 class="mb-0">{{ number_format($estatisticas['total_voos_geral']) }}</h2>
                    <small>{{ $estatisticas['aeronaves_com_dados'] }} aeronaves com dados</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title mb-2">Total de Passageiros</h6>
                    <h2 class="mb-0">{{ number_format($estatisticas['total_passageiros_geral']) }}</h2>
                    <small>transportados no total</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title mb-2">Nota Média Geral</h6>
                    <h2 class="mb-0">{{ number_format($estatisticas['media_nota_geral'], 1) }}</h2>
                    <small>⭐ média de todas as aeronaves</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Distribuição por Porte --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-center mb-3">
                        <i class="bi bi-pie-chart"></i> Distribuição por Porte
                    </h5>
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <i class="bi bi-airplane fs-1 text-secondary"></i>
                                <h6 class="mt-2">Pequeno Porte (≤100)</h6>
                                <span class="badge bg-secondary rounded-pill fs-6">{{ $estatisticas['porte_pequeno'] }} aeronaves</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <i class="bi bi-airplane-fill fs-1 text-info"></i>
                                <h6 class="mt-2">Médio Porte (101-299)</h6>
                                <span class="badge bg-info rounded-pill fs-6">{{ $estatisticas['porte_medio'] }} aeronaves</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <i class="bi bi-airplane-engines fs-1 text-primary"></i>
                                <h6 class="mt-2">Grande Porte (≥300)</h6>
                                <span class="badge bg-primary rounded-pill fs-6">{{ $estatisticas['porte_grande'] }} aeronaves</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Destaques do Ranking --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient text-white">
                    <h5 class="mb-0"><i class="bi bi-star-fill"></i> Destaques do Ranking</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="p-3 border rounded bg-light">
                                <i class="bi bi-trophy-fill text-warning fs-1"></i>
                                <h6 class="mt-2">Melhor Nota Geral</h6>
                                @if($melhorNotaGeral)
                                    <strong>{{ $melhorNotaGeral['modelo'] ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $melhorNotaGeral['fabricante'] ?? 'N/A' }}</small><br>
                                    <span class="badge bg-success mt-2 fs-6">⭐ {{ $melhorNotaGeral['nota_geral'] ?? 0 }}</span>
                                    <small class="d-block text-muted">{{ number_format($melhorNotaGeral['total_voos'] ?? 0) }} voos</small>
                                @else
                                    <span class="text-muted">Sem dados suficientes</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="p-3 border rounded bg-light">
                                <i class="bi bi-graph-up text-success fs-1"></i>
                                <h6 class="mt-2">Mais Voos Realizados</h6>
                                @if($maisVoos)
                                    <strong>{{ $maisVoos['modelo'] ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $maisVoos['fabricante'] ?? 'N/A' }}</small><br>
                                    <span class="badge bg-primary mt-2 fs-6">{{ number_format($maisVoos['total_voos'] ?? 0) }} voos</span>
                                @else
                                    <span class="text-muted">Sem dados</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <i class="bi bi-people-fill text-danger fs-1"></i>
                                <h6 class="mt-2">Mais Passageiros</h6>
                                @if($maisPassageiros)
                                    <strong>{{ $maisPassageiros['modelo'] ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $maisPassageiros['fabricante'] ?? 'N/A' }}</small><br>
                                    <span class="badge bg-danger mt-2 fs-6">{{ number_format($maisPassageiros['total_passageiros'] ?? 0) }} passageiros</span>
                                @else
                                    <span class="text-muted">Sem dados</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Rankings por Notas --}}
    @if($estatisticas['aviso_sem_dados'] ?? false)
        <div class="alert alert-warning text-center">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <strong>Atenção:</strong> Não há aeronaves com mínimo de 3 registros de voo para exibir as classificações por nota.
            <br><small>O ranking de notas requer no mínimo 3 avaliações por aeronave para garantir significância estatística.</small>
        </div>
    @elseif(isset($rankingsObjetivo) && $rankingsObjetivo->isEmpty())
        <div class="alert alert-info text-center">
            <i class="bi bi-info-circle-fill"></i>
            <strong>Sem dados suficientes:</strong> Nenhuma aeronave atingiu o mínimo de 3 registros de voo para participar do ranking de notas.
        </div>
    @else
    <div class="row g-4 mt-4">
        {{-- Ranking Objetivo --}}
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-center mb-3">
                        <i class="bi bi-bullseye text-success"></i> Ranking - Nota de Objetivo
                    </h5>
                    <div class="list-group list-group-flush">
                        @forelse($rankingsObjetivo->take(10) as $index => $aeronave)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    @if($index == 0) 🥇
                                    @elseif($index == 1) 🥈
                                    @elseif($index == 2) 🥉
                                    @else {{ $index + 1 }}º
                                    @endif
                                    <a href="{{ route('aeronaves.dashboard', $aeronave['id']) }}" 
                                       class="text-decoration-none ms-2 fw-semibold">
                                        {{ $aeronave['modelo'] }}
                                    </a>
                                    <small class="text-muted ms-2">({{ $aeronave['fabricante'] }})</small>
                                    <br>
                                    <small class="text-muted ms-4">
                                        <i class="bi bi-calendar-check"></i> {{ number_format($aeronave['total_voos']) }} voos
                                    </small>
                                </div>
                                <span class="badge bg-success rounded-pill fs-6 px-3 py-2">
                                    {{ number_format($aeronave['media_objetivo'], 1) }}
                                </span>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-muted">
                                Nenhuma aeronave com dados suficientes
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Ranking Pontualidade --}}
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-center mb-3">
                        <i class="bi bi-clock-history text-info"></i> Ranking - Nota de Pontualidade
                    </h5>
                    <div class="list-group list-group-flush">
                        @forelse($rankingsPontualidade->take(10) as $index => $aeronave)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    @if($index == 0) 🥇
                                    @elseif($index == 1) 🥈
                                    @elseif($index == 2) 🥉
                                    @else {{ $index + 1 }}º
                                    @endif
                                    <a href="{{ route('aeronaves.dashboard', $aeronave['id']) }}" 
                                       class="text-decoration-none ms-2 fw-semibold">
                                        {{ $aeronave['modelo'] }}
                                    </a>
                                    <small class="text-muted ms-2">({{ $aeronave['fabricante'] }})</small>
                                    <br>
                                    <small class="text-muted ms-4">
                                        <i class="bi bi-calendar-check"></i> {{ number_format($aeronave['total_voos']) }} voos
                                    </small>
                                </div>
                                <span class="badge bg-info rounded-pill fs-6 px-3 py-2">
                                    {{ number_format($aeronave['media_pontualidade'], 1) }}
                                </span>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-muted">
                                Nenhuma aeronave com dados suficientes
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Ranking Serviços --}}
        <div class="col-md-6 mt-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-center mb-3">
                        <i class="bi bi-gem text-warning"></i> Ranking - Nota de Serviços
                    </h5>
                    <div class="list-group list-group-flush">
                        @forelse($rankingsServicos->take(10) as $index => $aeronave)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    @if($index == 0) 🥇
                                    @elseif($index == 1) 🥈
                                    @elseif($index == 2) 🥉
                                    @else {{ $index + 1 }}º
                                    @endif
                                    <a href="{{ route('aeronaves.dashboard', $aeronave['id']) }}" 
                                       class="text-decoration-none ms-2 fw-semibold">
                                        {{ $aeronave['modelo'] }}
                                    </a>
                                    <small class="text-muted ms-2">({{ $aeronave['fabricante'] }})</small>
                                    <br>
                                    <small class="text-muted ms-4">
                                        <i class="bi bi-calendar-check"></i> {{ number_format($aeronave['total_voos']) }} voos
                                    </small>
                                </div>
                                <span class="badge bg-warning rounded-pill fs-6 px-3 py-2 text-dark">
                                    {{ number_format($aeronave['media_servicos'], 1) }}
                                </span>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-muted">
                                Nenhuma aeronave com dados suficientes
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Ranking Pátio --}}
        <div class="col-md-6 mt-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-center mb-3">
                        <i class="bi bi-building text-danger"></i> Ranking - Nota de Pátio
                    </h5>
                    <div class="list-group list-group-flush">
                        @forelse($rankingsPatio->take(10) as $index => $aeronave)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    @if($index == 0) 🥇
                                    @elseif($index == 1) 🥈
                                    @elseif($index == 2) 🥉
                                    @else {{ $index + 1 }}º
                                    @endif
                                    <a href="{{ route('aeronaves.dashboard', $aeronave['id']) }}" 
                                       class="text-decoration-none ms-2 fw-semibold">
                                        {{ $aeronave['modelo'] }}
                                    </a>
                                    <small class="text-muted ms-2">({{ $aeronave['fabricante'] }})</small>
                                    <br>
                                    <small class="text-muted ms-4">
                                        <i class="bi bi-calendar-check"></i> {{ number_format($aeronave['total_voos']) }} voos
                                    </small>
                                </div>
                                <span class="badge bg-danger rounded-pill fs-6 px-3 py-2">
                                    {{ number_format($aeronave['media_patio'], 1) }}
                                </span>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-muted">
                                Nenhuma aeronave com dados suficientes
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Rankings Gerais (Nota Geral, Voos, Passageiros, Capacidade) --}}
    <div class="row g-4 mt-4">
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-center mb-3">
                        <i class="bi bi-trophy-fill text-warning"></i> Ranking - Nota Geral
                        <small class="text-muted d-block fs-6">(mínimo 3 registros de voo)</small>
                    </h5>
                    <div class="list-group list-group-flush">
                        @forelse($rankingsPorNota->take(10) as $index => $aeronave)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    @if($index == 0) 🥇
                                    @elseif($index == 1) 🥈
                                    @elseif($index == 2) 🥉
                                    @else {{ $index + 1 }}º
                                    @endif
                                    <a href="{{ route('aeronaves.dashboard', $aeronave['id']) }}" 
                                       class="text-decoration-none ms-2">
                                        {{ $aeronave['modelo'] }}
                                    </a>
                                    <small class="text-muted ms-2">({{ $aeronave['fabricante'] }})</small>
                                    <br>
                                    <small class="text-muted ms-4">
                                        <i class="bi bi-calendar-check"></i> {{ number_format($aeronave['total_voos']) }} voos
                                    </small>
                                </div>
                                <span class="badge bg-{{ $aeronave['nota_geral'] >= 8 ? 'success' : ($aeronave['nota_geral'] >= 6 ? 'warning' : 'danger') }} rounded-pill fs-6 px-3 py-2">
                                    ⭐ {{ number_format($aeronave['nota_geral'], 1) }}
                                </span>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-muted">
                                Nenhuma aeronave com dados suficientes
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-center mb-3">
                        <i class="bi bi-graph-up"></i> Ranking - Total de Voos
                    </h5>
                    <div class="list-group list-group-flush">
                        @forelse($rankingsPorVoos->take(10) as $index => $aeronave)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    {{ $index + 1 }}º
                                    <a href="{{ route('aeronaves.dashboard', $aeronave['id']) }}" 
                                       class="text-decoration-none ms-2">
                                        {{ $aeronave['modelo'] }}
                                    </a>
                                    <small class="text-muted ms-2">({{ $aeronave['fabricante'] }})</small>
                                </div>
                                <span class="badge bg-primary rounded-pill fs-6 px-3 py-2">
                                    {{ number_format($aeronave['total_voos']) }} voos
                                </span>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-muted">
                                Nenhum dado disponível
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-center mb-3">
                        <i class="bi bi-people-fill"></i> Ranking - Passageiros Transportados
                    </h5>
                    <div class="list-group list-group-flush">
                        @forelse($rankingsPorPassageiros->take(10) as $index => $aeronave)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    {{ $index + 1 }}º
                                    <a href="{{ route('aeronaves.dashboard', $aeronave['id']) }}" 
                                       class="text-decoration-none ms-2">
                                        {{ $aeronave['modelo'] }}
                                    </a>
                                    <small class="text-muted ms-2">({{ $aeronave['fabricante'] }})</small>
                                </div>
                                <span class="badge bg-danger rounded-pill fs-6 px-3 py-2">
                                    {{ number_format($aeronave['total_passageiros']) }} passageiros
                                </span>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-muted">
                                Nenhum dado disponível
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-center mb-3">
                        <i class="bi bi-airplane"></i> Ranking - Capacidade
                    </h5>
                    <div class="list-group list-group-flush">
                        @forelse($rankingsPorCapacidade->take(10) as $index => $aeronave)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    {{ $index + 1 }}º
                                    <a href="{{ route('aeronaves.dashboard', $aeronave['id']) }}" 
                                       class="text-decoration-none ms-2">
                                        {{ $aeronave['modelo'] }}
                                    </a>
                                    <small class="text-muted ms-2">({{ $aeronave['fabricante'] }})</small>
                                </div>
                                <span class="badge bg-secondary rounded-pill fs-6 px-3 py-2">
                                    {{ number_format($aeronave['capacidade']) }} passageiros
                                </span>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-muted">
                                Nenhum dado disponível
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detalhamento completo das notas --}}
    @if(!($estatisticas['aviso_sem_dados'] ?? false) && isset($rankingsPorNota) && $rankingsPorNota->isNotEmpty())
    <div class="row g-4 mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-table"></i> Detalhamento Completo das Notas por Aeronave
                        <small class="text-muted fs-6">(apenas aeronaves com 3+ registros de voo)</small>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Modelo</th>
                                    <th>Fabricante</th>
                                    <th class="text-center">🎯 Objetivo</th>
                                    <th class="text-center">⏰ Pontualidade</th>
                                    <th class="text-center">🛎️ Serviços</th>
                                    <th class="text-center">🅿️ Pátio</th>
                                    <th class="text-center">⭐ Geral</th>
                                    <th class="text-center">📊 Total Voos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rankingsPorNota->take(15) as $index => $aeronave)
                                <tr>
                                    <td class="text-center">
                                        @if($index == 0) 🥇
                                        @elseif($index == 1) 🥈
                                        @elseif($index == 2) 🥉
                                        @else {{ $index + 1 }}º
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $aeronave['modelo'] }}</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $aeronave['fabricante'] }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success rounded-pill">
                                            {{ number_format($aeronave['media_objetivo'], 1) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info rounded-pill">
                                            {{ number_format($aeronave['media_pontualidade'], 1) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning rounded-pill text-dark">
                                            {{ number_format($aeronave['media_servicos'], 1) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger rounded-pill">
                                            {{ number_format($aeronave['media_patio'], 1) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $aeronave['nota_geral'] >= 8 ? 'success' : ($aeronave['nota_geral'] >= 6 ? 'warning' : 'danger') }} rounded-pill fs-6">
                                            ⭐ {{ number_format($aeronave['nota_geral'], 1) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill">
                                            {{ number_format($aeronave['total_voos']) }}
                                        </span>
                                    </td>
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

<hr class="mt-5">
<p class="text-center text-muted mb-0">
    Desenvolvido por <strong>Airport Manager</strong>
</p>
@endsection

@push('styles')
<style>
.card {
    border-radius: 10px;
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.badge {
    font-size: 0.85em;
    padding: 0.5em 0.8em;
}

.list-group-item {
    border-left: none;
    border-right: none;
    border-radius: 0;
    transition: background-color 0.2s;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:last-child {
    border-bottom: none;
}

.bg-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.table td, .table th {
    vertical-align: middle;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

@media (max-width: 768px) {
    .badge {
        font-size: 0.75em;
        padding: 0.3em 0.6em;
    }
    
    .table {
        font-size: 0.85rem;
    }
}
</style>
@endpush