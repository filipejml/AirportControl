{{-- resources/views/admin/aeroportos/wizard/step3.blade.php --}}
@extends('layouts.app')

@section('title', 'Adicionar Veículos - ' . $aeroporto->nome_aeroporto)

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">🚗 Adicionar Veículos</h2>
            <p class="text-muted">Aeroporto: <strong>{{ $aeroporto->nome_aeroporto }}</strong></p>
            <p class="text-muted">Passo 3 de 3 - Cadastre os veículos nos depósitos</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <!-- Progresso -->
                    <div class="mb-4">
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: 100%"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-success">✓ Informações Básicas</small>
                            <small class="text-success">✓ Depósitos</small>
                            <small class="text-primary fw-bold">Veículos</small>
                        </div>
                    </div>

                    @if($depositos->count() > 0)
                        <form method="POST" action="{{ route('aeroportos.store.step3', $aeroporto) }}" id="veiculosForm">
                            @csrf

                            @foreach($depositos as $deposito)
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">
                                            <i class="bi bi-building"></i> 
                                            Depósito: {{ $deposito->nome }}
                                            <span class="badge bg-secondary ms-2">{{ $deposito->veiculos->sum('quantidade') }}/{{ $deposito->capacidade_maxima ?? '∞' }} veículos</span>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="veiculos-deposito-{{ $deposito->id }}">
                                            @foreach($deposito->veiculos as $veiculoIndex => $veiculo)
                                                <div class="veiculo-item mb-3 p-3 border rounded">
                                                    @include('admin.aeroportos.wizard.partials.veiculo-form', [
                                                        'depositoId' => $deposito->id,
                                                        'index' => $veiculoIndex,
                                                        'veiculo' => $veiculo
                                                    ])
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        <button type="button" class="btn btn-sm btn-outline-primary add-veiculo" 
                                                data-deposito-id="{{ $deposito->id }}">
                                            <i class="bi bi-plus-circle"></i> Adicionar veículo neste depósito
                                        </button>
                                    </div>
                                </div>
                            @endforeach

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> 
                                Você pode adicionar quantos veículos precisar em cada depósito.
                            </div>

                            <div class="d-flex justify-content-between gap-2 mt-4">
                                <a href="{{ route('aeroportos.create.step2', $aeroporto) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Voltar
                                </a>
                                <div>
                                    <a href="{{ route('aeroportos.show', $aeroporto) }}" class="btn btn-secondary">
                                        Finalizar sem veículos <i class="bi bi-check-circle"></i>
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        Finalizar Cadastro <i class="bi bi-check-circle"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            Nenhum depósito cadastrado. 
                            <a href="{{ route('aeroportos.create.step2', $aeroporto) }}">Voltar e adicionar depósitos</a> primeiro.
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('aeroportos.create.step2', $aeroporto) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Voltar para Depósitos
                            </a>
                            <a href="{{ route('aeroportos.show', $aeroporto) }}" class="btn btn-success">
                                Finalizar sem depósitos <i class="bi bi-check-circle"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let veiculoCounters = {};

function checkVeiculoCodigo(input, depositoId) {
    const codigo = input.value;
    if (!codigo) return;
    
    fetch('{{ route("aeroportos.veiculos.check-codigo") }}', {
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

document.querySelectorAll('.add-veiculo').forEach(button => {
    const depositoId = button.dataset.depositoId;
    veiculoCounters[depositoId] = document.querySelectorAll(`#veiculos-deposito-${depositoId} .veiculo-item`).length;
    
    button.addEventListener('click', function() {
        const depositoId = this.dataset.depositoId;
        const container = document.getElementById(`veiculos-deposito-${depositoId}`);
        const currentCount = veiculoCounters[depositoId];
        
        fetch('{{ route("aeroportos.veiculos.template") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ deposito_id: depositoId, index: currentCount })
        })
        .then(response => response.text())
        .then(html => {
            container.insertAdjacentHTML('beforeend', html);
            veiculoCounters[depositoId]++;
            
            const newItem = container.lastElementChild;
            const codigoInput = newItem.querySelector('input[name*="[codigo]"]');
            if (codigoInput) {
                codigoInput.addEventListener('blur', function() {
                    checkVeiculoCodigo(this, depositoId);
                });
            }
            
            const removeBtn = newItem.querySelector('.remove-veiculo');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    newItem.remove();
                    veiculoCounters[depositoId]--;
                });
            }
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[name*="[codigo]"]').forEach(input => {
        const depositoId = input.closest('.veiculo-item').querySelector('input[name*="[deposito_id]"]')?.value;
        input.addEventListener('blur', function() {
            checkVeiculoCodigo(this, depositoId);
        });
    });
});
</script>
@endsection