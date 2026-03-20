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
                                
                                <!-- Nota Objetivo - AGORA EM FORMATO NUMÉRICO -->
                                <td class="px-4 text-center">
                                    @if($voo->nota_obj)
                                        <span class="badge bg-info bg-opacity-10 text-info p-2" 
                                              data-bs-toggle="tooltip" 
                                              title="Nota: {{ $voo->nota_obj_letra }} ({{ $voo->nota_obj }} pontos)">
                                            {{ $voo->nota_obj }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                                <!-- Nota Pontualidade - AGORA EM FORMATO NUMÉRICO -->
                                <td class="px-4 text-center">
                                    @if($voo->nota_pontualidade)
                                        <span class="badge bg-info bg-opacity-10 text-info p-2" 
                                              data-bs-toggle="tooltip" 
                                              title="Nota: {{ $voo->nota_pontualidade_letra }} ({{ $voo->nota_pontualidade }} pontos)">
                                            {{ $voo->nota_pontualidade }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                                <!-- Nota Serviços - AGORA EM FORMATO NUMÉRICO -->
                                <td class="px-4 text-center">
                                    @if($voo->nota_servicos)
                                        <span class="badge bg-info bg-opacity-10 text-info p-2" 
                                              data-bs-toggle="tooltip" 
                                              title="Nota: {{ $voo->nota_servicos_letra }} ({{ $voo->nota_servicos }} pontos)">
                                            {{ $voo->nota_servicos }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                                <!-- Nota Pátio - AGORA EM FORMATO NUMÉRICO -->
                                <td class="px-4 text-center">
                                    @if($voo->nota_patio)
                                        <span class="badge bg-info bg-opacity-10 text-info p-2" 
                                              data-bs-toggle="tooltip" 
                                              title="Nota: {{ $voo->nota_patio_letra }} ({{ $voo->nota_patio }} pontos)">
                                            {{ $voo->nota_patio }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                
                                <!-- Média - Mantém formato numérico -->
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
                                                  style="font-size: 1rem;"
                                                  data-bs-toggle="tooltip"
                                                  title="Classificação: {{ $voo->media_notas_letra }}">
                                                {{ number_format($voo->media_notas, 1) }}
                                            </span>
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
                        </div>
                        <div class="col-md-6">
                            <!-- Botão de Exportar - Substituir o botão existente -->
                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn btn-sm btn-outline-secondary" id="btnExportarCSV">
                                    <i class="bi bi-download me-2"></i>
                                    Exportar CSV
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
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // ==========================================
    // FILTROS E BUSCA
    // ==========================================
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

    // ==========================================
    // EXPORTAÇÃO CSV (NOVA VERSÃO COM FILTROS)
    // ==========================================
    const btnExportarCSV = document.getElementById('btnExportarCSV');
    
    if (btnExportarCSV) {
        btnExportarCSV.addEventListener('click', function() {
            // Coletar os valores dos filtros atuais
            const searchValue = document.getElementById('searchInput')?.value || '';
            const tipoValue = document.getElementById('filterTipo')?.value || '';
            const horarioValue = document.getElementById('filterHorario')?.value || '';
            const dataValue = document.getElementById('filterData')?.value || '';
            
            // Verificar se há registros visíveis
            const visibleRows = Array.from(document.querySelectorAll('.voo-row')).filter(row => row.style.display !== 'none');
            
            if (visibleRows.length === 0) {
                showToast('warning', 'Não há voos para exportar com os filtros atuais.', 'warning');
                return;
            }
            
            // Construir URL com os parâmetros dos filtros
            let url = '{{ route("voos.export.csv") }}?';
            const params = [];
            
            if (searchValue) params.push(`search=${encodeURIComponent(searchValue)}`);
            if (tipoValue) params.push(`tipo=${encodeURIComponent(tipoValue)}`);
            if (horarioValue) params.push(`horario=${encodeURIComponent(horarioValue)}`);
            if (dataValue) params.push(`dias=${encodeURIComponent(dataValue)}`);
            
            url += params.join('&');
            
            // Salvar texto original do botão
            const originalText = btnExportarCSV.innerHTML;
            btnExportarCSV.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Exportando...';
            btnExportarCSV.disabled = true;
            
            // Fazer o download via fetch
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Erro HTTP: ${response.status}`);
                }
                return response.blob();
            })
            .then(blob => {
                // Criar link para download
                const downloadUrl = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = downloadUrl;
                
                // Extrair nome do arquivo do header Content-Disposition
                let filename = `voos_filtrados_${visibleRows.length}_registros_${new Date().toISOString().slice(0, 19).replace(/:/g, '-')}.csv`;
                
                // Tentar extrair o nome do arquivo do response headers (se disponível)
                if (response.headers && response.headers.get('Content-Disposition')) {
                    const contentDisposition = response.headers.get('Content-Disposition');
                    const match = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                    if (match && match[1]) {
                        filename = match[1].replace(/['"]/g, '');
                    }
                }
                
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(downloadUrl);
                
                // Feedback de sucesso
                showToast('success', `${visibleRows.length} voos exportados com sucesso!`, 'success');
            })
            .catch(error => {
                console.error('Erro na exportação:', error);
                showToast('error', 'Erro ao exportar. Tente novamente.', 'danger');
            })
            .finally(() => {
                // Restaurar botão
                setTimeout(() => {
                    btnExportarCSV.innerHTML = originalText;
                    btnExportarCSV.disabled = false;
                }, 500);
            });
        });
    }

    // ==========================================
    // FUNÇÃO DE TOAST (FEEDBACK VISUAL)
    // ==========================================
    function showToast(type, message, iconType = null) {
        // Configurações de cores e ícones
        const config = {
            success: { bg: 'bg-success', icon: 'check-circle-fill' },
            error: { bg: 'bg-danger', icon: 'exclamation-triangle-fill' },
            warning: { bg: 'bg-warning', icon: 'exclamation-triangle-fill' },
            info: { bg: 'bg-info', icon: 'info-circle-fill' }
        };
        
        const selectedConfig = config[type] || config.info;
        const toastIcon = iconType || selectedConfig.icon;
        
        // Criar elemento toast
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white ${selectedConfig.bg} border-0 position-fixed top-0 end-0 m-3`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        toast.style.zIndex = '9999';
        toast.style.minWidth = '300px';
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-${toastIcon} me-2"></i>
                    <strong>${type === 'success' ? 'Sucesso!' : type === 'error' ? 'Erro!' : type === 'warning' ? 'Atenção!' : 'Info'}</strong>
                    <br>
                    <small>${message}</small>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Inicializar e mostrar toast
        const bsToast = new bootstrap.Toast(toast, { autohide: true, delay: 3000 });
        bsToast.show();
        
        // Remover do DOM após fechar
        toast.addEventListener('hidden.bs.toast', () => toast.remove());
    }

    // ==========================================
    // FUNÇÃO DE IMPRESSÃO (OPCIONAL)
    // ==========================================
    window.printTable = function() {
        window.print();
    };

    // ==========================================
    // FUNÇÃO DE CONFIRMAÇÃO DE EXCLUSÃO
    // ==========================================
    window.confirmDelete = function(id, idVoo) {
        document.getElementById('vooIdToDelete').textContent = idVoo;
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
        
        document.getElementById('confirmDeleteBtn').onclick = function() {
            document.getElementById('delete-form-' + id).submit();
        };
    };

    // ==========================================
    // FUNÇÃO DE ATUALIZAÇÃO AUTOMÁTICA (OPCIONAL)
    // ==========================================
    // Atualizar a cada 5 minutos (comentado para não atrapalhar)
    // setTimeout(function() {
    //     location.reload();
    // }, 300000);
    
    // ==========================================
    // FUNÇÃO PARA EXPORTAR APENAS LINHAS VISÍVEIS (CLIENT-SIDE)
    // Caso queira manter a opção de exportação client-side como fallback
    // ==========================================
    window.exportToCSVClientSide = function() {
        try {
            // Pegar apenas as linhas visíveis (após filtros)
            const rows = document.querySelectorAll('.voo-row');
            const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
            
            if (visibleRows.length === 0) {
                showToast('warning', 'Não há voos para exportar com os filtros atuais.', 'warning');
                return;
            }
            
            // Mostrar indicador de carregamento
            const exportBtn = document.querySelector('[onclick="exportToCSVClientSide()"]');
            let originalText = '';
            if (exportBtn) {
                originalText = exportBtn.innerHTML;
                exportBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Exportando...';
                exportBtn.disabled = true;
            }
            
            // Preparar dados para CSV
            const csv = [];
            
            // Cabeçalho
            csv.push([
                'ID Voo',
                'Data Cadastro',
                'Companhia Aérea',
                'Aeroporto',
                'Aeronave',
                'Tipo de Voo',
                'Horário',
                'Quantidade Voos',
                'Total Passageiros',
                'Nota Objetivo',
                'Nota Pontualidade',
                'Nota Serviços',
                'Nota Pátio',
                'Média'
            ].join(';'));
            
            // Dados
            visibleRows.forEach(row => {
                const cells = row.cells;
                
                // ID do Voo e Data
                const idCell = cells[0];
                const idVoo = idCell.querySelector('.badge')?.textContent.trim() || '';
                const dataVoo = idCell.querySelector('.small')?.textContent.trim() || '';
                
                // Aeroporto
                const aeroporto = cells[1]?.textContent.trim() || '';
                
                // Companhia
                const companhia = cells[2]?.textContent.trim() || '';
                
                // Aeronave
                const aeronave = cells[3]?.textContent.trim() || '';
                
                // Tipo e Horário
                const tipoHorarioCell = cells[4];
                const tipo = tipoHorarioCell.querySelector('.badge:first-child')?.textContent.trim() || '';
                const horario = tipoHorarioCell.querySelector('.badge:last-child')?.textContent.trim() || '';
                
                // Notas
                const notaObj = cells[5]?.textContent.trim() || '';
                const notaPont = cells[6]?.textContent.trim() || '';
                const notaServ = cells[7]?.textContent.trim() || '';
                const notaPatio = cells[8]?.textContent.trim() || '';
                
                // Média
                const media = cells[9]?.textContent.trim() || '';
                
                // Quantidade de Voos e Total de Passageiros
                const voosPaxCell = cells[10];
                const qtdVoos = voosPaxCell?.querySelector('.fw-semibold')?.textContent.replace('x', '').trim() || '';
                const totalPax = voosPaxCell?.querySelector('.fw-bold')?.textContent.replace(/[^\d]/g, '') || '';
                
                // Adicionar linha ao CSV
                csv.push([
                    idVoo,
                    dataVoo,
                    companhia,
                    aeroporto,
                    aeronave,
                    tipo,
                    horario,
                    qtdVoos,
                    totalPax,
                    notaObj,
                    notaPont,
                    notaServ,
                    notaPatio,
                    media
                ].join(';'));
            });
            
            // Criar e baixar o arquivo
            const csvString = '\uFEFF' + csv.join('\n'); // BOM para UTF-8
            const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            
            // Nome do arquivo com data e quantidade de registros
            const dataAtual = new Date();
            const dataFormatada = dataAtual.toISOString().slice(0, 19).replace(/:/g, '-');
            link.setAttribute('href', url);
            link.setAttribute('download', `voos_filtrados_${visibleRows.length}_registros_${dataFormatada}.csv`);
            link.style.visibility = 'hidden';
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
            
            // Restaurar botão e mostrar feedback
            if (exportBtn) {
                setTimeout(() => {
                    exportBtn.innerHTML = originalText;
                    exportBtn.disabled = false;
                    showToast('success', `${visibleRows.length} voos exportados com sucesso!`, 'success');
                }, 500);
            }
            
        } catch (error) {
            console.error('Erro ao exportar CSV:', error);
            showToast('error', 'Erro ao exportar. Tente novamente.', 'danger');
            
            // Restaurar botão se necessário
            const exportBtn = document.querySelector('[onclick="exportToCSVClientSide()"]');
            if (exportBtn) {
                exportBtn.innerHTML = '<i class="bi bi-download me-1"></i> Exportar CSV';
                exportBtn.disabled = false;
            }
        }
    };
    
    // ==========================================
    // FUNÇÃO PARA EXPORTAR VIA SERVIDOR (USANDO A ROTA)
    // Mantida como fallback, mas o botão principal já usa fetch
    // ==========================================
    window.exportToCSVServer = function() {
        // Coletar filtros
        const searchValue = document.getElementById('searchInput')?.value || '';
        const tipoValue = document.getElementById('filterTipo')?.value || '';
        const horarioValue = document.getElementById('filterHorario')?.value || '';
        const dataValue = document.getElementById('filterData')?.value || '';
        
        // Construir URL
        let url = '{{ route("voos.export.csv") }}?';
        const params = [];
        
        if (searchValue) params.push(`search=${encodeURIComponent(searchValue)}`);
        if (tipoValue) params.push(`tipo=${encodeURIComponent(tipoValue)}`);
        if (horarioValue) params.push(`horario=${encodeURIComponent(horarioValue)}`);
        if (dataValue) params.push(`dias=${encodeURIComponent(dataValue)}`);
        
        url += params.join('&');
        
        // Redirecionar para download
        window.location.href = url;
    };
    
    // ==========================================
    // INICIALIZAÇÃO ADICIONAL
    // ==========================================
    
    // Verificar se há tooltips dinâmicos
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                var newTooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]:not([data-bs-original-title])'));
                newTooltips.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        });
    });
    
    observer.observe(document.body, { childList: true, subtree: true });
    
    // Adicionar atalho de teclado para exportar (Ctrl + E)
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
            e.preventDefault();
            const exportBtn = document.getElementById('btnExportarCSV');
            if (exportBtn && !exportBtn.disabled) {
                exportBtn.click();
            }
        }
    });
    
    console.log('Sistema de exportação CSV inicializado com sucesso!');
});
</script>
@endsection