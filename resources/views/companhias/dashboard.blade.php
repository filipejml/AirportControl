<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $companhia->nome }} - Dashboard</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Ícones -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            background-color: #ffffff;
        }
        
        .stat-card {
            transition: transform 0.2s;
            cursor: pointer;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid rgba(0,0,0,0.08);
        }
        
        /* Estilo para cards de nota com borda lateral colorida */
        .rating-card {
            border: 1px solid #e9ecef;
            border-left: 4px solid;
            border-radius: 0.5rem;
            transition: all 0.2s;
            background: #ffffff;
        }
        
        .rating-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08);
        }
        
        .rating-value {
            font-size: 2rem;
            font-weight: bold;
            line-height: 1;
        }
        
        .rating-label {
            font-size: 0.875rem;
            color: #6c757d;
            font-weight: 500;
        }
        
        .rating-progress {
            height: 4px;
            border-radius: 2px;
        }
        
        /* Fundo branco para todos os elementos */
        .card, .container, body {
            background-color: #ffffff;
        }
        
        .accordion-button:focus {
            box-shadow: none;
        }
        
        .accordion-button:not(.collapsed) {
            background-color: #f8f9fa;
            color: #000;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    @include('components.navbar')

    <div class="container mt-4">
        <!-- Cabeçalho -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 fw-bold">Dashboard - {{ $companhia->nome }}</h1>
                        @if($companhia->codigo)
                            <p class="text-muted mb-0">Código: {{ $companhia->codigo }}</p>
                        @endif
                    </div>
                    <a href="{{ route('companhias.informacoes') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>

        {{-- FILTROS --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border shadow-sm">
                    <div class="card-body">
                        <form method="GET" action="{{ route('companhias.dashboard', $companhia->id) }}" 
                            id="filtroGlobalForm" class="row g-3 align-items-end">
                            
                            {{-- Filtro de Aeroporto --}}
                            <div class="col-md-4">
                                <label for="aeroporto" class="form-label fw-semibold small text-muted">
                                    <i class="bi bi-geo-alt"></i> Filtrar por Aeroporto:
                                </label>
                                <select name="aeroporto" id="aeroporto" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="geral">Visualização Geral</option>
                                    @foreach($aeroportosDisponiveis as $aeroporto)
                                        <option value="{{ $aeroporto }}" {{ ($aeroportoSelecionado ?? 'geral') == $aeroporto ? 'selected' : '' }}>
                                            {{ $aeroporto }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            {{-- Filtro de Período --}}
                            <div class="col-md-3">
                                <label for="periodo" class="form-label fw-semibold small text-muted">
                                    <i class="bi bi-calendar"></i> Período:
                                </label>
                                <select name="periodo" id="periodo" class="form-select form-select-sm" onchange="atualizarFiltrosGlobais()">
                                    <option value="geral" {{ ($periodoSelecionado ?? 'geral') == 'geral' ? 'selected' : '' }}>Todos os dados</option>
                                    <option value="semanal" {{ ($periodoSelecionado ?? '') == 'semanal' ? 'selected' : '' }}>Semanal</option>
                                    <option value="mensal" {{ ($periodoSelecionado ?? '') == 'mensal' ? 'selected' : '' }}>Mensal</option>
                                    <option value="anual" {{ ($periodoSelecionado ?? '') == 'anual' ? 'selected' : '' }}>Anual</option>
                                </select>
                            </div>
                            
                            {{-- Filtro Semanal --}}
                            <div class="col-md-3" id="filtroSemanalGlobal" style="display: none;">
                                <label for="semana" class="form-label fw-semibold small text-muted">
                                    <i class="bi bi-calendar-week"></i> Semana:
                                </label>
                                <select name="semana" id="semana" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">Selecione a semana</option>
                                    @foreach($semanasDisponiveis as $semana)
                                        <option value="{{ $semana->semana }}" 
                                            {{ ($semanaSelecionada ?? '') == $semana->semana ? 'selected' : '' }}>
                                            Semana {{ $semana->numero_semana }} - {{ $semana->ano }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            {{-- Filtro Mensal --}}
                            <div class="col-md-4" id="filtroMensalGlobal" style="display: none;">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label for="ano" class="form-label fw-semibold small text-muted">Ano:</label>
                                        <select name="ano" id="ano" class="form-select form-select-sm" onchange="atualizarMesesDisponiveis(this.value)">
                                            <option value="">Selecione</option>
                                            @foreach($anosDisponiveis as $anoOption)
                                                <option value="{{ $anoOption }}" {{ ($anoFiltro ?? '') == $anoOption ? 'selected' : '' }}>
                                                    {{ $anoOption }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label for="mes" class="form-label fw-semibold small text-muted">Mês:</label>
                                        <select name="mes" id="mes" class="form-select form-select-sm" onchange="this.form.submit()" {{ !$anoFiltro ? 'disabled' : '' }}>
                                            <option value="">Selecione</option>
                                            @for($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}" {{ ($mesSelecionado ?? '') == $i ? 'selected' : '' }}>
                                                    {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Filtro Anual --}}
                            <div class="col-md-3" id="filtroAnualGlobal" style="display: none;">
                                <label for="ano_selecionado" class="form-label fw-semibold small text-muted">Ano:</label>
                                <select name="ano_selecionado" id="ano_selecionado" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">Selecione</option>
                                    @foreach($anosDisponiveis as $anoOption)
                                        <option value="{{ $anoOption }}" {{ ($anoSelecionado ?? '') == $anoOption ? 'selected' : '' }}>
                                            {{ $anoOption }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            {{-- Botão Limpar --}}
                            <div class="col-md-2">
                                @if(($periodoSelecionado ?? 'geral') != 'geral' || ($aeroportoSelecionado ?? 'geral') != 'geral')
                                    <a href="{{ route('companhias.dashboard', $companhia->id) }}" class="btn btn-outline-secondary btn-sm w-100">
                                        <i class="bi bi-x-circle"></i> Limpar
                                    </a>
                                @endif
                            </div>
                        </form>
                        
                        {{-- Filtros Ativos --}}
                        @php
                            $temFiltroAtivo = false;
                            $filtrosAtivos = [];
                            
                            if (isset($aeroportoSelecionado) && $aeroportoSelecionado !== 'geral') {
                                $temFiltroAtivo = true;
                                $filtrosAtivos[] = "Aeroporto: {$aeroportoSelecionado}";
                            }
                            
                            if (isset($periodoSelecionado) && $periodoSelecionado !== 'geral') {
                                $temFiltroAtivo = true;
                                if ($periodoSelecionado == 'semanal' && isset($semanaSelecionada)) {
                                    $filtrosAtivos[] = "Semana: " . str_replace('-W', ' ', $semanaSelecionada);
                                } elseif ($periodoSelecionado == 'mensal' && isset($mesSelecionado) && isset($anoFiltro)) {
                                    $filtrosAtivos[] = "Mês: " . DateTime::createFromFormat('!m', $mesSelecionado)->format('F') . "/{$anoFiltro}";
                                } elseif ($periodoSelecionado == 'anual' && isset($anoSelecionado)) {
                                    $filtrosAtivos[] = "Ano: {$anoSelecionado}";
                                }
                            }
                        @endphp

                        @if($temFiltroAtivo)
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="bi bi-funnel"></i> Filtros ativos: 
                                    @foreach($filtrosAtivos as $filtro)
                                        <span class="badge bg-light text-dark me-1">{{ $filtro }}</span>
                                    @endforeach
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Título da Seção de Desempenho --}}
        <div class="row mb-3">
            <div class="col-12">
                <h5 class="fw-semibold mb-0">Desempenho por Categoria</h5>
                <p class="text-muted small">Médias das avaliações da companhia (escala de 0-10)</p>
            </div>
        </div>

        {{-- Cards de Desempenho por Categoria --}}
        <div class="row g-3 mb-4">
            {{-- Card Objetivo --}}
            <div class="col-md-3">
                <div class="rating-card p-3" style="border-left-color: #0d6efd !important;">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <div class="h5 rating-value text-primary">{{ number_format($notaObj, 1) }}</div>
                            <div class="rating-label mt-1">Objetivo</div>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                            <i class="bi bi-bullseye text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Card Pontualidade --}}
            <div class="col-md-3">
                <div class="rating-card p-3" style="border-left-color: #198754 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <div class="h5 rating-value text-success">{{ number_format($notaPontualidade, 1) }}</div>
                            <div class="rating-label mt-1">Pontualidade</div>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-2">
                            <i class="bi bi-clock text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Card Serviços --}}
            <div class="col-md-3">
                <div class="rating-card p-3" style="border-left-color: #0dcaf0 !important;">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <div class="h5 rating-value text-info">{{ number_format($notaServicos, 1) }}</div>
                            <div class="rating-label mt-1">Serviços</div>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded-circle p-2">
                            <i class="bi bi-cone-striped text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Card Pátio --}}
            <div class="col-md-3">
                <div class="rating-card p-3" style="border-left-color: #6c757d !important;">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <div class="h5 rating-value text-secondary">{{ number_format($notaPatio, 1) }}</div>
                            <div class="rating-label mt-1">Pátio</div>
                        </div>
                        <div class="bg-secondary bg-opacity-10 rounded-circle p-2">
                            <i class="bi bi-building text-secondary fs-4"></i>
                        </div>
                    </div> 
                </div>
            </div>
        </div>

        {{-- Legenda de Avaliação --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-light bg-light">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="bi bi-info-circle text-primary fs-5"></i>
                            </div>
                            <div class="small">
                                <strong class="text-dark">Legenda da Avaliação:</strong> 
                                <span class="text-muted ms-2">0-3: Baixo</span>
                                <span class="text-muted ms-2">|</span>
                                <span class="text-muted ms-2">4-6: Médio</span>
                                <span class="text-muted ms-2">|</span>
                                <span class="text-muted ms-2">7-8: Bom</span>
                                <span class="text-muted ms-2">|</span>
                                <span class="text-muted ms-2">9-10: Excelente</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Título da Seção de Estatísticas --}}
        <div class="row mb-3">
            <div class="col-12">
                <h5 class="fw-semibold mb-0">Estatísticas de Volume</h5>
                <p class="text-muted small">Métricas operacionais da companhia</p>
            </div>
        </div>

        {{-- Cards de Estatísticas de Volume --}}
        <div class="row g-3 mb-4">
            {{-- Card Total de Voos --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0d6efd !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-airplane text-primary fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold text-dark">Total de Voos</h6>
                                <div class="d-flex align-items-baseline justify-content-between">
                                    <h4 class="mb-0 fw-bold text-primary">
                                        {{ number_format($totalVoos, 0, ',', '.') }}
                                    </h4>
                                    <div class="text-end">
                                        <span class="badge bg-primary bg-opacity-25 text-primary px-2 py-1">
                                            {{ $totalVoos > 0 ? '100' : '0' }}%
                                        </span>
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">
                                            do total
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">Voos realizados</small>
                    </div>
                </div>
            </div>

            {{-- Card Total de Passageiros --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0dcaf0 !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-people text-info fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold text-dark">Total de Passageiros</h6>
                                <div class="d-flex align-items-baseline justify-content-between">
                                    <h4 class="mb-0 fw-bold text-info">
                                        {{ number_format($totalPassageiros, 0, ',', '.') }}
                                    </h4>
                                    <div class="text-end">
                                        <span class="badge bg-info bg-opacity-25 text-info px-2 py-1">
                                            {{ $totalPassageiros > 0 ? min(100, number_format(($totalPassageiros / 100) * 100, 0)) : '0' }}%
                                        </span>
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">
                                            da capacidade
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">Passageiros transportados</small>
                    </div>
                </div>
            </div>

            {{-- Card Modelos Utilizados --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #198754 !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-airplane-engines text-success fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold text-dark">Modelos Utilizados</h6>
                                <div class="d-flex align-items-baseline justify-content-between">
                                    <h4 class="mb-0 fw-bold text-success">
                                        {{ $aeronaves->unique('modelo')->count() }}
                                    </h4>
                                    <div class="text-end">
                                        <span class="badge bg-success bg-opacity-25 text-success px-2 py-1">
                                            {{ $aeronaves->count() > 0 ? number_format(($aeronaves->unique('modelo')->count() / $aeronaves->count()) * 100, 0) : '0' }}%
                                        </span>
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">
                                            da frota
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">Tipos de aeronaves</small>
                    </div>
                </div>
            </div>

            {{-- Card Aeroportos Operados --}}
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-building text-warning fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold text-dark">Aeroportos Operados</h6>
                                <div class="d-flex align-items-baseline justify-content-between">
                                    <h4 class="mb-0 fw-bold text-warning">
                                        {{ $totalAeroportos }}
                                    </h4>
                                    <div class="text-end">
                                        <span class="badge bg-warning bg-opacity-25 text-warning px-2 py-1">
                                            {{ $totalAeroportos > 0 ? '100' : '0' }}%
                                        </span>
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">
                                            disponíveis
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">Aeroportos atendidos</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Voos e Passageiros por Aeroporto --}}
        <div class="row mb-4">
            {{-- Voos por Aeroporto --}}
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1 fw-bold text-dark">
                                    <i class="bi bi-airplane text-primary me-2"></i>Voos por Aeroporto
                                </h5>
                                <p class="text-muted small mb-0">Distribuição de voos por aeroporto operado</p>
                            </div>
                            <span class="badge bg-primary fs-6 py-2 px-3">
                                <i class="bi bi-airplane me-1"></i>
                                {{ number_format($totalVoos, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless mb-0">
                                <thead>
                                    <tr class="border-bottom">
                                        <th class="text-muted small fw-normal">Aeroporto</th>
                                        <th class="text-muted small fw-normal text-end">Voos</th>
                                        <th class="text-muted small fw-normal text-end" style="width: 80px;">%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $contador = 0;
                                        $medalhas = ['🥇', '🥈', '🥉'];
                                    @endphp
                                    @foreach($voosPorAeroporto as $aeroporto => $quantidade)
                                        @php
                                            $percentual = $totalVoos > 0 ? ($quantidade / $totalVoos) * 100 : 0;
                                            $contador++;
                                            $medalha = $contador <= 3 ? $medalhas[$contador - 1] : '🏅';
                                            $corMedalha = match($contador) {
                                                1 => '#FFD700',
                                                2 => '#C0C0C0',
                                                3 => '#CD7F32',
                                                default => '#6c757d'
                                            };
                                        @endphp
                                        <tr class="{{ $contador <= 3 ? 'fw-bold' : '' }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <span class="fw-bold" style="color: {{ $corMedalha }}; font-size: 1.1rem;">
                                                            {{ $medalha }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span class="{{ $contador <= 3 ? 'text-dark' : 'text-muted' }}">
                                                            {{ $aeroporto }}
                                                        </span>
                                                        @if($contador <= 3)
                                                            <small class="text-muted d-block" style="font-size: 0.7rem;">
                                                                {{ $contador }}º lugar
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold {{ $contador <= 3 ? 'text-primary' : 'text-dark' }}">
                                                    {{ number_format($quantidade, 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex align-items-center justify-content-end">
                                                    <span class="text-muted small me-2">{{ number_format($percentual, 1) }}%</span>
                                                    <div class="progress flex-grow-1" style="height: 6px; width: 60px;">
                                                        <div class="progress-bar bg-primary" 
                                                            role="progressbar" 
                                                            style="width: {{ $percentual }}%"
                                                            aria-valuenow="{{ $percentual }}" 
                                                            aria-valuemin="0" 
                                                            aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @if($contador === 3 && $loop->remaining > 0)
                                            <tr>
                                                <td colspan="3" class="py-2">
                                                    <div class="text-center py-1 bg-light rounded">
                                                        <small class="text-muted fw-bold">
                                                            <i class="bi bi-chevron-down me-1"></i>Outros aeroportos
                                                        </small>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        {{-- Resumo Estatístico --}}
                        @if($voosPorAeroporto->count() > 0)
                        <div class="row mt-4 pt-3 border-top">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <h6 class="text-muted small mb-1">Total de Aeroportos</h6>
                                    <h4 class="fw-bold text-primary mb-0">{{ $voosPorAeroporto->count() }}</h4>
                                    <small class="text-muted">aeroportos operados</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <h6 class="text-muted small mb-1">Média por Aeroporto</h6>
                                    <h4 class="fw-bold text-primary mb-0">{{ number_format($totalVoos / $voosPorAeroporto->count(), 1) }}</h4>
                                    <small class="text-muted">voos em média</small>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Passageiros por Aeroporto --}}
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1 fw-bold text-dark">
                                    <i class="bi bi-people text-info me-2"></i>Passageiros por Aeroporto
                                </h5>
                                <p class="text-muted small mb-0">Distribuição de passageiros por aeroporto operado</p>
                            </div>
                            <span class="badge bg-info fs-6 py-2 px-3">
                                <i class="bi bi-people me-1"></i>
                                {{ number_format($totalPassageiros, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless mb-0">
                                <thead>
                                    <tr class="border-bottom">
                                        <th class="text-muted small fw-normal">Aeroporto</th>
                                        <th class="text-muted small fw-normal text-end">Passageiros</th>
                                        <th class="text-muted small fw-normal text-end" style="width: 80px;">%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $contador = 0;
                                        $medalhas = ['🥇', '🥈', '🥉'];
                                    @endphp
                                    @foreach($passageirosPorAeroporto as $aeroporto => $quantidade)
                                        @php
                                            $percentual = $totalPassageiros > 0 ? ($quantidade / $totalPassageiros) * 100 : 0;
                                            $contador++;
                                            $medalha = $contador <= 3 ? $medalhas[$contador - 1] : '🏅';
                                            $corMedalha = match($contador) {
                                                1 => '#FFD700',
                                                2 => '#C0C0C0',
                                                3 => '#CD7F32',
                                                default => '#6c757d'
                                            };
                                        @endphp
                                        <tr class="{{ $contador <= 3 ? 'fw-bold' : '' }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <span class="fw-bold" style="color: {{ $corMedalha }}; font-size: 1.1rem;">
                                                            {{ $medalha }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span class="{{ $contador <= 3 ? 'text-dark' : 'text-muted' }}">
                                                            {{ $aeroporto }}
                                                        </span>
                                                        @if($contador <= 3)
                                                            <small class="text-muted d-block" style="font-size: 0.7rem;">
                                                                {{ $contador }}º lugar
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold {{ $contador <= 3 ? 'text-info' : 'text-dark' }}">
                                                    {{ number_format($quantidade, 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-flex align-items-center justify-content-end">
                                                    <span class="text-muted small me-2">{{ number_format($percentual, 1) }}%</span>
                                                    <div class="progress flex-grow-1" style="height: 6px; width: 60px;">
                                                        <div class="progress-bar bg-info" 
                                                            role="progressbar" 
                                                            style="width: {{ $percentual }}%"
                                                            aria-valuenow="{{ $percentual }}" 
                                                            aria-valuemin="0" 
                                                            aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @if($contador === 3 && $loop->remaining > 0)
                                            <tr>
                                                <td colspan="3" class="py-2">
                                                    <div class="text-center py-1 bg-light rounded">
                                                        <small class="text-muted fw-bold">
                                                            <i class="bi bi-chevron-down me-1"></i>Outros aeroportos
                                                        </small>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        {{-- Resumo Estatístico --}}
                        @if($passageirosPorAeroporto->count() > 0)
                        <div class="row mt-4 pt-3 border-top">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <h6 class="text-muted small mb-1">Total de Aeroportos</h6>
                                    <h4 class="fw-bold text-info mb-0">{{ $passageirosPorAeroporto->count() }}</h4>
                                    <small class="text-muted">aeroportos com passageiros</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <h6 class="text-muted small mb-1">Média por Aeroporto</h6>
                                    <h4 class="fw-bold text-info mb-0">{{ number_format($totalPassageiros / $passageirosPorAeroporto->count(), 0, ',', '.') }}</h4>
                                    <small class="text-muted">passageiros em média</small>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Voos e Passageiros por Modelo --}}
        <div class="row mb-4">
            {{-- Voos por Modelo --}}
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1 fw-bold text-dark">
                                    <i class="bi bi-airplane-engines text-primary me-2"></i>Voos por Modelo
                                </h5>
                                <p class="text-muted small mb-0">Distribuição de voos por modelo de aeronave</p>
                            </div>
                            <span class="badge bg-primary fs-6 py-2 px-3">
                                <i class="bi bi-list-check me-1"></i>
                                @php
                                    // Criar array de voos por modelo
                                    $voosPorModelo = [];
                                    foreach($aeronaves as $aeronave) {
                                        $totalVoosModelo = $companhia->voos()
                                            ->where('aeronave_id', $aeronave->id)
                                            ->count();
                                        $voosPorModelo[$aeronave->modelo] = $totalVoosModelo;
                                    }
                                    $modelosUtilizadosCount = count(array_filter($voosPorModelo, fn($value) => $value > 0));
                                    $totalModelosCount = count($voosPorModelo);
                                @endphp
                                {{ $modelosUtilizadosCount }}/{{ $totalModelosCount }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless mb-0">
                                <thead>
                                    <tr class="border-bottom">
                                        <th class="text-muted small fw-normal">Modelo</th>
                                        <th class="text-muted small fw-normal text-end">Voos</th>
                                        <th class="text-muted small fw-normal text-end" style="width: 80px;">%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $contador = 0;
                                        $medalhas = ['🥇', '🥈', '🥉'];
                                        $modelosUtilizados = [];
                                        $modelosNaoUtilizados = [];
                                    @endphp
                                    
                                    {{-- Separar modelos utilizados e não utilizados --}}
                                    @foreach($voosPorModelo as $modelo => $quantidade)
                                        @if($quantidade > 0)
                                            @php $modelosUtilizados[$modelo] = $quantidade; @endphp
                                        @else
                                            @php $modelosNaoUtilizados[$modelo] = $quantidade; @endphp
                                        @endif
                                    @endforeach
                                    
                                    {{-- Ordenar modelos utilizados por quantidade (maior primeiro) --}}
                                    @php
                                        arsort($modelosUtilizados);
                                    @endphp
                                    
                                    {{-- Mostrar modelos utilizados --}}
                                    @foreach($modelosUtilizados as $modelo => $quantidade)
                                        @php
                                            $percentual = $totalVoos > 0 && $quantidade > 0 ? ($quantidade / $totalVoos) * 100 : 0;
                                            $contador++;
                                            $medalha = $contador <= 3 ? $medalhas[$contador - 1] : '🏅';
                                            $corMedalha = match($contador) {
                                                1 => '#FFD700',
                                                2 => '#C0C0C0',
                                                3 => '#CD7F32',
                                                default => '#6c757d'
                                            };
                                        @endphp
                                        <tr class="{{ $contador <= 3 ? 'fw-bold' : '' }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <span class="fw-bold" style="color: {{ $corMedalha }}; font-size: 1.1rem;">
                                                            {{ $medalha }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span class="text-dark">
                                                            {{ $modelo }}
                                                        </span>
                                                        @if($contador <= 3)
                                                            <small class="text-muted d-block" style="font-size: 0.7rem;">
                                                                {{ $contador }}º lugar
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold text-primary">
                                                    {{ number_format($quantidade, 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                @if($percentual > 0)
                                                    <div class="d-flex align-items-center justify-content-end">
                                                        <span class="text-muted small me-2">{{ number_format($percentual, 1) }}%</span>
                                                        <div class="progress flex-grow-1" style="height: 6px; width: 60px;">
                                                            <div class="progress-bar bg-primary" 
                                                                role="progressbar" 
                                                                style="width: {{ $percentual }}%"
                                                                aria-valuenow="{{ $percentual }}" 
                                                                aria-valuemin="0" 
                                                                aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($contador === 3 && count($modelosUtilizados) > 3)
                                            <tr>
                                                <td colspan="3" class="py-2">
                                                    <div class="text-center py-1 bg-light rounded">
                                                        <small class="text-muted fw-bold">
                                                            <i class="bi bi-chevron-down me-1"></i>Outros modelos
                                                        </small>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    
                                    {{-- Dropdown para modelos não utilizados --}}
                                    @if(count($modelosNaoUtilizados) > 0)
                                        <tr>
                                            <td colspan="3" class="p-0">
                                                <div class="accordion" id="accordionModelosNaoUtilizados">
                                                    <div class="accordion-item border-0">
                                                        <h2 class="accordion-header" id="headingModelosNaoUtilizados">
                                                            <button class="accordion-button collapsed bg-light rounded" 
                                                                    type="button" 
                                                                    data-bs-toggle="collapse" 
                                                                    data-bs-target="#collapseModelosNaoUtilizados" 
                                                                    aria-expanded="false" 
                                                                    aria-controls="collapseModelosNaoUtilizados">
                                                                <div class="d-flex align-items-center">
                                                                    <span class="fw-bold text-muted me-2" style="font-size: 1.1rem;">
                                                                        🎯
                                                                    </span>
                                                                    <span class="fw-bold text-muted">
                                                                        Modelos não utilizados
                                                                        <span class="badge bg-secondary ms-2">{{ count($modelosNaoUtilizados) }}</span>
                                                                    </span>
                                                                    <i class="bi bi-chevron-down ms-auto"></i>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseModelosNaoUtilizados" 
                                                            class="accordion-collapse collapse" 
                                                            aria-labelledby="headingModelosNaoUtilizados" 
                                                            data-bs-parent="#accordionModelosNaoUtilizados">
                                                            <div class="accordion-body p-0">
                                                                <table class="table table-borderless mb-0">
                                                                    <tbody>
                                                                        @foreach($modelosNaoUtilizados as $modelo => $quantidade)
                                                                            <tr class="opacity-75">
                                                                                <td>
                                                                                    <div class="d-flex align-items-center">
                                                                                        <div class="me-3">
                                                                                            <span class="text-muted" style="font-size: 1.1rem;">
                                                                                                ✈️
                                                                                            </span>
                                                                                        </div>
                                                                                        <div>
                                                                                            <span class="text-muted">
                                                                                                {{ $modelo }}
                                                                                            </span>
                                                                                            <br>
                                                                                            <small class="text-muted">Sem voos registrados</small>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                                <td class="text-end">
                                                                                    <span class="text-muted">0</span>
                                                                                </td>
                                                                                <td class="text-end">
                                                                                    <span class="text-muted small">0%</span>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        
                        {{-- Resumo Estatístico --}}
                        @if($totalModelosCount > 0)
                        <div class="row mt-4 pt-3 border-top">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h6 class="text-muted small mb-1">Modelos Utilizados</h6>
                                    <h4 class="fw-bold text-success mb-0">
                                        {{ $modelosUtilizadosCount }}
                                    </h4>
                                    <small class="text-muted">
                                        {{ $totalModelosCount > 0 ? number_format(($modelosUtilizadosCount / $totalModelosCount) * 100, 0) : 0 }}% da frota
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h6 class="text-muted small mb-1">Modelos Não Utilizados</h6>
                                    <h4 class="fw-bold text-secondary mb-0">
                                        {{ $totalModelosCount - $modelosUtilizadosCount }}
                                    </h4>
                                    <small class="text-muted">
                                        {{ $totalModelosCount > 0 ? number_format((($totalModelosCount - $modelosUtilizadosCount) / $totalModelosCount) * 100, 0) : 0 }}% da frota
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h6 class="text-muted small mb-1">Média por Modelo</h6>
                                    <h4 class="fw-bold text-primary mb-0">
                                        @php
                                            $media = $modelosUtilizadosCount > 0 ? $totalVoos / $modelosUtilizadosCount : 0;
                                        @endphp
                                        {{ number_format($media, 1) }}
                                    </h4>
                                    <small class="text-muted">voos por modelo</small>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Passageiros por Modelo --}}
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1 fw-bold text-dark">
                                    <i class="bi bi-people text-info me-2"></i>Passageiros por Modelo
                                </h5>
                                <p class="text-muted small mb-0">Distribuição de passageiros por modelo de aeronave</p>
                            </div>
                            <span class="badge bg-info fs-6 py-2 px-3">
                                <i class="bi bi-list-check me-1"></i>
                                @php
                                    // Criar array de passageiros por modelo
                                    $passageirosPorModelo = [];
                                    foreach($aeronaves as $aeronave) {
                                        $totalPassageirosModelo = $companhia->voos()
                                            ->where('aeronave_id', $aeronave->id)
                                            ->sum('total_passageiros');
                                        $passageirosPorModelo[$aeronave->modelo] = $totalPassageirosModelo;
                                    }
                                    $modelosUtilizadosCountPassageiros = count(array_filter($passageirosPorModelo, fn($value) => $value > 0));
                                    $totalModelosCountPassageiros = count($passageirosPorModelo);
                                @endphp
                                {{ $modelosUtilizadosCountPassageiros }}/{{ $totalModelosCountPassageiros }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless mb-0">
                                <thead>
                                    <tr class="border-bottom">
                                        <th class="text-muted small fw-normal">Modelo</th>
                                        <th class="text-muted small fw-normal text-end">Passageiros</th>
                                        <th class="text-muted small fw-normal text-end" style="width: 80px;">%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $contador = 0;
                                        $medalhas = ['🥇', '🥈', '🥉'];
                                        $modelosUtilizadosPassageiros = [];
                                        $modelosNaoUtilizadosPassageiros = [];
                                    @endphp
                                    
                                    {{-- Separar modelos utilizados e não utilizados --}}
                                    @foreach($passageirosPorModelo as $modelo => $quantidade)
                                        @if($quantidade > 0)
                                            @php $modelosUtilizadosPassageiros[$modelo] = $quantidade; @endphp
                                        @else
                                            @php $modelosNaoUtilizadosPassageiros[$modelo] = $quantidade; @endphp
                                        @endif
                                    @endforeach
                                    
                                    {{-- Ordenar modelos utilizados por quantidade (maior primeiro) --}}
                                    @php
                                        arsort($modelosUtilizadosPassageiros);
                                    @endphp
                                    
                                    {{-- Mostrar modelos utilizados --}}
                                    @foreach($modelosUtilizadosPassageiros as $modelo => $quantidade)
                                        @php
                                            $percentual = $totalPassageiros > 0 && $quantidade > 0 ? ($quantidade / $totalPassageiros) * 100 : 0;
                                            $contador++;
                                            $medalha = $contador <= 3 ? $medalhas[$contador - 1] : '🏅';
                                            $corMedalha = match($contador) {
                                                1 => '#FFD700',
                                                2 => '#C0C0C0',
                                                3 => '#CD7F32',
                                                default => '#6c757d'
                                            };
                                        @endphp
                                        <tr class="{{ $contador <= 3 ? 'fw-bold' : '' }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <span class="fw-bold" style="color: {{ $corMedalha }}; font-size: 1.1rem;">
                                                            {{ $medalha }}
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span class="text-dark">
                                                            {{ $modelo }}
                                                        </span>
                                                        @if($contador <= 3)
                                                            <small class="text-muted d-block" style="font-size: 0.7rem;">
                                                                {{ $contador }}º lugar
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold text-info">
                                                    {{ number_format($quantidade, 0, ',', '.') }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                @if($percentual > 0)
                                                    <div class="d-flex align-items-center justify-content-end">
                                                        <span class="text-muted small me-2">{{ number_format($percentual, 1) }}%</span>
                                                        <div class="progress flex-grow-1" style="height: 6px; width: 60px;">
                                                            <div class="progress-bar bg-info" 
                                                                role="progressbar" 
                                                                style="width: {{ $percentual }}%"
                                                                aria-valuenow="{{ $percentual }}" 
                                                                aria-valuemin="0" 
                                                                aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                        @if($contador === 3 && count($modelosUtilizadosPassageiros) > 3)
                                            <tr>
                                                <td colspan="3" class="py-2">
                                                    <div class="text-center py-1 bg-light rounded">
                                                        <small class="text-muted fw-bold">
                                                            <i class="bi bi-chevron-down me-1"></i>Outros modelos
                                                        </small>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    
                                    {{-- Dropdown para modelos não utilizados --}}
                                    @if(count($modelosNaoUtilizadosPassageiros) > 0)
                                        <tr>
                                            <td colspan="3" class="p-0">
                                                <div class="accordion" id="accordionModelosNaoUtilizadosPassageiros">
                                                    <div class="accordion-item border-0">
                                                        <h2 class="accordion-header" id="headingModelosNaoUtilizadosPassageiros">
                                                            <button class="accordion-button collapsed bg-light rounded" 
                                                                    type="button" 
                                                                    data-bs-toggle="collapse" 
                                                                    data-bs-target="#collapseModelosNaoUtilizadosPassageiros" 
                                                                    aria-expanded="false" 
                                                                    aria-controls="collapseModelosNaoUtilizadosPassageiros">
                                                                <div class="d-flex align-items-center">
                                                                    <span class="fw-bold text-muted me-2" style="font-size: 1.1rem;">
                                                                        🎯
                                                                    </span>
                                                                    <span class="fw-bold text-muted">
                                                                        Modelos sem passageiros
                                                                        <span class="badge bg-secondary ms-2">{{ count($modelosNaoUtilizadosPassageiros) }}</span>
                                                                    </span>
                                                                    <i class="bi bi-chevron-down ms-auto"></i>
                                                                </div>
                                                            </button>
                                                        </h2>
                                                        <div id="collapseModelosNaoUtilizadosPassageiros" 
                                                            class="accordion-collapse collapse" 
                                                            aria-labelledby="headingModelosNaoUtilizadosPassageiros" 
                                                            data-bs-parent="#accordionModelosNaoUtilizadosPassageiros">
                                                            <div class="accordion-body p-0">
                                                                <table class="table table-borderless mb-0">
                                                                    <tbody>
                                                                        @foreach($modelosNaoUtilizadosPassageiros as $modelo => $quantidade)
                                                                            <tr class="opacity-75">
                                                                                <td>
                                                                                    <div class="d-flex align-items-center">
                                                                                        <div class="me-3">
                                                                                            <span class="text-muted" style="font-size: 1.1rem;">
                                                                                                ✈️
                                                                                            </span>
                                                                                        </div>
                                                                                        <div>
                                                                                            <span class="text-muted">
                                                                                                {{ $modelo }}
                                                                                            </span>
                                                                                            <br>
                                                                                            <small class="text-muted">Sem passageiros registrados</small>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                                <td class="text-end">
                                                                                    <span class="text-muted">0</span>
                                                                                </td>
                                                                                <td class="text-end">
                                                                                    <span class="text-muted small">0%</span>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        
                        {{-- Resumo Estatístico --}}
                        @if($totalModelosCountPassageiros > 0)
                        <div class="row mt-4 pt-3 border-top">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h6 class="text-muted small mb-1">Modelos com Passageiros</h6>
                                    <h4 class="fw-bold text-success mb-0">
                                        {{ $modelosUtilizadosCountPassageiros }}
                                    </h4>
                                    <small class="text-muted">
                                        {{ $totalModelosCountPassageiros > 0 ? number_format(($modelosUtilizadosCountPassageiros / $totalModelosCountPassageiros) * 100, 0) : 0 }}% da frota
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h6 class="text-muted small mb-1">Modelos sem Passageiros</h6>
                                    <h4 class="fw-bold text-secondary mb-0">
                                        {{ $totalModelosCountPassageiros - $modelosUtilizadosCountPassageiros }}
                                    </h4>
                                    <small class="text-muted">
                                        {{ $totalModelosCountPassageiros > 0 ? number_format((($totalModelosCountPassageiros - $modelosUtilizadosCountPassageiros) / $totalModelosCountPassageiros) * 100, 0) : 0 }}% da frota
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <h6 class="text-muted small mb-1">Média por Modelo</h6>
                                    <h4 class="fw-bold text-info mb-0">
                                        @php
                                            $mediaPassageiros = $modelosUtilizadosCountPassageiros > 0 ? $totalPassageiros / $modelosUtilizadosCountPassageiros : 0;
                                        @endphp
                                        {{ number_format($mediaPassageiros, 0, ',', '.') }}
                                    </h4>
                                    <small class="text-muted">passageiros por modelo</small>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Análise de Voos por Horário --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="bi bi-clock-history me-2 text-primary"></i>Análise de Voos por Horário
                        </h5>
                        <p class="text-muted small mb-0">Distribuição de voos e passageiros ao longo do dia</p>
                    </div>
                    <div class="card-body pt-3">
                        <div class="row g-4">
                            {{-- Voos por Horário --}}
                            <div class="col-md-6">
                                <div class="card h-100 shadow-sm border-0">
                                    <div class="card-header bg-white border-bottom-0 pb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="mb-1 fw-bold text-dark">
                                                    <i class="bi bi-airplane text-primary me-2"></i>Voos por Horário
                                                </h5>
                                                <p class="text-muted small mb-0">Distribuição de voos ao longo do dia</p>
                                            </div>
                                            <span class="badge bg-primary fs-6 py-2 px-3">
                                                <i class="bi bi-airplane me-1"></i>
                                                {{ number_format($totalVoos, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0">
                                        {{-- Gráfico --}}
                                        <div class="chart-container mb-4" style="position: relative; height: 250px;">
                                            <canvas id="voosHorarioChart"></canvas>
                                        </div>
                                        
                                        {{-- Tabela Detalhada --}}
                                        <div class="table-responsive">
                                            <table class="table table-hover table-borderless">
                                                <thead>
                                                    <tr class="border-bottom">
                                                        <th class="text-muted small fw-normal">Horário</th>
                                                        <th class="text-muted small fw-normal text-end">Voos</th>
                                                        <th class="text-muted small fw-normal text-end" style="width: 80px;">%</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $totalVoosHorario = array_sum($voosPorHorario);
                                                        $horariosInfo = [
                                                            'EAM' => ['icon' => 'moon-stars', 'color' => '#4A6A8A', 'label' => 'EAM'],
                                                            'AM' => ['icon' => 'sunrise', 'color' => '#6c9bcf', 'label' => 'AM'],
                                                            'AN' => ['icon' => 'sun', 'color' => '#ff8c00', 'label' => 'AN'],
                                                            'PM' => ['icon' => 'moon', 'color' => '#dc3545', 'label' => 'PM'],
                                                            'ALL' => ['icon' => 'clock-history', 'color' => '#9b59b6', 'label' => 'ALL'],
                                                        ];
                                                        
                                                        // Ordenar na sequência correta
                                                        $horariosOrdenados = ['EAM', 'AM', 'AN', 'PM', 'ALL'];
                                                    @endphp
                                                    @foreach($horariosOrdenados as $horario)
                                                        @php
                                                            $quantidade = $voosPorHorario[$horario] ?? 0;
                                                            $percentual = $totalVoosHorario > 0 ? ($quantidade / $totalVoosHorario) * 100 : 0;
                                                            $horarioInfo = $horariosInfo[$horario] ?? ['icon' => 'clock', 'color' => '#6c757d', 'label' => $horario];
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="me-3">
                                                                        <i class="bi bi-{{ $horarioInfo['icon'] }} fs-5" style="color: {{ $horarioInfo['color'] }};"></i>
                                                                    </div>
                                                                    <div>
                                                                        <span class="fw-bold text-dark">{{ $horarioInfo['label'] }}</span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="text-end align-middle">
                                                                <span class="fw-bold text-dark">
                                                                    {{ number_format($quantidade, 0, ',', '.') }}
                                                                </span>
                                                            </td>
                                                            <td class="text-end align-middle">
                                                                <span class="fw-bold" style="color: {{ $horarioInfo['color'] }};">
                                                                    {{ number_format($percentual, 1) }}%
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr class="border-top">
                                                        <td class="fw-bold text-dark">Total</td>
                                                        <td class="text-end fw-bold text-dark">
                                                            {{ number_format($totalVoosHorario, 0, ',', '.') }}
                                                        </td>
                                                        <td class="text-end fw-bold" style="color: #4A6A8A;">100%</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        
                                        {{-- Estatísticas Adicionais --}}
                                        @php
                                            $horariosComVoos = collect($voosPorHorario)->filter(fn($value) => $value > 0);
                                            $horarioMaisMovimentadoVoos = collect($voosPorHorario)->sortDesc()->keys()->first();
                                            $horarioMaisMovimentadoQuantidadeVoos = $voosPorHorario[$horarioMaisMovimentadoVoos] ?? 0;
                                            $horarioMaisMovimentadoPercentualVoos = $totalVoosHorario > 0 ? ($horarioMaisMovimentadoQuantidadeVoos / $totalVoosHorario) * 100 : 0;
                                            $horarioMaisMovimentadoInfo = $horariosInfo[$horarioMaisMovimentadoVoos] ?? ['color' => '#4A6A8A', 'icon' => 'clock'];
                                            $mediaVoosPorHorarioAtivo = $horariosComVoos->count() > 0 ? $totalVoosHorario / $horariosComVoos->count() : 0;
                                        @endphp
                                        
                                        <div class="row mt-4 pt-3 border-top">
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <h6 class="text-muted small mb-1">Horário Mais Movimentado</h6>
                                                    <div class="d-flex align-items-center justify-content-center mb-1">
                                                        <i class="bi bi-{{ $horarioMaisMovimentadoInfo['icon'] }} me-2 fs-4" 
                                                        style="color: {{ $horarioMaisMovimentadoInfo['color'] }};"></i>
                                                        <h5 class="fw-bold text-dark mb-0">
                                                            {{ $horariosInfo[$horarioMaisMovimentadoVoos]['label'] ?? $horarioMaisMovimentadoVoos }}
                                                        </h5>
                                                    </div>
                                                    <small class="text-muted">{{ number_format($horarioMaisMovimentadoPercentualVoos, 1) }}% dos voos</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <h6 class="text-muted small mb-1">Horários Ativos</h6>
                                                    <h4 class="fw-bold" style="color: #4A6A8A;">
                                                        {{ $horariosComVoos->count() }}
                                                    </h4>
                                                    <small class="text-muted">com voos</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <h6 class="text-muted small mb-1">Média por Horário</h6>
                                                    <h4 class="fw-bold" style="color: #ff8c00;">
                                                        {{ $horariosComVoos->count() > 0 ? number_format($mediaVoosPorHorarioAtivo, 1, ',', '.') : 0 }}
                                                    </h4>
                                                    <small class="text-muted">voos por horário ativo</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Passageiros por Horário --}}
                            <div class="col-md-6">
                                <div class="card h-100 shadow-sm border-0">
                                    <div class="card-header bg-white border-bottom-0 pb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h5 class="mb-1 fw-bold text-dark">
                                                    <i class="bi bi-people text-info me-2"></i>Passageiros por Horário
                                                </h5>
                                                <p class="text-muted small mb-0">Distribuição de passageiros ao longo do dia</p>
                                            </div>
                                            <span class="badge bg-info fs-6 py-2 px-3">
                                                <i class="bi bi-people me-1"></i>
                                                {{ number_format($totalPassageiros, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0">
                                        {{-- Gráfico --}}
                                        <div class="chart-container mb-4" style="position: relative; height: 250px;">
                                            <canvas id="passageirosHorarioChart"></canvas>
                                        </div>
                                        
                                        {{-- Tabela Detalhada --}}
                                        <div class="table-responsive">
                                            <table class="table table-hover table-borderless">
                                                <thead>
                                                    <tr class="border-bottom">
                                                        <th class="text-muted small fw-normal">Horário</th>
                                                        <th class="text-muted small fw-normal text-end">Passageiros</th>
                                                        <th class="text-muted small fw-normal text-end" style="width: 80px;">%</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $totalPassageirosHorario = array_sum($passageirosPorHorario);
                                                    @endphp
                                                    @foreach($horariosOrdenados as $horario)
                                                        @php
                                                            $quantidade = $passageirosPorHorario[$horario] ?? 0;
                                                            $percentual = $totalPassageirosHorario > 0 ? ($quantidade / $totalPassageirosHorario) * 100 : 0;
                                                            $horarioInfo = $horariosInfo[$horario] ?? ['icon' => 'clock', 'color' => '#6c757d', 'label' => $horario];
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="me-3">
                                                                        <i class="bi bi-{{ $horarioInfo['icon'] }} fs-5" style="color: {{ $horarioInfo['color'] }};"></i>
                                                                    </div>
                                                                    <div>
                                                                        <span class="fw-bold text-dark">{{ $horarioInfo['label'] }}</span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="text-end align-middle">
                                                                <span class="fw-bold text-dark">
                                                                    {{ number_format($quantidade, 0, ',', '.') }}
                                                                </span>
                                                            </td>
                                                            <td class="text-end align-middle">
                                                                <span class="fw-bold" style="color: {{ $horarioInfo['color'] }};">
                                                                    {{ number_format($percentual, 1) }}%
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr class="border-top">
                                                        <td class="fw-bold text-dark">Total</td>
                                                        <td class="text-end fw-bold text-dark">
                                                            {{ number_format($totalPassageirosHorario, 0, ',', '.') }}
                                                        </td>
                                                        <td class="text-end fw-bold" style="color: #4A6A8A;">100%</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        
                                        {{-- Estatísticas Adicionais --}}
                                        @php
                                            $horariosComPassageiros = collect($passageirosPorHorario)->filter(fn($value) => $value > 0);
                                            $horarioMaisMovimentadoPassageiros = collect($passageirosPorHorario)->sortDesc()->keys()->first();
                                            $horarioMaisMovimentadoQuantidadePassageiros = $passageirosPorHorario[$horarioMaisMovimentadoPassageiros] ?? 0;
                                            $horarioMaisMovimentadoPercentualPassageiros = $totalPassageirosHorario > 0 ? ($horarioMaisMovimentadoQuantidadePassageiros / $totalPassageirosHorario) * 100 : 0;
                                            $horarioMaisMovimentadoInfoPass = $horariosInfo[$horarioMaisMovimentadoPassageiros] ?? ['color' => '#4A6A8A', 'icon' => 'clock'];
                                            $mediaPassageirosPorHorarioAtivo = $horariosComPassageiros->count() > 0 ? $totalPassageirosHorario / $horariosComPassageiros->count() : 0;
                                        @endphp
                                        
                                        <div class="row mt-4 pt-3 border-top">
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <h6 class="text-muted small mb-1">Horário Mais Movimentado</h6>
                                                    <div class="d-flex align-items-center justify-content-center mb-1">
                                                        <i class="bi bi-{{ $horarioMaisMovimentadoInfoPass['icon'] }} me-2 fs-4" 
                                                        style="color: {{ $horarioMaisMovimentadoInfoPass['color'] }};"></i>
                                                        <h5 class="fw-bold text-dark mb-0">
                                                            {{ $horariosInfo[$horarioMaisMovimentadoPassageiros]['label'] ?? $horarioMaisMovimentadoPassageiros }}
                                                        </h5>
                                                    </div>
                                                    <small class="text-muted">{{ number_format($horarioMaisMovimentadoPercentualPassageiros, 1) }}% dos passageiros</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <h6 class="text-muted small mb-1">Horários Ativos</h6>
                                                    <h4 class="fw-bold" style="color: #6c9bcf;">
                                                        {{ $horariosComPassageiros->count() }}
                                                    </h4>
                                                    <small class="text-muted">com passageiros</small>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <h6 class="text-muted small mb-1">Média por Horário</h6>
                                                    <h4 class="fw-bold" style="color: #ff8c00;">
                                                        {{ $horariosComPassageiros->count() > 0 ? number_format($mediaPassageirosPorHorarioAtivo, 0, ',', '.') : 0 }}
                                                    </h4>
                                                    <small class="text-muted">passageiros por horário ativo</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Últimos Voos -->
        @if($ultimosVoos->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1 fw-bold text-dark">
                                    <i class="bi bi-clock-history me-2 text-primary"></i>Últimos Voos
                                </h5>
                                <p class="text-muted small mb-0">Últimos 5 registros de voos da companhia</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-3">
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless align-middle">
                                <thead>
                                    <tr class="border-bottom">
                                        <th class="text-muted small fw-normal">ID Voo</th>
                                        <th class="text-muted small fw-normal">
                                            <i class="bi bi-geo-alt me-1"></i>Aeroporto
                                        </th>
                                        <th class="text-muted small fw-normal">
                                            <i class="bi bi-calendar me-1"></i>Data
                                        </th>
                                        <th class="text-muted small fw-normal text-end">
                                            <i class="bi bi-airplane me-1"></i>Total de Voos
                                        </th>
                                        <th class="text-muted small fw-normal text-end">
                                            <i class="bi bi-people me-1"></i>Passageiros
                                        </th>
                                        <th class="text-muted small fw-normal text-end">
                                            <i class="bi bi-star me-1"></i>Avaliação
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ultimosVoos->take(5) as $index => $voo)
                                    <tr class="border-bottom">
                                        <td>
                                            <span class="fw-bold text-dark">{{ $voo->id_voo }}</span>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="text-dark">{{ $voo->aeroporto->nome_aeroporto ?? 'N/A' }}</span>
                                                @if($voo->aeroporto && $voo->aeroporto->codigo_iata)
                                                    <br>
                                                    <small class="text-muted">{{ $voo->aeroporto->codigo_iata }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="text-dark">{{ $voo->created_at->format('d/m/Y') }}</span>
                                                <br>
                                                <small class="text-muted">{{ $voo->created_at->format('H:i') }}</small>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold text-primary">{{ number_format($totalVoos, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold text-dark">{{ number_format($voo->total_passageiros, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="text-end">
                                            @if($voo->media_notas)
                                                @php
                                                    $nota = $voo->media_notas;
                                                    $corNota = $nota >= 8 ? 'success' : ($nota >= 6 ? 'warning' : ($nota >= 4 ? 'info' : 'danger'));
                                                @endphp
                                                <span class="fw-bold text-{{ $corNota }} fs-5">{{ number_format($nota, 1) }}</span>
                                            @else
                                                <span class="fw-bold text-secondary">--</span>
                                            @endif
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

        <!-- Frota de Aeronaves -->
        @if($aeronaves->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1 fw-bold text-dark">
                                    <i class="bi bi-building me-2 text-primary"></i>Frota de Aeronaves
                                </h5>
                                <p class="text-muted small mb-0">Aeronaves operadas pela companhia</p>
                            </div>
                            <span class="badge bg-primary fs-6 py-2 px-3">
                                <i class="bi bi-airplane-engines me-1"></i>
                                {{ number_format($aeronaves->count(), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body pt-3">
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless align-middle">
                                <thead>
                                    <tr class="border-bottom">
                                        <th class="text-muted small fw-normal">
                                            <i class="bi bi-airplane-engines me-1"></i>Modelo
                                        </th>
                                        <th class="text-muted small fw-normal">
                                            <i class="bi bi-building me-1"></i>Fabricante
                                        </th>
                                        <th class="text-muted small fw-normal text-end">
                                            <i class="bi bi-people me-1"></i>Capacidade
                                        </th>
                                        <th class="text-muted small fw-normal text-end">
                                            <i class="bi bi-graph-up me-1"></i>Total Voos
                                        </th>
                                        <th class="text-muted small fw-normal text-end">
                                            <i class="bi bi-person-check me-1"></i>Passageiros
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Calcular dados para cada aeronave e ordenar por total de passageiros (decrescente)
                                        $aeronavesComDados = [];
                                        foreach($aeronaves as $aeronave) {
                                            $totalVoosAeronave = $companhia->voos()
                                                ->where('aeronave_id', $aeronave->id)
                                                ->count();
                                            $totalPassageirosAeronave = $companhia->voos()
                                                ->where('aeronave_id', $aeronave->id)
                                                ->sum('total_passageiros');
                                            $percentualPassageiros = $totalPassageiros > 0 
                                                ? ($totalPassageirosAeronave / $totalPassageiros) * 100 
                                                : 0;
                                            
                                            $aeronavesComDados[] = [
                                                'aeronave' => $aeronave,
                                                'totalVoos' => $totalVoosAeronave,
                                                'totalPassageiros' => $totalPassageirosAeronave,
                                                'percentualPassageiros' => $percentualPassageiros,
                                            ];
                                        }
                                        
                                        // Ordenar por total de passageiros (decrescente)
                                        usort($aeronavesComDados, function($a, $b) {
                                            return $b['totalPassageiros'] <=> $a['totalPassageiros'];
                                        });
                                    @endphp
                                    
                                    @foreach($aeronavesComDados as $dados)
                                        @php
                                            $aeronave = $dados['aeronave'];
                                            $totalVoosAeronave = $dados['totalVoos'];
                                            $totalPassageirosAeronave = $dados['totalPassageiros'];
                                            $percentualPassageiros = $dados['percentualPassageiros'];
                                        @endphp
                                        <tr class="border-bottom">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3" style="width: 36px; height: 36px;">
                                                        <i class="bi bi-airplane-engines text-primary fs-6"></i>
                                                    </div>
                                                    <div>
                                                        <span class="fw-bold text-dark">{{ $aeronave->modelo }}</span>
                                                        @if($totalVoosAeronave > 0)
                                                            <br>
                                                            <small class="text-success">
                                                                <i class="bi bi-check-circle-fill"></i> em operação
                                                            </small>
                                                        @else
                                                            <br>
                                                            <small class="text-muted">
                                                                <i class="bi bi-clock"></i> sem voos
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-dark">{{ $aeronave->fabricante->nome ?? 'N/A' }}</span>
                                            </td>
                                            <td class="text-end">
                                                <span class="fw-bold text-dark">{{ number_format($aeronave->capacidade, 0, ',', '.') }}</span>
                                                <br>
                                                <small class="text-muted">passageiros</small>
                                            </td>
                                            <td class="text-end">
                                                <div>
                                                    <span class="fw-bold text-primary">{{ number_format($totalVoosAeronave, 0, ',', '.') }}</span>
                                                    <br>
                                                    @if($totalVoos > 0)
                                                        <small class="text-muted">{{ number_format(($totalVoosAeronave / $totalVoos) * 100, 1) }}% do total</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <div>
                                                    <span class="fw-bold text-info">{{ number_format($totalPassageirosAeronave, 0, ',', '.') }}</span>
                                                    <br>
                                                    <small class="text-muted">
                                                        @if($percentualPassageiros > 0)
                                                            {{ number_format($percentualPassageiros, 1) }}% do total
                                                        @else
                                                            sem dados
                                                        @endif
                                                    </small>
                                                    <br>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="border-top">
                                        <td colspan="5" class="pt-3">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="text-center">
                                                        <h6 class="text-muted small mb-1">Total de Aeronaves</h6>
                                                        <h4 class="fw-bold text-primary mb-0">{{ number_format($aeronaves->count(), 0, ',', '.') }}</h4>
                                                        <small class="text-muted">na frota</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="text-center">
                                                        <h6 class="text-muted small mb-1">Modelos Utilizados</h6>
                                                        @php
                                                            // Contar quantos modelos diferentes têm voos
                                                            $modelosUtilizados = 0;
                                                            foreach($aeronaves as $aeronave) {
                                                                $totalVoosModelo = $companhia->voos()
                                                                    ->where('aeronave_id', $aeronave->id)
                                                                    ->count();
                                                                if($totalVoosModelo > 0) {
                                                                    $modelosUtilizados++;
                                                                }
                                                            }
                                                            $totalModelosCadastrados = $aeronaves->unique('modelo')->count();
                                                            $percentualUtilizados = $totalModelosCadastrados > 0 
                                                                ? ($modelosUtilizados / $totalModelosCadastrados) * 100 
                                                                : 0;
                                                        @endphp
                                                        <h4 class="fw-bold text-info mb-0">
                                                            {{ number_format($modelosUtilizados, 0, ',', '.') }}/{{ number_format($totalModelosCadastrados, 0, ',', '.') }}
                                                        </h4>
                                                        <small class="text-muted">{{ number_format($percentualUtilizados, 1) }}% dos modelos cadastrados</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="text-center">
                                                        <h6 class="text-muted small mb-1">Capacidade Total</h6>
                                                        @php
                                                            $capacidadeTotal = $aeronaves->sum('capacidade');
                                                        @endphp
                                                        <h4 class="fw-bold text-success mb-0">{{ number_format($capacidadeTotal, 0, ',', '.') }}</h4>
                                                        <small class="text-muted">passageiros</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function atualizarFiltrosGlobais() {
            const periodo = document.getElementById('periodo').value;
            
            document.getElementById('filtroSemanalGlobal').style.display = 'none';
            document.getElementById('filtroMensalGlobal').style.display = 'none';
            document.getElementById('filtroAnualGlobal').style.display = 'none';
            
            if (periodo === 'semanal') {
                document.getElementById('filtroSemanalGlobal').style.display = 'block';
            } else if (periodo === 'mensal') {
                document.getElementById('filtroMensalGlobal').style.display = 'block';
            } else if (periodo === 'anual') {
                document.getElementById('filtroAnualGlobal').style.display = 'block';
            }
            
            if (periodo === 'geral') {
                document.getElementById('filtroGlobalForm').submit();
            }
        }
        
        function atualizarMesesDisponiveis(ano) {
            const mesSelect = document.getElementById('mes');
            if (ano) {
                mesSelect.disabled = false;
                if (mesSelect.value) {
                    document.getElementById('filtroGlobalForm').submit();
                }
            } else {
                mesSelect.disabled = true;
                mesSelect.value = '';
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const periodo = document.getElementById('periodo').value;
            if (periodo !== 'geral') {
                atualizarFiltrosGlobais();
            }
        });

        // ========== GRÁFICO DE VOOS POR HORÁRIO ==========
        const ctxVoosHorario = document.getElementById('voosHorarioChart');
        if (ctxVoosHorario) {
            const dadosVoosPorHorario = @json($voosPorHorario);
            
            // Ordenar os dados na sequência correta
            const horariosOrdenados = ['EAM', 'AM', 'AN', 'PM', 'ALL'];
            const dadosVoosOrdenados = horariosOrdenados.map(horario => {
                return dadosVoosPorHorario[horario] || 0;
            });

            // Calcular a MEDIANA de voos por horário
            const valoresVoosOrdenados = [...dadosVoosOrdenados].filter(v => v > 0).sort((a, b) => a - b);
            const countVoos = valoresVoosOrdenados.length;
            let medianaVoos = 0;
            
            if (countVoos > 0) {
                if (countVoos % 2 === 1) {
                    medianaVoos = valoresVoosOrdenados[Math.floor(countVoos / 2)];
                } else {
                    medianaVoos = (valoresVoosOrdenados[countVoos / 2 - 1] + valoresVoosOrdenados[countVoos / 2]) / 2;
                }
            }
            
            // Plugin para desenhar a linha da mediana com caixa de texto
            const medianLinePluginVoos = {
                id: 'medianLinePluginVoos',
                afterDraw(chart) {
                    const ctx = chart.ctx;
                    const yScale = chart.scales.y;
                    const xScale = chart.scales.x;

                    if (!yScale || !xScale || medianaVoos === 0) {
                        return;
                    }

                    const y = yScale.getPixelForValue(medianaVoos);

                    ctx.save();
                    ctx.strokeStyle = 'rgba(220, 20, 60, 0.85)';
                    ctx.lineWidth = 2;
                    ctx.setLineDash([6, 4]);
                    ctx.beginPath();
                    ctx.moveTo(xScale.left, y);
                    ctx.lineTo(xScale.right, y);
                    ctx.stroke();

                    const label = `Mediana: ${medianaVoos.toLocaleString('pt-BR', { maximumFractionDigits: 2 })}`;
                    ctx.font = '12px sans-serif';
                    ctx.textBaseline = 'middle';

                    const textWidth = ctx.measureText(label).width;
                    const padding = 6;
                    const rectHeight = 20;
                    
                    // Posicionar no lado direito
                    const rectX = xScale.right - textWidth - 2 * padding - 8;
                    const rectY = y - 12;

                    ctx.fillStyle = 'rgba(220, 20, 60, 0.8)';
                    ctx.fillRect(rectX, rectY, textWidth + 2 * padding, rectHeight);

                    ctx.fillStyle = '#ffffff';
                    ctx.fillText(label, rectX + padding, y);

                    ctx.restore();
                }
            };
            
            // Cores específicas para cada horário
            const coresHorario = {
                'EAM': 'rgba(74, 106, 138, 0.8)',   // Azul médio/escuro
                'AM': 'rgba(108, 155, 207, 0.8)',    // Azul claro
                'AN': 'rgba(255, 140, 0, 0.8)',      // Laranja
                'PM': 'rgba(220, 53, 69, 0.8)',      // Vermelho
                'ALL': 'rgba(155, 89, 182, 0.8)'     // Roxo
            };
            
            const coresVoosOrdenadas = horariosOrdenados.map(horario => coresHorario[horario]);

            new Chart(ctxVoosHorario, {
                type: 'bar',
                plugins: [medianLinePluginVoos],
                data: {
                    labels: horariosOrdenados,
                    datasets: [
                        {
                            label: 'Voos por Horário',
                            data: dadosVoosOrdenados,
                            backgroundColor: coresVoosOrdenadas,
                            borderColor: coresVoosOrdenadas.map(cor => cor.replace('0.8', '1')),
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                            order: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'rectRounded'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = {{ $totalVoos }};
                                    const percentual = total > 0 ? ((context.parsed.y / total) * 100).toFixed(1) : 0;
                                    
                                    const diferencaMediana = (context.parsed.y - medianaVoos).toFixed(1);
                                    const sinal = diferencaMediana >= 0 ? '+' : '';
                                    
                                    return [
                                        `Voos: ${context.parsed.y.toLocaleString('pt-BR')}`,
                                        `${percentual}% do total`,
                                        `${sinal}${diferencaMediana} em relação à mediana (${medianaVoos.toFixed(1)})`
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Quantidade de Voos'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('pt-BR');
                                }
                            },
                            grid: {
                                drawBorder: true
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Horário do Voo'
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false
                    }
                }
            });
        }

        // ========== GRÁFICO DE PASSAGEIROS POR HORÁRIO ==========
        const ctxPassageirosHorario = document.getElementById('passageirosHorarioChart');
        if (ctxPassageirosHorario) {
            const dadosPassageirosPorHorario = @json($passageirosPorHorario);
            
            const horariosOrdenados = ['EAM', 'AM', 'AN', 'PM', 'ALL'];
            const dadosPassageirosOrdenados = horariosOrdenados.map(horario => {
                return dadosPassageirosPorHorario[horario] || 0;
            });

            // Calcular a MEDIANA de passageiros por horário
            const valoresPassageirosOrdenados = [...dadosPassageirosOrdenados].filter(v => v > 0).sort((a, b) => a - b);
            const countPassageiros = valoresPassageirosOrdenados.length;
            let medianaPassageiros = 0;
            
            if (countPassageiros > 0) {
                if (countPassageiros % 2 === 1) {
                    medianaPassageiros = valoresPassageirosOrdenados[Math.floor(countPassageiros / 2)];
                } else {
                    medianaPassageiros = (valoresPassageirosOrdenados[countPassageiros / 2 - 1] + valoresPassageirosOrdenados[countPassageiros / 2]) / 2;
                }
            }
            
            // Plugin para desenhar a linha da mediana com caixa de texto
            const medianLinePluginPassageiros = {
                id: 'medianLinePluginPassageiros',
                afterDraw(chart) {
                    const ctx = chart.ctx;
                    const yScale = chart.scales.y;
                    const xScale = chart.scales.x;

                    if (!yScale || !xScale || medianaPassageiros === 0) {
                        return;
                    }

                    const y = yScale.getPixelForValue(medianaPassageiros);

                    ctx.save();
                    ctx.strokeStyle = 'rgba(220, 20, 60, 0.85)';
                    ctx.lineWidth = 2;
                    ctx.setLineDash([6, 4]);
                    ctx.beginPath();
                    ctx.moveTo(xScale.left, y);
                    ctx.lineTo(xScale.right, y);
                    ctx.stroke();

                    const label = `Mediana: ${medianaPassageiros.toLocaleString('pt-BR', { maximumFractionDigits: 2 })}`;
                    ctx.font = '12px sans-serif';
                    ctx.textBaseline = 'middle';

                    const textWidth = ctx.measureText(label).width;
                    const padding = 6;
                    const rectHeight = 20;
                    
                    // Posicionar no lado direito
                    const rectX = xScale.right - textWidth - 2 * padding - 8;
                    const rectY = y - 12;

                    ctx.fillStyle = 'rgba(220, 20, 60, 0.8)';
                    ctx.fillRect(rectX, rectY, textWidth + 2 * padding, rectHeight);

                    ctx.fillStyle = '#ffffff';
                    ctx.fillText(label, rectX + padding, y);

                    ctx.restore();
                }
            };
            
            const coresHorario = {
                'EAM': 'rgba(74, 106, 138, 0.8)',
                'AM': 'rgba(108, 155, 207, 0.8)',
                'AN': 'rgba(255, 140, 0, 0.8)',
                'PM': 'rgba(220, 53, 69, 0.8)',
                'ALL': 'rgba(155, 89, 182, 0.8)'
            };
            
            const coresPassageirosOrdenadas = horariosOrdenados.map(horario => coresHorario[horario]);

            new Chart(ctxPassageirosHorario, {
                type: 'bar',
                plugins: [medianLinePluginPassageiros],
                data: {
                    labels: horariosOrdenados,
                    datasets: [
                        {
                            label: 'Passageiros por Horário',
                            data: dadosPassageirosOrdenados,
                            backgroundColor: coresPassageirosOrdenadas,
                            borderColor: coresPassageirosOrdenadas.map(cor => cor.replace('0.8', '1')),
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                            order: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'rectRounded'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = {{ $totalPassageiros }};
                                    const percentual = total > 0 ? ((context.parsed.y / total) * 100).toFixed(1) : 0;
                                    
                                    const diferencaMediana = (context.parsed.y - medianaPassageiros).toFixed(1);
                                    const sinal = diferencaMediana >= 0 ? '+' : '';
                                    
                                    return [
                                        `Passageiros: ${context.parsed.y.toLocaleString('pt-BR')}`,
                                        `${percentual}% do total`,
                                        `${sinal}${diferencaMediana} em relação à mediana (${medianaPassageiros.toFixed(1)})`
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Quantidade de Passageiros'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString('pt-BR');
                                }
                            },
                            grid: {
                                drawBorder: true
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Horário do Voo'
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false
                    }
                }
            });
        }
    </script>
</body>
</html>