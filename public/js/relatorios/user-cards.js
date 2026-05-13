// public/js/relatorios/user-cards.js

class UserRelatorioCompanhias {
    constructor() {
        this.utils = window.RelatorioUtils;
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
        this.searchButton = document.getElementById('searchButton');
        this.clearButton = document.getElementById('clearButton');
        this.totalResultados = document.getElementById('totalResultados');
    }
    
    async carregarDados() {
        if (this.cardsContainer) {
            this.utils.mostrarLoading('cardsContainer');
        }
        
        const sucesso = await this.utils.carregarDados();
        
        if (sucesso) {
            this.renderizarCards();
            this.atualizarTotal();
        } else {
            if (this.cardsContainer) {
                this.utils.mostrarErro('cardsContainer');
            }
        }
    }
    
    configurarEventos() {
        // Busca em tempo real (opcional - pode usar com ou sem botão)
        if (this.filterAeroporto) {
            this.filterAeroporto.addEventListener('input', () => this.filtrar());
        }
        
        if (this.filterCompanhia) {
            this.filterCompanhia.addEventListener('input', () => this.filtrar());
        }
        
        // Busca com botão (estilo mais tradicional)
        if (this.searchButton) {
            this.searchButton.addEventListener('click', () => this.filtrar());
        }
        
        // Limpar filtros
        if (this.clearButton) {
            this.clearButton.addEventListener('click', () => this.limparFiltros());
        }
    }
    
    filtrar() {
        const aeroportoFilter = this.filterAeroporto?.value.toLowerCase() || '';
        const companhiaFilter = this.filterCompanhia?.value.toLowerCase() || '';
        
        this.utils.dadosFiltrados = this.utils.dadosOriginais.filter(item => {
            if (aeroportoFilter && !item.aeroporto.toLowerCase().includes(aeroportoFilter)) {
                return false;
            }
            
            if (companhiaFilter) {
                const temCompanhia = item.companhias.some(c => 
                    c.nome.toLowerCase().includes(companhiaFilter) || 
                    (c.codigo && c.codigo.toLowerCase().includes(companhiaFilter))
                );
                if (!temCompanhia) return false;
            }
            
            return true;
        });
        
        this.renderizarCards();
        this.atualizarTotal();
    }
    
    limparFiltros() {
        if (this.filterAeroporto) this.filterAeroporto.value = '';
        if (this.filterCompanhia) this.filterCompanhia.value = '';
        this.filtrar();
    }
    
    renderizarCards() {
        if (!this.cardsContainer) return;
        
        if (this.utils.dadosFiltrados.length === 0) {
            this.utils.mostrarSemResultados('cardsContainer');
            return;
        }
        
        this.cardsContainer.innerHTML = `
            <div class="row">
                ${this.utils.dadosFiltrados.map(item => `
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm hover-card">
                            <div class="card-header bg-gradient-primary text-white">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <i class="bi bi-building fs-4"></i>
                                        <h5 class="mb-0 mt-2">${this.utils.escapeHtml(item.aeroporto)}</h5>
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
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="flex-grow-1">
                                                        <i class="bi bi-building text-primary me-2"></i>
                                                        <strong>${this.utils.escapeHtml(c.nome)}</strong>
                                                    </div>
                                                    ${c.codigo ? `
                                                        <span class="badge bg-secondary ms-2">${this.utils.escapeHtml(c.codigo)}</span>
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
                                    Atualizado em ${this.utils.formatarData()}
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
            const total = this.utils.dadosFiltrados.length;
            const totalOriginal = this.utils.dadosOriginais.length;
            this.totalResultados.innerHTML = `
                <span class="badge bg-info">
                    Mostrando ${total} de ${totalOriginal} aeroporto${totalOriginal !== 1 ? 's' : ''}
                </span>
            `;
        }
    }
}

// Inicializar
document.addEventListener('DOMContentLoaded', () => {
    new UserRelatorioCompanhias();
});