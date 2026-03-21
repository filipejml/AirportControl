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
                    <div id="modeloFeedback" class="invalid-feedback"></div>
                    <div id="modeloValidFeedback" class="valid-feedback">
                        <i class="bi bi-check-circle"></i> Modelo disponível!
                    </div>
                    @error('modelo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Campos de Capacidade e Porte na mesma linha -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="capacidade" class="form-label fw-semibold">Capacidade de Passageiros</label>
                        <input type="number" 
                               class="form-control @error('capacidade') is-invalid @enderror" 
                               id="capacidade" 
                               name="capacidade" 
                               value="{{ old('capacidade') }}"
                               placeholder="Ex: 180"
                               min="1"
                               required>
                        @error('capacidade')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Porte da Aeronave</label>
                        <div id="porteDisplay" class="form-control" style="height: 38px; display: flex; align-items: center;">
                            <span id="porteTexto" class="fw-bold">-</span>
                            <small id="porteDescricao" class="ms-2"></small>
                        </div>
                        <input type="hidden" name="porte" id="porte" value="{{ old('porte') }}">
                        <div class="form-text mt-1">
                            <span class="badge bg-info">PC: ≤100 passageiros</span>
                            <span class="badge bg-warning text-dark">MC: 101-299 passageiros</span>
                            <span class="badge bg-danger">LC: ≥300 passageiros</span>
                        </div>
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
                        <a href="{{ route('fabricantes.create') }}" class="btn btn-outline-primary">
                            <i class="bi bi-plus-circle"></i> Novo Fabricante
                        </a>
                        @error('fabricante_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
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

<script>
// Função para classificar o porte
function classificarPorte() {
    var capacidade = document.getElementById('capacidade').value;
    var porteTexto = document.getElementById('porteTexto');
    var porteDescricao = document.getElementById('porteDescricao');
    var porteHidden = document.getElementById('porte');
    var porteDisplay = document.getElementById('porteDisplay');
    
    var porte = '-';
    var descricao = '';
    var corFundo = '#e9ecef';
    var corTexto = '#000';
    
    if (capacidade !== '' && parseInt(capacidade) > 0) {
        var cap = parseInt(capacidade);
        
        if (cap <= 100) {
            porte = 'PC';
            descricao = 'Pequeno Porte (≤100 passageiros)';
            corFundo = '#0dcaf0';
            corTexto = '#000';
        } else if (cap <= 299) {
            porte = 'MC';
            descricao = 'Médio Porte (101-299 passageiros)';
            corFundo = '#ffc107';
            corTexto = '#000';
        } else {
            porte = 'LC';
            descricao = 'Grande Porte (≥300 passageiros)';
            corFundo = '#dc3545';
            corTexto = '#fff';
        }
    }
    
    porteTexto.innerHTML = porte;
    porteDescricao.innerHTML = descricao;
    porteHidden.value = porte;
    porteDisplay.style.backgroundColor = corFundo;
    porteTexto.style.color = corTexto;
    porteDescricao.style.color = corTexto;
}

// Variável para controlar o timeout do debounce
var timeoutModelo = null;
var modeloValidado = false;

// Função para verificar se o modelo já existe
function verificarModelo() {
    var modelo = document.getElementById('modelo').value.trim();
    var modeloInput = document.getElementById('modelo');
    var modeloFeedback = document.getElementById('modeloFeedback');
    var modeloValidFeedback = document.getElementById('modeloValidFeedback');
    var btnSubmit = document.getElementById('btnSubmit');
    
    // Se o campo estiver vazio, não faz verificação
    if (modelo === '') {
        modeloInput.classList.remove('is-invalid', 'is-valid');
        modeloFeedback.style.display = 'none';
        modeloValidFeedback.style.display = 'none';
        modeloValidado = false;
        if (btnSubmit) btnSubmit.disabled = false;
        return;
    }
    
    // Mostrar estado de carregamento
    modeloInput.classList.remove('is-invalid', 'is-valid');
    modeloInput.classList.add('is-invalid');
    modeloFeedback.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Verificando...';
    modeloFeedback.style.display = 'block';
    modeloValidFeedback.style.display = 'none';
    
    // Fazer requisição AJAX
    fetch('{{ route("verificar.modelo") }}?modelo=' + encodeURIComponent(modelo))
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.existe) {
                // Modelo já existe - mostrar erro
                modeloInput.classList.add('is-invalid');
                modeloInput.classList.remove('is-valid');
                modeloFeedback.innerHTML = '<i class="bi bi-exclamation-triangle"></i> ' + data.message;
                modeloFeedback.style.display = 'block';
                modeloValidFeedback.style.display = 'none';
                modeloValidado = false;
                if (btnSubmit) btnSubmit.disabled = true;
            } else {
                // Modelo disponível - mostrar sucesso
                modeloInput.classList.remove('is-invalid');
                modeloInput.classList.add('is-valid');
                modeloFeedback.style.display = 'none';
                modeloValidFeedback.style.display = 'block';
                modeloValidado = true;
                if (btnSubmit) btnSubmit.disabled = false;
            }
        })
        .catch(function(error) {
            console.error('Erro ao verificar modelo:', error);
            modeloInput.classList.add('is-invalid');
            modeloInput.classList.remove('is-valid');
            modeloFeedback.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Erro ao verificar modelo. Tente novamente.';
            modeloFeedback.style.display = 'block';
            modeloValidFeedback.style.display = 'none';
            modeloValidado = false;
            if (btnSubmit) btnSubmit.disabled = false;
        });
}

// Aguardar o DOM carregar
document.addEventListener('DOMContentLoaded', function() {
    // Eventos para o campo modelo com debounce
    var modeloInput = document.getElementById('modelo');
    if (modeloInput) {
        modeloInput.addEventListener('input', function() {
            clearTimeout(timeoutModelo);
            timeoutModelo = setTimeout(verificarModelo, 500); // Aguarda 500ms após parar de digitar
        });
        
        modeloInput.addEventListener('blur', function() {
            clearTimeout(timeoutModelo);
            verificarModelo();
        });
    }
    
    // Eventos para o campo capacidade
    var capacidadeInput = document.getElementById('capacidade');
    if (capacidadeInput) {
        capacidadeInput.addEventListener('input', classificarPorte);
        capacidadeInput.addEventListener('keyup', classificarPorte);
        capacidadeInput.addEventListener('change', classificarPorte);
    }
    
    // Executar classificação inicial
    classificarPorte();
    
    // Se houver valor antigo no modelo, verificar
    if (modeloInput && modeloInput.value.trim() !== '') {
        setTimeout(verificarModelo, 100);
    }
});

// Prevenir envio do formulário se o modelo não for válido
document.getElementById('formAeronave').addEventListener('submit', function(e) {
    if (!modeloValidado && document.getElementById('modelo').value.trim() !== '') {
        e.preventDefault();
        alert('Por favor, corrija o modelo da aeronave antes de salvar.');
        document.getElementById('modelo').focus();
    }
});

// Código para o modal de fabricante
document.getElementById('formFabricante').addEventListener('submit', function(e) {
    e.preventDefault();
    
    var btn = document.getElementById('btnSalvarFabricante');
    var spinner = btn.querySelector('.spinner-border');
    var feedback = document.getElementById('fabricanteFeedback');
    
    btn.disabled = true;
    spinner.classList.remove('d-none');
    feedback.innerHTML = '';
    
    var formData = new FormData(this);
    
    fetch('{{ route("fabricantes.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            var select = document.getElementById('fabricante_id');
            var option = document.createElement('option');
            option.value = data.fabricante.id;
            option.text = data.fabricante.nome + ' (' + (data.fabricante.pais_origem || 'País não informado') + ')';
            option.selected = true;
            select.appendChild(option);
            
            var modal = bootstrap.Modal.getInstance(document.getElementById('modalFabricante'));
            if (modal) {
                modal.hide();
            }
            document.getElementById('formFabricante').reset();
        } else {
            feedback.innerHTML = data.message || 'Erro ao cadastrar fabricante.';
        }
    })
    .catch(function(error) {
        console.error('Erro:', error);
        feedback.innerHTML = 'Erro na requisição. Tente novamente.';
    })
    .finally(function() {
        btn.disabled = false;
        spinner.classList.add('d-none');
    });
});
</script>

<style>
/* Estilos para feedback */
#modeloValidFeedback {
    display: none;
}

#modeloValidFeedback i {
    margin-right: 5px;
}

/* Estilo para campos válidos */
.form-control.is-valid {
    border-color: #198754;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}
</style>

@endsection