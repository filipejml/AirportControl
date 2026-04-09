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
        <div class="col-md-10 mx-auto">
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

                    <!-- REUTILIZANDO O FORMULÁRIO EXISTENTE DO create.blade.php DE DEPÓSITOS -->
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

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nome do Depósito *</label>
                                        <input type="text" name="depositos[0][nome]" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Código *</label>
                                        <input type="text" name="depositos[0][codigo]" class="form-control" required>
                                        <small class="text-muted">Ex: DEP-001, GALPÃO-A, etc.</small>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Localização</label>
                                        <input type="text" name="depositos[0][localizacao]" class="form-control" 
                                               placeholder="Ex: Terminal 1, Ala Norte">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Área Total (m²)</label>
                                        <input type="number" step="any" name="depositos[0][area_total]" class="form-control">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Capacidade Máxima (veículos)</label>
                                        <input type="number" name="depositos[0][capacidade_maxima]" class="form-control">
                                        <small class="text-muted">Deixe em branco para capacidade ilimitada</small>
                                    </div>
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
    template.querySelectorAll('input').forEach(input => {
        input.value = '';
        input.classList.remove('is-valid', 'is-invalid');
    });
    
    // Atualizar índices
    const inputs = template.querySelectorAll('input');
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
        
        deposito.querySelectorAll('input').forEach(input => {
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

// Verificar código duplicado em tempo real
function checkCodigo(input) {
    const codigo = input.value;
    if (!codigo) return;
    
    fetch('{{ route("aeroportos.depositos.check-codigo", $aeroporto) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ codigo: codigo })
    })
    .then(response => response.json())
    .then(data => {
        if (data.exists) {
            input.classList.add('is-invalid');
            let feedback = input.nextElementSibling;
            if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                input.parentNode.appendChild(feedback);
            }
            feedback.textContent = 'Este código já está em uso';
        } else {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Delegar eventos para inputs de código
    document.addEventListener('blur', function(e) {
        if (e.target && e.target.name && e.target.name.match(/depositos\[\d+\]\[codigo\]/)) {
            checkCodigo(e.target);
        }
    }, true);
});
</script>
@endsection