{{-- resources/views/admin/aeroportos/wizard/step2.blade.php --}}
@extends('layouts.app')

@section('title', 'Adicionar Depósitos - ' . $aeroporto->nome_aeroporto)

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">🏢 Adicionar Depósitos</h2>
            <p class="text-muted">Aeroporto: <strong>{{ $aeroporto->nome_aeroporto }}</strong></p>
            <p class="text-muted">Passo 2 de 3 - Cadastre os depósitos do aeroporto</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <!-- Progresso -->
                    <div class="mb-4">
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: 66%"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-success">✓ Informações Básicas</small>
                            <small class="text-primary fw-bold">Depósitos</small>
                            <small class="text-muted">Veículos</small>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('aeroportos.store.step2', $aeroporto) }}" id="depositosForm">
                        @csrf

                        <div id="depositos-container">
                            <div class="deposito-item mb-4 p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Depósito #1</h5>
                                    <button type="button" class="btn btn-sm btn-danger remove-deposito" style="display: none;">
                                        <i class="bi bi-trash"></i> Remover
                                    </button>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Nome do Depósito *</label>
                                    <input type="text" name="depositos[0][nome]" class="form-control" required 
                                           placeholder="Ex: Depósito Central, Galpão de Manutenção, Estacionamento Sul">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Capacidade Máxima (veículos)</label>
                                    <input type="number" name="depositos[0][capacidade_maxima]" class="form-control" min="0">
                                    <small class="text-muted">Deixe em branco para capacidade ilimitada</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Observações</label>
                                    <textarea name="depositos[0][observacoes]" class="form-control" rows="2" 
                                              placeholder="Informações adicionais sobre o depósito..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-primary" id="add-deposito">
                                <i class="bi bi-plus-circle"></i> Adicionar outro depósito
                            </button>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            Você pode adicionar quantos depósitos precisar. Os depósitos serão vinculados automaticamente ao aeroporto.
                        </div>

                        <div class="d-flex justify-content-between gap-2 mt-4">
                            <a href="{{ route('aeroportos.create.step1') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <div>
                                <button type="submit" name="skip" value="1" class="btn btn-secondary">
                                    Pular esta etapa <i class="bi bi-arrow-right"></i>
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    Próximo: Veículos <i class="bi bi-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.deposito-item {
    background: #f8f9fa;
    transition: all 0.3s ease;
}
.deposito-item:hover {
    background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
</style>

<script>
let depositoCount = 1;

document.getElementById('add-deposito').addEventListener('click', function() {
    const container = document.getElementById('depositos-container');
    const template = container.querySelector('.deposito-item').cloneNode(true);
    
    // Limpar valores
    template.querySelectorAll('input, textarea').forEach(input => {
        input.value = '';
        input.classList.remove('is-valid', 'is-invalid');
    });
    
    // Atualizar índices
    const inputs = template.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        const name = input.getAttribute('name');
        if (name) {
            input.setAttribute('name', name.replace(/\[\d+\]/, `[${depositoCount}]`));
        }
    });
    
    // Atualizar título
    const title = template.querySelector('h5');
    if (title) {
        title.textContent = `Depósito #${depositoCount + 1}`;
    }
    
    // Mostrar botão remover
    const removeBtn = template.querySelector('.remove-deposito');
    if (removeBtn) {
        removeBtn.style.display = 'block';
        removeBtn.onclick = function() {
            template.remove();
            reindexDepositos();
        };
    }
    
    container.appendChild(template);
    depositoCount++;
});

function reindexDepositos() {
    const depositos = document.querySelectorAll('.deposito-item');
    depositos.forEach((deposito, index) => {
        const title = deposito.querySelector('h5');
        if (title) {
            title.textContent = `Depósito #${index + 1}`;
        }
        
        deposito.querySelectorAll('input, textarea').forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace(/\[\d+\]/, `[${index}]`));
            }
        });
        
        const removeBtn = deposito.querySelector('.remove-deposito');
        if (removeBtn) {
            if (index === 0) {
                removeBtn.style.display = 'none';
            } else {
                removeBtn.style.display = 'block';
            }
        }
    });
    depositoCount = depositos.length;
}
</script>
@endsection