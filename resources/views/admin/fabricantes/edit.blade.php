@extends('layouts.app')

@section('title', 'Editar Fabricante')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">🏭 Editar Fabricante</h2>
            <p class="text-muted">Altere os dados do fabricante conforme necessário</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('fabricantes.update', $fabricante) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="nome" class="form-label fw-semibold">Nome do Fabricante</label>
                    <input type="text" 
                           class="form-control @error('nome') is-invalid @enderror" 
                           id="nome" 
                           name="nome" 
                           value="{{ old('nome', $fabricante->nome) }}"
                           placeholder="Ex: Airbus, Boeing, Embraer..."
                           required>
                    @error('nome')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="pais_origem" class="form-label fw-semibold">País de Origem</label>
                    <input type="text" 
                           class="form-control @error('pais_origem') is-invalid @enderror" 
                           id="pais_origem" 
                           name="pais_origem" 
                           value="{{ old('pais_origem', $fabricante->pais_origem) }}"
                           placeholder="Ex: Brasil, EUA, França..."
                           >
                    @error('pais_origem')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Campo opcional. Informe o país sede do fabricante.</div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-pencil-square"></i> Atualizar Fabricante
                    </button>
                    <a href="{{ route('fabricantes.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection