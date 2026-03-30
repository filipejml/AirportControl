@extends('layouts.app')

@section('title', 'Painel - Dashboard')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2 text-dark mb-0">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard - Painel
            </h1>
            <hr class="mt-3">
        </div>
    </div>

    {{-- Primeira linha: totais e médias --}}
    <div class="row g-3 mb-4">

        {{-- Total de Voos --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0d6efd !important; border-radius: 8px;">
                <div class="card-body py-3 text-center">
                    <h6 class="text-muted mb-2">Total de Voos</h6>
                    <i class="bi bi-airplane-engines text-primary fs-1 mb-2 d-block"></i>
                    <p class="display-4 fw-bold text-dark mb-0">{{ number_format($stats['voos'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Total de Passageiros --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #198754 !important; border-radius: 8px;">
                <div class="card-body py-3 text-center">
                    <h6 class="text-muted mb-2">Total de Passageiros</h6>
                    <i class="bi bi-people-fill text-success fs-1 mb-2 d-block"></i>
                    <p class="display-4 fw-bold text-dark mb-0">{{ number_format($stats['passageiros_total'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Médias das Notas --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important; border-radius: 8px;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="text-muted mb-1">Desempenho por Categoria</h6>
                            <p class="small text-muted mb-2">Médias das avaliações (escala 0-10)</p>
                        </div>
                        <i class="bi bi-star-fill text-warning fs-3"></i>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <span class="text-muted small">Objetivo</span>
                            <p class="h5 fw-bold text-dark mb-0">{{ number_format($mediasNotas['objetivo'], 1) }}</p>
                        </div>
                        <div class="col-6">
                            <span class="text-muted small">Pontualidade</span>
                            <p class="h5 fw-bold text-dark mb-0">{{ number_format($mediasNotas['pontualidade'], 1) }}</p>
                        </div>
                        <div class="col-6">
                            <span class="text-muted small">Serviços</span>
                            <p class="h5 fw-bold text-dark mb-0">{{ number_format($mediasNotas['servicos'], 1) }}</p>
                        </div>
                        <div class="col-6">
                            <span class="text-muted small">Pátio</span>
                            <p class="h5 fw-bold text-dark mb-0">{{ number_format($mediasNotas['patio'], 1) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Segunda linha: voos e passageiros por horário --}}
    <div class="row g-3 mb-4">

        {{-- Card: Voos por Horário --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0d6efd !important; border-radius: 8px;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h6 class="text-muted mb-0">Voos por Horário</h6>
                        <i class="bi bi-clock-history text-primary fs-4"></i>
                    </div>
                    <div>
                        @php
                            $ordemHorarios = ['EAM', 'AM', 'AN', 'PM', 'ALL'];
                            $totalVoosHorario = array_sum($voosPorHorario);
                            $legendas = [
                                'EAM' => '05h-08h',
                                'AM' => '08h-12h',
                                'AN' => '12h-16h',
                                'PM' => '16h-20h',
                                'ALL' => '20h-05h'
                            ];
                            $cores = [
                                'EAM' => ['bg' => '#0a58ca', 'badge' => 'bg-dark', 'bar' => 'bg-primary'],
                                'AM' => ['bg' => '#0d6efd', 'badge' => 'bg-primary', 'bar' => 'bg-info'],
                                'AN' => ['bg' => '#ffc107', 'badge' => 'bg-warning', 'bar' => 'bg-warning'],
                                'PM' => ['bg' => '#dc3545', 'badge' => 'bg-danger', 'bar' => 'bg-danger'],
                                'ALL' => ['bg' => '#6f42c1', 'badge' => 'bg-purple', 'bar' => 'bg-purple']
                            ];
                        @endphp
                        @foreach($ordemHorarios as $horario)
                            @php
                                $qtdVoos = $voosPorHorario[$horario] ?? 0;
                                $percentual = $totalVoosHorario > 0 ? ($qtdVoos / $totalVoosHorario) * 100 : 0;
                                $cor = $cores[$horario];
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="small">
                                    <span class="fw-semibold" style="color: {{ $cor['bg'] }};">{{ $horario }}</span>
                                    <small class="text-muted">({{ $legendas[$horario] }})</small>
                                    <small class="text-muted ms-2">{{ number_format($percentual, 1) }}%</small>
                                </div>
                                <span class="badge rounded-pill" style="background-color: {{ $cor['bg'] }} !important;">
                                    {{ number_format($qtdVoos, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="progress mb-2" style="height: 3px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $percentual }}%; background-color: {{ $cor['bg'] }} !important;"></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Card: Passageiros por Horário --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #198754 !important; border-radius: 8px;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h6 class="text-muted mb-0">Passageiros por Horário</h6>
                        <i class="bi bi-people-fill text-success fs-4"></i>
                    </div>
                    <div>
                        @php
                            $ordemHorarios = ['EAM', 'AM', 'AN', 'PM', 'ALL'];
                            $totalPassageirosHorario = array_sum($passageirosPorHorario);
                            $legendas = [
                                'EAM' => '05h-08h',
                                'AM' => '08h-12h',
                                'AN' => '12h-16h',
                                'PM' => '16h-20h',
                                'ALL' => '20h-05h'
                            ];
                            $cores = [
                                'EAM' => ['bg' => '#0a58ca', 'badge' => 'bg-dark', 'bar' => 'bg-primary'],
                                'AM' => ['bg' => '#0d6efd', 'badge' => 'bg-primary', 'bar' => 'bg-info'],
                                'AN' => ['bg' => '#ffc107', 'badge' => 'bg-warning', 'bar' => 'bg-warning'],
                                'PM' => ['bg' => '#dc3545', 'badge' => 'bg-danger', 'bar' => 'bg-danger'],
                                'ALL' => ['bg' => '#6f42c1', 'badge' => 'bg-purple', 'bar' => 'bg-purple']
                            ];
                        @endphp
                        @foreach($ordemHorarios as $horario)
                            @php
                                $qtdPassageiros = $passageirosPorHorario[$horario] ?? 0;
                                $percentual = $totalPassageirosHorario > 0 ? ($qtdPassageiros / $totalPassageirosHorario) * 100 : 0;
                                $cor = $cores[$horario];
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="small">
                                    <span class="fw-semibold" style="color: {{ $cor['bg'] }};">{{ $horario }}</span>
                                    <small class="text-muted">({{ $legendas[$horario] }})</small>
                                    <small class="text-muted ms-2">{{ number_format($percentual, 1) }}%</small>
                                </div>
                                <span class="badge rounded-pill" style="background-color: {{ $cor['bg'] }} !important;">
                                    {{ number_format($qtdPassageiros, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="progress mb-2" style="height: 3px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $percentual }}%; background-color: {{ $cor['bg'] }} !important;"></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Terceira linha: voos e passageiros por tipo --}}
    <div class="row g-3 mb-4">

        {{-- Card: Voos por Tipo --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0d6efd !important; border-radius: 8px;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h6 class="text-muted mb-0">Voos por Tipo</h6>
                        <i class="bi bi-airplane-engines text-primary fs-4"></i>
                    </div>
                    <div class="d-flex justify-content-around mt-3">
                        @php
                            $totalVoosTipo = ($voosPorTipo['Regular'] ?? 0) + ($voosPorTipo['Charter'] ?? 0);
                            $percentualRegular = $totalVoosTipo > 0 ? (($voosPorTipo['Regular'] ?? 0) / $totalVoosTipo) * 100 : 0;
                            $percentualCharter = $totalVoosTipo > 0 ? (($voosPorTipo['Charter'] ?? 0) / $totalVoosTipo) * 100 : 0;
                        @endphp
                        <div class="text-center">
                            <span class="fw-semibold d-block mb-2">Regular</span>
                            <span class="badge rounded-pill px-3 py-2" style="background-color: #0d6efd !important; font-size: 1rem;">
                                {{ number_format($voosPorTipo['Regular'] ?? 0, 0, ',', '.') }}
                            </span>
                            <br>
                            <small class="text-muted d-block mt-2">{{ number_format($percentualRegular, 1) }}%</small>
                        </div>
                        <div class="text-center">
                            <span class="fw-semibold d-block mb-2">Charter</span>
                            <span class="badge rounded-pill px-3 py-2" style="background-color: #198754 !important; font-size: 1rem;">
                                {{ number_format($voosPorTipo['Charter'] ?? 0, 0, ',', '.') }}
                            </span>
                            <br>
                            <small class="text-muted d-block mt-2">{{ number_format($percentualCharter, 1) }}%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card: Passageiros por Tipo --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #198754 !important; border-radius: 8px;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h6 class="text-muted mb-0">Passageiros por Tipo</h6>
                        <i class="bi bi-people-fill text-success fs-4"></i>
                    </div>
                    <div class="d-flex justify-content-around mt-3">
                        @php
                            $totalPassageirosTipo = ($passageirosPorTipo['Regular'] ?? 0) + ($passageirosPorTipo['Charter'] ?? 0);
                            $percentualRegular = $totalPassageirosTipo > 0 ? (($passageirosPorTipo['Regular'] ?? 0) / $totalPassageirosTipo) * 100 : 0;
                            $percentualCharter = $totalPassageirosTipo > 0 ? (($passageirosPorTipo['Charter'] ?? 0) / $totalPassageirosTipo) * 100 : 0;
                        @endphp
                        <div class="text-center">
                            <span class="fw-semibold d-block mb-2">Regular</span>
                            <span class="badge rounded-pill px-3 py-2" style="background-color: #0d6efd !important; font-size: 1rem;">
                                {{ number_format($passageirosPorTipo['Regular'] ?? 0, 0, ',', '.') }}
                            </span>
                            <br>
                            <small class="text-muted d-block mt-2">{{ number_format($percentualRegular, 1) }}%</small>
                        </div>
                        <div class="text-center">
                            <span class="fw-semibold d-block mb-2">Charter</span>
                            <span class="badge rounded-pill px-3 py-2" style="background-color: #198754 !important; font-size: 1rem;">
                                {{ number_format($passageirosPorTipo['Charter'] ?? 0, 0, ',', '.') }}
                            </span>
                            <br>
                            <small class="text-muted d-block mt-2">{{ number_format($percentualCharter, 1) }}%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Quarta linha: voos e passageiros por tipo de aeronave --}}
    <div class="row g-3">

        {{-- Card: Voos por Tipo de Aeronave --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0d6efd !important; border-radius: 8px;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h6 class="text-muted mb-0">Total de Voos por Tipo de Aeronave</h6>
                        <i class="bi bi-airplane-engines text-primary fs-4"></i>
                    </div>
                    <div class="d-flex justify-content-around mt-3">
                        @php
                            $totalVoosAeronave = ($voosPorTipoAeronave['PC'] ?? 0) + ($voosPorTipoAeronave['MC'] ?? 0) + ($voosPorTipoAeronave['LC'] ?? 0);
                            $percentualPC = $totalVoosAeronave > 0 ? (($voosPorTipoAeronave['PC'] ?? 0) / $totalVoosAeronave) * 100 : 0;
                            $percentualMC = $totalVoosAeronave > 0 ? (($voosPorTipoAeronave['MC'] ?? 0) / $totalVoosAeronave) * 100 : 0;
                            $percentualLC = $totalVoosAeronave > 0 ? (($voosPorTipoAeronave['LC'] ?? 0) / $totalVoosAeronave) * 100 : 0;
                        @endphp
                        <div class="text-center">
                            <span class="fw-semibold d-block mb-2">PC</span>
                            <span class="badge rounded-pill px-3 py-2" style="background-color: #0d6efd !important; font-size: 1rem;">
                                {{ number_format($voosPorTipoAeronave['PC'] ?? 0, 0, ',', '.') }}
                            </span>
                            <br>
                            <small class="text-muted d-block mt-2">{{ number_format($percentualPC, 1) }}%</small>
                        </div>
                        <div class="text-center">
                            <span class="fw-semibold d-block mb-2">MC</span>
                            <span class="badge rounded-pill px-3 py-2" style="background-color: #198754 !important; font-size: 1rem;">
                                {{ number_format($voosPorTipoAeronave['MC'] ?? 0, 0, ',', '.') }}
                            </span>
                            <br>
                            <small class="text-muted d-block mt-2">{{ number_format($percentualMC, 1) }}%</small>
                        </div>
                        <div class="text-center">
                            <span class="fw-semibold d-block mb-2">LC</span>
                            <span class="badge rounded-pill px-3 py-2" style="background-color: #ffc107 !important; font-size: 1rem;">
                                {{ number_format($voosPorTipoAeronave['LC'] ?? 0, 0, ',', '.') }}
                            </span>
                            <br>
                            <small class="text-muted d-block mt-2">{{ number_format($percentualLC, 1) }}%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card: Passageiros por Tipo de Aeronave --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #198754 !important; border-radius: 8px;">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h6 class="text-muted mb-0">Total de Passageiros por Tipo de Aeronave</h6>
                        <i class="bi bi-people-fill text-success fs-4"></i>
                    </div>
                    <div class="d-flex justify-content-around mt-3">
                        @php
                            $totalPassageirosAeronave = ($passageirosPorTipoAeronave['PC'] ?? 0) + ($passageirosPorTipoAeronave['MC'] ?? 0) + ($passageirosPorTipoAeronave['LC'] ?? 0);
                            $percentualPC = $totalPassageirosAeronave > 0 ? (($passageirosPorTipoAeronave['PC'] ?? 0) / $totalPassageirosAeronave) * 100 : 0;
                            $percentualMC = $totalPassageirosAeronave > 0 ? (($passageirosPorTipoAeronave['MC'] ?? 0) / $totalPassageirosAeronave) * 100 : 0;
                            $percentualLC = $totalPassageirosAeronave > 0 ? (($passageirosPorTipoAeronave['LC'] ?? 0) / $totalPassageirosAeronave) * 100 : 0;
                        @endphp
                        <div class="text-center">
                            <span class="fw-semibold d-block mb-2">PC</span>
                            <span class="badge rounded-pill px-3 py-2" style="background-color: #0d6efd !important; font-size: 1rem;">
                                {{ number_format($passageirosPorTipoAeronave['PC'] ?? 0, 0, ',', '.') }}
                            </span>
                            <br>
                            <small class="text-muted d-block mt-2">{{ number_format($percentualPC, 1) }}%</small>
                        </div>
                        <div class="text-center">
                            <span class="fw-semibold d-block mb-2">MC</span>
                            <span class="badge rounded-pill px-3 py-2" style="background-color: #198754 !important; font-size: 1rem;">
                                {{ number_format($passageirosPorTipoAeronave['MC'] ?? 0, 0, ',', '.') }}
                            </span>
                            <br>
                            <small class="text-muted d-block mt-2">{{ number_format($percentualMC, 1) }}%</small>
                        </div>
                        <div class="text-center">
                            <span class="fw-semibold d-block mb-2">LC</span>
                            <span class="badge rounded-pill px-3 py-2" style="background-color: #ffc107 !important; font-size: 1rem;">
                                {{ number_format($passageirosPorTipoAeronave['LC'] ?? 0, 0, ',', '.') }}
                            </span>
                            <br>
                            <small class="text-muted d-block mt-2">{{ number_format($percentualLC, 1) }}%</small>
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
        Dashboard atualizado em tempo real
    </p>
</div>
@endsection