{{-- resources/views/relatorios/voos-por-aeroporto.blade.php --}}
@extends('layouts.app')

@section('title', 'Relatório - Voos por Aeroporto')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h3 class="fw-bold">
                    <i class="bi bi-airplane"></i> Voos por Aeroporto
                </h3>
                <p class="text-muted">Estatísticas de voos, passageiros e notas por aeroporto</p>
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
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calendar"></i> Período
                        </label>
                        <select id="filterPeriodo" class="form-select">
                            <option value="">📅 TODOS OS PERÍODOS</option>
                            <option value="hoje">📆 Hoje</option>
                            <option value="semana">📊 Esta Semana</option>
                            <option value="mes">📈 Este Mês</option>
                            <option value="ano">📅 Este Ano</option>
                        </select>
                    </div>
                    @include('relatorios.partials.filtros-globais', [
                        'ids' => [
                            'aeroporto' => 'filterAeroporto',
                            'companhia' => 'filterCompanhia',
                            'aeronave' => 'filterAeronave',
                        ],
                    ])
                    <div class="col-12 col-md-6 d-flex align-items-end">
                        <button id="clearButton" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-eraser"></i> Limpar Filtros
                        </button>
                    </div>
                </div>
                
                <div id="filtersStatus" class="mt-3"></div>
            </div>
        </div>

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

@push('scripts')
<script>
class UserRelatorioVoosPorAeroporto {
    constructor() {
        this.apiUrl = '/api/relatorios/voos-por-aeroporto';
        this.dados = [];
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
        this.filterPeriodo = document.getElementById('filterPeriodo');
        this.filterAeroporto = document.getElementById('filterAeroporto');
        this.filterCompanhia = document.getElementById('filterCompanhia');
        this.filterAeronave = document.getElementById('filterAeronave');
        this.clearButton = document.getElementById('clearButton');
        this.btnExportCSV = document.getElementById('btnExportCSV');
        this.btnExportPDF = document.getElementById('btnExportPDF');
        this.filtersStatus = document.getElementById('filtersStatus');
    }
    
    async carregarDados() {
        this.mostrarLoading();
        
        const params = new URLSearchParams();
        if (this.filterPeriodo?.value) params.set('periodo', this.filterPeriodo.value);
        if (this.filterAeroporto?.value) params.set('aeroporto_id', this.filterAeroporto.value);
        if (this.filterCompanhia?.value) params.set('companhia_id', this.filterCompanhia.value);
        if (this.filterAeronave?.value) params.set('aeronave_id', this.filterAeronave.value);
        const url = params.size ? `${this.apiUrl}?${params.toString()}` : this.apiUrl;
        
        try {
            const response = await fetch(url);
            const result = await response.json();
            
            if (result.success) {
                this.dados = result.data;
                this.renderizarCards();
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
        if (this.filterPeriodo) {
            this.filterPeriodo.addEventListener('change', () => this.carregarDados());
        }
        [this.filterAeroporto, this.filterCompanhia, this.filterAeronave]
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
        if (this.filterPeriodo) this.filterPeriodo.value = '';
        if (this.filterAeroporto) this.filterAeroporto.value = '';
        if (this.filterCompanhia) this.filterCompanhia.value = '';
        if (this.filterAeronave) this.filterAeronave.value = '';
        this.carregarDados();
    }
    
    atualizarStatusFiltros() {
        if (!this.filtersStatus) return;
        
        const periodo = this.filterPeriodo?.value;
        
        if (!periodo) {
            this.filtersStatus.innerHTML = `
                <div class="alert alert-info alert-sm mb-0">
                    <i class="bi bi-info-circle"></i> Mostrando <strong>TODOS</strong> os períodos
                </div>
            `;
            return;
        }
        
        const periodoTexto = {
            'hoje': 'Hoje',
            'semana': 'Esta Semana',
            'mes': 'Este Mês',
            'ano': 'Este Ano'
        }[periodo];
        
        this.filtersStatus.innerHTML = `
            <div class="alert alert-primary alert-sm mb-0">
                <i class="bi bi-funnel"></i> Filtro: <strong>${periodoTexto}</strong>
            </div>
        `;
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
                    Erro ao carregar dados. Tente novamente.
                </div>
            `;
        }
    }
    
    renderizarCards() {
        if (!this.cardsContainer) return;
        
        if (this.dados.length === 0) {
            this.cardsContainer.innerHTML = `
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> 
                    Nenhum voo encontrado para o período selecionado.
                </div>
            `;
            return;
        }
        
        this.cardsContainer.innerHTML = `
            <div class="row g-4">
                ${this.dados.map(item => `
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm hover-card">
                            <div class="card-header bg-gradient-primary text-white">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                    <div>
                                        <i class="bi bi-building fs-4"></i>
                                        <h5 class="mb-0 mt-2">${this.escapeHtml(item.aeroporto)}</h5>
                                    </div>
                                    <span class="badge bg-light text-dark rounded-pill">
                                        <i class="bi bi-airplane"></i> ${this.formatarNumero(item.total_voos)} voos
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>Passageiros</small>
                                        <small><strong>${this.formatarNumero(item.total_passageiros)}</strong></small>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>Média por voo</small>
                                        <small><strong>${this.formatarNumero(item.media_passageiros_por_voo)}</strong></small>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>⭐ Nota média</small>
                                        <small>
                                            <span class="badge ${item.media_geral >= 7 ? 'bg-success' : (item.media_geral >= 5 ? 'bg-warning' : 'bg-danger')}">
                                                ${item.media_geral}/10
                                            </span>
                                        </small>
                                    </div>
                                </div>
                                <div class="progress mb-2" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: ${(item.media_geral / 10) * 100}%"></div>
                                </div>
                                <div class="d-flex justify-content-between mt-3">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar"></i> Regular: ${item.voos_regulares}
                                    </small>
                                    <small class="text-muted">
                                        <i class="bi bi-star"></i> Charter: ${item.voos_charter}
                                    </small>
                                </div>
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
    
    exportarCSV() {
        if (this.dados.length === 0) {
            alert('Não há dados para exportar!');
            return;
        }
        
        const headers = ['Aeroporto', 'Total de Voos', 'Total de Passageiros', 'Média/Voo', 'Nota Média'];
        const rows = this.dados.map(item => [
            item.aeroporto,
            item.total_voos,
            item.total_passageiros,
            item.media_passageiros_por_voo,
            item.media_geral
        ]);
        
        const csvContent = [headers, ...rows]
            .map(row => row.map(cell => `"${String(cell).replace(/"/g, '""')}"`).join(','))
            .join('\n');
        
        const blob = new Blob(["\uFEFF" + csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.href = url;
        link.setAttribute('download', `relatorio_voos_por_aeroporto_${this.formatarDataArquivo()}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }
    
    exportarPDF() {
        if (this.dados.length === 0) {
            alert('Não há dados para exportar!');
            return;
        }
        
        const printWindow = window.open('', '_blank');
        
        let htmlContent = `
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Relatório - Voos por Aeroporto</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    h1 { color: #333; border-bottom: 2px solid #667eea; }
                    .header { text-align: center; margin-bottom: 30px; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background: #667eea; color: white; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>✈️ Relatório: Voos por Aeroporto</h1>
                    <p>Gerado em: ${new Date().toLocaleString('pt-BR')}</p>
                </div>
                <table>
                    <thead><tr><th>Aeroporto</th><th>Voos</th><th>Passageiros</th><th>Média/Voo</th><th>Nota</th></tr></thead>
                    <tbody>
                        ${this.dados.map(item => `
                            <tr>
                                <td>${this.escapeHtml(item.aeroporto)}</td>
                                <td>${item.total_voos}</td>
                                <td>${this.formatarNumero(item.total_passageiros)}</td>
                                <td>${item.media_passageiros_por_voo}</td>
                                <td>⭐ ${item.media_geral}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </body>
            </html>
        `;
        
        printWindow.document.write(htmlContent);
        printWindow.document.close();
        printWindow.onload = () => printWindow.print();
    }
    
    formatarNumero(num) {
        return new Intl.NumberFormat('pt-BR').format(num);
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

new UserRelatorioVoosPorAeroporto();
</script>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.hover-card {
    transition: transform 0.3s, box-shadow 0.3s;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.alert-sm {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}
</style>
@endpush
@endsection
