{{-- resources/views/admin/aeroportos/depositos/veiculos/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Cadastrar Veículo - ' . $deposito->nome)

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">🚗 Cadastrar Veículo</h2>
            <p class="text-muted">Depósito: {{ $deposito->nome }} - {{ $aeroporto->nome_aeroporto }}</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('aeroportos.depositos.veiculos.store', [$aeroporto, $deposito]) }}" id="veiculoForm">
                @csrf

                {{-- Tipo de Veículo --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Tipo de Veículo *</label>
                    <div class="row g-3" id="tiposContainer">
                        @foreach(\App\Models\Veiculo::TIPOS_VEICULOS as $key => $tipo)
                            <div class="col-md-3 col-sm-6">
                                <div class="tipo-card {{ old('tipo_veiculo', '') == $key ? 'selected' : '' }}" 
                                     data-tipo="{{ $key }}"
                                     onclick="selectTipo('{{ $key }}')">
                                    <div class="text-center p-3">
                                        <i class="bi {{ $tipo['icone'] }} fs-1"></i>
                                        <h6 class="mt-2 mb-0">{{ $tipo['nome'] }}</h6>
                                        <small class="text-muted">{{ $tipo['descricao'] }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="tipo_veiculo" id="tipo_veiculo" value="{{ old('tipo_veiculo') }}" required>
                    @error('tipo_veiculo')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Informações Básicas --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="codigo" class="form-label">Código do Veículo *</label>
                        <input type="text" class="form-control @error('codigo') is-invalid @enderror" 
                               id="codigo" name="codigo" value="{{ old('codigo') }}" 
                               placeholder="Ex: EB-001, CC-023, CP-045" required>
                        <small class="text-muted">Código único para identificação do veículo</small>
                        @error('codigo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status *</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="disponivel" {{ old('status') == 'disponivel' ? 'selected' : '' }}>✅ Disponível</option>
                            <option value="indisponivel" {{ old('status') == 'indisponivel' ? 'selected' : '' }}>❌ Indisponível</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="fabricante" class="form-label">Fabricante</label>
                        <input type="text" class="form-control" id="fabricante" name="fabricante" value="{{ old('fabricante') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="modelo" class="form-label">Modelo</label>
                        <input type="text" class="form-control" id="modelo" name="modelo" value="{{ old('modelo') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="ano_fabricacao" class="form-label">Ano de Fabricação</label>
                        <input type="number" class="form-control" id="ano_fabricacao" name="ano_fabricacao" 
                               value="{{ old('ano_fabricacao') }}" min="1970" max="{{ date('Y') }}">
                    </div>

                    <div class="col-md-6 mb-3" id="capacidadeRow">
                        <label for="capacidade_operacional" class="form-label" id="capacidadeLabel">Capacidade Operacional</label>
                        <div class="input-group">
                            <input type="number" step="any" class="form-control" id="capacidade_operacional" 
                                   name="capacidade_operacional" value="{{ old('capacidade_operacional') }}">
                            <span class="input-group-text" id="unidadeCapacidade">-</span>
                        </div>
                        <small class="text-muted" id="capacidadeHelp">Capacidade máxima de operação</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="observacoes" class="form-label">Observações</label>
                    <textarea class="form-control" id="observacoes" name="observacoes" rows="3" 
                              placeholder="Informações adicionais sobre o veículo...">{{ old('observacoes') }}</textarea>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Salvar Veículo
                    </button>
                    <a href="{{ route('aeroportos.depositos.veiculos.index', [$aeroporto, $deposito]) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.tipo-card {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.tipo-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #cbd5e1;
}

.tipo-card.selected {
    border-color: #3b82f6;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
}

.tipo-card.selected i {
    color: #3b82f6;
}

.tipo-card.selected h6 {
    color: #1e40af;
}
</style>

<script>
function selectTipo(tipo) {
    // Remove selected de todos
    document.querySelectorAll('.tipo-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Adiciona selected ao clicado
    document.querySelector(`.tipo-card[data-tipo="${tipo}"]`).classList.add('selected');
    document.getElementById('tipo_veiculo').value = tipo;
    
    // Atualiza unidade de capacidade baseada no tipo
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
    
    const info = tiposInfo[tipo] || tiposInfo['outro'];
    const capacidadeLabel = document.getElementById('capacidadeLabel');
    const unidadeSpan = document.getElementById('unidadeCapacidade');
    const capacidadeHelp = document.getElementById('capacidadeHelp');
    
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

// Preservar seleção se houver erro de validação
document.addEventListener('DOMContentLoaded', function() {
    const tipoSelecionado = document.getElementById('tipo_veiculo').value;
    if (tipoSelecionado) {
        selectTipo(tipoSelecionado);
    }
    
    // Verificar código duplicado
    const codigoInput = document.getElementById('codigo');
    if (codigoInput) {
        codigoInput.addEventListener('blur', function() {
            const codigo = this.value;
            if (codigo.length > 0) {
                fetch('{{ route("aeroportos.depositos.veiculos.check-codigo", [$aeroporto, $deposito]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ codigo: codigo })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        this.classList.add('is-invalid');
                        // Criar ou atualizar feedback
                        let feedback = this.nextElementSibling;
                        if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                            feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback';
                            this.parentNode.appendChild(feedback);
                        }
                        feedback.textContent = 'Este código já está em uso';
                    } else {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    }
                });
            }
        });
    }
});
</script>
@endsection