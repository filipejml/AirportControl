@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4 text-center h2">Bem-vindo ao Airport Manager!</h1>

    {{-- Primeira linha - Cards de Estatísticas --}}
    <div class="row g-3 align-items-stretch mb-4">

        {{-- Total de Companhias --}}
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 h-100 shadow-sm" style="cursor: default; border-left: 5px solid #0d6efd; border-radius: 8px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                            style="width: 40px; height: 40px; background-color: rgba(13, 110, 253, 0.1);">
                            <i class="bi bi-building fs-5 text-primary"></i>
                        </div>
                        <h6 class="card-title ms-2 mb-0 text-dark">Companhias</h6>
                    </div>
                    <div>
                        <p class="h3 fw-bold text-primary mb-0">{{ number_format($stats['companhias'], 0, ',', '.') }}</p>
                        <p class="text-muted small mb-0">Companhias registradas</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total de Modelos --}}
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 h-100 shadow-sm" style="cursor: default; border-left: 5px solid #198754; border-radius: 8px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                            style="width: 40px; height: 40px; background-color: rgba(25, 135, 84, 0.1);">
                            <i class="bi bi-airplane-fill fs-5 text-success"></i>
                        </div>
                        <h6 class="card-title ms-2 mb-0 text-dark">Modelos</h6>
                    </div>
                    <div>
                        <p class="h3 fw-bold text-success mb-0">{{ number_format($stats['modelos'], 0, ',', '.') }}</p>
                        <p class="text-muted small mb-0">Modelos distintos</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total de Aeroportos --}}
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 h-100 shadow-sm" style="cursor: default; border-left: 5px solid #0dcaf0; border-radius: 8px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                            style="width: 40px; height: 40px; background-color: rgba(13, 202, 240, 0.1);">
                            <i class="bi bi-geo-alt-fill fs-5 text-info"></i>
                        </div>
                        <h6 class="card-title ms-2 mb-0 text-dark">Aeroportos</h6>
                    </div>
                    <div>
                        <p class="h3 fw-bold text-info mb-0">{{ number_format($stats['aeroportos'], 0, ',', '.') }}</p>
                        <p class="text-muted small mb-0">Aeroportos registrados</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total de Voos --}}
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 h-100 shadow-sm" style="cursor: default; border-left: 5px solid #ffc107; border-radius: 8px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                            style="width: 40px; height: 40px; background-color: rgba(255, 193, 7, 0.1);">
                            <i class="bi bi-calendar-check-fill fs-5 text-warning"></i>
                        </div>
                        <h6 class="card-title ms-2 mb-0 text-dark">Voos</h6>
                    </div>
                    <div>
                        <p class="h3 fw-bold text-warning mb-0">{{ number_format($stats['voos'], 0, ',', '.') }}</p>
                        <p class="text-muted small mb-0">Voos realizados</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Segunda linha - Cards de Passageiros --}}
    <div class="row g-3 mt-3 align-items-stretch">
        
        {{-- Card: Total de Passageiros por Aeroporto --}}
        <div class="col-md-6">
            <div class="card shadow-lg border-0 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                style="width: 40px; height: 40px; background-color: rgba(220, 53, 69, 0.1);">
                                <i class="bi bi-people-fill text-danger"></i>
                            </div>
                            <h5 class="card-title mb-0 ms-2">Total de Passageiros</h5>
                        </div>
                        <span class="h4 fw-bold text-danger mb-0">{{ number_format($stats['passageiros_total'], 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="mt-2">
                        <ul class="list-group list-group-flush">
                            @php
                                $totalPassageirosAeroporto = array_sum($passageirosPorAeroporto);
                                arsort($passageirosPorAeroporto);
                                $contador = 0;
                                $medalhas = ['🥇', '🥈', '🥉'];
                            @endphp
                            
                            @foreach($passageirosPorAeroporto as $aeroporto => $total)
                                @php
                                    $contador++;
                                    $percentual = $totalPassageirosAeroporto > 0 ? ($total / $totalPassageirosAeroporto) * 100 : 0;
                                    $medalha = $contador <= 3 ? $medalhas[$contador - 1] : $contador . 'º';
                                    $corMedalha = match($contador) {
                                        1 => '#FFD700',
                                        2 => '#C0C0C0',
                                        3 => '#CD7F32',
                                        default => 'secondary'
                                    };
                                @endphp
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-2">
                                    <div class="d-flex align-items-center">
                                        <span class="me-2 fw-bold" style="color: {{ $corMedalha }}; font-size: 1rem;">
                                            {{ $medalha }}
                                        </span>
                                        <div>
                                            <span class="fw-medium">{{ $aeroporto }}</span>
                                            <small class="text-muted ms-1">({{ number_format($percentual, 1) }}%)</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-danger rounded-pill px-2 py-1">
                                        {{ number_format($total, 0, ',', '.') }}
                                    </span>
                                </li>
                                
                                @if($contador >= 5)
                                    @php
                                        $outros = array_slice($passageirosPorAeroporto, 5);
                                        $totalOutros = array_sum($outros);
                                        $percentualOutros = $totalPassageirosAeroporto > 0 ? ($totalOutros / $totalPassageirosAeroporto) * 100 : 0;
                                        $totalAeroportosRestantes = count($outros);
                                    @endphp
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-2 bg-light">
                                        <div class="d-flex align-items-center">
                                            <span class="me-2 fw-bold text-muted">+{{ $totalAeroportosRestantes }}</span>
                                            <div>
                                                <span class="fw-medium">Outros aeroportos</span>
                                                <small class="text-muted ms-1">({{ number_format($percentualOutros, 1) }}%)</small>
                                            </div>
                                        </div>
                                        <span class="badge bg-secondary rounded-pill px-2 py-1">
                                            {{ number_format($totalOutros, 0, ',', '.') }}
                                        </span>
                                    </li>
                                    @break
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card: Passageiros por Horário --}}
        <div class="col-md-6">
            <div class="card shadow-lg border-0 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                            style="width: 40px; height: 40px; background-color: rgba(13, 202, 240, 0.1);">
                            <i class="bi bi-clock-history text-info"></i>
                        </div>
                        <h5 class="card-title mb-0 ms-2">Passageiros por Horário</h5>
                    </div>
                    
                    <div class="mt-2">
                        <ul class="list-group list-group-flush">
                            @php
                                $ordemHorarios = [
                                    'EAM' => ['nome' => 'EAM (05h-08h)', 'cor' => 'primary', 'icone' => '🌅'],
                                    'AM'  => ['nome' => 'AM (08h-12h)', 'cor' => 'info', 'icone' => '☀️'],
                                    'AN'  => ['nome' => 'AN (12h-16h)', 'cor' => 'warning', 'icone' => '🌤️'],
                                    'PM'  => ['nome' => 'PM (16h-20h)', 'cor' => 'danger', 'icone' => '🌙'],
                                    'ALL' => ['nome' => 'ALL (20h-05h)', 'cor' => 'secondary', 'icone' => '🌃']
                                ];
                                $totalPassageirosHorario = array_sum($passageirosPorHorario);
                            @endphp

                            @foreach ($ordemHorarios as $sigla => $dados)
                                @php
                                    $qtdPassageiros = $passageirosPorHorario[$sigla] ?? 0;
                                    $percentual = $totalPassageirosHorario > 0 ? ($qtdPassageiros / $totalPassageirosHorario) * 100 : 0;
                                @endphp
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-2">
                                    <div class="d-flex align-items-center">
                                        <span class="me-2">{{ $dados['icone'] }}</span>
                                        <div>
                                            <strong class="small">{{ $dados['nome'] }}</strong>
                                            <small class="text-muted ms-1">({{ number_format($percentual, 1) }}%)</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-{{ $dados['cor'] }} rounded-pill px-2 py-1">
                                        {{ number_format($qtdPassageiros, 0, ',', '.') }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="mt-2 pt-2 border-top">
                        <div class="d-flex justify-content-between align-items-center small">
                            <div>
                                <i class="bi bi-people-fill text-info me-1"></i>
                                <strong>Total:</strong> {{ number_format($totalPassageirosHorario, 0, ',', '.') }}
                            </div>
                            <div class="text-muted">
                                <i class="bi bi-clock me-1"></i>Distribuição por período
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Terceira linha - Cards de Médias e Melhores Companhias --}}
    <div class="row g-3 mt-3 align-items-stretch">
        
        {{-- Card: Médias das Notas --}}
        <div class="col-md-6">
            <div class="card shadow-lg border-0 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                            style="width: 40px; height: 40px; background-color: rgba(108, 117, 125, 0.1);">
                            <i class="bi bi-star-fill text-secondary"></i>
                        </div>
                        <h5 class="card-title mb-0 ms-2">Médias das Notas</h5>
                    </div>
                    
                    <ul class="list-group list-group-flush mt-2">
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-2">
                            <span>🎯 Objetivo</span>
                            <span class="badge bg-primary rounded-pill">{{ number_format($mediasNotas['objetivo'], 1) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-2">
                            <span>⏱️ Pontualidade</span>
                            <span class="badge bg-success rounded-pill">{{ number_format($mediasNotas['pontualidade'], 1) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-2">
                            <span>🛎️ Serviços</span>
                            <span class="badge bg-info rounded-pill">{{ number_format($mediasNotas['servicos'], 1) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-2">
                            <span>🏢 Pátio</span>
                            <span class="badge bg-warning rounded-pill">{{ number_format($mediasNotas['patio'], 1) }}</span>
                        </li>
                    </ul>

                    @php
                        $mediaGeral = ($mediasNotas['objetivo'] + $mediasNotas['pontualidade'] + $mediasNotas['servicos'] + $mediasNotas['patio']) / 4;
                        $corMediaGeral = match(true) {
                            $mediaGeral >= 9.0 => 'success',
                            $mediaGeral >= 8.0 => 'info',
                            $mediaGeral >= 7.0 => 'warning',
                            default => 'secondary'
                        };
                    @endphp
                    
                    <div class="mt-2 pt-2 border-top">
                        <div class="d-flex justify-content-between align-items-center small">
                            <div>
                                <i class="bi bi-star-fill text-warning me-1"></i>
                                <strong>Média Geral:</strong>
                                <span class="badge bg-{{ $corMediaGeral }} ms-1">{{ number_format($mediaGeral, 1) }}</span>
                            </div>
                            <div class="text-muted">0-10</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card: Melhores Companhias --}}
        <div class="col-md-6">
            <div class="card shadow-lg border-0 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                            style="width: 40px; height: 40px; background-color: rgba(13, 110, 253, 0.1);">
                            <i class="bi bi-award-fill text-primary"></i>
                        </div>
                        <h5 class="card-title mb-0 ms-2">Melhores Companhias</h5>
                    </div>
                    
                    <ul class="list-group list-group-flush mt-2">
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-2">
                            <span>🎯 Objetivo</span>
                            <span class="badge bg-primary rounded-pill">{{ $melhoresCompanhias['objetivo'] ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-2">
                            <span>⏱️ Pontualidade</span>
                            <span class="badge bg-success rounded-pill">{{ $melhoresCompanhias['pontualidade'] ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-2">
                            <span>🛎️ Serviços</span>
                            <span class="badge bg-info rounded-pill">{{ $melhoresCompanhias['servicos'] ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-2">
                            <span>🏢 Pátio</span>
                            <span class="badge bg-warning rounded-pill">{{ $melhoresCompanhias['patio'] ?? 'N/A' }}</span>
                        </li>
                    </ul>

                    <div class="mt-2 pt-2 border-top">
                        <div class="d-flex justify-content-between align-items-center small">
                            <div>
                                <i class="bi bi-trophy text-primary me-1"></i>
                                <strong>Premiadas:</strong> {{ count(array_filter($melhoresCompanhias)) }}/4
                            </div>
                            <div class="text-muted">Melhor por categoria</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quarta linha - Cards de Voos por Aeroporto e Melhor Modelo --}}
    <div class="row g-3 mt-3 align-items-stretch">
        
        {{-- Card: Voos por Aeroporto --}}
        <div class="col-md-6">
            <div class="card shadow-lg border-0 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                            style="width: 40px; height: 40px; background-color: rgba(25, 135, 84, 0.1);">
                            <i class="bi bi-airplane-fill text-success"></i>
                        </div>
                        <h5 class="card-title mb-0 ms-2">Voos por Aeroporto</h5>
                    </div>
                    
                    <ul class="list-group list-group-flush mt-2">
                        @php
                            $totalVoosAeroporto = array_sum($voosPorAeroporto);
                            arsort($voosPorAeroporto);
                            $contador = 0;
                            $medalhas = ['🥇', '🥈', '🥉'];
                        @endphp
                        
                        @foreach($voosPorAeroporto as $aeroporto => $total)
                            @php
                                $contador++;
                                $percentual = $totalVoosAeroporto > 0 ? ($total / $totalVoosAeroporto) * 100 : 0;
                                $medalha = $contador <= 3 ? $medalhas[$contador - 1] : '';
                                $corMedalha = match($contador) {
                                    1 => '#FFD700',
                                    2 => '#C0C0C0',
                                    3 => '#CD7F32',
                                    default => 'text-dark'
                                };
                            @endphp
                            <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-2">
                                <div class="d-flex align-items-center">
                                    @if($medalha)
                                        <span class="me-2" style="color: {{ $corMedalha }};">{{ $medalha }}</span>
                                    @endif
                                    <div>
                                        <span class="fw-medium">{{ $aeroporto }}</span>
                                        <small class="text-muted ms-1">({{ number_format($percentual, 1) }}%)</small>
                                    </div>
                                </div>
                                <span class="badge bg-success rounded-pill px-2 py-1">{{ number_format($total, 0, ',', '.') }}</span>
                            </li>
                            
                            @if($contador >= 5)
                                @php
                                    $outros = array_slice($voosPorAeroporto, 5);
                                    $totalOutros = array_sum($outros);
                                    $percentualOutros = $totalVoosAeroporto > 0 ? ($totalOutros / $totalVoosAeroporto) * 100 : 0;
                                    $totalAeroportosRestantes = count($outros);
                                @endphp
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-2 bg-light">
                                    <div class="d-flex align-items-center">
                                        <span class="me-2 text-muted">+{{ $totalAeroportosRestantes }}</span>
                                        <div>
                                            <span class="fw-medium">Outros aeroportos</span>
                                            <small class="text-muted ms-1">({{ number_format($percentualOutros, 1) }}%)</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-secondary rounded-pill px-2 py-1">{{ number_format($totalOutros, 0, ',', '.') }}</span>
                                </li>
                                @break
                            @endif
                        @endforeach
                    </ul>

                    <div class="mt-2 pt-2 border-top">
                        <div class="d-flex justify-content-between align-items-center small">
                            <div>
                                <i class="bi bi-airplane-fill text-success me-1"></i>
                                <strong>Total:</strong> {{ number_format($totalVoosAeroporto, 0, ',', '.') }} voos
                            </div>
                            <div class="text-muted">{{ count($voosPorAeroporto) }} aeroportos</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card: Melhor Modelo por Nota --}}
        <div class="col-md-6">
            <div class="card shadow-lg border-0 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" 
                            style="width: 40px; height: 40px; background-color: rgba(255, 193, 7, 0.1);">
                            <i class="bi bi-airplane text-warning"></i>
                        </div>
                        <h5 class="card-title mb-0 ms-2">Melhor Modelo por Nota</h5>
                    </div>
                    
                    <ul class="list-group list-group-flush mt-2">
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-2">
                            <span>🎯 Objetivo</span>
                            <span class="badge bg-primary rounded-pill">{{ $melhoresModelos['objetivo'] ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-2">
                            <span>⏱️ Pontualidade</span>
                            <span class="badge bg-success rounded-pill">{{ $melhoresModelos['pontualidade'] ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-2">
                            <span>🛎️ Serviços</span>
                            <span class="badge bg-info rounded-pill">{{ $melhoresModelos['servicos'] ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-2">
                            <span>🏢 Pátio</span>
                            <span class="badge bg-warning rounded-pill">{{ $melhoresModelos['patio'] ?? 'N/A' }}</span>
                        </li>
                    </ul>

                    <div class="mt-2 pt-2 border-top">
                        <div class="d-flex justify-content-between align-items-center small">
                            <div>
                                <i class="bi bi-trophy text-warning me-1"></i>
                                <strong>Premiados:</strong> {{ count(array_filter($melhoresModelos)) }}/4
                            </div>
                            <div class="text-muted">Melhor por categoria</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

{{-- Bootstrap Icons --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<hr class="mt-4">
<p class="text-center text-muted small mb-0">
    Desenvolvido por <strong>Filipe Lopes</strong>
</p>
@endsection