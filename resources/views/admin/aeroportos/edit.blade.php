@extends('layouts.app')

@section('title', 'Editar Aeroporto')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">🛫 Editar Aeroporto</h2>
            <p class="text-muted">Altere os dados do aeroporto conforme necessário</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('aeroportos.update', $aeroporto) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="nome_aeroporto" class="form-label fw-semibold">Nome do Aeroporto</label>
                    <input type="text" 
                           class="form-control @error('nome_aeroporto') is-invalid @enderror" 
                           id="nome_aeroporto" 
                           name="nome_aeroporto" 
                           value="{{ old('nome_aeroporto', $aeroporto->nome_aeroporto) }}"
                           placeholder="Ex: Aeroporto Internacional de Guarulhos, Aeroporto de Congonhas..."
                           required>
                    @error('nome_aeroporto')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Companhias que Operam no Aeroporto</label>
                    <p class="text-muted small mb-2">Selecione as companhias aéreas que operam neste aeroporto</p>
                    
                    <div class="border rounded p-3 bg-light" style="max-height: 300px; overflow-y: auto;">
                        @forelse($companhias as $companhia)
                            <div class="form-check mb-2">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="companhias[]" 
                                       value="{{ $companhia->id }}"
                                       id="companhia{{ $companhia->id }}"
                                       {{ in_array($companhia->id, old('companhias', $aeroporto->companhias->pluck('id')->toArray())) ? 'checked' : '' }}>
                                <label class="form-check-label" for="companhia{{ $companhia->id }}">
                                    <strong>{{ $companhia->nome }}</strong>
                                    <span class="badge bg-primary">{{ $companhia->aeronaves_count ?? 0 }} aeronaves</span>
                                </label>
                            </div>
                        @empty
                            <p class="text-muted mb-0 text-center py-3">
                                <i class="bi bi-exclamation-circle"></i> 
                                Nenhuma companhia aérea cadastrada ainda. 
                                <a href="{{ route('companhias.create') }}" class="text-decoration-none">Cadastrar companhia</a>
                            </p>
                        @endforelse
                    </div>
                    @error('companhias')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Campo opcional. Selecione as companhias que operam neste aeroporto.</div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-pencil-square"></i> Atualizar Aeroporto
                    </button>
                    <a href="{{ route('aeroportos.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection