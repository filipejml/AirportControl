{{-- resources/views/admin/relatorios/companhias-por-aeroporto.blade.php --}}
@extends('layouts.app')

@section('title', 'Relatório - Companhias por Aeroporto')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h3 class="fw-bold">✈️ Relatório: Companhias por Aeroporto</h3>
                <p class="text-muted">Visualize todas as companhias aéreas operando em cada aeroporto</p>
                @include('relatorios.partials.status-badges', [
                    'relatorio' => $relatorio,
                    'class' => 'mt-2',
                ])
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

        <div class="card shadow-sm">
            <div class="card-body">
                <!-- Filtros -->
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-4">
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
                    @include('relatorios.partials.filtros-globais', [
                        'ids' => [
                            'periodo' => 'filterPeriodo',
                            'aeronave' => 'filterAeronave',
                        ],
                    ])
                    <div class="col-12 col-md-4">
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
                    <div class="col-12 col-md-4 d-flex align-items-end">
                        <button id="clearButton" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-eraser"></i> Limpar Filtros
                        </button>
                    </div>
                </div>

                <div id="filtersStatus" class="mb-3"></div>

                <!-- Tabela responsiva -->
                <div class="table-responsive">
                    <table class="table table-hover table-sm table-wide">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width: 150px;">Aeroporto</th>
                                <th class="text-center" style="width: 80px;">Qtd</th>
                                <th>Companhias Aéreas</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr>
                                <td colspan="3" class="text-center py-5">
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
@endsection

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
        this.filterPeriodo = document.getElementById('filterPeriodo');
        this.filterAeronave = document.getElementById('filterAeronave');
        this.clearButton = document.getElementById('clearButton');
        this.btnExportCSV = document.getElementById('btnExportCSV');
        this.btnExportPDF = document.getElementById('btnExportPDF');
        this.filtersStatus = document.getElementById('filtersStatus');
    }

    async carregarDados() {
        this.mostrarLoading();

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
        [this.filterPeriodo, this.filterAeronave]
            .filter(Boolean)
            .forEach(elemento => elemento.addEventListener('change', () => this.carregarDados()));

        if (this.clearButton) {
            this.clearButton.addEventListener('click', () => this.limparFiltros());
        }
        this.tbody?.addEventListener('click', (event) => {
            if (event.target.closest('[data-empty-clear]')) {
                this.limparFiltros();
            }
        });

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
                        <i class="bi bi-inbox"></i>
                        <div class="fw-semibold my-2">Sem dados para esse filtro.</div>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-empty-clear>Limpar filtros</button>
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
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
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
                <thead>
                    <tr><th>Aeroporto</th><th width="80">Qtd</th><th>Companhias Aéreas</th></tr>
                </thead>
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

new AdminRelatorioCompanhias();
</script>

<style>
.alert-sm {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

.companhia-item {
    transition: all 0.2s;
}

.companhia-item:hover {
    background-color: #e9ecef !important;
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

@media (max-width: 768px) {
    .companhia-item .d-flex {
        flex-direction: column;
        align-items: flex-start !important;
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
