@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4 text-center h2">Bem-vindo ao Airport Manager!</h1>

    {{-- Primeira linha - Cards de Estatísticas --}}
    <div class="row g-3 mb-4">

        {{-- Total de Companhias --}}
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0d6efd !important; border-radius: 8px;">
                <div class="card-body py-3 text-center">
                    <h6 class="text-muted mb-2">Companhias</h6>
                    <i class="bi bi-building text-primary fs-1 mb-2 d-block"></i>
                    <p class="display-4 fw-bold text-dark mb-0">{{ number_format($stats['companhias'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-muted small mb-0">Companhias registradas</p>
                </div>
            </div>
        </div>

        {{-- Total de Modelos --}}
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #198754 !important; border-radius: 8px;">
                <div class="card-body py-3 text-center">
                    <h6 class="text-muted mb-2">Modelos</h6>
                    <i class="bi bi-airplane-fill text-success fs-1 mb-2 d-block"></i>
                    <p class="display-4 fw-bold text-dark mb-0">{{ number_format($stats['modelos'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-muted small mb-0">Modelos distintos</p>
                </div>
            </div>
        </div>

        {{-- Total de Aeroportos --}}
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0dcaf0 !important; border-radius: 8px;">
                <div class="card-body py-3 text-center">
                    <h6 class="text-muted mb-2">Aeroportos</h6>
                    <i class="bi bi-geo-alt-fill text-info fs-1 mb-2 d-block"></i>
                    <p class="display-4 fw-bold text-dark mb-0">{{ number_format($stats['aeroportos'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-muted small mb-0">Aeroportos registrados</p>
                </div>
            </div>
        </div>

        {{-- Total de Voos --}}
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important; border-radius: 8px;">
                <div class="card-body py-3 text-center">
                    <h6 class="text-muted mb-2">Voos</h6>
                    <i class="bi bi-calendar-check-fill text-warning fs-1 mb-2 d-block"></i>
                    <p class="display-4 fw-bold text-dark mb-0">{{ number_format($stats['voos'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-muted small mb-0">Voos realizados</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Segunda linha - Passageiros e Médias --}}
    <div class="row g-3 mb-4">

        {{-- Total de Passageiros --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545 !important; border-radius: 8px;">
                <div class="card-body py-3 text-center">
                    <h6 class="text-muted mb-2">Total de Passageiros</h6>
                    <i class="bi bi-people-fill text-danger fs-1 mb-2 d-block"></i>
                    <p class="display-4 fw-bold text-dark mb-0">{{ number_format($stats['passageiros_total'] ?? 0, 0, ',', '.') }}</p>
                    <p class="text-muted small mb-0">Passageiros transportados</p>
                </div>
            </div>
        </div>

        {{-- Médias das Notas --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #6c757d !important; border-radius: 8px;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="text-muted mb-1">Desempenho por Categoria</h6>
                            <p class="small text-muted mb-2">Médias das avaliações (escala 0-10)</p>
                        </div>
                        <i class="bi bi-star-fill text-secondary fs-3"></i>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <span class="text-muted small">Objetivo</span>
                            <p class="h5 fw-bold text-dark mb-0">{{ number_format($mediasNotas['objetivo'] ?? 0, 1) }}</p>
                        </div>
                        <div class="col-3">
                            <span class="text-muted small">Pontualidade</span>
                            <p class="h5 fw-bold text-dark mb-0">{{ number_format($mediasNotas['pontualidade'] ?? 0, 1) }}</p>
                        </div>
                        <div class="col-3">
                            <span class="text-muted small">Serviços</span>
                            <p class="h5 fw-bold text-dark mb-0">{{ number_format($mediasNotas['servicos'] ?? 0, 1) }}</p>
                        </div>
                        <div class="col-3">
                            <span class="text-muted small">Pátio</span>
                            <p class="h5 fw-bold text-dark mb-0">{{ number_format($mediasNotas['patio'] ?? 0, 1) }}</p>
                        </div>
                    </div>
                    @php
                        $mediaGeral = (($mediasNotas['objetivo'] ?? 0) + ($mediasNotas['pontualidade'] ?? 0) + ($mediasNotas['servicos'] ?? 0) + ($mediasNotas['patio'] ?? 0)) / 4;
                    @endphp
                    <div class="mt-2 pt-2 border-top">
                        <div class="d-flex justify-content-between align-items-center small">
                            <span class="text-muted">Média Geral</span>
                            <span class="h5 fw-bold text-dark">{{ number_format($mediaGeral, 1) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Terceira linha - Passageiros por Aeroporto e Horário --}}
    <div class="row g-3 mb-4">

        {{-- Card: Passageiros por Aeroporto --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545 !important; border-radius: 8px;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h6 class="text-muted mb-0">Passageiros por Aeroporto</h6>
                        <i class="bi bi-people-fill text-danger fs-4"></i>
                    </div>
                    <div>
                        @php
                            $totalPassageirosAeroporto = array_sum($passageirosPorAeroporto ?? []);
                            if (!empty($passageirosPorAeroporto)) {
                                arsort($passageirosPorAeroporto);
                            }
                            $contador = 0;
                            $medalhas = ['🥇', '🥈', '🥉'];
                            $coresMedalha = ['#FFD700', '#C0C0C0', '#CD7F32'];
                        @endphp
                        @forelse(($passageirosPorAeroporto ?? []) as $aeroporto => $total)
                            @php
                                $contador++;
                                $percentual = $totalPassageirosAeroporto > 0 ? ($total / $totalPassageirosAeroporto) * 100 : 0;
                                $medalha = $contador <= 3 ? $medalhas[$contador - 1] : null;
                                $corMedalha = $contador <= 3 ? $coresMedalha[$contador - 1] : null;
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="small">
                                    @if($medalha)
                                        <span style="font-size: 1.1rem; margin-right: 5px;">{{ $medalha }}</span>
                                    @endif
                                    <span class="fw-semibold">{{ $aeroporto }}</span>
                                    <small class="text-muted ms-2">{{ number_format($percentual, 1) }}%</small>
                                </div>
                                <span class="badge rounded-pill" style="background-color: #dc3545 !important;">
                                    {{ number_format($total, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="progress mb-2" style="height: 3px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $percentual }}%; background-color: #dc3545 !important;"></div>
                            </div>
                            @if($contador >= 5 && count($passageirosPorAeroporto) > 5)
                                @php
                                    $outros = array_slice($passageirosPorAeroporto, 5);
                                    $totalOutros = array_sum($outros);
                                    $percentualOutros = $totalPassageirosAeroporto > 0 ? ($totalOutros / $totalPassageirosAeroporto) * 100 : 0;
                                    $totalAeroportosRestantes = count($outros);
                                @endphp
                                <div class="d-flex justify-content-between align-items-center mb-2 mt-2 pt-1 border-top">
                                    <div class="small">
                                        <span class="fw-semibold text-muted">+{{ $totalAeroportosRestantes }} outros</span>
                                        <small class="text-muted ms-2">{{ number_format($percentualOutros, 1) }}%</small>
                                    </div>
                                    <span class="badge rounded-pill bg-secondary">
                                        {{ number_format($totalOutros, 0, ',', '.') }}
                                    </span>
                                </div>
                                @break
                            @endif
                        @empty
                            <p class="text-muted text-center py-3">Nenhum dado de passageiro disponível</p>
                        @endforelse
                    </div>
                    <div class="mt-2 pt-2 border-top">
                        <div class="d-flex justify-content-between align-items-center small">
                            <span class="text-muted">Total Geral</span>
                            <span class="fw-bold text-dark">{{ number_format($totalPassageirosAeroporto, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card: Passageiros por Horário (sem medalhas) --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0dcaf0 !important; border-radius: 8px;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h6 class="text-muted mb-0">Passageiros por Horário</h6>
                        <i class="bi bi-clock-history text-info fs-4"></i>
                    </div>
                    <div>
                        @php
                            $ordemHorarios = ['EAM', 'AM', 'AN', 'PM', 'ALL'];
                            $totalPassageirosHorario = array_sum($passageirosPorHorario ?? []);
                            $legendas = [
                                'EAM' => '05h-08h',
                                'AM' => '08h-12h',
                                'AN' => '12h-16h',
                                'PM' => '16h-20h',
                                'ALL' => '20h-05h'
                            ];
                            $cores = [
                                'EAM' => '#0a58ca',
                                'AM' => '#0d6efd',
                                'AN' => '#ffc107',
                                'PM' => '#dc3545',
                                'ALL' => '#6f42c1'
                            ];
                        @endphp
                        @foreach($ordemHorarios as $horario)
                            @php
                                $qtdPassageiros = $passageirosPorHorario[$horario] ?? 0;
                                $percentual = $totalPassageirosHorario > 0 ? ($qtdPassageiros / $totalPassageirosHorario) * 100 : 0;
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="small">
                                    <span class="fw-semibold" style="color: {{ $cores[$horario] }};">{{ $horario }}</span>
                                    <small class="text-muted">({{ $legendas[$horario] }})</small>
                                    <small class="text-muted ms-2">{{ number_format($percentual, 1) }}%</small>
                                </div>
                                <span class="badge rounded-pill" style="background-color: {{ $cores[$horario] }} !important;">
                                    {{ number_format($qtdPassageiros, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="progress mb-2" style="height: 3px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $percentual }}%; background-color: {{ $cores[$horario] }} !important;"></div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-2 pt-2 border-top">
                        <div class="d-flex justify-content-between align-items-center small">
                            <span class="text-muted">Total Geral</span>
                            <span class="fw-bold text-dark">{{ number_format($totalPassageirosHorario, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quarta linha - Voos por Aeroporto e Melhores Companhias/Modelos --}}
    <div class="row g-3 mb-4">

        {{-- Card: Voos por Aeroporto (com medalhas) --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #198754 !important; border-radius: 8px;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h6 class="text-muted mb-0">Voos por Aeroporto</h6>
                        <i class="bi bi-airplane-fill text-success fs-4"></i>
                    </div>
                    <div>
                        @php
                            $totalVoosAeroporto = array_sum($voosPorAeroporto ?? []);
                            if (!empty($voosPorAeroporto)) {
                                arsort($voosPorAeroporto);
                            }
                            $contador = 0;
                            $medalhas = ['🥇', '🥈', '🥉'];
                            $coresMedalha = ['#FFD700', '#C0C0C0', '#CD7F32'];
                        @endphp
                        @forelse(($voosPorAeroporto ?? []) as $aeroporto => $total)
                            @php
                                $contador++;
                                $percentual = $totalVoosAeroporto > 0 ? ($total / $totalVoosAeroporto) * 100 : 0;
                                $medalha = $contador <= 3 ? $medalhas[$contador - 1] : null;
                                $corMedalha = $contador <= 3 ? $coresMedalha[$contador - 1] : null;
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="small">
                                    @if($medalha)
                                        <span style="font-size: 1.1rem; margin-right: 5px;">{{ $medalha }}</span>
                                    @endif
                                    <span class="fw-semibold">{{ $aeroporto }}</span>
                                    <small class="text-muted ms-2">{{ number_format($percentual, 1) }}%</small>
                                </div>
                                <span class="badge rounded-pill" style="background-color: #198754 !important;">
                                    {{ number_format($total, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="progress mb-2" style="height: 3px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $percentual }}%; background-color: #198754 !important;"></div>
                            </div>
                            @if($contador >= 5 && count($voosPorAeroporto) > 5)
                                @php
                                    $outros = array_slice($voosPorAeroporto, 5);
                                    $totalOutros = array_sum($outros);
                                    $percentualOutros = $totalVoosAeroporto > 0 ? ($totalOutros / $totalVoosAeroporto) * 100 : 0;
                                    $totalAeroportosRestantes = count($outros);
                                @endphp
                                <div class="d-flex justify-content-between align-items-center mb-2 mt-2 pt-1 border-top">
                                    <div class="small">
                                        <span class="fw-semibold text-muted">+{{ $totalAeroportosRestantes }} outros</span>
                                        <small class="text-muted ms-2">{{ number_format($percentualOutros, 1) }}%</small>
                                    </div>
                                    <span class="badge rounded-pill bg-secondary">
                                        {{ number_format($totalOutros, 0, ',', '.') }}
                                    </span>
                                </div>
                                @break
                            @endif
                        @empty
                            <p class="text-muted text-center py-3">Nenhum dado de voo disponível</p>
                        @endforelse
                    </div>
                    <div class="mt-2 pt-2 border-top">
                        <div class="d-flex justify-content-between align-items-center small">
                            <span class="text-muted">Total Geral</span>
                            <span class="fw-bold text-dark">{{ number_format($totalVoosAeroporto, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card: Melhores Companhias e Modelos --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important; border-radius: 8px;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h6 class="text-muted mb-0">Melhores por Categoria</h6>
                        <i class="bi bi-trophy-fill text-warning fs-4"></i>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <p class="small text-muted mb-1">🎯 Objetivo</p>
                            <p class="h6 fw-bold mb-2">Companhia: <span class="text-primary">{{ $melhoresCompanhias['objetivo'] ?? 'N/A' }}</span></p>
                            <p class="h6 fw-bold mb-3">Modelo: <span class="text-primary">{{ $melhoresModelos['objetivo'] ?? 'N/A' }}</span></p>
                            
                            <p class="small text-muted mb-1">⏱️ Pontualidade</p>
                            <p class="h6 fw-bold mb-2">Companhia: <span class="text-success">{{ $melhoresCompanhias['pontualidade'] ?? 'N/A' }}</span></p>
                            <p class="h6 fw-bold mb-3">Modelo: <span class="text-success">{{ $melhoresModelos['pontualidade'] ?? 'N/A' }}</span></p>
                        </div>
                        <div class="col-6">
                            <p class="small text-muted mb-1">🛎️ Serviços</p>
                            <p class="h6 fw-bold mb-2">Companhia: <span class="text-info">{{ $melhoresCompanhias['servicos'] ?? 'N/A' }}</span></p>
                            <p class="h6 fw-bold mb-3">Modelo: <span class="text-info">{{ $melhoresModelos['servicos'] ?? 'N/A' }}</span></p>
                            
                            <p class="small text-muted mb-1">🏢 Pátio</p>
                            <p class="h6 fw-bold mb-2">Companhia: <span class="text-warning">{{ $melhoresCompanhias['patio'] ?? 'N/A' }}</span></p>
                            <p class="h6 fw-bold mb-3">Modelo: <span class="text-warning">{{ $melhoresModelos['patio'] ?? 'N/A' }}</span></p>
                        </div>
                    </div>
                    <div class="mt-2 pt-2 border-top">
                        <div class="d-flex justify-content-between align-items-center small">
                            <span class="text-muted">Premiados</span>
                            <span class="fw-bold text-dark">
                                {{ count(array_filter($melhoresCompanhias ?? [])) }}/4 companhias • 
                                {{ count(array_filter($melhoresModelos ?? [])) }}/4 modelos
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Rodapé --}}
    <hr class="mt-4">
    <p class="text-center text-muted small mb-0">
        <i class="bi bi-bar-chart-line me-1"></i>
        Desenvolvido por <strong>Filipe Lopes</strong>
    </p>
</div>
@endsection