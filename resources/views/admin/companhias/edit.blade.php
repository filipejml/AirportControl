@extends('layouts.app')

@section('title', 'Editar Companhia Aérea')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">✈️ Editar Companhia Aérea</h2>
            <p class="text-muted">Altere os dados da companhia conforme necessário</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('companhias.update', $companhia) }}" id="companhiaForm">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="nome" class="form-label fw-semibold">Nome da Companhia</label>
                    <input type="text" 
                           class="form-control @error('nome') is-invalid @enderror" 
                           id="nome" 
                           name="nome" 
                           value="{{ old('nome', $companhia->nome) }}"
                           placeholder="Ex: Latam, Gol, Azul, American Airlines..."
                           required>
                    @error('nome')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <label class="form-label fw-semibold mb-0">Aeronaves da Companhia</label>
                            <p class="text-muted small mb-0 mt-1">Clique nos cards abaixo para selecionar as aeronaves</p>
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
                    
                    @php
                        $selectedAeronaves = old('aeronaves', $companhia->aeronaves->pluck('id')->toArray());
                    @endphp
                    
                    @if($aeronaves->count() > 0)
                        <div class="row g-4" id="aeronavesContainer">
                            @foreach($aeronaves as $aeronave)
                                <div class="col-md-4 col-lg-3">
                                    <div class="aeronave-card {{ in_array($aeronave->id, $selectedAeronaves) ? 'selected' : '' }}" 
                                         data-id="{{ $aeronave->id }}"
                                         onclick="toggleCard(this)">
                                        
                                        <div class="card-header">
                                            <h5 class="card-title">{{ $aeronave->modelo }}</h5>
                                            <div class="selection-badge">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </div>
                                        </div>
                                        
                                        <div class="card-stats">
                                            <div class="stat-item">
                                                <div class="stat-label">Fabricante</div>
                                                <div class="stat-value">{{ $aeronave->fabricante->nome ?? 'Não informado' }}</div>
                                            </div>
                                            <div class="stat-item">
                                                <div class="stat-label">Capacidade</div>
                                                <div class="stat-value">{{ $aeronave->capacidade }} passageiros</div>
                                            </div>
                                        </div>
                                        
                                        <input type="checkbox" 
                                               name="aeronaves[]" 
                                               class="aeronave-checkbox" 
                                               value="{{ $aeronave->id }}"
                                               style="display: none;"
                                               {{ in_array($aeronave->id, $selectedAeronaves) ? 'checked' : '' }}>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-3">
                            <span class="text-muted" id="selectedCount">
                                <i class="bi bi-info-circle"></i> <span id="countNumber">0</span> aeronave(s) selecionada(s)
                            </span>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            Nenhuma aeronave cadastrada ainda. 
                            <a href="{{ route('aeronaves.create') }}" class="alert-link">Cadastrar aeronave</a>
                        </div>
                    @endif
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-pencil-square"></i> Atualizar Companhia
                    </button>
                    <a href="{{ route('companhias.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Cards inspirados no design da imagem */
.aeronave-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.aeronave-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.15);
    border-color: #cbd5e1;
}

.aeronave-card.selected {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border-color: #1e40af;
    box-shadow: 0 8px 20px -6px rgba(59, 130, 246, 0.4);
}

.aeronave-card.selected .card-header .card-title {
    color: white;
}

.aeronave-card.selected .selection-badge {
    opacity: 1;
    transform: scale(1);
    color: white;
}

.aeronave-card.selected .card-stats .stat-label {
    color: rgba(255, 255, 255, 0.8);
}

.aeronave-card.selected .card-stats .stat-value {
    color: white;
}

.card-header {
    padding: 20px 20px 12px 20px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    border-bottom: 1px solid #e5e7eb;
}

.aeronave-card.selected .card-header {
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
function toggleCard(card) {
    card.classList.toggle('selected');
    
    const parentDiv = card.parentElement;
    const checkbox = parentDiv.querySelector('.aeronave-checkbox');
    
    if (card.classList.contains('selected')) {
        checkbox.checked = true;
    } else {
        checkbox.checked = false;
    }
    
    updateSelectedCount();
}

function updateSelectedCount() {
    const selectedCards = document.querySelectorAll('.aeronave-card.selected');
    const count = selectedCards.length;
    const countSpan = document.getElementById('countNumber');
    if (countSpan) {
        countSpan.textContent = count;
    }
}

function selectAll() {
    const cards = document.querySelectorAll('.aeronave-card');
    cards.forEach(card => {
        if (!card.classList.contains('selected')) {
            card.classList.add('selected');
            const parentDiv = card.parentElement;
            const checkbox = parentDiv.querySelector('.aeronave-checkbox');
            checkbox.checked = true;
        }
    });
    updateSelectedCount();
}

function deselectAll() {
    const cards = document.querySelectorAll('.aeronave-card');
    cards.forEach(card => {
        if (card.classList.contains('selected')) {
            card.classList.remove('selected');
            const parentDiv = card.parentElement;
            const checkbox = parentDiv.querySelector('.aeronave-checkbox');
            checkbox.checked = false;
        }
    });
    updateSelectedCount();
}

document.addEventListener('DOMContentLoaded', function() {
    const selectAllBtn = document.getElementById('selectAllBtn');
    const deselectAllBtn = document.getElementById('deselectAllBtn');
    
    if (selectAllBtn) {
        selectAllBtn.onclick = selectAll;
    }
    
    if (deselectAllBtn) {
        deselectAllBtn.onclick = deselectAll;
    }
    
    // Garantir que os cards estejam sincronizados com os checkboxes
    const cards = document.querySelectorAll('.aeronave-card');
    cards.forEach(card => {
        const parentDiv = card.parentElement;
        const checkbox = parentDiv.querySelector('.aeronave-checkbox');
        if (checkbox.checked) {
            card.classList.add('selected');
        }
    });
    
    updateSelectedCount();
});
</script>
@endsection