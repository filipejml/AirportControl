<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $aeronave->modelo }} - Dashboard</title>

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
        
        .progress {
            background-color: #e9ecef;
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
                        <h1 class="h2 fw-bold">Dashboard - {{ $aeronave->modelo }}</h1>
                        @if($aeronave->fabricante)
                            <p class="text-muted mb-0">
                                Fabricante: {{ $aeronave->fabricante->nome }} | 
                                Capacidade: {{ number_format($aeronave->capacidade, 0, ',', '.') }} passageiros | 
                                Porte: {{ $aeronave->porte_descricao }}
                            </p>
                        @endif
                    </div>
                    <a href="{{ route('aeronaves.informacoes') }}" class="btn btn-outline-secondary btn-sm">
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
                        <form method="GET" action="{{ route('aeronaves.dashboard', $aeronave->id) }}" 
                            id="filtroGlobalForm" class="row g-3 align-items-end">
                            
                            {{-- Filtro de Companhia Aérea --}}
                            <div class="col-md-4">
                                <label for="companhia" class="form-label fw-semibold small text-muted">
                                    <i class="bi bi-building"></i> Filtrar por Companhia Aérea:
                                </label>
                                <select name="companhia" id="companhia" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="geral">Visualização Geral</option>
                                    @foreach($companhiasDisponiveis as $companhia)
                                        <option value="{{ $companhia->id }}" {{ ($companhiaSelecionada ?? 'geral') == $companhia->id ? 'selected' : '' }}>
                                            {{ $companhia->nome }}
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
                                @if(($periodoSelecionado ?? 'geral') != 'geral' || ($companhiaSelecionada ?? 'geral') != 'geral')
                                    <a href="{{ route('aeronaves.dashboard', $aeronave->id) }}" class="btn btn-outline-secondary btn-sm w-100">
                                        <i class="bi bi-x-circle"></i> Limpar
                                    </a>
                                @endif
                            </div>
                        </form>
                        
                        {{-- Filtros Ativos --}}
                        @php
                            $temFiltroAtivo = false;
                            $filtrosAtivos = [];
                            
                            if (isset($companhiaSelecionada) && $companhiaSelecionada !== 'geral') {
                                $temFiltroAtivo = true;
                                $companhiaNome = $companhiasDisponiveis->firstWhere('id', $companhiaSelecionada);
                                $filtrosAtivos[] = "Companhia: " . ($companhiaNome->nome ?? $companhiaSelecionada);
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

        {{-- Cards de Estatísticas de Volume --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0d6efd !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-airplane text-primary fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold text-dark">Total de Voos</h6>
                                <h4 class="mb-0 fw-bold text-primary">{{ number_format($totalVoos, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        <small class="text-muted">Voos realizados com esta aeronave</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0dcaf0 !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-people text-info fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold text-dark">Total de Passageiros</h6>
                                <h4 class="mb-0 fw-bold text-info">{{ number_format($totalPassageiros, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        <small class="text-muted">Passageiros transportados</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #198754 !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-building text-success fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold text-dark">Companhias Operadoras</h6>
                                <h4 class="mb-0 fw-bold text-success">{{ number_format($totalCompanhias, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        <small class="text-muted">Companhias que utilizam este modelo</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                <i class="bi bi-geo-alt text-warning fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold text-dark">Aeroportos Atendidos</h6>
                                <h4 class="mb-0 fw-bold text-warning">{{ number_format($totalAeroportos, 0, ',', '.') }}</h4>
                            </div>
                        </div>
                        <small class="text-muted">Aeroportos onde operou</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Título da Seção de Desempenho --}}
        <div class="row mb-3">
            <div class="col-12">
                <h5 class="fw-semibold mb-0">Desempenho por Categoria</h5>
                <p class="text-muted small">Médias das avaliações dos voos (escala de 0-10)</p>
            </div>
        </div>

        {{-- Cards de Desempenho por Categoria --}}
        <div class="row g-3 mb-4">
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

        {{-- Voos por Companhia --}}
        <div class="row mb-4">
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1 fw-bold text-dark">
                                    <i class="bi bi-building text-primary me-2"></i>Voos por Companhia
                                </h5>
                                <p class="text-muted small mb-0">Distribuição de voos por companhia aérea</p>
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
                                        <th class="text-muted small fw-normal">Companhia</th>
                                        <th class="text-muted small fw-normal text-end">Voos</th>
                                        <th class="text-muted small fw-normal text-end" style="width: 80px;">%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $contador = 0; @endphp
                                    @foreach($voosPorCompanhia as $companhiaNome => $quantidade)
                                        @php
                                            $percentual = $totalVoos > 0 ? ($quantidade / $totalVoos) * 100 : 0;
                                            $contador++;
                                        @endphp
                                        <tr>
                                            <td>{{ $companhiaNome }}</td>
                                            <td class="text-end fw-bold text-primary">{{ number_format($quantidade, 0, ',', '.') }}</td>
                                            <td class="text-end">
                                                <div class="d-flex align-items-center justify-content-end">
                                                    <span class="text-muted small me-2">{{ number_format($percentual, 1) }}%</span>
                                                    <div class="progress flex-grow-1" style="height: 6px; width: 60px;">
                                                        <div class="progress-bar bg-primary" style="width: {{ $percentual }}%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Passageiros por Companhia --}}
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1 fw-bold text-dark">
                                    <i class="bi bi-people text-info me-2"></i>Passageiros por Companhia
                                </h5>
                                <p class="text-muted small mb-0">Distribuição de passageiros por companhia aérea</p>
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
                                        <th class="text-muted small fw-normal">Companhia</th>
                                        <th class="text-muted small fw-normal text-end">Passageiros</th>
                                        <th class="text-muted small fw-normal text-end" style="width: 80px;">%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $contador = 0; @endphp
                                    @foreach($passageirosPorCompanhia as $companhiaNome => $quantidade)
                                        @php
                                            $percentual = $totalPassageiros > 0 ? ($quantidade / $totalPassageiros) * 100 : 0;
                                            $contador++;
                                        @endphp
                                        <tr>
                                            <td>{{ $companhiaNome }}</td>
                                            <td class="text-end fw-bold text-info">{{ number_format($quantidade, 0, ',', '.') }}</td>
                                            <td class="text-end">
                                                <div class="d-flex align-items-center justify-content-end">
                                                    <span class="text-muted small me-2">{{ number_format($percentual, 1) }}%</span>
                                                    <div class="progress flex-grow-1" style="height: 6px; width: 60px;">
                                                        <div class="progress-bar bg-info" style="width: {{ $percentual }}%"></div>
                                                    </div>
                                                </div>
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

        {{-- Últimos Voos --}}
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
                                <p class="text-muted small mb-0">Últimos 5 registros de voos com esta aeronave</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-3">
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless align-middle">
                                <thead>
                                    <tr class="border-bottom">
                                        <th class="text-muted small fw-normal">ID Voo</th>
                                        <th class="text-muted small fw-normal">Companhia</th>
                                        <th class="text-muted small fw-normal">Aeroporto</th>
                                        <th class="text-muted small fw-normal text-center">Objetivo</th>
                                        <th class="text-muted small fw-normal text-center">Pontualidade</th>
                                        <th class="text-muted small fw-normal text-center">Serviços</th>
                                        <th class="text-muted small fw-normal text-center">Pátio</th>
                                        <th class="text-muted small fw-normal text-end">Passageiros</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ultimosVoos as $voo)
                                    <tr class="border-bottom">
                                        <td><span class="fw-bold text-dark">{{ $voo->id_voo }}</span></td>
                                        <td>{{ $voo->companhiaAerea->nome ?? 'N/A' }}</td>
                                        <td>{{ $voo->aeroporto->nome_aeroporto ?? 'N/A' }}</td>
                                        <td class="text-center">
                                            @php
                                                $corObj = $voo->nota_obj >= 8 ? 'success' : ($voo->nota_obj >= 6 ? 'warning' : 'danger');
                                            @endphp
                                            <span class="fw-bold text-{{ $corObj }}">{{ number_format($voo->nota_obj, 1) }}</span>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $corPont = $voo->nota_pontualidade >= 8 ? 'success' : ($voo->nota_pontualidade >= 6 ? 'warning' : 'danger');
                                            @endphp
                                            <span class="fw-bold text-{{ $corPont }}">{{ number_format($voo->nota_pontualidade, 1) }}</span>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $corServ = $voo->nota_servicos >= 8 ? 'success' : ($voo->nota_servicos >= 6 ? 'warning' : 'danger');
                                            @endphp
                                            <span class="fw-bold text-{{ $corServ }}">{{ number_format($voo->nota_servicos, 1) }}</span>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $corPat = $voo->nota_patio >= 8 ? 'success' : ($voo->nota_patio >= 6 ? 'warning' : 'danger');
                                            @endphp
                                            <span class="fw-bold text-{{ $corPat }}">{{ number_format($voo->nota_patio, 1) }}</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold text-dark">{{ number_format($voo->total_passageiros, 0, ',', '.') }}</span>
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
</body>
</html>