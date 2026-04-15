{{-- resources/views/admin/aeroportos/depositos/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Novo Depósito - ' . $aeroporto->nome_aeroporto)

@section('content')
<style>
.stats-card {
    background: white;
    border-radius: 20px;
    padding: 1.25rem 1rem;
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

.stats-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.stats-icon {
    width: 48px;
    height: 48px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
}

.stats-value {
    font-size: 1.75rem;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 0.25rem;
}

.stats-label {
    font-size: 0.85rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 500;
}

.stats-sub {
    font-size: 0.7rem;
    color: #adb5bd;
    margin-top: 0.25rem;
}
</style>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">🏢 Novo Depósito</h2>
            <p class="text-muted">Cadastrar novo depósito no aeroporto {{ $aeroporto->nome_aeroporto }}</p>
        </div>
        <div class="col text-end">
            <a href="{{ route('aeroportos.depositos.index', $aeroporto) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('aeroportos.depositos.store', $aeroporto) }}" id="depositoForm">
                        @csrf

                        <!-- Campo Nome do Depósito com sugestão automática -->
                        <div class="mb-4">
                            <label for="nome" class="form-label fw-semibold">Nome do Depósito</label>
                            <div class="position-relative">
                                <input type="text" 
                                       class="form-control @error('nome') is-invalid @enderror" 
                                       id="nome" 
                                       name="nome" 
                                       value="{{ old('nome', $nomeSugerido ?? '') }}"
                                       placeholder="Deixe em branco para gerar automaticamente">
                                <div class="position-absolute end-0 top-50 translate-middle-y me-3">
                                    <i class="bi bi-magic text-primary" style="font-size: 1.2rem;"></i>
                                </div>
                            </div>
                            <div class="form-text">
                                <i class="bi bi-info-circle"></i> 
                                Deixe em branco para gerar automaticamente (Depósito 1, Depósito 2, ...)
                            </div>
                            @error('nome')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Campo Capacidade Máxima -->
                        <div class="mb-4">
                            <label for="capacidade_maxima" class="form-label fw-semibold">Capacidade Máxima (veículos)</label>
                            <input type="number" 
                                   class="form-control @error('capacidade_maxima') is-invalid @enderror" 
                                   id="capacidade_maxima" 
                                   name="capacidade_maxima" 
                                   value="{{ old('capacidade_maxima') }}"
                                   placeholder="Ex: 50"
                                   min="0">
                            <div class="form-text">Deixe em branco para capacidade ilimitada</div>
                            @error('capacidade_maxima')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Campo Status -->
                        <div class="mb-4">
                            <label for="status" class="form-label fw-semibold">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="ativo" {{ old('status') == 'ativo' ? 'selected' : '' }} selected>Ativo</option>
                                <option value="inativo" {{ old('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
                                <option value="manutencao" {{ old('status') == 'manutencao' ? 'selected' : '' }}>Em Manutenção</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="bi bi-save"></i> Salvar Depósito
                            </button>
                            <a href="{{ route('aeroportos.depositos.index', $aeroporto) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Card de dica -->
    <div class="row mt-4">
        <div class="col-md-8 mx-auto">
            <div class="stats-card">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="stats-label">Dica Rápida</div>
                        <div class="stats-value" style="font-size: 1rem;">
                            <i class="bi bi-lightbulb text-warning"></i> 
                            O nome do depósito será gerado automaticamente se você deixar em branco
                        </div>
                        <div class="stats-sub mt-2">
                            Próximo depósito será: <strong>{{ $nomeSugerido ?? 'Depósito 1' }}</strong>
                        </div>
                    </div>
                    <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-magic"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const submitBtn = document.getElementById('submitBtn');
    const nomeInput = document.getElementById('nome');
    
    function updateSubmitButton() {
        submitBtn.disabled = false;
    }
    
    if (nomeInput) {
        nomeInput.addEventListener('input', updateSubmitButton);
    }
    
    updateSubmitButton();
});
</script>
@endsection