{{-- resources/views/admin/relatorios/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Controle de Relatórios')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold">📊 Controle de Relatórios</h3>
            <p class="text-muted">Gerencie quais relatórios são visíveis para usuários comuns</p>
        </div>
        <a href="{{ route('admin.relatorios.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Novo Relatório
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Tipo</th>
                            <th>Descrição</th>
                            <th class="text-center">Visível para Usuários</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($relatorios as $relatorio)
                            <tr>
                                <td>{{ $relatorio->id }}</td>
                                <td><strong>{{ $relatorio->nome }}</strong></td>
                                <td>
                                    @if($relatorio->tipo)
                                        <span class="badge bg-info">{{ $relatorio->tipo }}</span>
                                    @else
                                        <span class="badge bg-secondary">padrão</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($relatorio->descricao, 50) ?? '—' }}</td>
                                <td class="text-center">
                                    <!-- Toggle Switch sem label -->
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input 
                                            type="checkbox" 
                                            class="form-check-input toggle-visibilidade" 
                                            id="toggle_{{ $relatorio->id }}"
                                            data-id="{{ $relatorio->id }}"
                                            {{ $relatorio->visivel_usuario ? 'checked' : '' }}
                                            style="cursor: pointer; width: 50px; height: 25px;">
                                    </div>
                                </td>
                                <td class="text-end">
                                    <!-- Botão Visualizar (apenas para relatórios com tipo especial) -->
                                    @if($relatorio->tipo == 'companhias_por_aeroporto')
                                        <a href="{{ route('admin.relatorios.companhias-por-aeroporto') }}" 
                                           class="btn btn-sm btn-outline-info me-1">
                                            <i class="bi bi-eye"></i> Ver
                                        </a>
                                    @endif
                                    
                                    <!-- Botão Excluir -->
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal{{ $relatorio->id }}">
                                        <i class="bi bi-trash"></i> Excluir
                                    </button>
                                 </td>
                            </tr>

                            <!-- Modal de confirmação de exclusão -->
                            <div class="modal fade" id="deleteModal{{ $relatorio->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Confirmar exclusão</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Tem certeza que deseja excluir o relatório <strong>{{ $relatorio->nome }}</strong>?</p>
                                            <p class="text-danger small">Esta ação não pode ser desfeita.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <form action="{{ route('admin.relatorios.destroy', $relatorio) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Excluir</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Nenhum relatório cadastrado.
                                    <a href="{{ route('admin.relatorios.create') }}" class="text-primary">Criar o primeiro relatório</a>
                                 </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Aplicar cores iniciais nos toggles
    const toggles = document.querySelectorAll('.toggle-visibilidade');
    
    function updateToggleColor(toggle) {
        if (toggle.checked) {
            // Verde quando ativado (visível)
            toggle.style.backgroundColor = '#198754';
            toggle.style.borderColor = '#198754';
        } else {
            // Vermelho quando desativado (oculto)
            toggle.style.backgroundColor = '#dc3545';
            toggle.style.borderColor = '#dc3545';
        }
    }
    
    // Atualizar cores iniciais
    toggles.forEach(toggle => {
        updateToggleColor(toggle);
        
        // Adicionar evento de clique para atualizar cor
        toggle.addEventListener('change', function() {
            updateToggleColor(this);
        });
    });
    
    // Evento de mudança dos toggles
    toggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const relatorioId = this.dataset.id;
            const isVisible = this.checked;
            
            // Mostrar estado de loading
            this.disabled = true;
            this.style.opacity = '0.6';
            
            // Enviar requisição AJAX
            fetch(`/admin/relatorios/${relatorioId}/toggle-visibilidade`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    visivel_usuario: isVisible
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Atualizar cor após confirmação
                    updateToggleColor(this);
                    
                    // Feedback visual
                    if (isVisible) {
                        showToast('success', `✅ Relatório agora está VISÍVEL para usuários comuns`);
                    } else {
                        showToast('warning', `🔒 Relatório agora está OCULTO para usuários comuns`);
                    }
                } else {
                    // Reverter o toggle em caso de erro
                    this.checked = !isVisible;
                    updateToggleColor(this);
                    showToast('danger', '❌ Erro ao atualizar: ' + (data.message || 'Tente novamente'));
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                // Reverter o toggle
                this.checked = !isVisible;
                updateToggleColor(this);
                showToast('danger', '❌ Erro de conexão. Tente novamente.');
            })
            .finally(() => {
                this.disabled = false;
                this.style.opacity = '1';
            });
        });
    });
    
    // Função para mostrar toast de notificação
    function showToast(type, message) {
        // Verificar se já existe um container de toast
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            toastContainer.style.zIndex = '1050';
            document.body.appendChild(toastContainer);
        }
        
        // Criar o toast
        const toastId = 'toast_' + Date.now();
        let bgClass, icon;
        
        switch(type) {
            case 'success':
                bgClass = 'bg-success';
                icon = '✅';
                break;
            case 'warning':
                bgClass = 'bg-warning';
                icon = '🔒';
                break;
            case 'danger':
                bgClass = 'bg-danger';
                icon = '❌';
                break;
            default:
                bgClass = 'bg-info';
                icon = 'ℹ️';
        }
        
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3000">
                <div class="d-flex">
                    <div class="toast-body">
                        ${icon} ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        toastContainer.insertAdjacentHTML('beforeend', toastHtml);
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
        toast.show();
        
        // Remover do DOM após fechar
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }
});
</script>

<style>
/* Estilo para o toggle switch */
.form-check-input {
    cursor: pointer;
    transition: all 0.2s ease;
    background-color: #dc3545; /* Vermelho padrão para oculto */
    border-color: #dc3545;
}

.form-check-input:checked {
    background-color: #198754 !important; /* Verde quando ativado */
    border-color: #198754 !important;
}

.form-check-input:focus {
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}

.form-check-input:not(:checked):focus {
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.form-check-input:hover {
    transform: scale(1.05);
}

.form-check-input:disabled {
    cursor: wait;
}

/* Centralizar o toggle na tabela */
.table td.text-center {
    vertical-align: middle;
}

/* Toast container */
.toast-container {
    z-index: 1050;
}

/* Animações */
.btn-sm {
    transition: all 0.2s;
}

.btn-sm:hover {
    transform: translateY(-1px);
}

/* Badge de tipo */
.badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
}
</style>
@endpush
@endsection