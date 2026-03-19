@extends('layouts.app')

@section('title', 'Cadastrar Aeronave')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Cadastrar Nova Aeronave</h2>
            <p class="text-muted">Preencha os dados da aeronave abaixo</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('aeronaves.store') }}" id="formAeronave">
                @csrf

                <div class="mb-3">
                    <label for="modelo" class="form-label fw-semibold">Modelo da Aeronave</label>
                    <input type="text" 
                           class="form-control @error('modelo') is-invalid @enderror" 
                           id="modelo" 
                           name="modelo" 
                           value="{{ old('modelo') }}"
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
                           value="{{ old('capacidade') }}"
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
                        <span id="porteTexto" class="fw-bold">-</span>
                        <small id="porteDescricao" class="text-muted ms-2"></small>
                    </div>
                    <input type="hidden" name="porte" id="porte" value="{{ old('porte') }}">
                    <div class="form-text">
                        <span class="badge bg-info">PC: ≤100 passageiros</span>
                        <span class="badge bg-warning text-dark">MC: 101-299 passageiros</span>
                        <span class="badge bg-danger">LC: ≥300 passageiros</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="fabricante_id" class="form-label fw-semibold">Fabricante</label>
                    <div class="input-group">
                        <select class="form-select @error('fabricante_id') is-invalid @enderror" 
                                id="fabricante_id" 
                                name="fabricante_id" 
                                required>
                            <option value="">Selecione um fabricante</option>
                            @foreach($fabricantes as $fabricante)
                                <option value="{{ $fabricante->id }}" {{ old('fabricante_id') == $fabricante->id ? 'selected' : '' }}>
                                    {{ $fabricante->nome }} ({{ $fabricante->pais_origem }})
                                </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalFabricante">
                            <i class="bi bi-plus-circle"></i> Novo
                        </button>
                        @error('fabricante_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Salvar Aeronave
                    </button>
                    <a href="{{ route('aeronaves.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para cadastrar novo fabricante -->
<div class="modal fade" id="modalFabricante" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cadastrar Novo Fabricante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formFabricante">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nome_fabricante" class="form-label">Nome do Fabricante</label>
                        <input type="text" 
                               class="form-control" 
                               id="nome_fabricante" 
                               name="nome" 
                               required>
                    </div>
                    <div class="mb-3">
                        <label for="pais_origem" class="form-label">País de Origem</label>
                        <input type="text" 
                               class="form-control" 
                               id="pais_origem" 
                               name="pais_origem"
                               placeholder="Ex: Brasil">
                    </div>
                    <div id="fabricanteFeedback" class="text-danger small"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSalvarFabricante">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        Salvar Fabricante
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
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
    
    if (capacidade && capacidade > 0) {
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
    } else {
        // Se não houver capacidade ou for inválido, mostra placeholder
        porte = '-';
        descricao = '';
        bgClass = 'bg-light';
    }
    
    porteTexto.textContent = porte;
    porteDescricao.textContent = descricao;
    porteHidden.value = porte;
    porteDisplay.className = `form-control ${bgClass}`;
}

// Adicionar evento para atualizar enquanto digita
document.getElementById('capacidade').addEventListener('input', classificarPorte);

// Adicionar evento para atualizar quando sair do campo (onblur)
document.getElementById('capacidade').addEventListener('blur', classificarPorte);

// Executar na carga da página se houver valor antigo
document.addEventListener('DOMContentLoaded', function() {
    // Forçar atualização mesmo se tiver valor antigo
    setTimeout(classificarPorte, 100);
});

// Restante do código para o modal de fabricante permanece igual
document.getElementById('formFabricante').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('btnSalvarFabricante');
    const spinner = btn.querySelector('.spinner-border');
    const feedback = document.getElementById('fabricanteFeedback');
    
    // Mostrar loading
    btn.disabled = true;
    spinner.classList.remove('d-none');
    feedback.innerHTML = '';
    
    const formData = new FormData(this);
    
    fetch('{{ route("fabricantes.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Adicionar nova opção ao select
            const select = document.getElementById('fabricante_id');
            const option = document.createElement('option');
            option.value = data.fabricante.id;
            option.text = data.fabricante.nome + ' (' + (data.fabricante.pais_origem || 'País não informado') + ')';
            option.selected = true;
            select.appendChild(option);
            
            // Fechar modal e resetar formulário
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalFabricante'));
            if (modal) {
                modal.hide();
            }
            document.getElementById('formFabricante').reset();
        } else {
            feedback.innerHTML = 'Erro ao cadastrar fabricante.';
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        feedback.innerHTML = 'Erro na requisição. Tente novamente.';
    })
    .finally(() => {
        btn.disabled = false;
        spinner.classList.add('d-none');
    });
});
</script>
@endpush

@endsection