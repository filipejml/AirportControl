{{-- resources/views/admin/companhias/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detalhes da Companhia Aérea')

@section('content')
<style>
/* Estilos para os botões de ação */
.btn-action {
    padding: 0.5rem 1rem;
    font-size: 0.95rem;
    line-height: 1.5;
    border-radius: 0.375rem;
    min-width: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.2s ease;
}
.btn-action i {
    font-size: 1.1rem;
}
.btn-action span {
    display: inline-block;
}
.btn-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.btn-action-group {
    gap: 0.5rem !important;
}

/* Estilo para links de modelos */
.modelo-link {
    color: #0d6efd;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s ease;
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
}
.modelo-link:hover {
    color: #0a58ca;
    background-color: rgba(13, 110, 253, 0.1);
    text-decoration: underline;
    transform: translateX(2px);
}
.modelo-link i {
    font-size: 0.9rem;
    margin-right: 4px;
    opacity: 0.7;
}
.modelo-link:hover i {
    opacity: 1;
}

/* Estilo para card de informações adicionais */
.info-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: none;
    border-radius: 16px;
}
.info-card .card-body {
    padding: 1.25rem;
}
.info-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0;
}
.info-icon {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-radius: 10px;
    color: #3b82f6;
}
.info-label {
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
    margin-bottom: 2px;
}
.info-value {
    font-size: 0.9rem;
    font-weight: 600;
    color: #212529;
}

/* Ajustes responsivos */
@media (max-width: 768px) {
    .btn-action span {
        display: none;
    }
    .btn-action {
        min-width: 44px;
        padding: 0.5rem;
    }
}

/* Estilos para o toggle de disponibilidade */
.form-check.form-switch {
    padding-left: 2.5em;
}

.form-check-input {
    width: 2.5em;
    height: 1.25em;
    cursor: pointer;
}

.form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}

.form-check-input:not(:checked) {
    background-color: #dc3545;
    border-color: #dc3545;
}

.form-check-label {
    cursor: pointer;
}

.status-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    transition: all 0.3s ease;
}

/* Estilo para linhas de aeronaves indisponíveis */
tr.aeronave-indisponivel td:not(:last-child) {
    opacity: 0.7;
    background-color: #fff3f3;
}

tr.aeronave-indisponivel .modelo-link {
    text-decoration: line-through;
    color: #6c757d !important;
}

/* Estilo para linhas com alterações pendentes */
.status-badge.opacity-50 {
    opacity: 0.5 !important;
}

/* Botão de salvar flutuante */
.floating-save-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 1000;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    animation: pulse 1s ease-in-out;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Animação para fadeOut */
@keyframes fadeOut {
    0% { opacity: 1; transform: translateY(0); }
    70% { opacity: 1; transform: translateY(0); }
    100% { opacity: 0; transform: translateY(-10px); visibility: hidden; }
}

.temporary-feedback {
    animation: fadeOut 2s ease-in-out forwards;
}
</style>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="fw-bold">✈️ {{ $companhia->nome }}</h2>
            <p class="text-muted">Detalhes da companhia aérea</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('companhias.informacoes') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
            <a href="{{ route('companhias.edit', $companhia) }}" class="btn btn-primary btn-action">
                <i class="bi bi-pencil"></i>
                <span>Editar</span>
            </a>
        </div>
    </div>

    <!-- Linha com estatísticas principais -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total de Aeronaves</h5>
                    <h2 class="display-4">{{ $companhia->aeronaves_count ?? $companhia->aeronaves->count() }}</h2>
                    <small>aeronaves cadastradas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Capacidade Total</h5>
                    <h2 class="display-4">{{ $companhia->aeronaves->sum('capacidade') }}</h2>
                    <small>passageiros</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Média de Capacidade</h5>
                    <h2 class="display-4">{{ $companhia->aeronaves->avg('capacidade') ? round($companhia->aeronaves->avg('capacidade')) : 0 }}</h2>
                    <small>passageiros por aeronave</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-secondary text-white">
                <div class="card-body">
                    <h5 class="card-title">Código</h5>
                    <h2 class="display-4">{{ $companhia->codigo ?? '—' }}</h2>
                    <small>código identificador</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Card com informações adicionais (data de cadastro) -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card info-card shadow-sm">
                <div class="card-body">
                    <h6 class="mb-3 text-muted">📋 Informações do Cadastro</h6>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-calendar-plus"></i>
                        </div>
                        <div>
                            <div class="info-label">Data de Cadastro</div>
                            <div class="info-value">{{ $companhia->created_at ? $companhia->created_at->format('d/m/Y \à\s H:i') : 'Data não disponível' }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div>
                            <div class="info-label">Última Atualização</div>
                            <div class="info-value">{{ $companhia->updated_at ? $companhia->updated_at->format('d/m/Y \à\s H:i') : 'Data não disponível' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Aeronaves -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">✈️ Aeronaves da Companhia</h5>
            @if($companhia->aeronaves->count() > 0)
                <span class="badge bg-primary rounded-pill px-3 py-2">
                    Total: {{ $companhia->aeronaves->count() }} aeronaves
                </span>
            @endif
        </div>
        <div class="card-body">
            @if($companhia->aeronaves->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="aeronavesTable">
                        <thead class="table-light">
                            周末
                                <th>ID</th>
                                <th>Modelo</th>
                                <th>Fabricante</th>
                                <th>Capacidade</th>
                                <th>Porte</th>
                                <th width="120">Disponível</th>
                                <th width="180">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($companhia->aeronaves as $aeronave)
                                @php
                                    // Buscar o valor do pivot corretamente
                                    $disponivel = isset($aeronave->pivot->disponivel) ? (bool)$aeronave->pivot->disponivel : true;
                                @endphp
                                <tr data-aeronave-id="{{ $aeronave->id }}" 
                                    data-disponivel-original="{{ $disponivel ? 'true' : 'false' }}">
                                    <td><span class="fw-semibold">#{{ $aeronave->id }}</span></td>
                                    <td>
                                        <a href="{{ route('aeronaves.show', $aeronave) }}" 
                                           class="modelo-link"
                                           title="Ver detalhes da aeronave {{ $aeronave->modelo }}">
                                            <i class="bi bi-airplane"></i>
                                            {{ $aeronave->modelo }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($aeronave->fabricante)
                                            <span class="text-muted">{{ $aeronave->fabricante->nome }}</span>
                                        @else
                                            <span class="badge bg-secondary">Não informado</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info rounded-pill px-3 py-2">
                                            {{ $aeronave->capacidade }} passageiros
                                        </span>
                                    </td>
                                    <td>
                                        @if($aeronave->porte == 'PC')
                                            <span class="badge bg-info">PC - Pequeno Porte</span>
                                        @elseif($aeronave->porte == 'MC')
                                            <span class="badge bg-warning text-dark">MC - Médio Porte</span>
                                        @elseif($aeronave->porte == 'LC')
                                            <span class="badge bg-danger">LC - Grande Porte</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" 
                                                   class="form-check-input disponivel-toggle" 
                                                   id="disponivel_{{ $aeronave->id }}"
                                                   data-aeronave-id="{{ $aeronave->id }}"
                                                   data-companhia-id="{{ $companhia->id }}"
                                                   {{ $disponivel ? 'checked' : '' }}>
                                            <label class="form-check-label" for="disponivel_{{ $aeronave->id }}">
                                                <span class="badge {{ $disponivel ? 'bg-success' : 'bg-secondary' }} status-badge">
                                                    {{ $disponivel ? 'Disponível' : 'Indisponível' }}
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2 btn-action-group">
                                            <a href="{{ route('aeronaves.edit', $aeronave) }}" 
                                               class="btn btn-primary btn-action"
                                               title="Editar aeronave">
                                                <i class="bi bi-pencil"></i>
                                                <span>Editar</span>
                                            </a>
                                            <form action="{{ route('aeronaves.destroy', $aeronave) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Tem certeza que deseja excluir a aeronave {{ $aeronave->modelo }}? Esta ação não pode ser desfeita.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-danger btn-action"
                                                        title="Excluir aeronave">
                                                    <i class="bi bi-trash"></i>
                                                    <span>Excluir</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-exclamation-circle text-muted" style="font-size: 3rem;"></i>
                    <h5 class="text-muted mt-3">Nenhuma aeronave associada a esta companhia</h5>
                    <a href="{{ route('aeronaves.create') }}" class="btn btn-primary btn-action mt-3">
                        <i class="bi bi-plus-circle"></i>
                        <span>Cadastrar Nova Aeronave</span>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Armazenar alterações pendentes
    let pendingChanges = new Map();
    let isSaving = false;
    
    // Criar botão de salvar flutuante
    const saveButton = document.createElement('button');
    saveButton.id = 'floatingSaveBtn';
    saveButton.className = 'btn btn-success floating-save-btn d-none';
    saveButton.innerHTML = '<i class="bi bi-check-circle me-2"></i> Salvar Alterações <span id="pendingCount">0</span>';
    document.body.appendChild(saveButton);
    
    // Função para atualizar o contador de pendências
    function updatePendingCount() {
        const count = pendingChanges.size;
        const countSpan = document.getElementById('pendingCount');
        const saveBtn = document.getElementById('floatingSaveBtn');
        
        if (countSpan) countSpan.textContent = count;
        
        if (count > 0) {
            saveBtn.classList.remove('d-none');
            if (count === 1) {
                saveBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i> Salvar Alteração (1 pendente)';
            } else {
                saveBtn.innerHTML = `<i class="bi bi-check-circle me-2"></i> Salvar Alterações (${count} pendentes)`;
            }
        } else {
            saveBtn.classList.add('d-none');
        }
    }
    
    // Função para atualizar o status visual da linha
    function updateRowStatus(row, disponivel) {
        const statusBadge = row.querySelector('.status-badge');
        const toggleInput = row.querySelector('.disponivel-toggle');
        
        // Atualizar o texto e classe do badge
        if (statusBadge) {
            statusBadge.textContent = disponivel ? 'Disponível' : 'Indisponível';
            statusBadge.className = `badge ${disponivel ? 'bg-success' : 'bg-secondary'} status-badge`;
        }
        
        // Atualizar o checkbox
        if (toggleInput) {
            toggleInput.checked = disponivel;
        }
        
        // Atualizar a classe da linha
        if (disponivel) {
            row.classList.remove('aeronave-indisponivel');
        } else {
            row.classList.add('aeronave-indisponivel');
        }
    }
    
    // Função para salvar todas as alterações pendentes
    async function savePendingChanges() {
        if (isSaving || pendingChanges.size === 0) return;
        
        isSaving = true;
        const saveBtn = document.getElementById('floatingSaveBtn');
        const originalHtml = saveBtn.innerHTML;
        saveBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> Salvando...';
        saveBtn.disabled = true;
        
        const changes = Array.from(pendingChanges.entries());
        let successCount = 0;
        let errorCount = 0;
        
        for (const [key, data] of changes) {
            const [companhiaId, aeronaveId] = key.split('_');
            const disponivel = data.disponivel;
            
            try {
                const response = await fetch(`/companhias/${companhiaId}/aeronaves/${aeronaveId}/disponibilidade`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ disponivel: disponivel })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    successCount++;
                    pendingChanges.delete(key);
                    
                    // Atualizar status visual da linha com o valor que foi salvo
                    const row = document.querySelector(`tr[data-aeronave-id="${aeronaveId}"]`);
                    if (row) {
                        // Atualizar visualmente com o status salvo
                        updateRowStatus(row, disponivel);
                        
                        // Atualizar o estado original
                        row.setAttribute('data-disponivel-original', disponivel);
                        
                        // Remover opacidade do badge
                        const badge = row.querySelector('.status-badge');
                        if (badge) {
                            badge.classList.remove('opacity-50');
                        }
                        
                        // Mostrar feedback temporário na linha
                        showTemporaryFeedback(row, result.message, 'success');
                    }
                } else {
                    errorCount++;
                    // Reverter o toggle visualmente para o estado original
                    const row = document.querySelector(`tr[data-aeronave-id="${aeronaveId}"]`);
                    if (row) {
                        const originalDisponivel = row.getAttribute('data-disponivel-original') === 'true';
                        updateRowStatus(row, originalDisponivel);
                        showTemporaryFeedback(row, result.message || 'Erro ao salvar', 'error');
                    }
                }
            } catch (error) {
                errorCount++;
                console.error('Erro ao salvar:', error);
                
                // Reverter o toggle visualmente para o estado original
                const row = document.querySelector(`tr[data-aeronave-id="${aeronaveId}"]`);
                if (row) {
                    const originalDisponivel = row.getAttribute('data-disponivel-original') === 'true';
                    updateRowStatus(row, originalDisponivel);
                    showTemporaryFeedback(row, 'Erro de conexão com o servidor', 'error');
                }
            }
        }
        
        // Mostrar mensagem final
        if (successCount > 0) {
            showGlobalMessage(`✅ ${successCount} alteração(ões) salva(s) com sucesso!`, 'success');
        }
        if (errorCount > 0) {
            showGlobalMessage(`⚠️ ${errorCount} erro(s) ao salvar alterações.`, 'error');
        }
        
        // Resetar estado
        pendingChanges.clear();
        updatePendingCount();
        
        saveBtn.innerHTML = originalHtml;
        saveBtn.disabled = false;
        isSaving = false;
        
        // Esconder botão se não houver mais pendências
        if (pendingChanges.size === 0) {
            saveBtn.classList.add('d-none');
        }
    }
    
    // Função para mostrar feedback temporário na linha
    function showTemporaryFeedback(row, message, type) {
        // Remover feedbacks anteriores
        const oldFeedback = row.querySelector('.temporary-feedback');
        if (oldFeedback) oldFeedback.remove();
        
        const feedbackDiv = document.createElement('div');
        feedbackDiv.className = `temporary-feedback position-absolute end-0 top-0 mt-2 me-2 badge ${type === 'success' ? 'bg-success' : 'bg-danger'}`;
        feedbackDiv.style.zIndex = '10';
        feedbackDiv.style.fontSize = '0.7rem';
        feedbackDiv.style.padding = '0.25rem 0.5rem';
        feedbackDiv.innerHTML = message;
        
        // Garantir que a linha tenha position relative
        if (getComputedStyle(row).position === 'static') {
            row.style.position = 'relative';
        }
        
        row.appendChild(feedbackDiv);
        
        setTimeout(() => {
            if (feedbackDiv.parentNode) {
                feedbackDiv.remove();
            }
        }, 2000);
    }
    
    // Função para mostrar mensagem global
    function showGlobalMessage(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
        alertDiv.style.zIndex = '9999';
        alertDiv.style.minWidth = '300px';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }
    
    // Adicionar evento de clique nos toggles
    document.querySelectorAll('.disponivel-toggle').forEach(toggle => {
        // Remover event listeners antigos para evitar duplicação
        const newToggle = toggle.cloneNode(true);
        toggle.parentNode.replaceChild(newToggle, toggle);
        
        newToggle.addEventListener('change', function(e) {
            e.stopPropagation();
            
            const aeronaveId = this.dataset.aeronaveId;
            const companhiaId = this.dataset.companhiaId;
            const disponivel = this.checked;
            const row = this.closest('tr');
            
            // Obter o estado original do servidor
            const originalDisponivel = row.getAttribute('data-disponivel-original') === 'true';
            
            // Atualizar visualmente
            updateRowStatus(row, disponivel);
            
            // Armazenar alteração pendente
            const key = `${companhiaId}_${aeronaveId}`;
            pendingChanges.set(key, { 
                disponivel: disponivel, 
                originalDisponivel: originalDisponivel 
            });
            
            updatePendingCount();
            
            // Mostrar feedback visual de que a alteração está pendente
            const badge = row.querySelector('.status-badge');
            if (badge) {
                badge.classList.add('opacity-50');
            }
        });
    });
    
    // Adicionar evento de clique no botão de salvar
    saveButton.addEventListener('click', savePendingChanges);
    
    // Salvar ao sair da página se houver pendências
    window.addEventListener('beforeunload', function(e) {
        if (pendingChanges.size > 0) {
            e.preventDefault();
            e.returnValue = 'Você tem alterações pendentes. Tem certeza que deseja sair?';
            return 'Você tem alterações pendentes. Tem certeza que deseja sair?';
        }
    });
    
    // Inicializar o estado visual das linhas baseado no valor do banco
    document.querySelectorAll('#aeronavesTable tbody tr').forEach(row => {
        const toggle = row.querySelector('.disponivel-toggle');
        if (toggle) {
            const disponivelOriginal = row.getAttribute('data-disponivel-original') === 'true';
            // Garantir que o visual está correto
            updateRowStatus(row, disponivelOriginal);
            // Garantir que o checkbox está correto
            toggle.checked = disponivelOriginal;
        }
    });
});
</script>
@endsection