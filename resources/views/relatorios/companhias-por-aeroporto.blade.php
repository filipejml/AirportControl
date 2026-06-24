{{-- resources/views/relatorios/companhias-por-aeroporto.blade.php --}}
@extends('layouts.app')

@section('title', 'Relatório - Companhias por Aeroporto')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h3 class="fw-bold">
                    <i class="bi bi-building"></i> Companhias Aéreas por Aeroporto
                </h3>
                <p class="text-muted">Visualize quais companhias operam em cada aeroporto</p>
            </div>
            
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-success" id="btnExportCSV">
                    <i class="bi bi-file-spreadsheet"></i> Exportar CSV
                </button>
                <button class="btn btn-danger" id="btnExportPDF">
                    <i class="bi bi-file-pdf"></i> Exportar PDF
                </button>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-5">
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
                    <div class="col-12 col-md-5">
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
                    @include('relatorios.partials.filtros-globais', [
                        'ids' => [
                            'periodo' => 'filterPeriodo',
                            'aeronave' => 'filterAeronave',
                        ],
                    ])
                    <div class="col-12 col-md-2 d-flex align-items-end">
                        <button id="clearButton" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-eraser"></i> Limpar
                        </button>
                    </div>
                </div>
                
                <div id="filtersStatus" class="mt-3"></div>
            </div>
        </div>

        <div id="totalResultados" class="mb-3 text-end"></div>

        <div id="cardsContainer">
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="mt-2 text-muted">Carregando dados...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const aeroportosList = @json($aeroportos);
const companhiasList = @json($companhias);

class UserRelatorioCompanhias {
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
        this.cardsContainer = document.getElementById('cardsContainer');
        this.filterAeroporto = document.getElementById('filterAeroporto');
        this.filterCompanhia = document.getElementById('filterCompanhia');
        this.filterPeriodo = document.getElementById('filterPeriodo');
        this.filterAeronave = document.getElementById('filterAeronave');
        this.clearButton = document.getElementById('clearButton');
        this.totalResultados = document.getElementById('totalResultados');
        this.filtersStatus = document.getElementById('filtersStatus');
        this.btnExportCSV = document.getElementById('btnExportCSV');
        this.btnExportPDF = document.getElementById('btnExportPDF');
    }
    
    async carregarDados() {
        if (this.cardsContainer) {
            this.mostrarLoading();
        }
        
        const aeroportoId = this.filterAeroporto?.value || '';
        const companhiaId = this.filterCompanhia?.value || '';
        const periodo = this.filterPeriodo?.value || '';
        const aeronaveId = this.filterAeronave?.value || '';
        
        let url = this.apiUrl;
        const params = [];
        if (aeroportoId) params.push(`aeroporto_id=${aeroportoId}`);
        if (companhiaId) params.push(`companhia_id=${companhiaId}`);
        if (periodo) params.push(`periodo=${periodo}`);
        if (aeronaveId) params.push(`aeronave_id=${aeronaveId}`);
        if (params.length) url += `?${params.join('&')}`;
        
        try {
            const response = await fetch(url);
            const result = await response.json();
            
            if (result.success) {
                this.dadosOriginais = result.data;
                this.dadosFiltrados = [...result.data];
                this.renderizarCards();
                this.atualizarTotal();
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
        [this.filterPeriodo, this.filterAeronave]
            .filter(Boolean)
            .forEach(elemento => elemento.addEventListener('change', () => this.carregarDados()));
        
        if (this.clearButton) {
            this.clearButton.addEventListener('click', () => this.limparFiltros());
        }
        
        if (this.btnExportCSV) {
            this.btnExportCSV.addEventListener('click', () => this.exportarCSV());
        }
        
        if (this.btnExportPDF) {
            this.btnExportPDF.addEventListener('click', () => this.exportarPDF());
        }
    }
    
    limparFiltros() {
        if (this.filterAeroporto) this.filterAeroporto.value = '';
        if (this.filterCompanhia) this.filterCompanhia.value = '';
        if (this.filterPeriodo) this.filterPeriodo.value = '';
        if (this.filterAeronave) this.filterAeronave.value = '';
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
            if (aeroporto) filtros.push(`<span class="badge bg-primary">Aeroporto: ${this.escapeHtml(aeroporto.nome_aeroporto)}</span>`);
        }
        
        if (companhiaSelecionada) {
            const companhia = companhiasList.find(c => c.id == companhiaSelecionada);
            if (companhia) filtros.push(`<span class="badge bg-primary">Companhia: ${this.escapeHtml(companhia.nome)}</span>`);
        }
        
        html += filtros.join(' ') + '</div>';
        this.filtersStatus.innerHTML = html;
    }
    
    mostrarLoading() {
        if (this.cardsContainer) {
            this.cardsContainer.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2 text-muted">Carregando dados...</p>
                </div>
            `;
        }
    }
    
    mostrarErro() {
        if (this.cardsContainer) {
            this.cardsContainer.innerHTML = `
                <div class="alert alert-danger text-center">
                    <i class="bi bi-exclamation-triangle"></i> 
                    Erro ao carregar dados. Tente novamente mais tarde.
                </div>
            `;
        }
    }
    
    renderizarCards() {
        if (!this.cardsContainer) return;
        
        if (this.dadosFiltrados.length === 0) {
            this.cardsContainer.innerHTML = `
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> 
                    Nenhum aeroporto encontrado com os filtros selecionados.
                </div>
            `;
            return;
        }
        
        this.cardsContainer.innerHTML = `
            <div class="row g-4">
                ${this.dadosFiltrados.map(item => `
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm hover-card">
                            <div class="card-header bg-gradient-primary text-white">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                    <div>
                                        <i class="bi bi-building fs-4"></i>
                                        <h5 class="mb-0 mt-2">${this.escapeHtml(item.aeroporto)}</h5>
                                    </div>
                                    <span class="badge bg-light text-dark rounded-pill">
                                        <i class="bi bi-airplane"></i> ${item.quantidade_companhias}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                ${item.companhias.length > 0 ? `
                                    <div class="companhias-list">
                                        ${item.companhias.map(c => `
                                            <div class="companhia-card mb-2 p-2 border rounded">
                                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                    <div class="flex-grow-1">
                                                        <i class="bi bi-building text-primary me-2"></i>
                                                        <strong>${this.escapeHtml(c.nome)}</strong>
                                                    </div>
                                                    ${c.codigo ? `
                                                        <span class="badge bg-secondary">${this.escapeHtml(c.codigo)}</span>
                                                    ` : ''}
                                                </div>
                                            </div>
                                        `).join('')}
                                    </div>
                                ` : `
                                    <div class="text-center text-muted py-4">
                                        <i class="bi bi-building-x fs-1"></i>
                                        <p class="mt-2 mb-0">Nenhuma companhia associada</p>
                                    </div>
                                `}
                            </div>
                            <div class="card-footer bg-transparent">
                                <small class="text-muted">
                                    <i class="bi bi-calendar3"></i> 
                                    Atualizado em ${this.formatarData()}
                                </small>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;
    }
    
    atualizarTotal() {
        if (this.totalResultados) {
            const total = this.dadosFiltrados.length;
            const texto = total === 1 ? 'aeroporto encontrado' : 'aeroportos encontrados';
            this.totalResultados.innerHTML = `
                <span class="badge bg-info fs-6">
                    <i class="bi bi-search"></i> ${total} ${texto}
                </span>
            `;
        }
    }
    
    exportarCSV() {
        if (this.dadosFiltrados.length === 0) {
            alert('Não há dados para exportar!');
            return;
        }
        
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
        link.setAttribute('download', `relatorio_companhias_por_aeroporto_${this.formatarDataArquivo()}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }
    
    exportarPDF() {
        if (this.dadosFiltrados.length === 0) {
            alert('Não há dados para exportar!');
            return;
        }
        
        const printWindow = window.open('', '_blank');
        
        let htmlContent = `
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Relatório - Companhias por Aeroporto</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    h1 { color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
                    .header { text-align: center; margin-bottom: 30px; }
                    .filters-info { background: #f0f0f0; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: top; }
                    th { background: #667eea; color: white; }
                    .badge { display: inline-block; padding: 2px 6px; margin: 2px; background: #764ba2; color: white; border-radius: 12px; font-size: 11px; }
                    .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>✈️ Relatório: Companhias Aéreas por Aeroporto</h1>
                    <p>Gerado em: ${new Date().toLocaleString('pt-BR')}</p>
                </div>
        `;
        
        const aeroportoSelecionado = this.filterAeroporto?.value;
        const companhiaSelecionada = this.filterCompanhia?.value;
        
        if (aeroportoSelecionado || companhiaSelecionada) {
            htmlContent += `<div class="filters-info"><strong>📊 Filtros aplicados:</strong><br>`;
            if (aeroportoSelecionado) {
                const aeroporto = aeroportosList.find(a => a.id == aeroportoSelecionado);
                if (aeroporto) htmlContent += `• Aeroporto: ${aeroporto.nome_aeroporto}<br>`;
            }
            if (companhiaSelecionada) {
                const companhia = companhiasList.find(c => c.id == companhiaSelecionada);
                if (companhia) htmlContent += `• Companhia: ${companhia.nome}<br>`;
            }
            htmlContent += `</div>`;
        }
        
        htmlContent += `
            <table>
                <thead><tr><th>Aeroporto</th><th width="80">Qtd</th><th>Companhias Aéreas</th></tr></thead>
                <tbody>
        `;
        
        this.dadosFiltrados.forEach(item => {
            htmlContent += `
                <tr>
                    <td><strong>${this.escapeHtml(item.aeroporto)}</strong></td>
                    <td style="text-align:center">${item.quantidade_companhias}</td>
                    <td>${item.companhias.map(c => `<span class="badge">${this.escapeHtml(c.nome)}</span>`).join('')}</td>
                </tr>
            `;
        });
        
        htmlContent += `
                </tbody>
            </table>
            <div class="footer">
                <p>Total de aeroportos listados: ${this.dadosFiltrados.length}</p>
                <p>Relatório gerado automaticamente pelo sistema</p>
            </div>
            </body>
            </html>
        `;
        
        printWindow.document.write(htmlContent);
        printWindow.document.close();
        printWindow.onload = () => printWindow.print();
    }
    
    formatarData() {
        return new Date().toLocaleDateString('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    formatarDataArquivo() {
        return new Date().toISOString().slice(0, 19).replace(/:/g, '-');
    }
    
    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

new UserRelatorioCompanhias();
</script>

<style>
.alert-sm {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.hover-card {
    transition: transform 0.3s, box-shadow 0.3s;
    overflow: hidden;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.companhia-card {
    transition: all 0.2s;
    background-color: #f8f9fa;
}

.companhia-card:hover {
    background-color: #e9ecef;
}

.card-header {
    border-bottom: none;
}

.card-footer {
    border-top: 1px solid rgba(0,0,0,0.05);
}

@media (max-width: 768px) {
    .hover-card:hover {
        transform: translateY(-3px);
    }
    
    .btn {
        width: 100%;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
    }
}
</style>
@endpush
