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
                    <!-- Primeira linha: Nome e Capacidade Máxima -->
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
                        <label for="capacidade_maxima" class="form-label fw-semibold">Capacidade Máxima (veículos)</label>
                        <input type="number" 
                               class="form-control @error('capacidade_maxima') is-invalid @enderror" 
                               id="capacidade_maxima" 
                               name="capacidade_maxima" 
                               value="{{ old('capacidade_maxima', $deposito->capacidade_maxima) }}"
                               placeholder="Ex: 50"
                               min="0">
                        <small class="text-muted">Deixe em branco para capacidade ilimitada</small>
                        @error('capacidade_maxima')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <!-- Segunda linha: Status e Observações -->
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label fw-semibold">Status *</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="ativo" {{ old('status', $deposito->status) == 'ativo' ? 'selected' : '' }}>Ativo</option>
                            <option value="inativo" {{ old('status', $deposito->status) == 'inativo' ? 'selected' : '' }}>Inativo</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="observacoes" class="form-label fw-semibold">Observações</label>
                        <textarea class="form-control @error('observacoes') is-invalid @enderror" 
                                  id="observacoes" 
                                  name="observacoes" 
                                  rows="1"
                                  placeholder="Informações adicionais sobre o depósito...">{{ old('observacoes', $deposito->observacoes) }}</textarea>
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
document.addEventListener('DOMContentLoaded', function() {
    const submitBtn = document.getElementById('submitBtn');
    const nomeInput = document.getElementById('nome');
    
    function updateSubmitButton() {
        const nomeValue = nomeInput.value.trim();
        submitBtn.disabled = nomeValue === '';
    }
    
    if (nomeInput) {
        nomeInput.addEventListener('input', updateSubmitButton);
        updateSubmitButton();
    }
});
</script>
@endsection