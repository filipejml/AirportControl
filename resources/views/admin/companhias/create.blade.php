@extends('layouts.app')

@section('title', 'Cadastrar Companhia Aérea')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">✈️ Cadastrar Nova Companhia Aérea</h2>
            <p class="text-muted">Preencha os dados da companhia abaixo</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('companhias.store') }}" id="form-companhia">
                @csrf

                <div class="mb-4">
                    <label for="nome" class="form-label fw-semibold">Nome da Companhia</label>
                    <input type="text" 
                           class="form-control @error('nome') is-invalid @enderror" 
                           id="nome" 
                           name="nome" 
                           value="{{ old('nome') }}"
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
                            <p class="text-muted small mb-0">Selecione as aeronaves que pertencem a esta companhia</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="selecionarTodas">
                                <i class="bi bi-check2-all"></i> Selecionar Todas
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="desmarcarTodas">
                                <i class="bi bi-x-lg"></i> Limpar Tudo
                            </button>
                        </div>
                    </div>
                    
                    @if($aeronaves->count() > 0)
                        <div class="border rounded-3 bg-light p-3" style="max-height: 450px; overflow-y: auto;">
                            <div class="row g-3">
                                @foreach($aeronaves as $aeronave)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card aeronave-card h-100 border-0 shadow-sm {{ in_array($aeronave->id, old('aeronaves', [])) ? 'selected' : '' }}" 
                                             data-aeronave-id="{{ $aeronave->id }}">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-start justify-content-between mb-2">
                                                    <h6 class="card-title fw-bold mb-0">{{ $aeronave->modelo }}</h6>
                                                    <div class="selected-icon {{ in_array($aeronave->id, old('aeronaves', [])) ? 'opacity-100' : 'opacity-0' }}">
                                                        <i class="bi bi-check-circle-fill text-primary fs-5"></i>
                                                    </div>
                                                </div>
                                                
                                                <div class="mt-2">
                                                    <div class="d-flex align-items-center gap-2 mb-2">
                                                        <i class="bi bi-building text-muted small"></i>
                                                        <span class="small text-muted">{{ $aeronave->fabricante->nome ?? 'Fabricante não informado' }}</span>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <i class="bi bi-people text-muted small"></i>
                                                        <span class="small text-muted">{{ $aeronave->capacidade }} passageiros</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info text-center py-4">
                            <i class="bi bi-info-circle fs-3"></i>
                            <p class="mb-0 mt-2">Nenhuma aeronave cadastrada ainda.</p>
                            <a href="{{ route('aeronaves.create') }}" class="btn btn-sm btn-primary mt-2">
                                <i class="bi bi-plus-circle"></i> Cadastrar Aeronave
                            </a>
                        </div>
                    @endif
                    
                    @error('aeronaves')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                    <div class="form-text mt-3">
                        <i class="bi bi-hand-index-thumb"></i> Clique no card para selecionar a aeronave
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Salvar Companhia
                    </button>
                    <a href="{{ route('companhias.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.aeronave-card {
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.aeronave-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(13, 110, 253, 0.1) 0%, rgba(13, 110, 253, 0.05) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

.aeronave-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
}

.aeronave-card:hover::before {
    opacity: 1;
}

.aeronave-card.selected {
    background: linear-gradient(135deg, #e7f1ff 0%, #d4e4ff 100%);
    border: 1px solid #0d6efd !important;
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
}

.aeronave-card.selected .card-title {
    color: #0d6efd;
}

.aeronave-card.selected .selected-icon {
    opacity: 1 !important;
}

.selected-icon {
    transition: opacity 0.2s ease;
}

.selected-icon i {
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
}

/* Efeito de clique */
.aeronave-card:active {
    transform: translateY(-2px);
    transition: transform 0.1s ease;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.aeronave-card');
    const selecionarTodasBtn = document.getElementById('selecionarTodas');
    const desmarcarTodasBtn = document.getElementById('desmarcarTodas');
    const form = document.getElementById('form-companhia');
    
    // Função para atualizar os inputs hidden antes do submit
    function updateHiddenInputs() {
        // Remover todos os inputs hidden existentes
        const existingInputs = form.querySelectorAll('input[name="aeronaves[]"]');
        existingInputs.forEach(input => input.remove());
        
        // Adicionar inputs hidden para cada card selecionado
        cards.forEach(card => {
            if (card.classList.contains('selected')) {
                const aeronaveId = card.dataset.aeronaveId;
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'aeronaves[]';
                hiddenInput.value = aeronaveId;
                form.appendChild(hiddenInput);
            }
        });
    }
    
    // Função para atualizar o estado visual do card
    function updateCardState(card, isSelected) {
        if (isSelected) {
            card.classList.add('selected');
        } else {
            card.classList.remove('selected');
        }
    }
    
    // Inicializar estado dos cards
    cards.forEach(card => {
        // Evento de clique no card
        card.addEventListener('click', function(e) {
            e.preventDefault();
            const currentlySelected = this.classList.contains('selected');
            updateCardState(this, !currentlySelected);
            
            // Adicionar efeito de ripple
            const ripple = document.createElement('div');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.backgroundColor = 'rgba(13, 110, 253, 0.3)';
            ripple.style.width = '100px';
            ripple.style.height = '100px';
            ripple.style.marginLeft = '-50px';
            ripple.style.marginTop = '-50px';
            ripple.style.pointerEvents = 'none';
            
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.style.transform = 'scale(0)';
            ripple.style.transition = 'transform 0.4s ease-out, opacity 0.4s ease-out';
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.style.transform = 'scale(2)';
                ripple.style.opacity = '0';
            }, 10);
            
            setTimeout(() => {
                ripple.remove();
            }, 400);
        });
    });
    
    // Selecionar todas as aeronaves
    if (selecionarTodasBtn) {
        selecionarTodasBtn.addEventListener('click', function() {
            cards.forEach(card => {
                if (!card.classList.contains('selected')) {
                    updateCardState(card, true);
                }
            });
        });
    }
    
    // Desmarcar todas as aeronaves
    if (desmarcarTodasBtn) {
        desmarcarTodasBtn.addEventListener('click', function() {
            cards.forEach(card => {
                if (card.classList.contains('selected')) {
                    updateCardState(card, false);
                }
            });
        });
    }
    
    // Antes de enviar o formulário, atualizar os inputs hidden
    form.addEventListener('submit', function() {
        updateHiddenInputs();
    });
});
</script>
@endpush
@endsection