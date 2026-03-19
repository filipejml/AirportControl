@extends('layouts.app')

@section('title', 'Editar Companhia Aérea')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">✈️ Editar Companhia Aérea</h2>
            <p class="text-muted">Altere os dados da companhia conforme necessário</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('companhias.update', $companhia) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="nome" class="form-label fw-semibold">Nome da Companhia</label>
                    <input type="text" 
                           class="form-control @error('nome') is-invalid @enderror" 
                           id="nome" 
                           name="nome" 
                           value="{{ old('nome', $companhia->nome) }}"
                           placeholder="Ex: Latam, Gol, Azul, American Airlines..."
                           required>
                    @error('nome')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Aeronaves da Companhia</label>
                    <p class="text-muted small mb-2">Selecione as aeronaves que pertencem a esta companhia</p>
                    
                    <div class="border rounded p-3 bg-light" style="max-height: 300px; overflow-y: auto;">
                        @forelse($aeronaves as $aeronave)
                            <div class="form-check mb-2">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="aeronaves[]" 
                                       value="{{ $aeronave->id }}"
                                       id="aeronave{{ $aeronave->id }}"
                                       {{ in_array($aeronave->id, old('aeronaves', $companhia->aeronaves->pluck('id')->toArray())) ? 'checked' : '' }}>
                                <label class="form-check-label" for="aeronave{{ $aeronave->id }}">
                                    <strong>{{ $aeronave->modelo }}</strong> 
                                    <span class="text-muted">({{ $aeronave->fabricante->nome ?? 'Fabricante não informado' }})</span>
                                    <span class="badge bg-info">{{ $aeronave->capacidade }} passageiros</span>
                                </label>
                            </div>
                        @empty
                            <p class="text-muted mb-0 text-center py-3">
                                <i class="bi bi-exclamation-circle"></i> 
                                Nenhuma aeronave cadastrada ainda. 
                                <a href="{{ route('aeronaves.create') }}" class="text-decoration-none">Cadastrar aeronave</a>
                            </p>
                        @endforelse
                    </div>
                    @error('aeronaves')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Campo opcional. Selecione as aeronaves que serão operadas por esta companhia.</div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-pencil-square"></i> Atualizar Companhia
                    </button>
                    <a href="{{ route('companhias.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection