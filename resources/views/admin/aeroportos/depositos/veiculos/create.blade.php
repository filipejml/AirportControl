{{-- resources/views/admin/aeroportos/depositos/veiculos/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Cadastrar Veículo - ' . $deposito->nome)

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">🚗 Cadastrar Veículo</h2>
            <p class="text-muted">Depósito: <strong>{{ $deposito->nome }}</strong> - {{ $aeroporto->nome_aeroporto }}</p>
        </div>
        <div class="col text-end">
            <a href="{{ route('aeroportos.depositos.veiculos.index', [$aeroporto, $deposito]) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('aeroportos.depositos.veiculos.store', [$aeroporto, $deposito]) }}" id="veiculoForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="tipo_veiculo" class="form-label fw-semibold">Tipo de Veículo *</label>
                                <select class="form-select @error('tipo_veiculo') is-invalid @enderror" 
                                        id="tipo_veiculo" 
                                        name="tipo_veiculo" 
                                        required>
                                    <option value="">Selecione o tipo de veículo...</option>
                                    @foreach(\App\Models\Veiculo::TIPOS_VEICULOS as $key => $tipo)
                                        <option value="{{ $key }}" {{ old('tipo_veiculo') == $key ? 'selected' : '' }}>
                                            {{ $tipo['nome'] }} - {{ $tipo['descricao'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipo_veiculo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="codigo" class="form-label fw-semibold">Código do Veículo *</label>
                                <div class="position-relative">
                                    <input type="text" 
                                           class="form-control @error('codigo') is-invalid @enderror" 
                                           id="codigo" 
                                           name="codigo" 
                                           value="{{ old('codigo') }}"
                                           placeholder="Ex: EB-001, CC-023, CP-045"
                                           required
                                           autocomplete="off">
                                    <div class="position-absolute end-0 top-50 translate-middle-y me-3">
                                        <div class="spinner-border spinner-border-sm text-primary d-none" id="codigoSpinner" role="status">
                                            <span class="visually-hidden">Verificando...</span>
                                        </div>
                                        <i class="bi bi-check-circle-fill text-success d-none" id="codigoCheckIcon"></i>
                                        <i class="bi bi-x-circle-fill text-danger d-none" id="codigoXIcon"></i>
                                    </div>
                                </div>
                                <div id="codigoFeedback" class="form-text"></div>
                                <small class="text-muted">Código único para identificação do veículo</small>
                                @error('codigo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="quantidade" class="form-label fw-semibold">Quantidade</label>
                                <input type="number" 
                                       class="form-control @error('quantidade') is-invalid @enderror" 
                                       id="quantidade" 
                                       name="quantidade" 
                                       value="{{ old('quantidade', 1) }}"
                                       min="1"
                                       step="1">
                                <small class="text-muted">Quantidade deste veículo no depósito</small>
                                @error('quantidade')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="observacoes" class="form-label fw-semibold">Observações</label>
                                <textarea class="form-control @error('observacoes') is-invalid @enderror" 
                                          id="observacoes" 
                                          name="observacoes" 
                                          rows="3"
                                          placeholder="Informações adicionais sobre o veículo...">{{ old('observacoes') }}</textarea>
                                @error('observacoes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-save"></i> Salvar Veículo
                            </button>
                            <a href="{{ route('aeroportos.depositos.veiculos.index', [$aeroporto, $deposito]) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let checkCodigoTimeout = null;
let isCodigoValid = false;
let isChecking = false;

function checkVeiculoCodigo(codigo) {
    if (!codigo || codigo.trim() === '') {
        resetCodigoValidation();
        return;
    }
    
    if (checkCodigoTimeout) {
        clearTimeout(checkCodigoTimeout);
    }
    
    const spinner = document.getElementById('codigoSpinner');
    const checkIcon = document.getElementById('codigoCheckIcon');
    const xIcon = document.getElementById('codigoXIcon');
    const codigoInput = document.getElementById('codigo');
    const feedbackDiv = document.getElementById('codigoFeedback');
    
    spinner.classList.remove('d-none');
    checkIcon.classList.add('d-none');
    xIcon.classList.add('d-none');
    feedbackDiv.innerHTML = '<span class="text-muted">Verificando disponibilidade...</span>';
    codigoInput.classList.remove('is-valid', 'is-invalid');
    isChecking = true;
    
    checkCodigoTimeout = setTimeout(() => {
        fetch('{{ route("aeroportos.depositos.veiculos.check-codigo", [$aeroporto, $deposito]) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ codigo: codigo })
        })
        .then(response => response.json())
        .then(data => {
            spinner.classList.add('d-none');
            isChecking = false;
            
            if (data.exists) {
                codigoInput.classList.add('is-invalid');
                codigoInput.classList.remove('is-valid');
                checkIcon.classList.add('d-none');
                xIcon.classList.remove('d-none');
                feedbackDiv.innerHTML = '<span class="text-danger">⚠️ Este código já está em uso</span>';
                isCodigoValid = false;
            } else {
                codigoInput.classList.add('is-valid');
                codigoInput.classList.remove('is-invalid');
                checkIcon.classList.remove('d-none');
                xIcon.classList.add('d-none');
                feedbackDiv.innerHTML = '<span class="text-success">✓ Código disponível</span>';
                isCodigoValid = true;
            }
            
            updateSubmitButton();
        })
        .catch(error => {
            console.error('Erro ao verificar código:', error);
            spinner.classList.add('d-none');
            isChecking = false;
            feedbackDiv.innerHTML = '<span class="text-warning">⚠️ Não foi possível verificar disponibilidade</span>';
            isCodigoValid = true;
            updateSubmitButton();
        });
    }, 500);
}

function resetCodigoValidation() {
    const codigoInput = document.getElementById('codigo');
    const spinner = document.getElementById('codigoSpinner');
    const checkIcon = document.getElementById('codigoCheckIcon');
    const xIcon = document.getElementById('codigoXIcon');
    const feedbackDiv = document.getElementById('codigoFeedback');
    
    spinner.classList.add('d-none');
    checkIcon.classList.add('d-none');
    xIcon.classList.add('d-none');
    codigoInput.classList.remove('is-valid', 'is-invalid');
    feedbackDiv.innerHTML = '';
    isCodigoValid = false;
    isChecking = false;
    updateSubmitButton();
}

function updateSubmitButton() {
    const submitBtn = document.getElementById('submitBtn');
    const codigoValue = document.getElementById('codigo').value.trim();
    const tipoValue = document.getElementById('tipo_veiculo').value;
    
    if (codigoValue === '' || tipoValue === '') {
        submitBtn.disabled = true;
    } else if (isChecking) {
        submitBtn.disabled = true;
    } else if (!isCodigoValid) {
        submitBtn.disabled = true;
    } else {
        submitBtn.disabled = false;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const codigoInput = document.getElementById('codigo');
    const tipoInput = document.getElementById('tipo_veiculo');
    
    if (codigoInput) {
        codigoInput.addEventListener('input', function(e) {
            const codigo = e.target.value.trim();
            if (codigo === '') {
                resetCodigoValidation();
            } else {
                checkVeiculoCodigo(codigo);
            }
        });
    }
    
    if (tipoInput) {
        tipoInput.addEventListener('change', updateSubmitButton);
    }
    
    updateSubmitButton();
});
</script>
@endsection