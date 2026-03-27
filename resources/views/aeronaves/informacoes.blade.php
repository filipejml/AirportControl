{{-- resources/views/aeronaves/informacoes.blade.php --}}
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aeronaves - Informações Gerais</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Ícones -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: white;
        }
        
        .container {
            margin-top: 20px;
        }
        
        .modelo-card {
            transition: transform 0.2s;
        }
        
        .modelo-card:hover {
            transform: translateY(-5px);
        }
        
        .card {
            transition: box-shadow 0.3s;
        }
        
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    @include('components.navbar')

    <!-- Conteúdo -->
    <div class="container">
        {{-- Linha do título --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">Catálogo de Aeronaves</h1>
        </div>

        {{-- Estatísticas Gerais --}}
        <div class="row mb-4">
            <div class="col-md-3 col-6 mb-3 mb-md-0">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0d6efd !important;">
                    <div class="card-body">
                        <div>
                            <h6 class="text-muted mb-2">Modelos Cadastrados</h6>
                            <h3 class="mb-0 fw-bold text-primary">{{ count($modelosComDados) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-6 mb-3 mb-md-0">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #198754 !important;">
                    <div class="card-body">
                        <div>
                            <h6 class="text-muted mb-2">Total de Voos</h6>
                            <h3 class="mb-0 fw-bold text-success">{{ number_format(array_sum(array_column($modelosComDados, 'total_voos'))) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-6 mb-3 mb-md-0">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0dcaf0 !important;">
                    <div class="card-body">
                        <div>
                            <h6 class="text-muted mb-2">Total de Passageiros</h6>
                            <h3 class="mb-0 fw-bold text-info">{{ number_format(array_sum(array_column($modelosComDados, 'total_passageiros'))) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-6 mb-3 mb-md-0">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important;">
                    <div class="card-body">
                        <div>
                            <h6 class="text-muted mb-2">Fabricantes</h6>
                            <h3 class="mb-0 fw-bold text-warning">{{ number_format(count(array_unique(array_column($modelosComDados, 'fabricante')))) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="row mb-4">
            <div class="col-md-6">
                <select id="searchSelect" class="form-select">
                    <option value="">Todos os modelos</option>
                    @foreach($modelosComDados as $modelo => $dados)
                        <option value="{{ strtolower($modelo) }}">{{ $modelo }} - {{ $dados['fabricante'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select id="filterFabricante" class="form-select">
                    <option value="">Todas as fabricantes</option>
                    @php
                        $fabricantesUnicas = collect($modelosComDados)->pluck('fabricante')->unique()->sort();
                    @endphp
                    @foreach($fabricantesUnicas as $fabricante)
                        <option value="{{ strtolower($fabricante) }}">{{ $fabricante }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select id="sortSelect" class="form-select">
                    <option value="dados">Com dados primeiro</option>
                    <option value="modelo">Ordenar por Modelo (A-Z)</option>
                    <option value="modelo-desc">Ordenar por Modelo (Z-A)</option>
                    <option value="voos">Mais Voos</option>
                    <option value="passageiros">Mais Passageiros</option>
                    <option value="objetivo">Melhor Nota Objetivo</option>
                    <option value="pontualidade">Melhor Nota Pontualidade</option>
                    <option value="servicos">Melhor Nota Serviços</option>
                    <option value="patio">Melhor Nota Pátio</option>
                </select>
            </div>
        </div>

        {{-- Cards dos Modelos --}}
        <div class="row" id="modelosContainer">
            @foreach($modelosComDados as $modelo => $dados)
                @php
                    // Determinar cor da borda baseada na disponibilidade de dados
                    $borderColor = $dados['tem_dados'] ? '#0d6efd' : '#6c757d';
                    $textColor = $dados['tem_dados'] ? 'text-primary' : 'text-muted';
                @endphp
                
                <div class="col-md-6 col-lg-4 mb-4 modelo-card" 
                     data-modelo="{{ strtolower($modelo) }}"
                     data-fabricante="{{ strtolower($dados['fabricante']) }}"
                     data-voos="{{ $dados['total_voos'] }}"
                     data-passageiros="{{ $dados['total_passageiros'] }}"
                     data-objetivo="{{ $dados['media_objetivo'] }}"
                     data-pontualidade="{{ $dados['media_pontualidade'] }}"
                     data-servicos="{{ $dados['media_servicos'] }}"
                     data-patio="{{ $dados['media_patio'] }}"
                     data-tem-dados="{{ $dados['tem_dados'] ? 'true' : 'false' }}">
                    <div class="card h-100 shadow-sm" style="border-left:5px solid {{ $borderColor }};">
                        
                        {{-- Cabeçalho --}}
                        <div class="card-header bg-transparent border-0 pb-0 pt-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title mb-1 {{ $textColor }}">
                                        <i class="bi bi-airplane me-2"></i>{{ $modelo }}
                                    </h5>
                                    <p class="card-text text-muted small mb-0">{{ $dados['fabricante'] }}</p>
                                </div>
                                <span class="badge bg-light text-dark">{{ $dados['capacidade'] }} assentos</span>
                            </div>
                        </div>

                        {{-- Corpo do card --}}
                        <div class="card-body pt-3">
                            {{-- Status dos dados --}}
                            <div class="mb-3">
                                @if($dados['tem_dados'])
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle-fill"></i> Com dados disponíveis
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-dash-circle"></i> Sem dados disponíveis
                                    </span>
                                @endif
                            </div>

                            {{-- Informações principais --}}
                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-airplane-fill text-primary me-2"></i>
                                        <div>
                                            <p class="mb-0 fw-bold">{{ $dados['total_voos'] }}</p>
                                            <small class="text-muted">Voos</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-people-fill text-success me-2"></i>
                                        <div>
                                            <p class="mb-0 fw-bold">{{ number_format($dados['total_passageiros']) }}</p>
                                            <small class="text-muted">Passageiros</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Médias das notas --}}
                            <div class="border-top pt-3">
                                <p class="small text-muted mb-2">Médias de Avaliação:</p>
                                <div class="row text-center">
                                    <div class="col-3">
                                        <div class="border rounded p-2 bg-light">
                                            <i class="bi bi-flag-fill text-primary d-block mb-1"></i>
                                            <p class="mb-0 fw-bold small">{{ number_format($dados['media_objetivo'], 1) }}</p>
                                            <small class="text-muted">Obj</small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="border rounded p-2 bg-light">
                                            <i class="bi bi-clock-fill text-success d-block mb-1"></i>
                                            <p class="mb-0 fw-bold small">{{ number_format($dados['media_pontualidade'], 1) }}</p>
                                            <small class="text-muted">Pont</small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="border rounded p-2 bg-light">
                                            <i class="bi bi-gear-fill text-info d-block mb-1"></i>
                                            <p class="mb-0 fw-bold small">{{ number_format($dados['media_servicos'], 1) }}</p>
                                            <small class="text-muted">Serv</small>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="border rounded p-2 bg-light">
                                            <i class="bi bi-pin-fill text-warning d-block mb-1"></i>
                                            <p class="mb-0 fw-bold small">{{ number_format($dados['media_patio'], 1) }}</p>
                                            <small class="text-muted">Pátio</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Botão de ação --}}
                            <div class="mt-3">
                                @if($dados['tem_dados'])
                                    <div class="d-grid">
                                        <a href="{{ route('aeronaves.dashboard', $dados['id']) }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-graph-up me-1"></i> Ver Dashboard
                                        </a>
                                    </div>
                                @else
                                    <div class="d-grid">
                                        <button class="btn btn-outline-secondary btn-sm" disabled>
                                            <i class="bi bi-eye-slash me-1"></i> Dashboard Indisponível
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if(count($modelosComDados) === 0)
            <div class="text-center py-5">
                <i class="bi bi-airplane display-1 text-muted"></i>
                <p class="text-muted mt-3">Nenhum modelo encontrado no banco de dados.</p>
            </div>
        @endif

        <p class="text-center text-muted mt-4">
            Desenvolvido por <strong>Filipe Lopes</strong>
        </p>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Filtro por modelo
        document.getElementById('searchSelect').addEventListener('change', function() {
            const searchTerm = this.value;
            filterCards();
        });

        // Filtro por fabricante
        document.getElementById('filterFabricante').addEventListener('change', function() {
            const fabricante = this.value;
            filterCards();
        });

        // Ordenação
        document.getElementById('sortSelect').addEventListener('change', function() {
            const sortType = this.value;
            sortCards(sortType);
        });

        function filterCards() {
            const searchTerm = document.getElementById('searchSelect').value.toLowerCase();
            const fabricanteFilter = document.getElementById('filterFabricante').value.toLowerCase();
            
            const cards = document.querySelectorAll('.modelo-card');
            let visibleCount = 0;
            
            cards.forEach(card => {
                const modelo = card.dataset.modelo;
                const fabricante = card.dataset.fabricante;
                
                let showByModelo = true;
                let showByFabricante = true;
                
                if (searchTerm && !modelo.includes(searchTerm)) {
                    showByModelo = false;
                }
                
                if (fabricanteFilter && fabricante !== fabricanteFilter) {
                    showByFabricante = false;
                }
                
                if (showByModelo && showByFabricante) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function sortCards(sortType) {
            const container = document.getElementById('modelosContainer');
            const cards = Array.from(document.querySelectorAll('.modelo-card'));
            
            cards.sort((a, b) => {
                switch(sortType) {
                    case 'modelo':
                        return a.dataset.modelo.localeCompare(b.dataset.modelo);
                    case 'modelo-desc':
                        return b.dataset.modelo.localeCompare(a.dataset.modelo);
                    case 'voos':
                        return parseInt(b.dataset.voos) - parseInt(a.dataset.voos);
                    case 'passageiros':
                        return parseInt(b.dataset.passageiros) - parseInt(a.dataset.passageiros);
                    case 'objetivo':
                        return parseFloat(b.dataset.objetivo) - parseFloat(a.dataset.objetivo);
                    case 'pontualidade':
                        return parseFloat(b.dataset.pontualidade) - parseFloat(a.dataset.pontualidade);
                    case 'servicos':
                        return parseFloat(b.dataset.servicos) - parseFloat(a.dataset.servicos);
                    case 'patio':
                        return parseFloat(b.dataset.patio) - parseFloat(a.dataset.patio);
                    case 'dados':
                    default:
                        // Com dados primeiro
                        const aHasData = a.dataset.temDados === 'true';
                        const bHasData = b.dataset.temDados === 'true';
                        if (aHasData && !bHasData) return -1;
                        if (!aHasData && bHasData) return 1;
                        return 0;
                }
            });
            
            // Reordenar os cards no DOM
            cards.forEach(card => {
                container.appendChild(card);
            });
        }
    </script>
</body>
</html>