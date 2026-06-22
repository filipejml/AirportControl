class RelatorioOcupacaoVoos {
    constructor(config) {
        this.apiUrl = config.apiUrl;
        this.modoAdmin = Boolean(config.modoAdmin);
        this.dados = [];
        this.graficoFaixas = null;
        this.graficoRanking = null;
    }

    iniciar() {
        this.container = document.getElementById('ocupacaoResultado');
        this.periodo = document.getElementById('ocupPeriodo');
        this.companhia = document.getElementById('ocupCompanhia');
        this.aeroporto = document.getElementById('ocupAeroporto');
        this.faixa = document.getElementById('ocupFaixa');

        if (!this.container) return;

        [this.periodo, this.companhia, this.aeroporto, this.faixa]
            .forEach(elemento => elemento.addEventListener('change', () => this.carregar()));
        document.getElementById('limparOcupacaoFiltros')
            ?.addEventListener('click', () => this.limparFiltros());
        document.getElementById('exportarOcupacaoCsv')
            ?.addEventListener('click', () => this.exportarCsv());

        this.carregar();
    }

    numero(valor) {
        return new Intl.NumberFormat('pt-BR').format(valor || 0);
    }

    percentual(valor) {
        return `${Number(valor || 0).toFixed(1)}%`;
    }

    escapeHtml(valor) {
        const elemento = document.createElement('div');
        elemento.textContent = valor ?? '';
        return elemento.innerHTML;
    }

    faixaBadge(faixa, taxa) {
        const classes = {
            baixa: 'danger',
            media: 'warning text-dark',
            alta: 'success',
            lotado: 'primary',
        };
        const nomes = {
            baixa: 'Baixa',
            media: 'Média',
            alta: 'Alta',
            lotado: 'Lotado',
        };
        return `<span class="badge bg-${classes[faixa]}">${nomes[faixa]} · ${this.percentual(taxa)}</span>`;
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
                <td><strong>${this.escapeHtml(item.id_voo)}</strong><small class="d-block text-muted">${item.data || ''}</small></td>
                <td>${this.escapeHtml(item.companhia)}<small class="d-block text-muted">${this.escapeHtml(item.aeroporto)}</small></td>
                <td>${this.escapeHtml(item.aeronave)}<small class="d-block text-muted">${this.numero(item.capacidade)} lugares</small></td>
                <td class="text-center">${this.numero(item.qtd_voos)}</td>
                <td class="text-center">${this.numero(item.total_passageiros)}</td>
                <td class="text-center">${this.numero(item.assentos_ofertados)}</td>
                <td class="text-center">${this.faixaBadge(item.faixa_ocupacao, item.taxa_ocupacao)}</td>
            </tr>
        `).join('');
    }

    renderizarCards() {
        this.container.innerHTML = this.dados.map(item => `
            <div class="col-md-6 col-xl-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <strong>${this.escapeHtml(item.id_voo)}</strong>
                        ${this.faixaBadge(item.faixa_ocupacao, item.taxa_ocupacao)}
                    </div>
                    <div class="card-body">
                        <h6>${this.escapeHtml(item.companhia)}</h6>
                        <p class="text-muted small">${this.escapeHtml(item.aeroporto)} · ${this.escapeHtml(item.aeronave)}</p>
                        <div class="progress mb-3" style="height: 10px;">
                            <div class="progress-bar" style="width: ${Math.min(item.taxa_ocupacao, 100)}%"></div>
                        </div>
                        <div class="row text-center g-2">
                            <div class="col-4"><strong>${this.numero(item.qtd_voos)}</strong><small class="d-block text-muted">Voos</small></div>
                            <div class="col-4"><strong>${this.numero(item.total_passageiros)}</strong><small class="d-block text-muted">Passageiros</small></div>
                            <div class="col-4"><strong>${this.numero(item.assentos_ofertados)}</strong><small class="d-block text-muted">Assentos</small></div>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
    }

    atualizarTotais(totais) {
        document.getElementById('ocupTotalVoos').textContent = this.numero(totais.total_voos);
        document.getElementById('ocupTotalPassageiros').textContent = this.numero(totais.total_passageiros);
        document.getElementById('ocupAssentos').textContent = this.numero(totais.assentos_ofertados);
        document.getElementById('ocupTaxaGeral').textContent = this.percentual(totais.taxa_ocupacao_geral);
    }

    renderizarGraficos(distribuicao) {
        if (typeof Chart === 'undefined') return;

        this.graficoFaixas?.destroy();
        this.graficoRanking?.destroy();

        this.graficoFaixas = new Chart(document.getElementById('ocupacaoFaixasChart'), {
            type: 'doughnut',
            data: {
                labels: ['Baixa', 'Média', 'Alta', 'Lotado'],
                datasets: [{
                    data: [distribuicao.baixa, distribuicao.media, distribuicao.alta, distribuicao.lotado],
                    backgroundColor: ['#dc3545', '#ffc107', '#198754', '#0d6efd'],
                }],
            },
            options: { responsive: true, maintainAspectRatio: false },
        });

        const top = this.dados.slice(0, 10);
        this.graficoRanking = new Chart(document.getElementById('ocupacaoRankingChart'), {
            type: 'bar',
            data: {
                labels: top.map(item => item.id_voo),
                datasets: [{
                    data: top.map(item => item.taxa_ocupacao),
                    backgroundColor: '#0d6efd',
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, suggestedMax: 100 } },
            },
        });
    }

    async carregar() {
        this.mostrarLoading();
        const params = new URLSearchParams();
        if (this.periodo.value) params.set('periodo', this.periodo.value);
        if (this.companhia.value) params.set('companhia_id', this.companhia.value);
        if (this.aeroporto.value) params.set('aeroporto_id', this.aeroporto.value);
        if (this.faixa.value) params.set('faixa', this.faixa.value);

        const url = params.size ? `${this.apiUrl}?${params}` : this.apiUrl;

        try {
            const response = await fetch(url, { headers: { Accept: 'application/json' } });
            const resultado = await response.json();
            if (!response.ok || !resultado.success) {
                throw new Error(Object.values(resultado.errors || {}).flat()[0] || 'Não foi possível carregar o relatório.');
            }

            this.dados = resultado.data;
            this.atualizarTotais(resultado.totais);
            this.renderizarGraficos(resultado.distribuicao);

            if (!this.dados.length) {
                this.mostrarMensagem('Nenhum voo encontrado para os filtros selecionados.');
                return;
            }
            this.modoAdmin ? this.renderizarTabela() : this.renderizarCards();
        } catch (error) {
            this.mostrarMensagem(error.message || 'Erro ao carregar o relatório.', true);
        }
    }

    limparFiltros() {
        this.periodo.value = '';
        this.companhia.value = '';
        this.aeroporto.value = '';
        this.faixa.value = '';
        this.carregar();
    }

    exportarCsv() {
        if (!this.dados.length) return;
        const linhas = [
            ['Voo', 'Data', 'Companhia', 'Aeroporto', 'Aeronave', 'Capacidade', 'Qtd voos', 'Passageiros', 'Assentos', 'Ocupação %', 'Faixa'],
            ...this.dados.map(item => [
                item.id_voo, item.data, item.companhia, item.aeroporto,
                item.aeronave, item.capacidade, item.qtd_voos,
                item.total_passageiros, item.assentos_ofertados,
                item.taxa_ocupacao, item.faixa_ocupacao,
            ]),
        ];
        const csv = linhas.map(linha => linha.map(valor =>
            `"${String(valor ?? '').replaceAll('"', '""')}"`
        ).join(';')).join('\n');
        const url = URL.createObjectURL(new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8' }));
        const link = document.createElement('a');
        link.href = url;
        link.download = 'ocupacao_voos.csv';
        link.click();
        URL.revokeObjectURL(url);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (window.ocupacaoVoosConfig) {
        new RelatorioOcupacaoVoos(window.ocupacaoVoosConfig).iniciar();
    }
});
