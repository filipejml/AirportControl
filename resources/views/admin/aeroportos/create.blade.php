@extends('layouts.app')

@section('title', 'Cadastrar Aeroporto')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">🛫 Cadastrar Novo Aeroporto</h2>
            <p class="text-muted">Preencha os dados do aeroporto abaixo</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('aeroportos.store') }}" id="aeroportoForm">
                @csrf

                <div class="mb-4">
                    <label for="nome_aeroporto" class="form-label fw-semibold">Nome do Aeroporto</label>
                    <div class="position-relative">
                        <input type="text" 
                               class="form-control @error('nome_aeroporto') is-invalid @enderror" 
                               id="nome_aeroporto" 
                               name="nome_aeroporto" 
                               value="{{ old('nome_aeroporto') }}"
                               placeholder="Ex: Aeroporto Internacional de Guarulhos, Aeroporto de Congonhas..."
                               required
                               autocomplete="off">
                        <div class="position-absolute end-0 top-50 translate-middle-y me-3">
                            <div class="spinner-border spinner-border-sm text-primary d-none" id="nomeSpinner" role="status">
                                <span class="visually-hidden">Verificando...</span>
                            </div>
                            <i class="bi bi-check-circle-fill text-success d-none" id="nomeCheckIcon"></i>
                            <i class="bi bi-x-circle-fill text-danger d-none" id="nomeXIcon"></i>
                        </div>
                    </div>
                    <div id="nomeFeedback" class="form-text"></div>
                    @error('nome_aeroporto')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <label class="form-label fw-semibold mb-0">Companhias que Operam no Aeroporto</label>
                            <p class="text-muted small mb-0 mt-1">Clique nos cards abaixo para selecionar as companhias</p>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllBtn">
                                <i class="bi bi-check-all"></i> Selecionar Todas
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllBtn">
                                <i class="bi bi-x-circle"></i> Desmarcar Todas
                            </button>
                        </div>
                    </div>
                    
                    @if($companhias->count() > 0)
                        <div class="row g-4" id="companhiasContainer">
                            @foreach($companhias as $companhia)
                                <div class="col-md-4 col-lg-3">
                                    <div class="companhia-card {{ in_array($companhia->id, old('companhias', [])) ? 'selected' : '' }}" 
                                         data-id="{{ $companhia->id }}"
                                         onclick="toggleCard(this)">
                                        
                                        <div class="card-header">
                                            <h5 class="card-title">{{ $companhia->nome }}</h5>
                                            <div class="selection-badge">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                        </div>
                                        
                                        <div class="card-stats">
                                            <div class="stat-item">
                                                <div class="stat-label">Aeronaves</div>
                                                <div class="stat-value">{{ $companhia->aeronaves_count ?? 0 }} aeronaves</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-label">Status</div>
                                                <div class="stat-value">
                                                    @if(($companhia->aeronaves_count ?? 0) > 0)
                                                        <span class="badge bg-success">Ativa</span>
                                                    @else
                                                        <span class="badge bg-warning">Sem aeronaves</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <input type="checkbox" 
                                               name="companhias[]" 
                                               class="companhia-checkbox" 
                                               value="{{ $companhia->id }}"
                                               style="display: none;"
                                               {{ in_array($companhia->id, old('companhias', [])) ? 'checked' : '' }}>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-3">
                            <span class="text-muted" id="selectedCount">
                                <i class="bi bi-info-circle"></i> <span id="countNumber">0</span> companhia(s) selecionada(s)
                            </span>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            Nenhuma companhia aérea cadastrada ainda. 
                            <a href="{{ route('companhias.create') }}" class="alert-link">Cadastrar companhia</a>
                        </div>
                    @endif
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="bi bi-save"></i> Salvar Aeroporto
                    </button>
                    <a href="{{ route('aeroportos.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Cards inspirados no design das companhias aéreas */
.companhia-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.companhia-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.15);
    border-color: #cbd5e1;
}

.companhia-card.selected {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border-color: #1e40af;
    box-shadow: 0 8px 20px -6px rgba(59, 130, 246, 0.4);
}

.companhia-card.selected .card-header .card-title {
    color: white;
}

.companhia-card.selected .selection-badge {
    opacity: 1;
    transform: scale(1);
    color: white;
}

.companhia-card.selected .card-stats .stat-label {
    color: rgba(255, 255, 255, 0.8);
}

.companhia-card.selected .card-stats .stat-value {
    color: white;
}

.companhia-card.selected .badge {
    background-color: rgba(255, 255, 255, 0.2) !important;
    color: white;
}

.card-header {
    padding: 20px 20px 12px 20px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    border-bottom: 1px solid #e5e7eb;
}

.companhia-card.selected .card-header {
    border-bottom-color: rgba(255, 255, 255, 0.2);
}

.card-title {
    font-size: 1rem;
    font-weight: 700;
    margin: 0;
    color: #111827;
    letter-spacing: -0.3px;
}

.selection-badge {
    opacity: 0;
    transform: scale(0.8);
    transition: all 0.2s ease;
    color: #3b82f6;
}

.selection-badge svg {
    width: 18px;
    height: 18px;
}

.card-stats {
    padding: 16px 20px 20px 20px;
}

.stat-item {
    margin-bottom: 12px;
}

.stat-item:last-child {
    margin-bottom: 0;
}

.stat-label {
    font-size: 0.7rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6b7280;
    margin-bottom: 4px;
}

.stat-value {
    font-size: 0.9rem;
    font-weight: 600;
    color: #111827;
}

/* Badge styles */
.badge {
    font-size: 0.7rem;
    padding: 4px 8px;
    font-weight: 500;
}

.bg-success {
    background-color: #10b981 !important;
}

.bg-warning {
    background-color: #f59e0b !important;
    color: #1f2937;
}

/* Botões */
.btn-group .btn {
    font-size: 0.85rem;
    padding: 6px 14px;
}

.btn-outline-primary {
    border: 1px solid #3b82f6;
    color: #3b82f6;
    background: white;
}

.btn-outline-primary:hover {
    background: #3b82f6;
    color: white;
}

.btn-outline-secondary {
    border: 1px solid #d1d5db;
    color: #6b7280;
    background: white;
}

.btn-outline-secondary:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
    color: #374151;
}

.btn-primary {
    background: #3b82f6;
    border: none;
    padding: 10px 24px;
    font-weight: 500;
}

.btn-primary:hover {
    background: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-primary:disabled {
    background: #9ca3af;
    cursor: not-allowed;
    transform: none;
}

/* Formulário */
.form-control {
    border: 1px solid #e5e7eb;
    padding: 10px 16px;
    border-radius: 12px;
}

.form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-control.is-valid {
    border-color: #10b981;
    background-image: none;
}

.form-control.is-invalid {
    border-color: #ef4444;
    background-image: none;
}

/* Contador */
#selectedCount {
    font-size: 0.85rem;
    color: #6b7280;
}

#countNumber {
    font-weight: 700;
    color: #3b82f6;
}
</style>

<script>
let checkNameTimeout = null;
let isNameValid = false;
let isChecking = false;

function toggleCard(card) {
    card.classList.toggle('selected');
    
    const parentDiv = card.parentElement;
    const checkbox = parentDiv.querySelector('.companhia-checkbox');
    
    if (card.classList.contains('selected')) {
        checkbox.checked = true;
    } else {
        checkbox.checked = false;
    }
    
    updateSelectedCount();
}

function updateSelectedCount() {
    const selectedCards = document.querySelectorAll('.companhia-card.selected');
    const count = selectedCards.length;
    const countSpan = document.getElementById('countNumber');
    if (countSpan) {
        countSpan.textContent = count;
    }
}

function selectAll() {
    const cards = document.querySelectorAll('.companhia-card');
    cards.forEach(card => {
        if (!card.classList.contains('selected')) {
            card.classList.add('selected');
            const parentDiv = card.parentElement;
            const checkbox = parentDiv.querySelector('.companhia-checkbox');
            checkbox.checked = true;
        }
    });
    updateSelectedCount();
}

function deselectAll() {
    const cards = document.querySelectorAll('.companhia-card');
    cards.forEach(card => {
        if (card.classList.contains('selected')) {
            card.classList.remove('selected');
            const parentDiv = card.parentElement;
            const checkbox = parentDiv.querySelector('.companhia-checkbox');
            checkbox.checked = false;
        }
    });
    updateSelectedCount();
}

function checkAirportName(nome, airportId = null) {
    if (!nome || nome.trim() === '') {
        resetNameValidation();
        return;
    }
    
    // Limpar timeout anterior
    if (checkNameTimeout) {
        clearTimeout(checkNameTimeout);
    }
    
    // Mostrar spinner
    const spinner = document.getElementById('nomeSpinner');
    const checkIcon = document.getElementById('nomeCheckIcon');
    const xIcon = document.getElementById('nomeXIcon');
    const nomeInput = document.getElementById('nome_aeroporto');
    const feedbackDiv = document.getElementById('nomeFeedback');
    
    spinner.classList.remove('d-none');
    checkIcon.classList.add('d-none');
    xIcon.classList.add('d-none');
    feedbackDiv.innerHTML = '<span class="text-muted">Verificando disponibilidade...</span>';
    nomeInput.classList.remove('is-valid', 'is-invalid');
    isChecking = true;
    
    // Preparar dados para enviar
    const formData = new FormData();
    formData.append('nome', nome);
    if (airportId) {
        formData.append('id', airportId);
    }
    
    checkNameTimeout = setTimeout(() => {
        fetch('{{ route("aeroportos.check-name") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            spinner.classList.add('d-none');
            isChecking = false;
            
            if (data.exists) {
                // Nome já existe
                nomeInput.classList.add('is-invalid');
                nomeInput.classList.remove('is-valid');
                checkIcon.classList.add('d-none');
                xIcon.classList.remove('d-none');
                feedbackDiv.innerHTML = `<span class="text-danger">⚠️ ${data.message}</span>`;
                isNameValid = false;
            } else {
                // Nome disponível
                nomeInput.classList.add('is-valid');
                nomeInput.classList.remove('is-invalid');
                checkIcon.classList.remove('d-none');
                xIcon.classList.add('d-none');
                feedbackDiv.innerHTML = '<span class="text-success">✓ Nome disponível</span>';
                isNameValid = true;
            }
            
            // Atualizar estado do botão de submit
            updateSubmitButton();
        })
        .catch(error => {
            console.error('Erro ao verificar nome:', error);
            spinner.classList.add('d-none');
            isChecking = false;
            feedbackDiv.innerHTML = '<span class="text-warning">⚠️ Não foi possível verificar disponibilidade</span>';
            // Em caso de erro, permitir submit (a validação do backend vai pegar)
            isNameValid = true;
            updateSubmitButton();
        });
    }, 500); // Delay de 500ms para não fazer muitas requisições
}

function resetNameValidation() {
    const nomeInput = document.getElementById('nome_aeroporto');
    const spinner = document.getElementById('nomeSpinner');
    const checkIcon = document.getElementById('nomeCheckIcon');
    const xIcon = document.getElementById('nomeXIcon');
    const feedbackDiv = document.getElementById('nomeFeedback');
    
    spinner.classList.add('d-none');
    checkIcon.classList.add('d-none');
    xIcon.classList.add('d-none');
    nomeInput.classList.remove('is-valid', 'is-invalid');
    feedbackDiv.innerHTML = '';
    isNameValid = false;
    isChecking = false;
    updateSubmitButton();
}

function updateSubmitButton() {
    const submitBtn = document.getElementById('submitBtn');
    const nomeInput = document.getElementById('nome_aeroporto');
    const nomeValue = nomeInput.value.trim();
    
    if (nomeValue === '') {
        submitBtn.disabled = true;
        submitBtn.title = 'Digite o nome do aeroporto';
    } else if (isChecking) {
        submitBtn.disabled = true;
        submitBtn.title = 'Verificando disponibilidade do nome...';
    } else if (!isNameValid) {
        submitBtn.disabled = true;
        submitBtn.title = 'Este nome não está disponível';
    } else {
        submitBtn.disabled = false;
        submitBtn.title = '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const selectAllBtn = document.getElementById('selectAllBtn');
    const deselectAllBtn = document.getElementById('deselectAllBtn');
    const nomeInput = document.getElementById('nome_aeroporto');
    
    if (selectAllBtn) {
        selectAllBtn.onclick = selectAll;
    }
    
    if (deselectAllBtn) {
        deselectAllBtn.onclick = deselectAll;
    }
    
    // Adicionar evento de input para verificar nome em tempo real
    if (nomeInput) {
        nomeInput.addEventListener('input', function(e) {
            const nome = e.target.value.trim();
            if (nome === '') {
                resetNameValidation();
            } else {
                checkAirportName(nome);
            }
        });
        
        // Prevenir submit se o nome for inválido
        const form = document.getElementById('aeroportoForm');
        form.addEventListener('submit', function(e) {
            if (!isNameValid && nomeInput.value.trim() !== '') {
                e.preventDefault();
                alert('Por favor, aguarde a verificação do nome ou corrija o nome do aeroporto.');
                return false;
            }
        });
    }
    
    // Garantir que os cards estejam sincronizados com os checkboxes (para old values)
    const cards = document.querySelectorAll('.companhia-card');
    cards.forEach(card => {
        const parentDiv = card.parentElement;
        const checkbox = parentDiv.querySelector('.companhia-checkbox');
        if (checkbox.checked) {
            card.classList.add('selected');
        }
    });
    
    updateSelectedCount();
    
    // Inicializar botão de submit desabilitado
    updateSubmitButton();
    
    // Debug: verificar envio
    const form = document.getElementById('aeroportoForm');
    if (form) {
        form.addEventListener('submit', function() {
            const selectedValues = [];
            document.querySelectorAll('.companhia-checkbox:checked').forEach(cb => {
                selectedValues.push(cb.value);
            });
            console.log('Enviando companhias selecionadas:', selectedValues);
        });
    }
});
</script>
@endsection