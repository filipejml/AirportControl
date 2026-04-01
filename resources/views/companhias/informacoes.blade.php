{{-- resources/views/companhias/informacoes.blade.php --}}
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Companhias Aéreas - Informações Gerais</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Ícones -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #ffffff;
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
    </style>
</head>
<body>

    <!-- Navbar -->
    @include('components.navbar')

    <!-- Conteúdo -->
    <div class="container mt-4">
        
        {{-- Título --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Catálogo de Companhias Aéreas</h1>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('companhias.informacoes') }}" class="row g-2 align-items-end">
                            {{-- Filtro por Companhia --}}
                            <div class="col-md-4">
                                <label for="filtro_companhia" class="form-label fw-bold">
                                    <i class="bi bi-building"></i> Filtrar por Companhia:
                                </label>
                                <select name="companhia" id="filtro_companhia" class="form-select" onchange="this.form.submit()">
                                    <option value="">Todas as Companhias</option>
                                    @foreach($companhias as $companhia)
                                        <option value="{{ $companhia->id }}" {{ request('companhia') == $companhia->id ? 'selected' : '' }}>
                                            {{ $companhia->nome }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filtro por Aeroporto --}}
                            <div class="col-md-3">
                                <label for="filtro_aeroporto" class="form-label fw-bold">
                                    <i class="bi bi-geo-alt"></i> Filtrar por Aeroporto:
                                </label>
                                <select name="aeroporto" id="filtro_aeroporto" class="form-select" onchange="this.form.submit()">
                                    <option value="">Todos os Aeroportos</option>
                                    @foreach($aeroportos as $aeroporto)
                                        <option value="{{ $aeroporto->id }}" {{ request('aeroporto') == $aeroporto->id ? 'selected' : '' }}>
                                            {{ $aeroporto->nome_aeroporto }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filtro de Ordenação --}}
                            <div class="col-md-3">
                                <label for="filtro_ordenacao" class="form-label fw-bold">
                                    <i class="bi bi-sort-down"></i> Ordenar por:
                                </label>
                                <select name="ordenacao" id="filtro_ordenacao" class="form-select" onchange="this.form.submit()">
                                    <option value="nome_az" {{ request('ordenacao') == 'nome_az' ? 'selected' : '' }}>Ordenar por Nome (A-Z)</option>
                                    <option value="nome_za" {{ request('ordenacao') == 'nome_za' ? 'selected' : '' }}>Ordenar por Nome (Z-A)</option>
                                    <option value="mais_voos" {{ request('ordenacao') == 'mais_voos' ? 'selected' : '' }}>Mais Voos</option>
                                    <option value="mais_passageiros" {{ request('ordenacao') == 'mais_passageiros' ? 'selected' : '' }}>Mais Passageiros</option>
                                    <option value="melhor_objetivo" {{ request('ordenacao') == 'melhor_objetivo' ? 'selected' : '' }}>Melhor Nota Objetivo</option>
                                    <option value="melhor_pontualidade" {{ request('ordenacao') == 'melhor_pontualidade' ? 'selected' : '' }}>Melhor Nota Pontualidade</option>
                                    <option value="melhor_servicos" {{ request('ordenacao') == 'melhor_servicos' ? 'selected' : '' }}>Melhor Nota Serviços</option>
                                    <option value="melhor_patio" {{ request('ordenacao') == 'melhor_patio' ? 'selected' : '' }}>Melhor Nota Patio</option>
                                </select>
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
        </div>

        {{-- Indicador de Filtros Ativos --}}
        @if(request('companhia') || request('aeroporto') || request('ordenacao'))
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-info d-flex align-items-center">
                    <i class="bi bi-funnel me-2"></i>
                    <div>
                        <strong>Filtros ativos:</strong>
                        @if(request('companhia'))
                            @php
                                $companhiaSelecionada = $companhias->firstWhere('id', request('companhia'));
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
                                    {{ $companhia->aeroportos->count() }} aeroportos operados
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
                                    {{ $companhia->aeroportos->count() }} aeroportos operados
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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>