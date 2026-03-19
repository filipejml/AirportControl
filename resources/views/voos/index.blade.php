{{-- resources/views/voos/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Lista de Voos - Airport Manager')

@section('content')
<div class="container-fluid px-4">
    <!-- Cabeçalho com breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Voos</li>
                </ol>
            </nav>
            
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="bi bi-airplane-fill text-primary fs-1"></i>
                    </div>
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
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Total de Voos</h6>
                            <h3 class="mb-0">{{ $estatisticas['total_voos'] }}</h3>
                        </div>
                        <i class="bi bi-airplane fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Total Passageiros</h6>
                            <h3 class="mb-0">{{ number_format($estatisticas['total_passageiros'], 0, ',', '.') }}</h3>
                        </div>
                        <i class="bi bi-people fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-info text-white h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Média Pax/Voo</h6>
                            <h3 class="mb-0">{{ $estatisticas['media_pax_voo'] }}</h3>
                        </div>
                        <i class="bi bi-bar-chart fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-white h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Média Geral Notas</h6>
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
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" 
                               class="form-control border-start-0 ps-0" 
                               id="searchInput" 
                               placeholder="Buscar..."
                               style="background-color: transparent;">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="filterTipo">
                        <option value="">Todos os tipos</option>
                        <option value="Regular">Regular</option>
                        <option value="Charter">Charter</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="filterHorario">
                        <option value="">Todos horários</option>
                        <option value="EAM">EAM</option>
                        <option value="AM">AM</option>
                        <option value="AN">AN</option>
                        <option value="PM">PM</option>
                        <option value="ALL">ALL</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="filterData">
                        <option value="">Últimos</option>
                        <option value="7">7 dias</option>
                        <option value="15">15 dias</option>
                        <option value="30">30 dias</option>
                        <option value="90">90 dias</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" id="clearFilters">
                        <i class="bi bi-x-circle me-2"></i>
                        Limpar
                    </button>
                </div>
            </div>
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

    <!-- Lista de Voos -->
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
                    <table class="table table-hover align-middle mb-0" id="voosTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3">ID Voo</th>
                                <th class="px-4 py-3">Aeroporto</th>
                                <th class="px-4 py-3">Companhia</th>
                                <th class="px-4 py-3">Aeronave</th>
                                <th class="px-4 py-3 text-center">Tipo/Horário</th>
                                <th class="px-4 py-3 text-center">Nota Obj</th>
                                <th class="px-4 py-3 text-center">Nota Pont</th>
                                <th class="px-4 py-3 text-center">Nota Serv</th>
                                <th class="px-4 py-3 text-center">Nota Patio</th>
                                <th class="px-4 py-3 text-center">Média</th>
                                <th class="px-4 py-3 text-end">Voos/Pax</th>
                                <th class="px-4 py-3 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($voos as $voo)
                            <tr class="voo-row" 
                                data-tipo="{{ $voo->tipo_voo }}" 
                                data-horario="{{ $voo->horario_voo }}" 
                                data-data="{{ $voo->created_at->format('Y-m-d') }}"
                                data-media="{{ $voo->media_notas ?? '' }}">
                                <td class="px-4">
                                    <span class="badge bg-primary bg-opacity-10 text-primary p-2">
                                        <i class="bi bi-tag me-1"></i>
                                        {{ $voo->id_voo }}
                                    </span>
                                    <div class="small text-muted mt-1">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ $voo->created_at->format('d/m/Y') }}
                                    </div>
                                </td>
                                <td class="px-4">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-geo-alt text-muted me-2"></i>
                                        {{ $voo->aeroporto->nome_aeroporto }}
                                    </div>
                                </td>
                                <td class="px-4">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-building text-muted me-2"></i>
                                        {{ $voo->companhiaAerea->nome }}
                                    </div>
                                </td>
                                <td class="px-4">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-airplane text-muted me-2"></i>
                                        {{ $voo->aeronave->modelo }}
                                        @if($voo->aeronave->porte)
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary ms-2">
                                                {{ $voo->aeronave->porte }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4">
                                    <div class="d-flex flex-column gap-1">
                                        @if($voo->tipo_voo == 'Regular')
                                            <span class="badge bg-success bg-opacity-10 text-success">
                                                <i class="bi bi-check-circle me-1"></i>Regular
                                            </span>
                                        @else
                                            <span class="badge bg-info bg-opacity-10 text-info">
                                                <i class="bi bi-star me-1"></i>Charter
                                            </span>
                                        @endif
                                        
                                        @php
                                            $horarioClasses = [
                                                'EAM' => ['bg-warning', 'text-warning', 'bi-sunrise'],
                                                'AM' => ['bg-success', 'text-success', 'bi-sun'],
                                                'AN' => ['bg-info', 'text-info', 'bi-cloud-sun'],
                                                'PM' => ['bg-primary', 'text-primary', 'bi-moon-stars'],
                                                'ALL' => ['bg-secondary', 'text-secondary', 'bi-calendar-check']
                                            ];
                                            $horarioClass = $horarioClasses[$voo->horario_voo] ?? ['bg-secondary', 'text-secondary', 'bi-clock'];
                                        @endphp
                                        <span class="badge {{ $horarioClass[0] }} bg-opacity-10 text-{{ explode('-', $horarioClass[1])[1] }}">
                                            <i class="bi {{ $horarioClass[2] }} me-1"></i>
                                            {{ $voo->horario_voo }}
                                        </span>
                                    </div>
                                </td>
                                
                                <!-- Nota Objetivo -->
                                <td class="px-4 text-center">
                                    @if($voo->nota_obj)
                                        <span class="badge bg-info bg-opacity-10 text-info p-2" 
                                              data-bs-toggle="tooltip" 
                                              title="{{ $voo->nota_obj }} pontos">
                                            {{ $voo->nota_obj_letra }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                                <!-- Nota Pontualidade -->
                                <td class="px-4 text-center">
                                    @if($voo->nota_pontualidade)
                                        <span class="badge bg-info bg-opacity-10 text-info p-2" 
                                              data-bs-toggle="tooltip" 
                                              title="{{ $voo->nota_pontualidade }} pontos">
                                            {{ $voo->nota_pontualidade_letra }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                                <!-- Nota Serviços -->
                                <td class="px-4 text-center">
                                    @if($voo->nota_servicos)
                                        <span class="badge bg-info bg-opacity-10 text-info p-2" 
                                              data-bs-toggle="tooltip" 
                                              title="{{ $voo->nota_servicos }} pontos">
                                            {{ $voo->nota_servicos_letra }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                                <!-- Nota Patio -->
                                <td class="px-4 text-center">
                                    @if($voo->nota_patio)
                                        <span class="badge bg-info bg-opacity-10 text-info p-2" 
                                              data-bs-toggle="tooltip" 
                                              title="{{ $voo->nota_patio }} pontos">
                                            {{ $voo->nota_patio_letra }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                                <!-- Média -->
                                <td class="px-4 text-center">
                                    @if($voo->media_notas)
                                        @php
                                            $mediaCor = match(true) {
                                                $voo->media_notas >= 9 => 'success',
                                                $voo->media_notas >= 7 => 'info',
                                                $voo->media_notas >= 5 => 'warning',
                                                default => 'danger'
                                            };
                                        @endphp
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="badge bg-{{ $mediaCor }} rounded-pill p-2" 
                                                  style="font-size: 1rem;">
                                                {{ number_format($voo->media_notas, 1) }}
                                            </span>
                                            <small class="text-muted">{{ $voo->media_notas_letra }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                                <td class="px-4">
                                    <div class="text-end">
                                        <div class="fw-semibold">
                                            <i class="bi bi-sort-numeric-up text-muted me-1"></i>
                                            {{ $voo->qtd_voos }}x
                                        </div>
                                        <div class="fw-bold text-primary">
                                            <i class="bi bi-people text-muted me-1"></i>
                                            {{ number_format($voo->total_passageiros, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-4 text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('voos.edit', $voo) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Editar"
                                           data-bs-toggle="tooltip">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Excluir"
                                                data-bs-toggle="tooltip"
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
                
                <!-- Rodapé da tabela -->
                <div class="card-footer bg-light border-0 py-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <p class="text-muted mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                Total de <strong>{{ $voos->count() }}</strong> voos encontrados
                                @if($estatisticas['voos_com_notas'] > 0)
                                    | <strong>{{ $estatisticas['voos_com_notas'] }}</strong> com notas
                                    | Média geral: <strong class="text-{{ 
                                        $estatisticas['media_geral_notas'] >= 9 ? 'success' : 
                                        ($estatisticas['media_geral_notas'] >= 7 ? 'info' : 
                                        ($estatisticas['media_geral_notas'] >= 5 ? 'warning' : 'danger')) 
                                    }}">{{ number_format($estatisticas['media_geral_notas'], 1) }}</strong>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn btn-sm btn-outline-secondary" onclick="exportToCSV()">
                                    <i class="bi bi-download me-2"></i>
                                    Exportar CSV
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="printTable()">
                                    <i class="bi bi-printer me-2"></i>
                                    Imprimir
                                </button>
                            </div>
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
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o voo <strong id="vooIdToDelete"></strong>?</p>
                <p class="text-muted small mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Esta ação não poderá ser desfeita.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>
                    Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="bi bi-trash me-2"></i>
                    Excluir
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Animações */
@import url('https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css');

/* Cards de estatísticas */
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

/* Tabela */
.table th {
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
    white-space: nowrap;
}

.table td {
    font-size: 0.95rem;
    vertical-align: middle;
}

.voo-row {
    transition: all 0.2s ease;
}

.voo-row:hover {
    background-color: #f8f9fa !important;
}

/* Badges */
.badge {
    font-weight: 500;
    border-radius: 20px;
}

/* Botões de ação */
.btn-group .btn {
    padding: 0.25rem 0.5rem;
    border-radius: 6px !important;
    margin: 0 2px;
    transition: all 0.2s ease;
}

.btn-group .btn:hover {
    transform: translateY(-2px);
}

/* Filtros */
.input-group:focus-within {
    box-shadow: 0 0 0 0.25rem rgba(13, 92, 139, 0.1);
}

/* Breadcrumb */
.breadcrumb-item a {
    color: #0d5c8b;
    transition: color 0.2s ease;
}

.breadcrumb-item a:hover {
    color: #0a4a70;
}

/* Scroll suave */
html {
    scroll-behavior: smooth;
}

/* Responsividade */
@media (max-width: 768px) {
    .d-flex.justify-content-between.align-items-center {
        flex-direction: column;
        gap: 1rem;
    }
    
    .btn-group {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .btn-group .btn {
        border-radius: 6px !important;
        width: 100%;
    }
}
</style>

<script>
// Inicializar tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Filtros
    const searchInput = document.getElementById('searchInput');
    const filterTipo = document.getElementById('filterTipo');
    const filterHorario = document.getElementById('filterHorario');
    const filterData = document.getElementById('filterData');
    const clearFilters = document.getElementById('clearFilters');
    const rows = document.querySelectorAll('.voo-row');

    function filterRows() {
        const searchTerm = searchInput.value.toLowerCase();
        const tipoFilter = filterTipo.value;
        const horarioFilter = filterHorario.value;
        const dataFilter = parseInt(filterData.value);
        
        let dataLimite = null;
        if (dataFilter) {
            dataLimite = new Date();
            dataLimite.setDate(dataLimite.getDate() - dataFilter);
        }

        rows.forEach(row => {
            let show = true;
            
            // Busca por texto
            if (searchTerm) {
                const text = row.textContent.toLowerCase();
                if (!text.includes(searchTerm)) {
                    show = false;
                }
            }
            
            // Filtro por tipo
            if (show && tipoFilter && row.dataset.tipo !== tipoFilter) {
                show = false;
            }
            
            // Filtro por horário
            if (show && horarioFilter && row.dataset.horario !== horarioFilter) {
                show = false;
            }
            
            // Filtro por data
            if (show && dataLimite) {
                const rowDate = new Date(row.dataset.data);
                if (rowDate < dataLimite) {
                    show = false;
                }
            }
            
            row.style.display = show ? '' : 'none';
        });
    }

    searchInput.addEventListener('keyup', filterRows);
    filterTipo.addEventListener('change', filterRows);
    filterHorario.addEventListener('change', filterRows);
    filterData.addEventListener('change', filterRows);

    clearFilters.addEventListener('click', function() {
        searchInput.value = '';
        filterTipo.value = '';
        filterHorario.value = '';
        filterData.value = '';
        filterRows();
    });

    // Exportar para CSV
    window.exportToCSV = function() {
        const rows = document.querySelectorAll('.voo-row');
        let csv = [];
        
        // Cabeçalho
        csv.push(['ID Voo', 'Aeroporto', 'Companhia', 'Aeronave', 'Tipo', 'Horário', 
                  'Nota Obj', 'Nota Pont', 'Nota Serv', 'Nota Patio', 'Média', 'Qtd Voos', 'Total Pax']);
        
        // Dados
        rows.forEach(row => {
            const cells = row.cells;
            
            const linha = [
                cells[0].textContent.trim().replace(/\n.*$/, ''), // Pega só o ID, sem a data
                cells[1].textContent.trim(),
                cells[2].textContent.trim(),
                cells[3].textContent.trim(),
                cells[4].querySelector('.badge:first-child')?.textContent.trim() || '',
                cells[4].querySelector('.badge:last-child')?.textContent.trim() || '',
                cells[5].textContent.trim() || '',
                cells[6].textContent.trim() || '',
                cells[7].textContent.trim() || '',
                cells[8].textContent.trim() || '',
                cells[9].textContent.trim() || '',
                cells[10].querySelector('.fw-semibold')?.textContent.replace('x', '').trim() || '',
                cells[10].querySelector('.fw-bold')?.textContent.replace(/[^\d]/g, '') || ''
            ];
            csv.push(linha);
        });
        
        // Converter para string
        const csvString = csv.map(row => row.join(';')).join('\n');
        
        // Download
        const blob = new Blob(['\uFEFF' + csvString], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'voos_' + new Date().toISOString().slice(0,10) + '.csv';
        link.click();
    };

    // Imprimir
    window.printTable = function() {
        window.print();
    };

    // Confirmar exclusão
    window.confirmDelete = function(id, idVoo) {
        document.getElementById('vooIdToDelete').textContent = idVoo;
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
        
        document.getElementById('confirmDeleteBtn').onclick = function() {
            document.getElementById('delete-form-' + id).submit();
        };
    };
});

// Atualizar a cada 5 minutos
setTimeout(function() {
    location.reload();
}, 300000);
</script>
@endsection