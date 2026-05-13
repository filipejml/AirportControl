{{-- resources/views/relatorios/companhias-por-aeroporto.blade.php --}}
@extends('layouts.app')

@section('title', 'Relatório - Companhias por Aeroporto')

@section('content')
<div class="container">
    <div class="mb-4">
        <h3 class="fw-bold">
            <i class="bi bi-building"></i> Companhias Aéreas por Aeroporto
        </h3>
        <p class="text-muted">Visualize quais companhias operam em cada aeroporto</p>
    </div>

    <!-- Filtros Melhorados -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-5">
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
                <div class="col-md-5">
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
                <div class="col-md-2 d-flex align-items-end">
                    <div class="d-grid gap-2 w-100">
                        <button id="clearButton" class="btn btn-outline-secondary">
                            <i class="bi bi-eraser"></i> Limpar Filtros
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Status dos filtros -->
            <div id="filtersStatus" class="mt-3"></div>
        </div>
    </div>

    <!-- Contador de resultados -->
    <div id="totalResultados" class="mb-3 text-end"></div>

    <!-- Cards -->
    <div id="cardsContainer">
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
            <p class="mt-2 text-muted">Carregando dados...</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Dados dos filtros passados do PHP para JavaScript
const aeroportosList = @json($aeroportos);
const companhiasList = @json($companhias);

class UserRelatorioCompanhias {
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
        this.cardsContainer = document.getElementById('cardsContainer');
        this.filterAeroporto = document.getElementById('filterAeroporto');
        this.filterCompanhia = document.getElementById('filterCompanhia');
        this.clearButton = document.getElementById('clearButton');
        this.totalResultados = document.getElementById('totalResultados');
        this.filtersStatus = document.getElementById('filtersStatus');
    }
    
    async carregarDados() {
        if (this.cardsContainer) {
            this.mostrarLoading();
        }
        
        // Pegar filtros atuais
        const aeroportoId = this.filterAeroporto?.value || '';
        const companhiaId = this.filterCompanhia?.value || '';
        
        // Construir URL com filtros
        let url = this.apiUrl;
        const params = [];
        if (aeroportoId) params.push(`aeroporto_id=${aeroportoId}`);
        if (companhiaId) params.push(`companhia_id=${companhiaId}`);
        if (params.length) url += `?${params.join('&')}`;
        
        try {
            const response = await fetch(url);
            const result = await response.json();
            
            if (result.success) {
                this.dadosOriginais = result.data;
                this.dadosFiltrados = [...result.data];
                this.renderizarCards();
                this.atualizarTotal();
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
        
        if (this.clearButton) {
            this.clearButton.addEventListener('click', () => this.limparFiltros());
        }
    }
    
    limparFiltros() {
        if (this.filterAeroporto) this.filterAeroporto.value = '';
        if (this.filterCompanhia) this.filterCompanhia.value = '';
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
            if (aeroporto) filtros.push(`<span class="badge bg-primary">Aeroporto: ${aeroporto.nome_aeroporto}</span>`);
        }
        
        if (companhiaSelecionada) {
            const companhia = companhiasList.find(c => c.id == companhiaSelecionada);
            if (companhia) filtros.push(`<span class="badge bg-primary">Companhia: ${companhia.nome}</span>`);
        }
        
        html += filtros.join(' ') + '</div>';
        this.filtersStatus.innerHTML = html;
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
                    Erro ao carregar dados. Tente novamente mais tarde.
                </div>
            `;
        }
    }
    
    renderizarCards() {
        if (!this.cardsContainer) return;
        
        if (this.dadosFiltrados.length === 0) {
            this.cardsContainer.innerHTML = `
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> 
                    Nenhum aeroporto encontrado com os filtros selecionados.
                </div>
            `;
            return;
        }
        
        this.cardsContainer.innerHTML = `
            <div class="row">
                ${this.dadosFiltrados.map(item => `
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm hover-card">
                            <div class="card-header bg-gradient-primary text-white">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <i class="bi bi-building fs-4"></i>
                                        <h5 class="mb-0 mt-2">${this.escapeHtml(item.aeroporto)}</h5>
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
                                                        <strong>${this.escapeHtml(c.nome)}</strong>
                                                    </div>
                                                    ${c.codigo ? `
                                                        <span class="badge bg-secondary ms-2">${this.escapeHtml(c.codigo)}</span>
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
                                    Atualizado em ${this.formatarData()}
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
            const total = this.dadosFiltrados.length;
            const texto = total === 1 ? 'aeroporto encontrado' : 'aeroportos encontrados';
            this.totalResultados.innerHTML = `
                <span class="badge bg-info fs-6">
                    <i class="bi bi-search"></i> ${total} ${texto}
                </span>
            `;
        }
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
    
    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Inicializar
new UserRelatorioCompanhias();
</script>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.hover-card {
    transition: transform 0.3s, box-shadow 0.3s;
    overflow: hidden;
}

.hover-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.15) !important;
}

.companhia-card {
    transition: all 0.2s;
    background-color: #f8f9fa;
}

.companhia-card:hover {
    background-color: #e9ecef;
    transform: translateX(5px);
}

.card-header {
    border-bottom: none;
}

.card-footer {
    border-top: 1px solid rgba(0,0,0,0.05);
}

.alert-sm {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .hover-card:hover {
        transform: translateY(-4px);
    }
}
</style>
@endpush