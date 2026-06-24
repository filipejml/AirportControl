class RelatorioMovimentacaoPorPeriodo {
    constructor(config) {
        this.apiUrl = config.apiUrl;
        this.modoAdmin = Boolean(config.modoAdmin);
        this.dados = [];
        this.grafico = null;
    }

    iniciar() {
        this.container = document.getElementById('movimentacaoResultado');
        this.agrupamento = document.getElementById('movAgrupamento');
        this.periodo = document.getElementById('movPeriodo');
        this.aeroporto = document.getElementById('movAeroporto');
        this.companhia = document.getElementById('movCompanhia');
        this.aeronave = document.getElementById('movAeronave');
        this.dataInicio = document.getElementById('movDataInicio');
        this.dataFim = document.getElementById('movDataFim');

        if (!this.container || !this.agrupamento) {
            return;
        }

        this.agrupamento.addEventListener('change', () => this.carregar());
        this.periodo?.addEventListener('change', () => this.carregar());
        this.aeroporto?.addEventListener('change', () => this.carregar());
        this.companhia?.addEventListener('change', () => this.carregar());
        this.aeronave?.addEventListener('change', () => this.carregar());
        this.dataInicio.addEventListener('change', () => this.carregar());
        this.dataFim.addEventListener('change', () => this.carregar());
        document.getElementById('limparMovimentacaoFiltros')
            ?.addEventListener('click', () => this.limparFiltros());
        document.getElementById('exportarMovimentacaoCsv')
            ?.addEventListener('click', () => this.exportarCsv());

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

    variacaoBadge(variacao) {
        if (variacao === null || variacao === undefined) {
            return '<span class="badge bg-secondary">Base</span>';
        }

        const classe = variacao > 0 ? 'success' : variacao < 0 ? 'danger' : 'secondary';
        const icone = variacao > 0 ? '↑' : variacao < 0 ? '↓' : '→';
        return `<span class="badge bg-${classe}">${icone} ${Math.abs(variacao).toFixed(1)}%</span>`;
    }

    mostrarLoading() {
        this.container.innerHTML = this.modoAdmin
            ? '<tr><td colspan="7" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>'
            : '<div class="col-12 text-center py-5"><div class="spinner-border text-primary"></div></div>';
    }

    mostrarMensagem(mensagem, erro = false) {
        const classe = erro ? 'danger' : 'info';
        const texto = this.escapeHtml(mensagem);
        this.container.innerHTML = this.modoAdmin
            ? `<tr><td colspan="7" class="text-center text-${classe} py-5">${texto}</td></tr>`
            : `<div class="col-12"><div class="alert alert-${classe} text-center">${texto}</div></div>`;
    }

    renderizarTabela() {
        this.container.innerHTML = this.dados.map(item => `
            <tr>
                <td>
                    <strong>${this.escapeHtml(item.label)}</strong>
                    <small class="d-block text-muted">${item.data_inicio} a ${item.data_fim}</small>
                </td>
                <td class="text-center">${this.numero(item.total_voos)}</td>
                <td class="text-center">${this.numero(item.total_passageiros)}</td>
                <td class="text-center">${this.numero(item.media_passageiros_por_voo)}</td>
                <td class="text-center">
                    <span class="badge bg-info text-dark">${this.numero(item.voos_regulares)}</span>
                    <span class="badge bg-secondary">${this.numero(item.voos_charter)}</span>
                </td>
                <td class="text-center">${this.variacaoBadge(item.variacao_percentual)}</td>
                <td class="text-center">${this.decimal(item.media_geral)}/10</td>
            </tr>
        `).join('');
    }

    renderizarCards() {
        this.container.innerHTML = this.dados.map(item => `
            <div class="col-md-6 col-xl-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <strong>${this.escapeHtml(item.label)}</strong>
                        ${this.variacaoBadge(item.variacao_percentual)}
                    </div>
                    <div class="card-body">
                        <div class="row text-center g-3 mb-3">
                            <div class="col-4"><strong>${this.numero(item.total_voos)}</strong><small class="d-block text-muted">Voos</small></div>
                            <div class="col-4"><strong>${this.numero(item.total_passageiros)}</strong><small class="d-block text-muted">Passageiros</small></div>
                            <div class="col-4"><strong>${this.numero(item.media_passageiros_por_voo)}</strong><small class="d-block text-muted">Pax/voo</small></div>
                        </div>
                        <p class="small mb-2"><strong>Operação:</strong> ${this.numero(item.voos_regulares)} regulares / ${this.numero(item.voos_charter)} charter</p>
                        <p class="small mb-2"><strong>Cobertura:</strong> ${item.total_aeroportos} aeroportos e ${item.total_companhias} companhias</p>
                        <p class="small mb-0"><strong>Avaliação:</strong> ${this.decimal(item.media_geral)}/10</p>
                    </div>
                </div>
            </div>
        `).join('');
    }

    atualizarTotais(totais) {
        document.getElementById('movTotalPeriodos').textContent = this.numero(totais.total_periodos);
        document.getElementById('movTotalVoos').textContent = this.numero(totais.total_voos);
        document.getElementById('movTotalPassageiros').textContent = this.numero(totais.total_passageiros);

        const maior = totais.maior_movimento;
        document.getElementById('movMaiorMovimento').textContent = maior
            ? `${maior.label} (${this.numero(maior.total_voos)})`
            : '-';
    }

    renderizarGrafico() {
        if (typeof Chart === 'undefined') {
            return;
        }

        this.grafico?.destroy();
        const contexto = document.getElementById('movimentacaoChart');
        this.grafico = new Chart(contexto, {
            type: 'line',
            data: {
                labels: this.dados.map(item => item.label),
                datasets: [
                    {
                        label: 'Voos',
                        data: this.dados.map(item => item.total_voos),
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25, 135, 84, 0.12)',
                        tension: 0.25,
                        fill: true,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Passageiros',
                        data: this.dados.map(item => item.total_passageiros),
                        borderColor: '#0dcaf0',
                        backgroundColor: 'rgba(13, 202, 240, 0.12)',
                        tension: 0.25,
                        yAxisID: 'y1',
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: { beginAtZero: true, position: 'left' },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                    },
                },
            },
        });
    }

    async carregar() {
        this.mostrarLoading();
        const params = new URLSearchParams({ agrupamento: this.agrupamento.value });
        if (this.periodo?.value) params.set('periodo', this.periodo.value);
        if (this.aeroporto?.value) params.set('aeroporto_id', this.aeroporto.value);
        if (this.companhia?.value) params.set('companhia_id', this.companhia.value);
        if (this.aeronave?.value) params.set('aeronave_id', this.aeronave.value);
        if (this.dataInicio.value) params.set('data_inicio', this.dataInicio.value);
        if (this.dataFim.value) params.set('data_fim', this.dataFim.value);

        try {
            const response = await fetch(`${this.apiUrl}?${params.toString()}`, {
                headers: { Accept: 'application/json' },
            });
            const resultado = await response.json();

            if (!response.ok || !resultado.success) {
                const mensagem = resultado.message
                    || Object.values(resultado.errors || {}).flat()[0]
                    || 'Não foi possível carregar o relatório.';
                throw new Error(mensagem);
            }

            this.dados = resultado.data;
            this.atualizarTotais(resultado.totais);
            this.renderizarGrafico();

            if (!this.dados.length) {
                this.mostrarMensagem('Nenhuma movimentação encontrada para os filtros selecionados.');
                return;
            }

            this.modoAdmin ? this.renderizarTabela() : this.renderizarCards();
        } catch (error) {
            this.mostrarMensagem(error.message || 'Erro ao carregar o relatório.', true);
        }
    }

    limparFiltros() {
        this.agrupamento.value = 'mes';
        if (this.periodo) this.periodo.value = '';
        if (this.aeroporto) this.aeroporto.value = '';
        if (this.companhia) this.companhia.value = '';
        if (this.aeronave) this.aeronave.value = '';
        this.dataInicio.value = '';
        this.dataFim.value = '';
        this.carregar();
    }

    exportarCsv() {
        if (!this.dados.length) return;

        const linhas = [
            ['Período', 'Início', 'Fim', 'Voos', 'Passageiros', 'Pax por voo', 'Regular', 'Charter', 'Variação %', 'Média geral'],
            ...this.dados.map(item => [
                item.label, item.data_inicio, item.data_fim, item.total_voos,
                item.total_passageiros, item.media_passageiros_por_voo,
                item.voos_regulares, item.voos_charter,
                item.variacao_percentual, item.media_geral,
            ]),
        ];
        const csv = linhas.map(linha => linha.map(valor =>
            `"${String(valor ?? '').replaceAll('"', '""')}"`
        ).join(';')).join('\n');
        const url = URL.createObjectURL(new Blob(['\uFEFF' + csv], {
            type: 'text/csv;charset=utf-8',
        }));
        const link = document.createElement('a');
        link.href = url;
        link.download = 'movimentacao_por_periodo.csv';
        link.click();
        URL.revokeObjectURL(url);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (window.movimentacaoPorPeriodoConfig) {
        new RelatorioMovimentacaoPorPeriodo(
            window.movimentacaoPorPeriodoConfig
        ).iniciar();
    }
});
