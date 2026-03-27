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
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <div>
                                                <h6 class="fw-bold text-dark mb-1">
                                                    <i class="bi bi-airplane text-primary me-2"></i>Voos por Horário
                                                </h6>
                                                <p class="text-muted small mb-0">Distribuição de voos ao longo do dia</p>
                                            </div>
                                            <span class="badge bg-primary fs-6 py-2 px-3">
                                                <i class="bi bi-airplane me-1"></i>
                                                {{ number_format($totalVoos, 0, ',', '.') }}
                                            </span>
                                        </div>

                                        {{-- Gráfico de Barras --}}
                                        <div class="mb-4">
                                            <canvas id="voosHorarioChart" height="150"></canvas>
                                        </div>

                                        {{-- Tabela de Dados --}}
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Horário</th>
                                                        <th class="text-end">Voos</th>
                                                        <th class="text-end" style="width: 100px;">%</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $totalVoosHorario = array_sum($voosPorHorario);
                                                        $horariosInfo = [
                                                            'EAM' => ['label' => 'EAM (00h-06h)', 'icon' => '🌙'],
                                                            'AM' => ['label' => 'AM (06h-12h)', 'icon' => '☀️'],
                                                            'AN' => ['label' => 'AN (12h-18h)', 'icon' => '🌤️'],
                                                            'PM' => ['label' => 'PM (18h-00h)', 'icon' => '🌙'],
                                                            'ALL' => ['label' => 'ALL (Diário)', 'icon' => '📅']
                                                        ];
                                                    @endphp
                                                    @foreach($voosPorHorario as $horario => $quantidade)
                                                        @php
                                                            $percentual = $totalVoosHorario > 0 ? ($quantidade / $totalVoosHorario) * 100 : 0;
                                                            $info = $horariosInfo[$horario] ?? ['label' => $horario, 'icon' => '✈️'];
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="me-2">{{ $info['icon'] }}</span>
                                                                    <strong>{{ $info['label'] }}</strong>
                                                                </div>
                                                            </td>
                                                            <td class="text-end">
                                                                <span class="fw-bold text-primary">
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
                                                    @endforeach
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr class="fw-bold">
                                                        <td>Total</td>
                                                        <td class="text-end">{{ number_format($totalVoosHorario, 0, ',', '.') }}</td>
                                                        <td class="text-end">100%</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>

                                        {{-- Resumo Estatístico --}}
                                        <div class="row mt-3 pt-2 border-top">
                                            <div class="col-12">
                                                <div class="d-flex justify-content-around">
                                                    <div class="text-center">
                                                        <h6 class="text-muted small mb-1">Média de Voos</h6>
                                                        @php
                                                            $mediaVoos = count(array_filter($voosPorHorario)) > 0 ? $totalVoosHorario / count(array_filter($voosPorHorario)) : 0;
                                                        @endphp
                                                        <h4 class="fw-bold text-primary mb-0">{{ number_format($mediaVoos, 1) }}</h4>
                                                        <small class="text-muted">voos por horário</small>
                                                    </div>
                                                    <div class="text-center">
                                                        <h6 class="text-muted small mb-1">Horário com mais voos</h6>
                                                        @php
                                                            $maxHorario = array_keys($voosPorHorario, max($voosPorHorario))[0] ?? 'N/A';
                                                            $maxLabel = $horariosInfo[$maxHorario]['label'] ?? $maxHorario;
                                                        @endphp
                                                        <h4 class="fw-bold text-primary mb-0">{{ $maxLabel }}</h4>
                                                        <small class="text-muted">{{ number_format(max($voosPorHorario), 0, ',', '.') }} voos</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Passageiros por Horário --}}
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <div>
                                                <h6 class="fw-bold text-dark mb-1">
                                                    <i class="bi bi-people text-info me-2"></i>Passageiros por Horário
                                                </h6>
                                                <p class="text-muted small mb-0">Distribuição de passageiros ao longo do dia</p>
                                            </div>
                                            <span class="badge bg-info fs-6 py-2 px-3">
                                                <i class="bi bi-people me-1"></i>
                                                {{ number_format($totalPassageiros, 0, ',', '.') }}
                                            </span>
                                        </div>

                                        {{-- Gráfico de Barras --}}
                                        <div class="mb-4">
                                            <canvas id="passageirosHorarioChart" height="150"></canvas>
                                        </div>

                                        {{-- Tabela de Dados --}}
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Horário</th>
                                                        <th class="text-end">Passageiros</th>
                                                        <th class="text-end" style="width: 100px;">%</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $totalPassageirosHorario = array_sum($passageirosPorHorario);
                                                    @endphp
                                                    @foreach($passageirosPorHorario as $horario => $quantidade)
                                                        @php
                                                            $percentual = $totalPassageirosHorario > 0 ? ($quantidade / $totalPassageirosHorario) * 100 : 0;
                                                            $info = $horariosInfo[$horario] ?? ['label' => $horario, 'icon' => '✈️'];
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <span class="me-2">{{ $info['icon'] }}</span>
                                                                    <strong>{{ $info['label'] }}</strong>
                                                                </div>
                                                            </td>
                                                            <td class="text-end">
                                                                <span class="fw-bold text-info">
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
                                                    @endforeach
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr class="fw-bold">
                                                        <td>Total</td>
                                                        <td class="text-end">{{ number_format($totalPassageirosHorario, 0, ',', '.') }}</td>
                                                        <td class="text-end">100%</td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>

                                        {{-- Resumo Estatístico --}}
                                        <div class="row mt-3 pt-2 border-top">
                                            <div class="col-12">
                                                <div class="d-flex justify-content-around">
                                                    <div class="text-center">
                                                        <h6 class="text-muted small mb-1">Média de Passageiros</h6>
                                                        @php
                                                            $mediaPassageirosHorario = count(array_filter($passageirosPorHorario)) > 0 ? $totalPassageirosHorario / count(array_filter($passageirosPorHorario)) : 0;
                                                        @endphp
                                                        <h4 class="fw-bold text-info mb-0">{{ number_format($mediaPassageirosHorario, 0, ',', '.') }}</h4>
                                                        <small class="text-muted">passageiros por horário</small>
                                                    </div>
                                                    <div class="text-center">
                                                        <h6 class="text-muted small mb-1">Horário com mais passageiros</h6>
                                                        @php
                                                            $maxHorarioPass = array_keys($passageirosPorHorario, max($passageirosPorHorario))[0] ?? 'N/A';
                                                            $maxLabelPass = $horariosInfo[$maxHorarioPass]['label'] ?? $maxHorarioPass;
                                                        @endphp
                                                        <h4 class="fw-bold text-info mb-0">{{ $maxLabelPass }}</h4>
                                                        <small class="text-muted">{{ number_format(max($passageirosPorHorario), 0, ',', '.') }} passageiros</small>
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
        </div>

        <!-- Últimos Voos -->
        @if($ultimosVoos->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2"></i>Últimos Voos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>ID Voo</th>
                                        <th>Aeroporto</th>
                                        <th>Data</th>
                                        <th>Passageiros</th>
                                        <th>Média</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ultimosVoos as $voo)
                                    <tr>
                                        <td><span class="badge bg-secondary">{{ $voo->id_voo }}</span></td>
                                        <td>{{ $voo->aeroporto->nome_aeroporto ?? 'N/A' }}</td>
                                        <td>{{ $voo->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ number_format($voo->total_passageiros, 0, ',', '.') }}</td>
                                        <td>
                                            @if($voo->media_notas)
                                                <span class="badge bg-info">{{ number_format($voo->media_notas, 1) }}/10</span>
                                            @else
                                                <span class="text-muted">-</span>
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
                <div class="card border shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-semibold"><i class="bi bi-building me-2"></i>Frota de Aeronaves</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Modelo</th>
                                        <th>Fabricante</th>
                                        <th>Capacidade</th>
                                        <th>Ano</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($aeronaves as $aeronave)
                                    <tr>
                                        <td><strong>{{ $aeronave->modelo }}</strong></td>
                                        <td>{{ $aeronave->fabricante->nome ?? 'N/A' }}NonNull
                                        <td>{{ number_format($aeronave->capacidade, 0, ',', '.') }} passageirosNonNull
                                        <td>{{ $aeronave->ano_fabricacao ?? 'N/A' }}NonNull
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
    </script>
    
    <script>
        // Gráfico de Voos por Horário
        const voosHorarioCtx = document.getElementById('voosHorarioChart').getContext('2d');
        const voosHorarioData = @json($voosPorHorario);
        const horariosLabels = {
            'EAM': 'EAM (00h-06h)',
            'AM': 'AM (06h-12h)',
            'AN': 'AN (12h-18h)',
            'PM': 'PM (18h-00h)',
            'ALL': 'ALL (Diário)'
        };
        
        new Chart(voosHorarioCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(voosHorarioData).map(key => horariosLabels[key]),
                datasets: [{
                    label: 'Quantidade de Voos',
                    data: Object.values(voosHorarioData),
                    backgroundColor: 'rgba(13, 110, 253, 0.7)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('pt-BR').format(context.parsed.y);
                                }
                                return label;
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
                            stepSize: 1
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Horário do Voo'
                        }
                    }
                }
            }
        });
        
        // Gráfico de Passageiros por Horário
        const passageirosHorarioCtx = document.getElementById('passageirosHorarioChart').getContext('2d');
        const passageirosHorarioData = @json($passageirosPorHorario);
        
        new Chart(passageirosHorarioCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(passageirosHorarioData).map(key => horariosLabels[key]),
                datasets: [{
                    label: 'Quantidade de Passageiros',
                    data: Object.values(passageirosHorarioData),
                    backgroundColor: 'rgba(13, 202, 240, 0.7)',
                    borderColor: 'rgba(13, 202, 240, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('pt-BR').format(context.parsed.y);
                                }
                                return label;
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
                            stepSize: 1
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Horário do Voo'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>