// public/js/relatorios/admin-tabela.js

class AdminRelatorioCompanhias {
    constructor() {
        this.utils = window.RelatorioUtils;
        this.init();
    }

    async init() {
        // Aguardar DOM carregar
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
        this.filterMinCompanhias = document.getElementById('filterMinCompanhias');
        this.btnExportCSV = document.getElementById('btnExportCSV');
        this.btnExportPDF = document.getElementById('btnExportPDF');
        this.btnPrint = document.getElementById('btnPrint');
    }

    async carregarDados() {
        this.utils.mostrarLoading('tableBody');

        const sucesso = await this.utils.carregarDados();

        if (sucesso) {
            this.renderizarTabela();
        } else {
            this.utils.mostrarErro('tableBody');
        }
    }

    configurarEventos() {
        if (this.filterAeroporto) {
            this.filterAeroporto.addEventListener('input', () => this.filtrar());
        }

        if (this.filterCompanhia) {
            this.filterCompanhia.addEventListener('input', () => this.filtrar());
        }

        if (this.filterMinCompanhias) {
            this.filterMinCompanhias.addEventListener('change', () => this.filtrar());
        }

        this.tbody?.addEventListener('click', (event) => {
            if (event.target.closest('[data-empty-clear]')) {
                this.limparFiltros();
            }
        });

        if (this.btnExportCSV) {
            this.btnExportCSV.addEventListener('click', () => {
                this.utils.exportarCSV(this.utils.dadosFiltrados, 'relatorio_companhias_por_aeroporto');
            });
        }

        if (this.btnExportPDF) {
            this.btnExportPDF.addEventListener('click', () => {
                this.utils.exportarPDF();
            });
        }

        if (this.btnPrint) {
            this.btnPrint.addEventListener('click', () => {
                window.print();
            });
        }
    }

    filtrar() {
        const aeroportoFilter = this.filterAeroporto?.value.toLowerCase() || '';
        const companhiaFilter = this.filterCompanhia?.value.toLowerCase() || '';
        const minCompanhias = parseInt(this.filterMinCompanhias?.value || '0');

        this.utils.dadosFiltrados = this.utils.dadosOriginais.filter(item => {
            // Filtro por nome do aeroporto
            if (aeroportoFilter && !item.aeroporto.toLowerCase().includes(aeroportoFilter)) {
                return false;
            }

            // Filtro por nome da companhia
            if (companhiaFilter) {
                const temCompanhia = item.companhias.some(c =>
                    c.nome.toLowerCase().includes(companhiaFilter) ||
                    (c.codigo && c.codigo.toLowerCase().includes(companhiaFilter))
                );
                if (!temCompanhia) return false;
            }

            // Filtro por quantidade mínima
            if (minCompanhias && item.quantidade_companhias < minCompanhias) {
                return false;
            }

            return true;
        });

        this.renderizarTabela();
        this.atualizarContador();
    }

    limparFiltros() {
        if (this.filterAeroporto) this.filterAeroporto.value = '';
        if (this.filterCompanhia) this.filterCompanhia.value = '';
        if (this.filterMinCompanhias) this.filterMinCompanhias.value = '';
        this.filtrar();
    }

    renderizarTabela() {
        if (!this.tbody) return;

        if (this.utils.dadosFiltrados.length === 0) {
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

        this.tbody.innerHTML = this.utils.dadosFiltrados.map(item => `
            <tr>
                <td class="align-middle">
                    <strong>${this.utils.escapeHtml(item.aeroporto)}</strong>
                    <br>
                    <small class="text-muted">ID: ${item.id_aeroporto}</small>
                </td>
                <td class="align-middle text-center">
                    <span class="badge bg-primary rounded-pill fs-6">${item.quantidade_companhias}</span>
                </td>
                <td class="align-middle">
                    <div class="companhias-container">
                        ${item.companhias.map(c => `
                            <div class="companhia-item mb-2 p-2 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bi bi-building"></i>
                                        <strong>${this.utils.escapeHtml(c.nome)}</strong>
                                    </div>
                                    <div>
                                        ${c.codigo ? `
                                            <span class="badge bg-secondary me-1">${this.utils.escapeHtml(c.codigo)}</span>
                                        ` : ''}
                                        <span class="badge bg-info">ID: ${c.id}</span>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                        ${item.companhias.length === 0 ?
                            '<span class="text-muted"><i class="bi bi-info-circle"></i> Nenhuma companhia associada</span>' :
                            ''}
                    </div>
                </td>
            </tr>
        `).join('');
    }

    atualizarContador() {
        const contador = document.getElementById('resultadosContador');
        if (contador) {
            const total = this.utils.dadosFiltrados.length;
            const totalOriginal = this.utils.dadosOriginais.length;
            contador.innerHTML = `Mostrando ${total} de ${totalOriginal} resultado${total !== 1 ? 's' : ''}`;
        }
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    new AdminRelatorioCompanhias();
});
