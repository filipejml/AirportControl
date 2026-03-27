{{-- resources/views/voos/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Lista de Voos - Airport Manager')

@section('content')
<div class="container-fluid px-4">
    <!-- Cabeçalho com breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1">Gerenciamento de Voos</h2>
                        <p class="text-muted mb-0">Lista completa de todos os voos cadastrados no sistema</p>
                    </div>
                </div>
                <a href="{{ route('voos.create') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-plus-circle me-2"></i>
                    Novo Voo
                </a>
            </div>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white h-100 border-0 shadow-sm">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total de Voos</h6>
                            <h3 class="mb-0">{{ $estatisticas['total_voos'] }}</h3>
                        </div>
                        <i class="bi bi-airplane fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white h-100 border-0 shadow-sm">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Passageiros</h6>
                            <h3 class="mb-0">{{ number_format($estatisticas['total_passageiros'], 0, ',', '.') }}</h3>
                        </div>
                        <i class="bi bi-people fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-info text-white h-100 border-0 shadow-sm">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Média Pax/Voo</h6>
                            <h3 class="mb-0">{{ $estatisticas['media_pax_voo'] }}</h3>
                        </div>
                        <i class="bi bi-bar-chart fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-white h-100 border-0 shadow-sm">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Média Geral Notas</h6>
                            <h3 class="mb-0">{{ $estatisticas['media_geral_notas'] ? number_format($estatisticas['media_geral_notas'], 1) : 'N/A' }}</h3>
                            <small class="text-white-50">{{ $estatisticas['voos_com_notas'] }} voos avaliados</small>
                        </div>
                        <i class="bi bi-star fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros e Busca -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-2">
            <form id="filterForm" method="GET" action="{{ route('voos.index') }}">
                <div class="row g-2">
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" 
                                   class="form-control form-control-sm border-start-0 ps-0" 
                                   name="search"
                                   id="searchInput" 
                                   placeholder="Buscar..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select form-select-sm" name="tipo" id="filterTipo">
                            <option value="">Todos os tipos</option>
                            <option value="Regular" {{ request('tipo') == 'Regular' ? 'selected' : '' }}>Regular</option>
                            <option value="Charter" {{ request('tipo') == 'Charter' ? 'selected' : '' }}>Charter</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select form-select-sm" name="horario" id="filterHorario">
                            <option value="">Todos horários</option>
                            <option value="EAM" {{ request('horario') == 'EAM' ? 'selected' : '' }}>EAM</option>
                            <option value="AM" {{ request('horario') == 'AM' ? 'selected' : '' }}>AM</option>
                            <option value="AN" {{ request('horario') == 'AN' ? 'selected' : '' }}>AN</option>
                            <option value="PM" {{ request('horario') == 'PM' ? 'selected' : '' }}>PM</option>
                            <option value="ALL" {{ request('horario') == 'ALL' ? 'selected' : '' }}>ALL</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select form-select-sm" name="dias" id="filterData">
                            <option value="">Últimos</option>
                            <option value="7" {{ request('dias') == '7' ? 'selected' : '' }}>7 dias</option>
                            <option value="15" {{ request('dias') == '15' ? 'selected' : '' }}>15 dias</option>
                            <option value="30" {{ request('dias') == '30' ? 'selected' : '' }}>30 dias</option>
                            <option value="90" {{ request('dias') == '90' ? 'selected' : '' }}>90 dias</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-funnel me-1"></i>
                            Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Mensagens de Feedback -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeInDown" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-4"></i>
                <div>
                    <strong>Sucesso!</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeInDown" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
                <div>
                    <strong>Erro!</strong> {{ session('error') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Lista de Voos - TABELA COMPACTA -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            @if($voos->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                    </div>
                    <h4 class="fw-light text-muted mb-3">Nenhum voo cadastrado ainda</h4>
                    <p class="text-muted mb-4">Comece cadastrando o primeiro voo do sistema</p>
                    <a href="{{ route('voos.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Cadastrar Primeiro Voo
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0" id="voosTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-2 py-2 text-center" style="width: 30px;">#</th>
                                <th class="px-2 py-2">Aeroporto</th>
                                <th class="px-2 py-2 text-center">Nº Voo</th>
                                <th class="px-2 py-2">Companhia</th>
                                <th class="px-2 py-2">Modelo</th>
                                <th class="px-2 py-2 text-center">Tipo Voo</th>
                                <th class="px-2 py-2 text-center">Tipo Av.</th>
                                <th class="px-2 py-2 text-center">Qtde Voos</th>
                                <th class="px-2 py-2 text-center">Horário</th>
                                <th class="px-2 py-2 text-center">Passag.</th>
                                <th class="px-2 py-2 text-center">Obj.</th>
                                <th class="px-2 py-2 text-center">Pont.</th>
                                <th class="px-2 py-2 text-center">Serv.</th>
                                <th class="px-2 py-2 text-center">Pátio</th>
                                <th class="px-2 py-2 text-center">Média</th>
                                <th class="px-2 py-2 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($voos as $index => $voo)
                            <tr class="voo-row" 
                                data-tipo="{{ $voo->tipo_voo }}" 
                                data-horario="{{ $voo->horario_voo }}" 
                                data-data="{{ $voo->created_at->format('Y-m-d') }}"
                                data-media="{{ $voo->media_notas ?? '' }}">
                                <td class="px-2 py-1 text-center">
                                    <span class="fw-semibold">{{ $voo->id }}</span>
                                </td>
                                <td class="px-2 py-1">
                                    <span class="small">{{ $voo->aeroporto->codigo_icao ?? $voo->aeroporto->codigo_iata ?? $voo->aeroporto->nome_aeroporto }}</span>
                                </td>
                                <td class="px-2 py-1 text-center">
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1" style="font-size: 0.75rem;">
                                        {{ $voo->id_voo }}
                                    </span>
                                </td>
                                <td class="px-2 py-1">
                                    <span class="small">{{ $voo->companhiaAerea->codigo ?? $voo->companhiaAerea->nome }}</span>
                                </td>
                                <td class="px-2 py-1">
                                    <span class="small">{{ $voo->aeronave->modelo }}</span>
                                </td>
                                <td class="px-2 py-1 text-center">
                                    @if($voo->tipo_voo == 'Regular')
                                        <span class="badge bg-success bg-opacity-10 text-success px-2 py-1" style="font-size: 0.7rem;">R</span>
                                    @else
                                        <span class="badge bg-info bg-opacity-10 text-info px-2 py-1" style="font-size: 0.7rem;">C</span>
                                    @endif
                                </td>
                                <td class="px-2 py-1 text-center">
                                    @php
                                        $porteClasses = [
                                            'PC' => 'bg-secondary',
                                            'MC' => 'bg-primary',
                                            'LC' => 'bg-danger'
                                        ];
                                        $porteTexto = [
                                            'PC' => 'P',
                                            'MC' => 'M',
                                            'LC' => 'G'
                                        ];
                                    @endphp
                                    <span class="badge {{ $porteClasses[$voo->tipo_aeronave] ?? 'bg-secondary' }} bg-opacity-10 text-{{ str_replace('bg-', '', $porteClasses[$voo->tipo_aeronave] ?? 'secondary') }} px-2 py-1" style="font-size: 0.7rem;">
                                        {{ $porteTexto[$voo->tipo_aeronave] ?? $voo->tipo_aeronave }}
                                    </span>
                                </td>
                                <td class="px-2 py-1 text-center fw-semibold">
                                    {{ $voo->qtd_voos }}
                                </td>
                                <td class="px-2 py-1 text-center">
                                    @php
                                        $horarioClasses = [
                                            'EAM' => ['bg-warning', 'text-warning'],
                                            'AM' => ['bg-success', 'text-success'],
                                            'AN' => ['bg-info', 'text-info'],
                                            'PM' => ['bg-primary', 'text-primary'],
                                            'ALL' => ['bg-secondary', 'text-secondary']
                                        ];
                                        $horarioClass = $horarioClasses[$voo->horario_voo] ?? ['bg-secondary', 'text-secondary'];
                                    @endphp
                                    <span class="badge {{ $horarioClass[0] }} bg-opacity-10 {{ $horarioClass[1] }} px-2 py-1" style="font-size: 0.7rem;">
                                        {{ $voo->horario_voo }}
                                    </span>
                                </td>
                                <td class="px-2 py-1 text-center fw-semibold">
                                    {{ number_format($voo->total_passageiros, 0, ',', '.') }}
                                </td>
                                <td class="px-2 py-1 text-center">
                                    @if($voo->nota_obj)
                                        <span class="fw-semibold {{ $voo->nota_obj >= 8 ? 'text-success' : ($voo->nota_obj >= 6 ? 'text-warning' : 'text-danger') }}">
                                            {{ $voo->nota_obj }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="px-2 py-1 text-center">
                                    @if($voo->nota_pontualidade)
                                        <span class="fw-semibold {{ $voo->nota_pontualidade >= 8 ? 'text-success' : ($voo->nota_pontualidade >= 6 ? 'text-warning' : 'text-danger') }}">
                                            {{ $voo->nota_pontualidade }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="px-2 py-1 text-center">
                                    @if($voo->nota_servicos)
                                        <span class="fw-semibold {{ $voo->nota_servicos >= 8 ? 'text-success' : ($voo->nota_servicos >= 6 ? 'text-warning' : 'text-danger') }}">
                                            {{ $voo->nota_servicos }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="px-2 py-1 text-center">
                                    @if($voo->nota_patio)
                                        <span class="fw-semibold {{ $voo->nota_patio >= 8 ? 'text-success' : ($voo->nota_patio >= 6 ? 'text-warning' : 'text-danger') }}">
                                            {{ $voo->nota_patio }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="px-2 py-1 text-center">
                                    @if($voo->media_notas)
                                        @php
                                            $mediaCor = match(true) {
                                                $voo->media_notas >= 9 => 'success',
                                                $voo->media_notas >= 7 => 'info',
                                                $voo->media_notas >= 5 => 'warning',
                                                default => 'danger'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $mediaCor }} rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                                            {{ number_format($voo->media_notas, 1) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="px-2 py-1 text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('voos.edit', $voo) }}" 
                                        class="btn btn-sm btn-outline-warning py-0 px-2" 
                                        title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger py-0 px-2" 
                                                title="Excluir"
                                                onclick="confirmDelete('{{ $voo->id }}', '{{ $voo->id_voo }}')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <form id="delete-form-{{ $voo->id }}" 
                                        action="{{ route('voos.destroy', $voo) }}" 
                                        method="POST" 
                                        class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginação Moderna -->
                <div class="card-footer bg-light border-0 py-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                        <!-- Informações de registros -->
                        <div class="small text-muted order-2 order-md-1">
                            <i class="bi bi-info-circle me-1"></i>
                            <span id="paginationInfo">
                                Mostrando <strong>{{ $voos->firstItem() }}</strong> a <strong>{{ $voos->lastItem() }}</strong> 
                                de <strong>{{ $voos->total() }}</strong> registros
                            </span>
                            <span class="mx-2 text-muted">|</span>
                            <span>
                                <i class="bi bi-files me-1"></i>
                                Página <strong>{{ $voos->currentPage() }}</strong> de <strong>{{ $voos->lastPage() }}</strong>
                            </span>
                        </div>
                        
                        <div class="order-2 order-md-1">
                            <div class="d-flex gap-3 align-items-center">
                                <!-- Seletor de itens por página -->
                                <div class="d-flex align-items-center gap-2">
                                    <label class="small text-muted mb-0">Itens por página:</label>
                                    <select id="perPageSelect" class="form-select form-select-sm" style="width: auto;">
                                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                </div>
                                
                                <!-- Botões de exportação -->
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-danger" id="btnExportarPDF">
                                        <i class="bi bi-file-pdf me-1"></i>
                                        PDF
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" id="btnExportarCSV">
                                        <i class="bi bi-download me-1"></i>
                                        CSV
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Paginação Links -->
                        <div class="order-1 order-md-3">
                            @if($voos->hasPages())
                                <nav aria-label="Navegação de páginas">
                                    <ul class="pagination pagination-sm mb-0">
                                        {{-- Botão Primeira Página --}}
                                        @if($voos->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link"><i class="bi bi-chevron-double-left"></i></span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $voos->url(1) . '&per_page=' . request('per_page', 10) }}" aria-label="Primeira">
                                                    <i class="bi bi-chevron-double-left"></i>
                                                </a>
                                            </li>
                                        @endif
                                        
                                        {{-- Botão Anterior --}}
                                        @if($voos->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $voos->previousPageUrl() . '&per_page=' . request('per_page', 10) }}" aria-label="Anterior">
                                                    <i class="bi bi-chevron-left"></i>
                                                </a>
                                            </li>
                                        @endif
                                        
                                        {{-- Links de Páginas com Intervalo --}}
                                        @php
                                            $currentPage = $voos->currentPage();
                                            $lastPage = $voos->lastPage();
                                            $start = max(1, $currentPage - 2);
                                            $end = min($lastPage, $currentPage + 2);
                                            
                                            if ($start > 1) {
                                                echo '<li class="page-item"><a class="page-link" href="' . $voos->url(1) . '&per_page=' . request('per_page', 10) . '">1</a></li>';
                                                if ($start > 2) {
                                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                                }
                                            }
                                            
                                            for ($i = $start; $i <= $end; $i++) {
                                                if ($i == $currentPage) {
                                                    echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
                                                } else {
                                                    echo '<li class="page-item"><a class="page-link" href="' . $voos->url($i) . '&per_page=' . request('per_page', 10) . '">' . $i . '</a></li>';
                                                }
                                            }
                                            
                                            if ($end < $lastPage) {
                                                if ($end < $lastPage - 1) {
                                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                                }
                                                echo '<li class="page-item"><a class="page-link" href="' . $voos->url($lastPage) . '&per_page=' . request('per_page', 10) . '">' . $lastPage . '</a></li>';
                                            }
                                        @endphp
                                        
                                        {{-- Botão Próximo --}}
                                        @if($voos->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $voos->nextPageUrl() . '&per_page=' . request('per_page', 10) }}" aria-label="Próximo">
                                                    <i class="bi bi-chevron-right"></i>
                                                </a>
                                            </li>
                                        @else
                                            <li class="page-item disabled">
                                                <span class="page-link"><i class="bi bi-chevron-right"></i></span>
                                            </li>
                                        @endif
                                        
                                        {{-- Botão Última Página --}}
                                        @if($voos->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $voos->url($voos->lastPage()) . '&per_page=' . request('per_page', 10) }}" aria-label="Última">
                                                    <i class="bi bi-chevron-double-right"></i>
                                                </a>
                                            </li>
                                        @else
                                            <li class="page-item disabled">
                                                <span class="page-link"><i class="bi bi-chevron-double-right"></i></span>
                                            </li>
                                        @endif
                                    </ul>
                                </nav>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white py-2">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-3">
                <p>Tem certeza que deseja excluir o voo <strong id="vooIdToDelete"></strong>?</p>
                <p class="text-muted small mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Esta ação não poderá ser desfeita.
                </p>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-sm btn-danger" id="confirmDeleteBtn">
                    <i class="bi bi-trash me-1"></i>
                    Excluir
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Tabela compacta */
.table-sm th, .table-sm td {
    padding: 0.25rem 0.5rem !important;
    font-size: 0.75rem;
}

.table th {
    font-weight: 600;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    color: #6c757d;
    white-space: nowrap;
}

.table td {
    vertical-align: middle;
}

/* Cards compactos */
.card .card-body.py-2 {
    padding-top: 0.5rem !important;
    padding-bottom: 0.5rem !important;
}

.card .card-body.py-2 h3 {
    font-size: 1.5rem;
}

.card .card-body.py-2 h6 {
    font-size: 0.7rem;
}

/* Badges menores */
.badge {
    font-weight: 500;
    border-radius: 12px;
}

/* Botões de ação */
.btn-group-sm .btn {
    padding: 0.125rem 0.375rem;
    font-size: 0.7rem;
}

/* Linhas da tabela */
.voo-row:hover {
    background-color: #f8f9fa !important;
}

/* Scroll horizontal para tabela pequena */
.table-responsive {
    overflow-x: auto;
}

/* Paginação moderna */
.pagination {
    margin-bottom: 0;
}

.pagination .page-link {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    color: #0d5c8b;
    border: none;
    background: transparent;
}

.pagination .page-link:hover {
    background-color: rgba(13, 92, 139, 0.1);
    color: #0d5c8b;
}

.pagination .page-item.active .page-link {
    background-color: #0d5c8b;
    border-color: #0d5c8b;
    color: white;
}

.pagination .page-item.disabled .page-link {
    color: #dee2e6;
    background: transparent;
}

/* Animações */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.voo-row {
    animation: fadeIn 0.3s ease-out;
}

/* Responsividade */
@media (max-width: 768px) {
    .pagination .page-link {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .card .card-body.py-2 h3 {
        font-size: 1.2rem;
    }
    
    .d-flex.gap-3 {
        gap: 0.5rem !important;
    }
}

/* Loading spinner */
.loading-spinner {
    display: inline-block;
    width: 1rem;
    height: 1rem;
    border: 2px solid rgba(0,0,0,0.1);
    border-left-color: #0d5c8b;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Auto-submit do formulário ao mudar os selects
    const filterForm = document.getElementById('filterForm');
    const filterTipo = document.getElementById('filterTipo');
    const filterHorario = document.getElementById('filterHorario');
    const filterData = document.getElementById('filterData');

    if (filterTipo) {
        filterTipo.addEventListener('change', function() {
            filterForm.submit();
        });
    }

    if (filterHorario) {
        filterHorario.addEventListener('change', function() {
            filterForm.submit();
        });
    }

    if (filterData) {
        filterData.addEventListener('change', function() {
            filterForm.submit();
        });
    }

    // Busca com debounce
    const searchInput = document.getElementById('searchInput');
    let searchTimeout;

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterForm.submit();
            }, 500);
        });
    }

    // Seletor de itens por página
    const perPageSelect = document.getElementById('perPageSelect');
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', this.value);
            window.location.href = url.toString();
        });
    }

    // ==========================================
    // EXPORTAÇÃO CSV
    // ==========================================
    const btnExportarCSV = document.getElementById('btnExportarCSV');

    if (btnExportarCSV) {
        btnExportarCSV.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Coletar os valores dos filtros atuais
            const searchValue = document.getElementById('searchInput')?.value || '';
            const tipoValue = document.getElementById('filterTipo')?.value || '';
            const horarioValue = document.getElementById('filterHorario')?.value || '';
            const dataValue = document.getElementById('filterData')?.value || '';
            
            // Construir URL com os parâmetros
            const params = new URLSearchParams();
            if (searchValue) params.append('search', searchValue);
            if (tipoValue) params.append('tipo', tipoValue);
            if (horarioValue) params.append('horario', horarioValue);
            if (dataValue) params.append('dias', dataValue);
            
            const url = `{{ route('voos.export.csv') }}?${params.toString()}`;
            
            // Criar link para download
            const link = document.createElement('a');
            link.href = url;
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            // Feedback visual
            const originalText = btnExportarCSV.innerHTML;
            btnExportarCSV.innerHTML = '<span class="loading-spinner me-1"></span> Exportando...';
            btnExportarCSV.disabled = true;
            
            setTimeout(() => {
                btnExportarCSV.innerHTML = originalText;
                btnExportarCSV.disabled = false;
            }, 2000);
        });
    }

    // ==========================================
    // EXPORTAÇÃO PDF
    // ==========================================
    const btnExportarPDF = document.getElementById('btnExportarPDF');

    if (btnExportarPDF) {
        btnExportarPDF.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Coletar os valores dos filtros atuais
            const searchValue = document.getElementById('searchInput')?.value || '';
            const tipoValue = document.getElementById('filterTipo')?.value || '';
            const horarioValue = document.getElementById('filterHorario')?.value || '';
            const dataValue = document.getElementById('filterData')?.value || '';
            
            // Construir URL com os parâmetros
            const params = new URLSearchParams();
            if (searchValue) params.append('search', searchValue);
            if (tipoValue) params.append('tipo', tipoValue);
            if (horarioValue) params.append('horario', horarioValue);
            if (dataValue) params.append('dias', dataValue);
            
            const url = `{{ route('voos.export.pdf') }}?${params.toString()}`;
            
            // Criar link para download
            const link = document.createElement('a');
            link.href = url;
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            // Feedback visual
            const originalText = btnExportarPDF.innerHTML;
            btnExportarPDF.innerHTML = '<span class="loading-spinner me-1"></span> Gerando PDF...';
            btnExportarPDF.disabled = true;
            
            setTimeout(() => {
                btnExportarPDF.innerHTML = originalText;
                btnExportarPDF.disabled = false;
            }, 3000);
        });
    }

    function showToast(type, message, iconType = null) {
        const config = {
            success: { bg: 'bg-success', icon: 'check-circle-fill' },
            error: { bg: 'bg-danger', icon: 'exclamation-triangle-fill' },
            warning: { bg: 'bg-warning', icon: 'exclamation-triangle-fill' },
            info: { bg: 'bg-info', icon: 'info-circle-fill' }
        };
        
        const selectedConfig = config[type] || config.info;
        const toastIcon = iconType || selectedConfig.icon;
        
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white ${selectedConfig.bg} border-0 position-fixed top-0 end-0 m-3`;
        toast.setAttribute('role', 'alert');
        toast.style.zIndex = '9999';
        toast.style.minWidth = '250px';
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body py-2">
                    <i class="bi bi-${toastIcon} me-2"></i>
                    <strong>${type === 'success' ? 'Sucesso!' : type === 'error' ? 'Erro!' : type === 'warning' ? 'Atenção!' : 'Info'}</strong>
                    <br>
                    <small>${message}</small>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast, { autohide: true, delay: 3000 });
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', () => toast.remove());
    }

    window.confirmDelete = function(id, idVoo) {
        document.getElementById('vooIdToDelete').textContent = idVoo;
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
        
        document.getElementById('confirmDeleteBtn').onclick = function() {
            document.getElementById('delete-form-' + id).submit();
        };
    };
    
    // Adicionar classe active nos links de paginação dinamicamente
    const paginationLinks = document.querySelectorAll('.pagination .page-link');
    paginationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!this.parentElement.classList.contains('active')) {
                // Adicionar efeito de loading
                const loadingDiv = document.createElement('div');
                loadingDiv.className = 'position-fixed top-50 start-50 translate-middle';
                loadingDiv.innerHTML = '<div class="loading-spinner" style="width: 2rem; height: 2rem;"></div>';
                document.body.appendChild(loadingDiv);
            }
        });
    });
});
</script>
@endsection