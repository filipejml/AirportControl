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
                                            Depósito: {{ $deposito->nome }} (Código: {{ $deposito->codigo }})
                                            <span class="badge bg-secondary ms-2">{{ $deposito->veiculos->count() }}/{{ $deposito->capacidade_maxima ?? '∞' }} veículos</span>
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
                                                data-deposito-id="{{ $deposito->id }}" 
                                                data-deposito-nome="{{ $deposito->nome }}">
                                            <i class="bi bi-plus-circle"></i> Adicionar veículo neste depósito
                                        </button>
                                    </div>
                                </div>
                            @endforeach

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> 
                                Você pode adicionar quantos veículos precisar em cada depósito, respeitando a capacidade máxima.
                            </div>

                            <div class="d-flex justify-content-between gap-2 mt-4">
                                <a href="{{ route('aeroportos.create.step2', $aeroporto) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Voltar
                                </a>
                                <div>
                                    <button type="submit" name="skip" value="1" class="btn btn-secondary">
                                        Finalizar sem veículos <i class="bi bi-check-circle"></i>
                                    </button>
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
                            <form method="POST" action="{{ route('aeroportos.store.step3', $aeroporto) }}">
                                @csrf
                                <input type="hidden" name="skip" value="1">
                                <button type="submit" class="btn btn-success">
                                    Finalizar sem depósitos <i class="bi bi-check-circle"></i>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Mapeamento dos tipos de veículos (mesmo do create.blade.php de veículos)
const tiposInfo = {
    'esteira_bagagem': { unidade: 'kg', label: 'Capacidade (kg)', help: 'Peso máximo suportado em kg' },
    'caminhao_combustivel': { unidade: 'litros', label: 'Capacidade (litros)', help: 'Capacidade do tanque em litros' },
    'carro_inspecao': { unidade: null, label: 'Capacidade', help: 'Informações adicionais nos observações' },
    'carrinho_bagagem': { unidade: 'unidades', label: 'Capacidade (unidades)', help: 'Número de bagagens por viagem' },
    'caminhao_pushback': { unidade: 'toneladas', label: 'Capacidade (toneladas)', help: 'Peso máximo de reboque em toneladas' },
    'caminhao_escada': { unidade: 'metros', label: 'Altura Máxima (metros)', help: 'Altura máxima de alcance da escada' },
    'caminhao_limpeza': { unidade: 'litros', label: 'Capacidade (litros)', help: 'Capacidade do reservatório em litros' },
    'outro': { unidade: null, label: 'Capacidade', help: 'Especifique a capacidade nos observações' }
};

function selectTipo(selectElement) {
    const tipo = selectElement.value;
    const veiculoItem = selectElement.closest('.veiculo-item');
    const unidadeSpan = veiculoItem.querySelector('.unidade-capacidade');
    const capacidadeLabel = veiculoItem.querySelector('.capacidade-label');
    const capacidadeHelp = veiculoItem.querySelector('.capacidade-help');
    
    const info = tiposInfo[tipo] || tiposInfo['outro'];
    
    if (capacidadeLabel) {
        capacidadeLabel.textContent = info.label;
    }
    
    if (unidadeSpan) {
        unidadeSpan.textContent = info.unidade || '-';
    }
    
    if (capacidadeHelp) {
        capacidadeHelp.textContent = info.help;
    }
}

function updateCapacidadeLabel() {
    document.querySelectorAll('select[name*="[tipo_veiculo]"]').forEach(select => {
        if (select.value) {
            selectTipo(select);
        }
        select.addEventListener('change', function() { selectTipo(this); });
    });
}

// Adicionar veículo dinamicamente
let veiculoCounters = {};

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
            newItem.querySelectorAll('select[name*="[tipo_veiculo]"]').forEach(select => {
                select.addEventListener('change', function() { selectTipo(this); });
            });
            
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

// Verificar código duplicado
function checkVeiculoCodigo(input) {
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

document.addEventListener('DOMContentLoaded', function() {
    updateCapacidadeLabel();
    
    document.addEventListener('blur', function(e) {
        if (e.target && e.target.name && e.target.name.match(/veiculos\[\d+\]\[codigo\]/)) {
            checkVeiculoCodigo(e.target);
        }
    }, true);
});
</script>
@endsection