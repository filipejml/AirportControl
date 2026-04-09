{{-- resources/views/admin/aeroportos/depositos/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Depósito - ' . $deposito->nome)

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">✏️ Editar Depósito</h2>
            <p class="text-muted">Alterando depósito: {{ $deposito->nome }}</p>
        </div>
        <div class="col text-end">
            <a href="{{ route('aeroportos.depositos.index', $aeroporto) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('aeroportos.depositos.update', [$aeroporto, $deposito]) }}" id="depositoForm">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nome" class="form-label fw-semibold">Nome do Depósito *</label>
                        <input type="text" 
                               class="form-control @error('nome') is-invalid @enderror" 
                               id="nome" 
                               name="nome" 
                               value="{{ old('nome', $deposito->nome) }}"
                               required>
                        @error('nome')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="codigo" class="form-label fw-semibold">Código do Depósito *</label>
                        <div class="position-relative">
                            <input type="text" 
                                   class="form-control @error('codigo') is-invalid @enderror" 
                                   id="codigo" 
                                   name="codigo" 
                                   value="{{ old('codigo', $deposito->codigo) }}"
                                   placeholder="Ex: DEP-001"
                                   required>
                            <div class="position-absolute end-0 top-50 translate-middle-y me-3">
                                <div class="spinner-border spinner-border-sm text-primary d-none" id="codigoSpinner" role="status">
                                    <span class="visually-hidden">Verificando...</span>
                                </div>
                                <i class="bi bi-check-circle-fill text-success d-none" id="codigoCheckIcon"></i>
                                <i class="bi bi-x-circle-fill text-danger d-none" id="codigoXIcon"></i>
                            </div>
                        </div>
                        <div id="codigoFeedback" class="form-text"></div>
                        @error('codigo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="localizacao" class="form-label fw-semibold">Localização</label>
                        <input type="text" 
                               class="form-control @error('localizacao') is-invalid @enderror" 
                               id="localizacao" 
                               name="localizacao" 
                               value="{{ old('localizacao', $deposito->localizacao) }}"
                               placeholder="Ex: Setor Sul, próximo ao terminal 2">
                        @error('localizacao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="area_total" class="form-label fw-semibold">Área Total (m²)</label>
                        <input type="number" 
                               step="0.01"
                               class="form-control @error('area_total') is-invalid @enderror" 
                               id="area_total" 
                               name="area_total" 
                               value="{{ old('area_total', $deposito->area_total) }}"
                               placeholder="Ex: 500">
                        @error('area_total')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="capacidade_maxima" class="form-label fw-semibold">Capacidade Máxima (veículos)</label>
                        <input type="number" 
                               class="form-control @error('capacidade_maxima') is-invalid @enderror" 
                               id="capacidade_maxima" 
                               name="capacidade_maxima" 
                               value="{{ old('capacidade_maxima', $deposito->capacidade_maxima) }}"
                               placeholder="Ex: 50">
                        @error('capacidade_maxima')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label fw-semibold">Status *</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="ativo" {{ old('status', $deposito->status) == 'ativo' ? 'selected' : '' }}>Ativo</option>
                            <option value="inativo" {{ old('status', $deposito->status) == 'inativo' ? 'selected' : '' }}>Inativo</option>
                            <option value="manutencao" {{ old('status', $deposito->status) == 'manutencao' ? 'selected' : '' }}>Em Manutenção</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="observacoes" class="form-label fw-semibold">Observações</label>
                        <textarea class="form-control @error('observacoes') is-invalid @enderror" 
                                  id="observacoes" 
                                  name="observacoes" 
                                  rows="3">{{ old('observacoes', $deposito->observacoes) }}</textarea>
                        @error('observacoes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="bi bi-save"></i> Atualizar Depósito
                    </button>
                    <a href="{{ route('aeroportos.depositos.index', $aeroporto) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let checkCodigoTimeout = null;
let isCodigoValid = true;
let isChecking = false;
let currentDepositoId = {{ $deposito->id }};

function checkDepositoCodigo(codigo, depositoId = null) {
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
    
    const formData = new FormData();
    formData.append('codigo', codigo);
    if (depositoId) {
        formData.append('id', depositoId);
    }
    
    checkCodigoTimeout = setTimeout(() => {
        fetch('{{ route("aeroportos.depositos.check-codigo", $aeroporto) }}', {
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
                codigoInput.classList.add('is-invalid');
                codigoInput.classList.remove('is-valid');
                checkIcon.classList.add('d-none');
                xIcon.classList.remove('d-none');
                feedbackDiv.innerHTML = `<span class="text-danger">⚠️ ${data.message}</span>`;
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
    const codigoInput = document.getElementById('codigo');
    const nomeInput = document.getElementById('nome');
    const codigoValue = codigoInput.value.trim();
    const nomeValue = nomeInput.value.trim();
    
    if (nomeValue === '' || codigoValue === '') {
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
    const nomeInput = document.getElementById('nome');
    
    // Marcar código atual como válido
    const currentCodigo = codigoInput.value.trim();
    if (currentCodigo) {
        isCodigoValid = true;
        codigoInput.classList.add('is-valid');
        const checkIcon = document.getElementById('codigoCheckIcon');
        if (checkIcon) checkIcon.classList.remove('d-none');
    }
    
    if (codigoInput) {
        codigoInput.addEventListener('input', function(e) {
            const codigo = e.target.value.trim();
            if (codigo === '') {
                resetCodigoValidation();
            } else {
                checkDepositoCodigo(codigo, currentDepositoId);
            }
        });
    }
    
    if (nomeInput) {
        nomeInput.addEventListener('input', updateSubmitButton);
    }
    
    updateSubmitButton();
});
</script>
@endsection