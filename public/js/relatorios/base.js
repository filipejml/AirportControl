// public/js/relatorios/base.js

/**
 * Utilitários comuns para os relatórios
 */
const RelatorioUtils = {
    // URL da API
    apiUrl: '/api/relatorios/companhias-por-aeroporto',
    
    // Dados armazenados
    dadosOriginais: [],
    dadosFiltrados: [],
    
    // Escape HTML para prevenir XSS
    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },
    
    // Formatar data
    formatarData() {
        return new Date().toLocaleDateString('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    },
    
    // Mostrar loading
    mostrarLoading(containerId) {
        const container = document.getElementById(containerId);
        if (container) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                    <p class="mt-2 text-muted">Carregando dados...</p>
                </div>
            `;
        }
    },
    
    // Mostrar erro
    mostrarErro(containerId, mensagem = 'Erro ao carregar dados. Tente novamente mais tarde.') {
        const container = document.getElementById(containerId);
        if (container) {
            container.innerHTML = `
                <div class="alert alert-danger text-center">
                    <i class="bi bi-exclamation-triangle"></i> ${mensagem}
                </div>
            `;
        }
    },
    
    // Mostrar sem resultados
    mostrarSemResultados(containerId, mensagem = 'Nenhum resultado encontrado com os filtros selecionados.') {
        const container = document.getElementById(containerId);
        if (container) {
            container.innerHTML = `
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> ${mensagem}
                </div>
            `;
        }
    },
    
    // Carregar dados da API
    async carregarDados() {
        try {
            const response = await fetch(this.apiUrl);
            const result = await response.json();
            
            if (result.success) {
                this.dadosOriginais = result.data;
                this.dadosFiltrados = [...result.data];
                return true;
            } else {
                throw new Error('Dados inválidos');
            }
        } catch (error) {
            console.error('Erro ao carregar dados:', error);
            return false;
        }
    },
    
    // Exportar para CSV
    exportarCSV(dados, nomeArquivo = 'relatorio_companhias_por_aeroporto') {
        const headers = ['Aeroporto', 'Quantidade de Companhias', 'Companhias'];
        const rows = dados.map(item => [
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
        link.setAttribute('download', `${nomeArquivo}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    },
    
    // Exportar para PDF (versão simples)
    exportarPDF() {
        window.print();
    }
};

// Exportar para uso global
window.RelatorioUtils = RelatorioUtils;