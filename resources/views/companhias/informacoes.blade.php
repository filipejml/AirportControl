@extends('layouts.app')

@section('title', 'Informações das companhias')

@push('styles')
    <style>
        .companies-info-page {
            --info-ink: #172033;
            --info-muted: #667085;
            --info-border: #e4e7ec;
            color: var(--info-ink);
        }

        .info-hero {
            position: relative;
            overflow: hidden;
            padding: clamp(1.5rem, 4vw, 2.5rem);
            border-radius: 1.5rem;
            color: #fff;
            background:
                radial-gradient(circle at 88% 15%, rgba(255,255,255,.16), transparent 24%),
                linear-gradient(125deg, #101828 0%, #1849a9 60%, #2e90fa 100%);
            box-shadow: 0 20px 45px rgba(16, 24, 40, .15);
        }

        .hero-symbol {
            display: grid;
            width: 3.75rem;
            height: 3.75rem;
            place-items: center;
            flex: 0 0 auto;
            border: 1px solid rgba(255,255,255,.28);
            border-radius: 1.1rem;
            background: rgba(255,255,255,.12);
            font-size: 1.55rem;
        }

        .info-hero .hero-actions .btn {
            border-radius: .75rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .info-hero .hero-actions .btn-outline-light:hover {
            color: #1849a9;
        }

        .summary-card {
            height: 100%;
            padding: 1.1rem 1.2rem;
            border: 1px solid var(--info-border);
            border-radius: 1rem;
            background: #fff;
            box-shadow: 0 8px 22px rgba(16, 24, 40, .045);
        }

        .summary-icon {
            display: grid;
            width: 2.55rem;
            height: 2.55rem;
            place-items: center;
            flex: 0 0 auto;
            border-radius: .75rem;
            font-size: 1.05rem;
        }

        .summary-value {
            font-size: 1.55rem;
            font-weight: 750;
            line-height: 1;
            letter-spacing: -.03em;
        }

        .filter-panel {
            padding: 1.3rem;
            border: 1px solid var(--info-border);
            border-radius: 1.15rem;
            background: #fff;
            box-shadow: 0 8px 24px rgba(16, 24, 40, .04);
        }

        .filter-panel .filter-select-button {
            min-height: 2.75rem;
            padding: .55rem 2.4rem .55rem .9rem;
            border: 1px solid #d0d5dd;
            border-radius: .75rem;
            color: var(--info-ink);
            background: #fff;
            text-align: left;
        }

        .filter-panel .filter-select-button:hover,
        .filter-panel .filter-select-button.show {
            border-color: #98a2b3;
            color: var(--info-ink);
            background: #fff;
        }

        .filter-panel .filter-select-button:focus {
            border-color: #2e90fa;
            box-shadow: 0 0 0 .25rem rgba(46, 144, 250, .14);
        }

        .filter-panel .filter-select-button::after {
            position: absolute;
            top: 50%;
            right: 1rem;
            transform: translateY(-50%);
        }

        .filter-panel .filter-options {
            width: 100%;
            max-height: 18rem;
            padding: .4rem;
            overflow-y: auto;
            border: 1px solid #e4e7ec;
            border-radius: 1rem;
            box-shadow: 0 14px 32px rgba(16, 24, 40, .16);
        }

        .filter-panel .filter-options .dropdown-item {
            padding: .65rem .8rem;
            border-radius: .7rem;
            white-space: normal;
        }

        .active-filters {
            padding: .8rem 1rem;
            border: 1px solid #b2ddff;
            border-radius: .9rem;
            background: #f0f9ff;
        }
        
        .hover-shadow {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        
        .progress {
            background-color: #e9ecef;
        }
        
        /* Estilo para cards sem dados */
        .card-sem-dados {
            opacity: 0.7;
            filter: grayscale(0.1);
        }
        
        .card-sem-dados:hover {
            opacity: 0.85;
            filter: grayscale(0.05);
        }

        @media (max-width: 767.98px) {
            .info-hero { border-radius: 1.15rem; }
        }
    </style>
@endpush

@section('content')
    <div class="companies-info-page pb-5">
        <header class="info-hero mb-4">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
                <div class="d-flex align-items-center gap-3">
                    <span class="hero-symbol" aria-hidden="true"><i class="bi bi-bar-chart-line"></i></span>
                    <div>
                        <div class="small text-white-50 fw-semibold text-uppercase mb-1">Visão operacional</div>
                        <h1 class="h2 fw-bold mb-1">Informações das companhias</h1>
                        <p class="mb-0 text-white-50">Compare frota, movimentação e desempenho das companhias aéreas.</p>
                    </div>
                </div>

                <nav class="hero-actions d-flex flex-wrap gap-2" aria-label="Navegação de companhias">
                    <a href="{{ route('companhias.ranking') }}" class="btn btn-light">
                        <i class="bi bi-trophy me-1"></i> Ranking
                    </a>
                    @if(auth()->user()?->tipo == 0)
                        <a href="{{ route('companhias.index') }}" class="btn btn-outline-light">
                            <i class="bi bi-gear me-1"></i> Gerenciar companhias
                        </a>
                    @endif
                </nav>
            </div>
        </header>

        <section class="row g-3 mb-4" aria-label="Resumo das companhias">
            <div class="col-6 col-xl-3">
                <div class="summary-card d-flex align-items-center gap-3">
                    <span class="summary-icon text-primary bg-primary-subtle"><i class="bi bi-buildings"></i></span>
                    <div><div class="summary-value">{{ number_format($totalCompanhias, 0, ',', '.') }}</div><div class="small text-muted">Companhias</div></div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="summary-card d-flex align-items-center gap-3">
                    <span class="summary-icon text-info bg-info-subtle"><i class="bi bi-airplane"></i></span>
                    <div><div class="summary-value">{{ number_format($totalVoos, 0, ',', '.') }}</div><div class="small text-muted">Voos</div></div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="summary-card d-flex align-items-center gap-3">
                    <span class="summary-icon text-success bg-success-subtle"><i class="bi bi-people"></i></span>
                    <div><div class="summary-value">{{ number_format($totalPassageiros, 0, ',', '.') }}</div><div class="small text-muted">Passageiros</div></div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="summary-card d-flex align-items-center gap-3">
                    <span class="summary-icon text-warning bg-warning-subtle"><i class="bi bi-star-fill"></i></span>
                    <div><div class="summary-value">{{ number_format($mediaGeralNotas, 1, ',', '.') }}</div><div class="small text-muted">Média geral</div></div>
                </div>
            </div>
        </section>

        {{-- Filtros --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="filter-panel">
                    <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                        <div>
                            <h2 class="h5 fw-bold mb-1"><i class="bi bi-funnel me-1 text-primary"></i> Filtros</h2>
                            <p class="small text-muted mb-0">Refine as companhias exibidas no catálogo.</p>
                        </div>
                    </div>
                        <form method="GET" action="{{ route('companhias.informacoes') }}" class="row g-2 align-items-end">
                            {{-- Filtro por Companhia --}}
                            <div class="col-md-4">
                                <label for="filtro_companhia" class="form-label fw-semibold">
                                    Companhia
                                </label>
                                @php $companhiaAtual = $companhiasFiltro->firstWhere('id', request('companhia')); @endphp
                                <input type="hidden" name="companhia" id="filtro_companhia" value="{{ request('companhia') }}">
                                <div class="dropdown">
                                    <button class="btn filter-select-button dropdown-toggle w-100 position-relative" type="button" data-bs-toggle="dropdown">
                                        {{ $companhiaAtual?->nome ?? 'Todas as Companhias' }}
                                    </button>
                                    <ul class="dropdown-menu filter-options">
                                        <li><button type="button" class="dropdown-item {{ request('companhia') ? '' : 'active' }}" data-filter-target="filtro_companhia" data-filter-value="">Todas as Companhias</button></li>
                                        @foreach($companhiasFiltro as $companhia)
                                            <li><button type="button" class="dropdown-item {{ request('companhia') == $companhia->id ? 'active' : '' }}" data-filter-target="filtro_companhia" data-filter-value="{{ $companhia->id }}">{{ $companhia->nome }}</button></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            {{-- Filtro por Aeroporto --}}
                            <div class="col-md-3">
                                <label for="filtro_aeroporto" class="form-label fw-semibold">
                                    Aeroporto
                                </label>
                                @php $aeroportoAtual = $aeroportos->firstWhere('id', request('aeroporto')); @endphp
                                <input type="hidden" name="aeroporto" id="filtro_aeroporto" value="{{ request('aeroporto') }}">
                                <div class="dropdown">
                                    <button class="btn filter-select-button dropdown-toggle w-100 position-relative" type="button" data-bs-toggle="dropdown">
                                        {{ $aeroportoAtual?->nome_aeroporto ?? 'Todos os Aeroportos' }}
                                    </button>
                                    <ul class="dropdown-menu filter-options">
                                        <li><button type="button" class="dropdown-item {{ request('aeroporto') ? '' : 'active' }}" data-filter-target="filtro_aeroporto" data-filter-value="">Todos os Aeroportos</button></li>
                                        @foreach($aeroportos as $aeroporto)
                                            <li><button type="button" class="dropdown-item {{ request('aeroporto') == $aeroporto->id ? 'active' : '' }}" data-filter-target="filtro_aeroporto" data-filter-value="{{ $aeroporto->id }}">{{ $aeroporto->nome_aeroporto }}</button></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            {{-- Filtro de Ordenação --}}
                            <div class="col-md-3">
                                <label for="filtro_ordenacao" class="form-label fw-semibold">
                                    Ordenar por
                                </label>
                                @php
                                    $ordenacoesFiltro = [
                                        'nome_az' => 'Ordenar por Nome (A-Z)',
                                        'nome_za' => 'Ordenar por Nome (Z-A)',
                                        'mais_voos' => 'Mais Voos',
                                        'mais_passageiros' => 'Mais Passageiros',
                                        'melhor_objetivo' => 'Melhor Nota Objetivo',
                                        'melhor_pontualidade' => 'Melhor Nota Pontualidade',
                                        'melhor_servicos' => 'Melhor Nota Serviços',
                                        'melhor_patio' => 'Melhor Nota Pátio',
                                    ];
                                    $ordenacaoAtual = request('ordenacao', 'nome_az');
                                @endphp
                                <input type="hidden" name="ordenacao" id="filtro_ordenacao" value="{{ $ordenacaoAtual }}">
                                <div class="dropdown">
                                    <button class="btn filter-select-button dropdown-toggle w-100 position-relative" type="button" data-bs-toggle="dropdown">
                                        {{ $ordenacoesFiltro[$ordenacaoAtual] ?? $ordenacoesFiltro['nome_az'] }}
                                    </button>
                                    <ul class="dropdown-menu filter-options">
                                        @foreach($ordenacoesFiltro as $valor => $rotulo)
                                            <li><button type="button" class="dropdown-item {{ $ordenacaoAtual === $valor ? 'active' : '' }}" data-filter-target="filtro_ordenacao" data-filter-value="{{ $valor }}">{{ $rotulo }}</button></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            {{-- Botão Limpar Filtros --}}
                            <div class="col-md-2">
                                <label class="form-label d-md-block d-none">&nbsp;</label>
                                @if(request('companhia') || request('aeroporto') || request('ordenacao'))
                                    <a href="{{ route('companhias.informacoes') }}" class="btn btn-outline-secondary w-100">
                                        <i class="bi bi-x-circle"></i> Limpar
                                    </a>
                                @else
                                    <div class="d-md-block d-none">&nbsp;</div>
                                @endif
                            </div>
                        </form>
                </div>
            </div>
        </div>

        {{-- Indicador de Filtros Ativos --}}
        @if(request('companhia') || request('aeroporto') || request('ordenacao'))
        <div class="row mb-3">
            <div class="col-12">
                <div class="active-filters d-flex align-items-center">
                    <i class="bi bi-funnel me-2"></i>
                    <div>
                        <strong>Filtros ativos:</strong>
                        @if(request('companhia'))
                            @php
                                $companhiaSelecionada = $companhiasFiltro->firstWhere('id', request('companhia'));
                            @endphp
                            <span class="badge bg-primary ms-1">Companhia: {{ $companhiaSelecionada->nome ?? request('companhia') }}</span>
                        @endif
                        @if(request('aeroporto'))
                            @php
                                $aeroportoSelecionado = $aeroportos->firstWhere('id', request('aeroporto'));
                            @endphp
                            <span class="badge bg-info ms-1">Aeroporto: {{ $aeroportoSelecionado->nome_aeroporto ?? request('aeroporto') }}</span>
                        @endif
                        @if(request('ordenacao'))
                            @php
                                $opcoesOrdenacao = [
                                    'nome_az' => 'Ordenar por Nome (A-Z)',
                                    'nome_za' => 'Ordenar por Nome (Z-A)',
                                    'mais_voos' => 'Mais Voos',
                                    'mais_passageiros' => 'Mais Passageiros',
                                    'melhor_objetivo' => 'Melhor Nota Objetivo',
                                    'melhor_pontualidade' => 'Melhor Nota Pontualidade',
                                    'melhor_servicos' => 'Melhor Nota Serviços',
                                    'melhor_patio' => 'Melhor Nota Patio'
                                ];
                            @endphp
                            <span class="badge bg-success ms-1">Ordenação: {{ $opcoesOrdenacao[request('ordenacao')] ?? request('ordenacao') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Separador visual entre companhias com e sem dados --}}
        @php
            $hasDataCompanies = $companhias->filter(fn($c) => $c->voos_count > 0);
            $noDataCompanies = $companhias->filter(fn($c) => $c->voos_count == 0);
        @endphp

        {{-- Companhias com dados --}}
        @if($hasDataCompanies->count() > 0)
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex align-items-center">
                    <div class="bg-success rounded-circle p-2 me-2" style="width: 8px; height: 8px;"></div>
                    <h5 class="mb-0 fw-semibold text-success">Companhias Ativas</h5>
                    <span class="badge bg-success ms-2">{{ $hasDataCompanies->count() }}</span>
                    <small class="text-muted ms-3">Companhias com registros de voos</small>
                </div>
                <hr class="mt-2 mb-0">
            </div>
        </div>

        <div class="row">
            @foreach($hasDataCompanies as $companhia)
                @php
                    $temRegistros = $companhia->voos_count > 0;
                    $notaMedia = $companhia->media_notas ?? 0;
                    $borderColor = $temRegistros 
                        ? ($notaMedia >= 7 ? '#198754' : ($notaMedia >= 5 ? '#fd7e14' : '#dc3545'))
                        : '#6c757d';
                @endphp

                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm hover-shadow" 
                        style="border-left: 5px solid {{ $borderColor }}; transition: transform 0.3s;">
                        
                        {{-- Cabeçalho com nome da companhia --}}
                        <div class="card-header bg-white border-bottom-0 pb-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title mb-1 fw-bold">{{ $companhia->nome }}</h5>
                                    @if($companhia->codigo)
                                        <small class="text-muted d-block">{{ $companhia->codigo }}</small>
                                    @endif
                                </div>
                                <span class="badge bg-primary">
                                    {{ $companhia->aeronaves_count }} aeronaves
                                </span>
                            </div>
                            @if(request('aeroporto'))
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt me-1"></i>Aeroporto: {{ request('aeroporto') }}
                                </small>
                            @endif
                        </div>

                        {{-- Corpo do card --}}
                        <div class="card-body pt-0">
                            {{-- Descrição/Metadados --}}
                            <div class="mb-3">
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    {{ $companhia->aeroportos_com_voos->count() }} aeroportos operados
                                </p>
                            </div>

                            {{-- Estatísticas principais --}}
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="p-2 border rounded text-center">
                                        <i class="bi bi-airplane-fill text-primary fs-5"></i>
                                        <h6 class="mb-0 mt-1 fw-bold">{{ number_format($companhia->voos_count, 0, ',', '.') }}</h6>
                                        <small class="text-muted">Voos</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 border rounded text-center">
                                        <i class="bi bi-people-fill text-success fs-5"></i>
                                        <h6 class="mb-0 mt-1 fw-bold">{{ number_format($companhia->total_passageiros, 0, ',', '.') }}</h6>
                                        <small class="text-muted">Passageiros</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Médias das notas --}}
                            <div class="border-top pt-3">
                                <h6 class="mb-2 fw-semibold">
                                    <i class="bi bi-star-fill me-1 text-warning"></i>Desempenho
                                </h6>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-2">
                                                <i class="bi bi-flag-fill text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block">Objetivo</small>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-primary" 
                                                        style="width: {{ ($companhia->nota_obj / 10) * 100 }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ number_format($companhia->nota_obj, 1) }}/10</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-2">
                                                <i class="bi bi-clock-fill text-success"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block">Pontualidade</small>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-success" 
                                                        style="width: {{ ($companhia->nota_pontualidade / 10) * 100 }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ number_format($companhia->nota_pontualidade, 1) }}/10</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-2">
                                                <i class="bi bi-gear-fill text-info"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block">Serviços</small>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-info" 
                                                        style="width: {{ ($companhia->nota_servicos / 10) * 100 }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ number_format($companhia->nota_servicos, 1) }}/10</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-2">
                                                <i class="bi bi-pin-fill text-warning"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="text-muted d-block">Pátio</small>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-warning" 
                                                        style="width: {{ ($companhia->nota_patio / 10) * 100 }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ number_format($companhia->nota_patio, 1) }}/10</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Nota média geral --}}
                                <div class="mt-2 text-center">
                                    <span class="badge" style="background-color: {{ $borderColor }}; color: white;">
                                        <i class="bi bi-star-fill me-1"></i>Média Geral: {{ number_format($notaMedia, 1) }}/10
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Rodapé com botões de ação --}}
                        <div class="card-footer bg-white border-top-0 pt-0">
                            <a href="{{ route('companhias.dashboard', $companhia->id) }}" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bi bi-graph-up me-1"></i> Ver Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        {{-- Companhias sem dados --}}
        @if($noDataCompanies->count() > 0)
        <div class="row mb-3 mt-4">
            <div class="col-12">
                <div class="d-flex align-items-center">
                    <div class="bg-secondary rounded-circle p-2 me-2" style="width: 8px; height: 8px;"></div>
                    <h5 class="mb-0 fw-semibold text-secondary">Companhias sem Registros</h5>
                    <span class="badge bg-secondary ms-2">{{ $noDataCompanies->count() }}</span>
                    <small class="text-muted ms-3">Companhias cadastradas sem voos realizados</small>
                </div>
                <hr class="mt-2 mb-0">
            </div>
        </div>

        <div class="row">
            @foreach($noDataCompanies as $companhia)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm hover-shadow card-sem-dados" 
                        style="border-left: 5px solid #6c757d; transition: transform 0.3s;">
                        
                        {{-- Cabeçalho com nome da companhia --}}
                        <div class="card-header bg-white border-bottom-0 pb-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title mb-1 fw-bold">{{ $companhia->nome }}</h5>
                                    <span class="badge bg-secondary">Sem registros</span>
                                    @if($companhia->codigo)
                                        <small class="text-muted d-block mt-1">{{ $companhia->codigo }}</small>
                                    @endif
                                </div>
                                <span class="badge bg-light text-dark">
                                    {{ $companhia->aeronaves_count }} aeronaves
                                </span>
                            </div>
                            @if(request('aeroporto'))
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt me-1"></i>Aeroporto: {{ request('aeroporto') }}
                                </small>
                            @endif
                        </div>

                        {{-- Corpo do card --}}
                        <div class="card-body pt-0">
                            {{-- Descrição/Metadados --}}
                            <div class="mb-3">
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    {{ $companhia->aeroportos_com_voos->count() }} aeroportos operados
                                </p>
                            </div>

                            {{-- Estatísticas principais (vazias) --}}
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="p-2 border rounded text-center bg-light">
                                        <i class="bi bi-airplane-fill text-secondary fs-5"></i>
                                        <h6 class="mb-0 mt-1 fw-bold text-secondary">0</h6>
                                        <small class="text-muted">Voos</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 border rounded text-center bg-light">
                                        <i class="bi bi-people-fill text-secondary fs-5"></i>
                                        <h6 class="mb-0 mt-1 fw-bold text-secondary">0</h6>
                                        <small class="text-muted">Passageiros</small>
                                    </div>
                                </div>
                            </div>

                            {{-- Mensagem de sem dados --}}
                            <div class="border-top pt-3 text-center">
                                <div class="p-3">
                                    <i class="bi bi-database-slash text-muted fs-1 mb-2"></i>
                                    <p class="text-muted mb-0">Nenhum registro encontrado</p>
                                    <small class="text-muted">Esta companhia não possui voos cadastrados</small>
                                </div>
                            </div>
                        </div>

                        {{-- Rodapé com botão desabilitado --}}
                        <div class="card-footer bg-white border-top-0 pt-0">
                            <button class="btn btn-outline-secondary btn-sm w-100" disabled>
                                <i class="bi bi-eye-slash me-1"></i> Sem dados disponíveis
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        {{-- Mensagem quando não há resultados --}}
        @if(count($companhias) === 0)
        <div class="col-12">
            <div class="alert alert-warning text-center">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Nenhuma companhia encontrada com os filtros selecionados.
            </div>
        </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-filter-target]').forEach((option) => {
        option.addEventListener('click', () => {
            const input = document.getElementById(option.dataset.filterTarget);
            input.value = option.dataset.filterValue;
            input.form.submit();
        });
    });
</script>
@endpush
