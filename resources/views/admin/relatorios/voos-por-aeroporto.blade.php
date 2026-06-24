{{-- resources/views/admin/relatorios/voos-por-aeroporto.blade.php --}}
@extends('layouts.app')

@section('title', 'Relatório - Voos por Aeroporto')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h3 class="fw-bold">✈️ Relatório: Voos por Aeroporto</h3>
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

        <!-- Cards de Totais -->
        <div class="row mb-4" id="cardsTotais">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Total de Aeroportos</h6>
                                <h2 class="mb-0" id="totalAeroportos">-</h2>
                            </div>
                            <i class="bi bi-building fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Total de Voos</h6>
                                <h2 class="mb-0" id="totalVoos">-</h2>
                            </div>
                            <i class="bi bi-airplane fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Total de Passageiros</h6>
                                <h2 class="mb-0" id="totalPassageiros">-</h2>
                            </div>
                            <i class="bi bi-people fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Média Geral de Notas</h6>
                                <h2 class="mb-0" id="mediaGeral">-</h2>
                            </div>
                            <i class="bi bi-star fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <!-- Filtros -->
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-4">
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
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-bold">
                            <i class="bi bi-sort-down"></i> Ordenar por
                        </label>
                        <select id="filterOrdenacao" class="form-select">
                            <option value="total_voos">✈️ Total de Voos</option>
                            <option value="total_passageiros">👥 Total de Passageiros</option>
                            <option value="media_geral">⭐ Média Geral</option>
                            <option value="aeroporto">🏢 Nome do Aeroporto</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-4 d-flex align-items-end">
                        <button id="clearButton" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-eraser"></i> Limpar Filtros
                        </button>
                    </div>
                </div>

                <div id="filtersStatus" class="mb-3"></div>

                <!-- Tabela responsiva -->
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width: 200px;">Aeroporto</th>
                                <th class="text-center">✈️ Voos</th>
                                <th class="text-center">👥 Passageiros</th>
                                <th class="text-center">Regular/Charter</th>
                                <th class="text-center">⭐ Notas</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Carregando...</span>
                                    </div>
                                    <p class="mt-2 text-muted">Carregando dados...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalhes -->
<div class="modal fade" id="detalhesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-info-circle"></i> Detalhes do Aeroporto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody">
                Carregando...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
class AdminRelatorioVoosPorAeroporto {
    constructor() {
        this.apiUrl = '/api/relatorios/voos-por-aeroporto';
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
        this.filterPeriodo = document.getElementById('filterPeriodo');
        this.filterAeroporto = document.getElementById('filterAeroporto');
        this.filterCompanhia = document.getElementById('filterCompanhia');
        this.filterAeronave = document.getElementById('filterAeronave');
        this.filterOrdenacao = document.getElementById('filterOrdenacao');
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
                this.dadosOriginais = result.data;
                this.dadosFiltrados = [...result.data];
                this.totais = result.totais;
                this.aplicarOrdenacao();
                this.renderizarTabela();
                this.atualizarCardsTotais();
                this.atualizarStatusFiltros();
            } else {
                this.mostrarErro();
            }
        } catch (error) {
            console.error('Erro:', error);
            this.mostrarErro();
        }
    }
    
    aplicarOrdenacao() {
        const ordenacao = this.filterOrdenacao?.value || 'total_voos';
        
        this.dadosFiltrados.sort((a, b) => {
            if (ordenacao === 'aeroporto') {
                return a.aeroporto.localeCompare(b.aeroporto);
            }
            return b[ordenacao] - a[ordenacao];
        });
    }
    
    configurarEventos() {
        if (this.filterPeriodo) {
            this.filterPeriodo.addEventListener('change', () => this.carregarDados());
        }
        [this.filterAeroporto, this.filterCompanhia, this.filterAeronave]
            .filter(Boolean)
            .forEach(elemento => elemento.addEventListener('change', () => this.carregarDados()));
        
        if (this.filterOrdenacao) {
            this.filterOrdenacao.addEventListener('change', () => {
                this.aplicarOrdenacao();
                this.renderizarTabela();
            });
        }
        
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
        if (this.filterOrdenacao) this.filterOrdenacao.value = 'total_voos';
        this.carregarDados();
    }
    
    atualizarStatusFiltros() {
        if (!this.filtersStatus) return;
        
        const periodo = this.filterPeriodo?.value;
        
        if (!periodo) {
            this.filtersStatus.innerHTML = `
                <div class="alert alert-info alert-sm mb-0">
                    <i class="bi bi-info-circle"></i> 
                    Mostrando <strong>TODOS</strong> os períodos
                </div>
            `;
            return;
        }
        
        const periodoTexto = {
            'hoje': 'Hoje',
            'semana': 'Esta Semana',
            'mes': 'Este Mês',
            'ano': 'Este Ano'
        }[periodo] || periodo;
        
        this.filtersStatus.innerHTML = `
            <div class="alert alert-primary alert-sm mb-0">
                <i class="bi bi-funnel"></i> 
                Filtro ativo: <strong>${periodoTexto}</strong>
            </div>
        `;
    }
    
    atualizarCardsTotais() {
        document.getElementById('totalAeroportos').innerHTML = this.totais.total_aeroportos || 0;
        document.getElementById('totalVoos').innerHTML = this.formatarNumero(this.totais.total_voos || 0);
        document.getElementById('totalPassageiros').innerHTML = this.formatarNumero(this.totais.total_passageiros || 0);
        document.getElementById('mediaGeral').innerHTML = (this.totais.media_geral_geral || 0).toFixed(1);
    }
    
    mostrarLoading() {
        if (this.tbody) {
            this.tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-5">
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
                    <td colspan="6" class="text-center text-danger py-5">
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
                    <td colspan="6" class="text-center text-muted py-4">
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
                </td>
                <td class="align-middle text-center">
                    <span class="badge bg-primary fs-6">${this.formatarNumero(item.total_voos)}</span>
                </td>
                <td class="align-middle text-center">
                    <span class="badge bg-success fs-6">${this.formatarNumero(item.total_passageiros)}</span>
                    <br>
                    <small class="text-muted">média: ${this.formatarNumero(item.media_passageiros_por_voo)}/voo</small>
                </td>
                <td class="align-middle text-center">
                    <span class="badge bg-info">Regular: ${item.voos_regulares}</span>
                    <br>
                    <span class="badge bg-warning text-dark">Charter: ${item.voos_charter}</span>
                </td>
                <td class="align-middle text-center">
                    <div class="d-flex flex-column gap-1">
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-success" style="width: ${(item.media_geral / 10) * 100}%"></div>
                        </div>
                        <span class="badge bg-secondary">⭐ ${item.media_geral}/10</span>
                    </div>
                </td>
                <td class="align-middle text-center">
                    <button class="btn btn-sm btn-outline-info" onclick="verDetalhes(${item.id})">
                        <i class="bi bi-eye"></i> Detalhes
                    </button>
                </td>
            </tr>
        `).join('');
    }
    
    verDetalhes(id) {
        const aeroporto = this.dadosFiltrados.find(a => a.id === id);
        if (!aeroporto) return;
        
        const modalBody = document.getElementById('modalBody');
        
        // HTML dos horários
        const horariosHtml = Object.entries(aeroporto.voos_por_horario)
            .map(([key, value]) => {
                const nomes = {
                    'EAM': '🌅 Madrugada (00h-06h)',
                    'AM': '☀️ Manhã (06h-12h)',
                    'AN': '🌤️ Tarde (12h-18h)',
                    'PM': '🌙 Noite (18h-00h)',
                    'ALL': '🔄 Diário'
                };
                return `<div class="col-6 mb-2"><strong>${nomes[key]}:</strong> ${value} voos</div>`;
            }).join('');
        
        modalBody.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <strong>📊 Estatísticas Gerais</strong>
                        </div>
                        <div class="card-body">
                            <p><strong>✈️ Total de Voos:</strong> ${this.formatarNumero(aeroporto.total_voos)}</p>
                            <p><strong>👥 Total de Passageiros:</strong> ${this.formatarNumero(aeroporto.total_passageiros)}</p>
                            <p><strong>📈 Média passageiros/voo:</strong> ${this.formatarNumero(aeroporto.media_passageiros_por_voo)}</p>
                            <p><strong>🔄 Voos Regulares:</strong> ${aeroporto.voos_regulares}</p>
                            <p><strong>✈️ Voos Charter:</strong> ${aeroporto.voos_charter}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header bg-success text-white">
                            <strong>⭐ Avaliações</strong>
                        </div>
                        <div class="card-body">
                            <p><strong>Objetivo:</strong> ${aeroporto.nota_obj}/10</p>
                            <div class="progress mb-2"><div class="progress-bar bg-success" style="width: ${aeroporto.nota_obj * 10}%"></div></div>
                            <p><strong>Pontualidade:</strong> ${aeroporto.nota_pontualidade}/10</p>
                            <div class="progress mb-2"><div class="progress-bar bg-info" style="width: ${aeroporto.nota_pontualidade * 10}%"></div></div>
                            <p><strong>Serviços:</strong> ${aeroporto.nota_servicos}/10</p>
                            <div class="progress mb-2"><div class="progress-bar bg-warning" style="width: ${aeroporto.nota_servicos * 10}%"></div></div>
                            <p><strong>Pátio:</strong> ${aeroporto.nota_patio}/10</p>
                            <div class="progress mb-2"><div class="progress-bar bg-danger" style="width: ${aeroporto.nota_patio * 10}%"></div></div>
                            <p><strong>⭐ Média Geral:</strong> <span class="badge bg-primary fs-6">${aeroporto.media_geral}/10</span></p>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <strong>🕐 Distribuição por Horário</strong>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                ${horariosHtml}
                            </div>
                        </div>
                    </div>
                </div>
                ${aeroporto.companhias.length > 0 ? `
                <div class="col-12 mt-3">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <strong>🏢 Companhias que operam no aeroporto</strong>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                ${aeroporto.companhias.map(c => `
                                    <div class="col-md-6 mb-2">
                                        <div class="border rounded p-2">
                                            <strong>${this.escapeHtml(c.nome)}</strong>
                                            ${c.codigo ? `<span class="badge bg-secondary">${c.codigo}</span>` : ''}
                                            <br>
                                            <small class="text-muted">${c.total_voos} voos | ${this.formatarNumero(c.total_passageiros)} passageiros</small>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                </div>
                ` : ''}
            </div>
        `;
        
        const modal = new bootstrap.Modal(document.getElementById('detalhesModal'));
        modal.show();
    }
    
    exportarCSV() {
        if (this.dadosFiltrados.length === 0) {
            alert('Não há dados para exportar!');
            return;
        }
        
        const headers = [
            'Aeroporto', 
            'Total de Voos', 
            'Total de Passageiros', 
            'Média Passageiros/Voo',
            'Voos Regulares',
            'Voos Charter',
            'Nota Objetivo',
            'Nota Pontualidade',
            'Nota Serviços',
            'Nota Pátio',
            'Média Geral'
        ];
        
        const rows = this.dadosFiltrados.map(item => [
            item.aeroporto,
            item.total_voos,
            item.total_passageiros,
            item.media_passageiros_por_voo,
            item.voos_regulares,
            item.voos_charter,
            item.nota_obj,
            item.nota_pontualidade,
            item.nota_servicos,
            item.nota_patio,
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
                <title>Relatório - Voos por Aeroporto</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    h1 { color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
                    .header { text-align: center; margin-bottom: 30px; }
                    .filters-info { background: #f0f0f0; padding: 10px; margin-bottom: 20px; border-radius: 5px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background: #667eea; color: white; }
                    .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
                    .badge { display: inline-block; padding: 2px 6px; background: #764ba2; color: white; border-radius: 4px; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>✈️ Relatório: Voos por Aeroporto</h1>
                    <p>Gerado em: ${new Date().toLocaleString('pt-BR')}</p>
                </div>
        `;
        
        const periodo = this.filterPeriodo?.value;
        if (periodo) {
            const periodoTexto = {'hoje':'Hoje','semana':'Esta Semana','mes':'Este Mês','ano':'Este Ano'}[periodo];
            htmlContent += `<div class="filters-info"><strong>📊 Período:</strong> ${periodoTexto}</div>`;
        }
        
        htmlContent += `
            <table>
                <thead>
                    <tr><th>Aeroporto</th><th>Voos</th><th>Passageiros</th><th>Média/Voo</th><th>Média Geral</th></tr>
                </thead>
                <tbody>
        `;
        
        this.dadosFiltrados.forEach(item => {
            htmlContent += `
                <tr>
                    <td>${this.escapeHtml(item.aeroporto)}</td>
                    <td>${item.total_voos}</td>
                    <td>${this.formatarNumero(item.total_passageiros)}</td>
                    <td>${item.media_passageiros_por_voo}</td>
                    <td>⭐ ${item.media_geral}</td>
                </tr>
            `;
        });
        
        htmlContent += `
                </tbody>
             </table>
            <div class="footer">
                <p>Total de aeroportos: ${this.dadosFiltrados.length} | Total de voos: ${this.totais.total_voos} | Total de passageiros: ${this.formatarNumero(this.totais.total_passageiros)}</p>
                <p>Relatório gerado automaticamente pelo sistema</p>
            </div>
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

window.verDetalhes = (id) => {
    const relatorio = new AdminRelatorioVoosPorAeroporto();
    relatorio.verDetalhes(id);
};

new AdminRelatorioVoosPorAeroporto();
</script>

<style>
.alert-sm {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.progress {
    background-color: #e9ecef;
    border-radius: 10px;
}

.progress-bar {
    transition: width 0.3s ease;
}

@media (max-width: 768px) {
    .btn {
        width: 100%;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
    }
}
</style>
@endpush
@endsection
