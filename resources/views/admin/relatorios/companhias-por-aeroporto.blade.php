{{-- resources/views/admin/relatorios/companhias-por-aeroporto.blade.php --}}
@extends('layouts.app')

@section('title', 'Relatório - Companhias por Aeroporto')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold">✈️ Relatório: Companhias por Aeroporto</h3>
            <p class="text-muted">Visualize todas as companhias aéreas operando em cada aeroporto</p>
        </div>
        <div>
            <button class="btn btn-success me-2" id="btnExportCSV">
                <i class="bi bi-file-spreadsheet"></i> Exportar CSV
            </button>
            <button class="btn btn-danger" id="btnExportPDF">
                <i class="bi bi-file-pdf"></i> Exportar PDF
            </button>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <!-- Filtros Melhorados -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">
                        <i class="bi bi-building"></i> Aeroporto
                    </label>
                    <select id="filterAeroporto" class="form-select">
                        <option value="">📊 TODOS OS AEROPORTOS</option>
                        @foreach($aeroportos as $aeroporto)
                            <option value="{{ $aeroporto->id }}">
                                ✈️ {{ $aeroporto->nome_aeroporto }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">
                        <i class="bi bi-airplane"></i> Companhia Aérea
                    </label>
                    <select id="filterCompanhia" class="form-select">
                        <option value="">🌍 TODAS AS COMPANHIAS</option>
                        @foreach($companhias as $companhia)
                            <option value="{{ $companhia->id }}">
                                🏢 {{ $companhia->nome }} @if($companhia->codigo) ({{ $companhia->codigo }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="d-grid gap-2 w-100">
                        <button id="clearButton" class="btn btn-outline-secondary">
                            <i class="bi bi-eraser"></i> Limpar Filtros
                        </button>
                    </div>
                </div>
            </div>

            <div id="filtersStatus" class="mb-3"></div>

            <div class="table-responsive">
                <table class="table table-hover" id="relatorioTable">
                    <thead class="table-light">
                        <tr>
                            <th>Aeroporto</th>
                            <th class="text-center">Qtd</th>
                            <th>Companhias Aéreas</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr>
                            <td colspan="3" class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const aeroportosList = @json($aeroportos);
const companhiasList = @json($companhias);

class AdminRelatorioCompanhias {
    constructor() {
        this.apiUrl = '/api/relatorios/companhias-por-aeroporto';
        this.dadosOriginais = [];
        this.dadosFiltrados = [];
        this.init();
    }
    
    async init() {
        document.addEventListener('DOMContentLoaded', async () => {
            this.configurarElementos();
            await this.carregarDados();
            this.configurarEventos();
        });
    }
    
    configurarElementos() {
        this.tbody = document.getElementById('tableBody');
        this.filterAeroporto = document.getElementById('filterAeroporto');
        this.filterCompanhia = document.getElementById('filterCompanhia');
        this.clearButton = document.getElementById('clearButton');
        this.btnExportCSV = document.getElementById('btnExportCSV');
        this.btnExportPDF = document.getElementById('btnExportPDF');
        this.filtersStatus = document.getElementById('filtersStatus');
    }
    
    async carregarDados() {
        this.mostrarLoading();
        
        const aeroportoId = this.filterAeroporto?.value || '';
        const companhiaId = this.filterCompanhia?.value || '';
        
        let url = this.apiUrl;
        const params = [];
        if (aeroportoId) params.push(`aeroporto_id=${aeroportoId}`);
        if (companhiaId) params.push(`companhia_id=${companhiaId}`);
        if (params.length) url += `?${params.join('&')}`;
        
        try {
            const response = await fetch(url);
            const result = await response.json();
            
            if (result.success) {
                this.dadosOriginais = result.data;
                this.dadosFiltrados = [...result.data];
                this.renderizarTabela();
                this.atualizarStatusFiltros();
            } else {
                this.mostrarErro();
            }
        } catch (error) {
            console.error('Erro:', error);
            this.mostrarErro();
        }
    }
    
    configurarEventos() {
        if (this.filterAeroporto) {
            this.filterAeroporto.addEventListener('change', () => this.carregarDados());
        }
        
        if (this.filterCompanhia) {
            this.filterCompanhia.addEventListener('change', () => this.carregarDados());
        }
        
        if (this.clearButton) {
            this.clearButton.addEventListener('click', () => this.limparFiltros());
        }
        
        if (this.btnExportCSV) {
            this.btnExportCSV.addEventListener('click', () => this.exportarCSV());
        }
        
        if (this.btnExportPDF) {
            this.btnExportPDF.addEventListener('click', () => window.print());
        }
    }
    
    limparFiltros() {
        if (this.filterAeroporto) this.filterAeroporto.value = '';
        if (this.filterCompanhia) this.filterCompanhia.value = '';
        this.carregarDados();
    }
    
    atualizarStatusFiltros() {
        if (!this.filtersStatus) return;
        
        const aeroportoSelecionado = this.filterAeroporto?.value;
        const companhiaSelecionada = this.filterCompanhia?.value;
        
        if (!aeroportoSelecionado && !companhiaSelecionada) {
            this.filtersStatus.innerHTML = `
                <div class="alert alert-info alert-sm mb-0">
                    <i class="bi bi-info-circle"></i> 
                    Mostrando <strong>TODOS</strong> os aeroportos e companhias
                </div>
            `;
            return;
        }
        
        let html = '<div class="alert alert-primary alert-sm mb-0"><i class="bi bi-funnel"></i> Filtros ativos: ';
        const filtros = [];
        
        if (aeroportoSelecionado) {
            const aeroporto = aeroportosList.find(a => a.id == aeroportoSelecionado);
            if (aeroporto) filtros.push(`<span class="badge bg-primary">Aeroporto: ${aeroporto.nome_aeroporto}</span>`);
        }
        
        if (companhiaSelecionada) {
            const companhia = companhiasList.find(c => c.id == companhiaSelecionada);
            if (companhia) filtros.push(`<span class="badge bg-primary">Companhia: ${companhia.nome}</span>`);
        }
        
        html += filtros.join(' ') + '</div>';
        this.filtersStatus.innerHTML = html;
    }
    
    mostrarLoading() {
        if (this.tbody) {
            this.tbody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="mt-2 text-muted">Carregando dados...</p>
                    </td>
                </tr>
            `;
        }
    }
    
    mostrarErro() {
        if (this.tbody) {
            this.tbody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-danger py-5">
                        <i class="bi bi-exclamation-triangle fs-1"></i>
                        <p class="mt-2">Erro ao carregar dados. Tente novamente.</p>
                    </td>
                </tr>
            `;
        }
    }
    
    renderizarTabela() {
        if (!this.tbody) return;
        
        if (this.dadosFiltrados.length === 0) {
            this.tbody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-muted py-4">
                        <i class="bi bi-inbox"></i> Nenhum resultado encontrado.
                    </td>
                </tr>
            `;
            return;
        }
        
        this.tbody.innerHTML = this.dadosFiltrados.map(item => `
            <tr>
                <td class="align-middle">
                    <strong>${this.escapeHtml(item.aeroporto)}</strong>
                    <br>
                    <small class="text-muted">ID: ${item.id_aeroporto}</small>
                </td>
                <td class="align-middle text-center">
                    <span class="badge bg-primary rounded-pill fs-6">${item.quantidade_companhias}</span>
                </td>
                <td class="align-middle">
                    ${item.companhias.map(c => `
                        <div class="companhia-item mb-2 p-2 bg-light rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-building"></i>
                                    <strong>${this.escapeHtml(c.nome)}</strong>
                                </div>
                                <div>
                                    ${c.codigo ? `<span class="badge bg-secondary me-1">${this.escapeHtml(c.codigo)}</span>` : ''}
                                    <span class="badge bg-info">ID: ${c.id}</span>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                    ${item.companhias.length === 0 ? '<span class="text-muted"><i class="bi bi-info-circle"></i> Nenhuma companhia associada</span>' : ''}
                </td>
            </tr>
        `).join('');
    }
    
    exportarCSV() {
        const headers = ['Aeroporto', 'Quantidade de Companhias', 'Companhias'];
        const rows = this.dadosFiltrados.map(item => [
            item.aeroporto,
            item.quantidade_companhias,
            item.companhias.map(c => c.nome).join('; ')
        ]);
        
        const csvContent = [headers, ...rows]
            .map(row => row.map(cell => `"${String(cell).replace(/"/g, '""')}"`).join(','))
            .join('\n');
        
        const blob = new Blob(["\uFEFF" + csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.href = url;
        link.setAttribute('download', 'relatorio_companhias_por_aeroporto.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }
    
    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

new AdminRelatorioCompanhias();
</script>

<style>
@media print {
    .btn, .card-header, .d-flex.justify-content-between, .row.mb-3 {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}

.companhia-item {
    transition: all 0.2s;
}
.companhia-item:hover {
    background-color: #e9ecef !important;
    transform: translateX(5px);
}

.alert-sm {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}
</style>
@endpush