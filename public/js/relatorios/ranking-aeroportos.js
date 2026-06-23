class RelatorioRankingAeroportos {
    constructor(config) {
        this.apiUrl = config.apiUrl;
        this.modoAdmin = Boolean(config.modoAdmin);
        this.dados = [];
        this.grafico = null;
    }

    iniciar() {
        this.container = document.getElementById('rankingAeroportosResultado');
        this.periodo = document.getElementById('rankPeriodo');
        this.ordenacao = document.getElementById('rankOrdenacao');

        if (!this.container || !this.periodo || !this.ordenacao) return;

        this.periodo.addEventListener('change', () => this.carregar());
        this.ordenacao.addEventListener('change', () => this.carregar());
        document.getElementById('limparRankingFiltros')
            ?.addEventListener('click', () => this.limparFiltros());
        document.getElementById('exportarRankingCsv')
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

    medalha(posicao) {
        return { 1: '🥇', 2: '🥈', 3: '🥉' }[posicao] || `#${posicao}`;
    }

    valorCriterio(item) {
        const campo = this.ordenacao.value;
        const valor = item[campo] || 0;
        return campo === 'media_geral'
            ? `${this.decimal(valor)}/10`
            : this.numero(valor);
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
                <td class="text-center fs-5">${this.medalha(item.posicao)}</td>
                <td><strong>${this.escapeHtml(item.nome)}</strong></td>
                <td class="text-center">${this.numero(item.total_voos)}</td>
                <td class="text-center">${this.numero(item.total_passageiros)}</td>
                <td class="text-center">${this.numero(item.media_passageiros_por_voo)}</td>
                <td class="text-center">${item.total_companhias} companhias<br><small class="text-muted">${item.total_aeronaves} aeronaves</small></td>
                <td class="text-center">${this.decimal(item.media_geral)}/10</td>
            </tr>
        `).join('');
    }

    renderizarCards() {
        this.container.innerHTML = this.dados.map(item => `
            <div class="col-md-6 col-xl-4">
                <div class="card shadow-sm h-100 ${item.posicao <= 3 ? 'border-warning' : ''}">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <strong>${this.medalha(item.posicao)} ${this.escapeHtml(item.nome)}</strong>
                        <span class="badge bg-primary">${this.valorCriterio(item)}</span>
                    </div>
                    <div class="card-body">
                        <div class="row text-center g-3 mb-3">
                            <div class="col-4"><strong>${this.numero(item.total_voos)}</strong><small class="d-block text-muted">Voos</small></div>
                            <div class="col-4"><strong>${this.numero(item.total_passageiros)}</strong><small class="d-block text-muted">Passageiros</small></div>
                            <div class="col-4"><strong>${this.numero(item.media_passageiros_por_voo)}</strong><small class="d-block text-muted">Pax/voo</small></div>
                        </div>
                        <p class="small mb-2"><strong>Cobertura:</strong> ${item.total_companhias} companhias e ${item.total_aeronaves} aeronaves</p>
                        <p class="small mb-2"><strong>Operação:</strong> ${this.numero(item.voos_regulares)} regulares / ${this.numero(item.voos_charter)} charter</p>
                        <p class="small mb-0"><strong>Avaliação:</strong> ${this.decimal(item.media_geral)}/10</p>
                    </div>
                </div>
            </div>
        `).join('');
    }

    atualizarTotais(totais) {
        document.getElementById('rankTotalAeroportos').textContent = this.numero(totais.total_aeroportos);
        document.getElementById('rankTotalVoos').textContent = this.numero(totais.total_voos);
        document.getElementById('rankTotalPassageiros').textContent = this.numero(totais.total_passageiros);
        document.getElementById('rankLider').textContent = totais.lider
            ? totais.lider.nome
            : '-';
    }

    renderizarGrafico() {
        if (typeof Chart === 'undefined') return;

        this.grafico?.destroy();
        const top = this.dados.slice(0, 10);
        this.grafico = new Chart(document.getElementById('rankingAeroportosChart'), {
            type: 'bar',
            data: {
                labels: top.map(item => item.nome),
                datasets: [{
                    label: this.ordenacao.options[this.ordenacao.selectedIndex].text,
                    data: top.map(item => item[this.ordenacao.value]),
                    backgroundColor: top.map((_, indice) =>
                        ['#ffc107', '#adb5bd', '#cd7f32'][indice] || '#0d6efd'
                    ),
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: { x: { beginAtZero: true } },
                plugins: { legend: { display: false } },
            },
        });
    }

    async carregar() {
        this.mostrarLoading();
        const params = new URLSearchParams({ ordenacao: this.ordenacao.value });
        if (this.periodo.value) params.set('periodo', this.periodo.value);

        try {
            const response = await fetch(`${this.apiUrl}?${params.toString()}`, {
                headers: { Accept: 'application/json' },
            });
            const resultado = await response.json();

            if (!response.ok || !resultado.success) {
                throw new Error(
                    Object.values(resultado.errors || {}).flat()[0]
                    || 'Não foi possível carregar o ranking.'
                );
            }

            this.dados = resultado.data;
            this.atualizarTotais(resultado.totais);
            this.renderizarGrafico();

            if (!this.dados.length) {
                this.mostrarMensagem('Nenhum aeroporto possui voos para os filtros selecionados.');
                return;
            }

            this.modoAdmin ? this.renderizarTabela() : this.renderizarCards();
        } catch (error) {
            this.mostrarMensagem(error.message || 'Erro ao carregar o ranking.', true);
        }
    }

    limparFiltros() {
        this.periodo.value = '';
        this.ordenacao.value = 'total_voos';
        this.carregar();
    }

    exportarCsv() {
        if (!this.dados.length) return;

        const linhas = [
            ['Posição', 'Aeroporto', 'Voos', 'Passageiros', 'Pax por voo', 'Companhias', 'Aeronaves', 'Regular', 'Charter', 'Média geral'],
            ...this.dados.map(item => [
                item.posicao, item.nome, item.total_voos, item.total_passageiros,
                item.media_passageiros_por_voo, item.total_companhias,
                item.total_aeronaves, item.voos_regulares,
                item.voos_charter, item.media_geral,
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
        link.download = 'ranking_aeroportos.csv';
        link.click();
        URL.revokeObjectURL(url);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (window.rankingAeroportosConfig) {
        new RelatorioRankingAeroportos(window.rankingAeroportosConfig).iniciar();
    }
});
