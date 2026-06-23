class RelatorioDesempenhoCompanhias {
    constructor(config) {
        this.apiUrl = config.apiUrl;
        this.modoAdmin = Boolean(config.modoAdmin);
        this.dados = [];
    }

    iniciar() {
        this.container = document.getElementById('resultadoRelatorio');
        this.filtroPeriodo = document.getElementById('filtroPeriodo');
        this.filtroCompanhia = document.getElementById('filtroCompanhia');
        this.botaoLimpar = document.getElementById('limparFiltros');
        this.botaoExportar = document.getElementById('exportarCsv');

        if (!this.container || !this.filtroPeriodo || !this.filtroCompanhia) {
            return;
        }

        this.filtroPeriodo.addEventListener('change', () => this.carregar());
        this.filtroCompanhia.addEventListener('change', () => this.carregar());
        this.botaoLimpar?.addEventListener('click', () => this.limparFiltros());
        this.botaoExportar?.addEventListener('click', () => this.exportarCsv());

        this.carregar();
    }

    numero(valor) {
        return new Intl.NumberFormat('pt-BR').format(valor || 0);
    }

    decimal(valor) {
        return Number(valor || 0).toFixed(1);
    }

    escapeHtml(valor) {
        const elemento = document.createElement('div');
        elemento.textContent = valor ?? '';
        return elemento.innerHTML;
    }

    notaBadge(nota) {
        const classe = nota >= 7
            ? 'success'
            : nota >= 5
                ? 'warning text-dark'
                : 'danger';

        return `<span class="badge bg-${classe}">${this.decimal(nota)}/10</span>`;
    }

    mostrarLoading() {
        this.container.innerHTML = this.modoAdmin
            ? '<tr><td colspan="7" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>'
            : '<div class="col-12 text-center py-5"><div class="spinner-border text-primary"></div></div>';
    }

    mostrarVazio() {
        this.container.innerHTML = this.modoAdmin
            ? '<tr><td colspan="7" class="text-center text-muted py-5">Nenhum voo encontrado para os filtros selecionados.</td></tr>'
            : '<div class="col-12"><div class="alert alert-info text-center">Nenhum voo encontrado para os filtros selecionados.</div></div>';
    }

    mostrarErro(mensagem) {
        const texto = this.escapeHtml(mensagem);
        this.container.innerHTML = this.modoAdmin
            ? `<tr><td colspan="7" class="text-center text-danger py-5">${texto}</td></tr>`
            : `<div class="col-12"><div class="alert alert-danger">${texto}</div></div>`;
    }

    renderizarTabela() {
        this.container.innerHTML = this.dados.map(item => `
            <tr>
                <td>
                    <strong>${this.escapeHtml(item.nome)}</strong>
                    <small class="d-block text-muted">${this.escapeHtml(item.codigo || 'Sem código')}</small>
                </td>
                <td class="text-center">${this.numero(item.total_voos)}</td>
                <td class="text-center">${this.numero(item.total_passageiros)}</td>
                <td class="text-center">${this.numero(item.media_passageiros_por_voo)}</td>
                <td class="text-center">
                    ${item.total_aeroportos} aeroportos<br>
                    <small class="text-muted">${item.total_aeronaves} aeronaves</small>
                </td>
                <td class="text-center">
                    <span class="badge bg-info text-dark">${this.numero(item.voos_regulares)}</span>
                    <span class="badge bg-secondary">${this.numero(item.voos_charter)}</span>
                </td>
                <td class="text-center">${this.notaBadge(item.media_geral)}</td>
            </tr>
        `).join('');
    }

    renderizarCards() {
        this.container.innerHTML = this.dados.map(item => `
            <div class="col-md-6 col-xl-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${this.escapeHtml(item.nome)}</strong>
                            <small class="d-block text-muted">${this.escapeHtml(item.codigo || '')}</small>
                        </div>
                        ${this.notaBadge(item.media_geral)}
                    </div>
                    <div class="card-body">
                        <div class="row text-center g-3 mb-3">
                            <div class="col-4"><strong>${this.numero(item.total_voos)}</strong><small class="d-block text-muted">Voos</small></div>
                            <div class="col-4"><strong>${this.numero(item.total_passageiros)}</strong><small class="d-block text-muted">Passageiros</small></div>
                            <div class="col-4"><strong>${this.numero(item.media_passageiros_por_voo)}</strong><small class="d-block text-muted">Pax/voo</small></div>
                        </div>
                        <p class="small mb-2"><strong>Cobertura:</strong> ${item.total_aeroportos} aeroportos e ${item.total_aeronaves} aeronaves</p>
                        <p class="small mb-3"><strong>Operação:</strong> ${this.numero(item.voos_regulares)} regulares / ${this.numero(item.voos_charter)} charter</p>
                        <div class="row g-2 small">
                            <div class="col-6">Objetivo: ${this.decimal(item.nota_obj)}</div>
                            <div class="col-6">Pontualidade: ${this.decimal(item.nota_pontualidade)}</div>
                            <div class="col-6">Serviços: ${this.decimal(item.nota_servicos)}</div>
                            <div class="col-6">Pátio: ${this.decimal(item.nota_patio)}</div>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    atualizarTotais(totais) {
        document.getElementById('totalCompanhias').textContent = this.numero(totais.total_companhias);
        document.getElementById('totalVoos').textContent = this.numero(totais.total_voos);
        document.getElementById('totalPassageiros').textContent = this.numero(totais.total_passageiros);
        document.getElementById('mediaGeral').textContent = `${this.decimal(totais.media_geral)}/10`;
    }

    async carregar() {
        this.mostrarLoading();

        const params = new URLSearchParams();
        if (this.filtroPeriodo.value) {
            params.set('periodo', this.filtroPeriodo.value);
        }
        if (this.filtroCompanhia.value) {
            params.set('companhia_id', this.filtroCompanhia.value);
        }

        const url = params.size
            ? `${this.apiUrl}?${params.toString()}`
            : this.apiUrl;

        try {
            const response = await fetch(url, {
                headers: { Accept: 'application/json' },
            });
            const resultado = await response.json();

            if (!response.ok || !resultado.success) {
                throw new Error('Não foi possível carregar o relatório.');
            }

            this.dados = resultado.data;
            this.atualizarTotais(resultado.totais);

            if (!this.dados.length) {
                this.mostrarVazio();
                return;
            }

            this.modoAdmin
                ? this.renderizarTabela()
                : this.renderizarCards();
        } catch (error) {
            this.mostrarErro(error.message || 'Erro ao carregar o relatório.');
        }
    }

    limparFiltros() {
        this.filtroPeriodo.value = '';
        this.filtroCompanhia.value = '';
        this.carregar();
    }

    exportarCsv() {
        if (!this.dados.length) {
            return;
        }

        const linhas = [
            ['Companhia', 'Código', 'Voos', 'Passageiros', 'Pax por voo', 'Aeroportos', 'Aeronaves', 'Regular', 'Charter', 'Média geral'],
            ...this.dados.map(item => [
                item.nome,
                item.codigo,
                item.total_voos,
                item.total_passageiros,
                item.media_passageiros_por_voo,
                item.total_aeroportos,
                item.total_aeronaves,
                item.voos_regulares,
                item.voos_charter,
                item.media_geral,
            ]),
        ];

        const csv = linhas.map(linha => linha.map(valor =>
            `"${String(valor ?? '').replaceAll('"', '""')}"`
        ).join(';')).join('\n');

        const url = URL.createObjectURL(
            new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8' })
        );
        const link = document.createElement('a');
        link.href = url;
        link.download = 'desempenho_companhias.csv';
        link.click();
        URL.revokeObjectURL(url);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (!window.desempenhoCompanhiasConfig) {
        return;
    }

    new RelatorioDesempenhoCompanhias(
        window.desempenhoCompanhiasConfig
    ).iniciar();
});
