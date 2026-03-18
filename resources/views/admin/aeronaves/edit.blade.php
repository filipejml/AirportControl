@extends('layouts.app')

@section('title', 'Editar Aeronave')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Editar Aeronave</h2>
            <p class="text-muted">Altere os dados da aeronave conforme necessário</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('aeronaves.update', $aeronave->id) }}" id="formAeronave">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="modelo" class="form-label fw-semibold">Modelo da Aeronave</label>
                    <input type="text" 
                           class="form-control @error('modelo') is-invalid @enderror" 
                           id="modelo" 
                           name="modelo" 
                           value="{{ old('modelo', $aeronave->modelo) }}"
                           placeholder="Ex: Boeing 737-800"
                           required>
                    @error('modelo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="capacidade" class="form-label fw-semibold">Capacidade de Passageiros</label>
                    <input type="number" 
                           class="form-control @error('capacidade') is-invalid @enderror" 
                           id="capacidade" 
                           name="capacidade" 
                           value="{{ old('capacidade', $aeronave->capacidade) }}"
                           placeholder="Ex: 180"
                           min="1"
                           required
                           oninput="classificarPorte()">
                    @error('capacidade')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Campo de Porte (automático) -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Porte da Aeronave</label>
                    <div id="porteDisplay" class="form-control bg-light">
                        <span id="porteTexto" class="fw-bold">{{ $aeronave->porte ?? '-' }}</span>
                        <small id="porteDescricao" class="text-muted ms-2">{{ $aeronave->porte_descricao ?? '' }}</small>
                    </div>
                    <input type="hidden" name="porte" id="porte" value="{{ $aeronave->porte }}">
                    <div class="form-text">
                        <span class="badge bg-info">PC: ≤100 passageiros</span>
                        <span class="badge bg-warning text-dark">MC: 101-299 passageiros</span>
                        <span class="badge bg-danger">LC: ≥300 passageiros</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="fabricante_id" class="form-label fw-semibold">Fabricante</label>
                    <select class="form-select @error('fabricante_id') is-invalid @enderror" 
                            id="fabricante_id" 
                            name="fabricante_id" 
                            required>
                        <option value="">Selecione um fabricante</option>
                        @foreach($fabricantes as $fabricante)
                            <option value="{{ $fabricante->id }}" {{ (old('fabricante_id', $aeronave->fabricante_id) == $fabricante->id) ? 'selected' : '' }}>
                                {{ $fabricante->nome }} ({{ $fabricante->pais_origem }})
                            </option>
                        @endforeach
                    </select>
                    @error('fabricante_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-pencil-square"></i> Atualizar Aeronave
                    </button>
                    <a href="{{ route('aeronaves.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function classificarPorte() {
    const capacidade = document.getElementById('capacidade').value;
    const porteTexto = document.getElementById('porteTexto');
    const porteDescricao = document.getElementById('porteDescricao');
    const porteHidden = document.getElementById('porte');
    const porteDisplay = document.getElementById('porteDisplay');
    
    let porte = '-';
    let descricao = '';
    let bgClass = 'bg-light';
    
    if (capacidade) {
        if (capacidade <= 100) {
            porte = 'PC';
            descricao = 'Pequeno Porte (≤100 passageiros)';
            bgClass = 'bg-info text-white';
        } else if (capacidade <= 299) {
            porte = 'MC';
            descricao = 'Médio Porte (101-299 passageiros)';
            bgClass = 'bg-warning text-dark';
        } else {
            porte = 'LC';
            descricao = 'Grande Porte (≥300 passageiros)';
            bgClass = 'bg-danger text-white';
        }
    }
    
    porteTexto.textContent = porte;
    porteDescricao.textContent = descricao;
    porteHidden.value = porte;
    porteDisplay.className = `form-control ${bgClass}`;
}

// Executar na carga da página
document.addEventListener('DOMContentLoaded', function() {
    classificarPorte();
});
</script>
@endsection