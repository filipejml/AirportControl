{{-- resources/views/admin/aeroportos/depositos/veiculos/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Cadastrar Veículos - ' . $deposito->nome)

@section('content')
<style>
/* Toast notification */
.toast-notification {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 300px;
    animation: slideInRight 0.3s ease;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Lista de veículos */
.veiculo-list-item {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 12px 16px;
    margin-bottom: 8px;
    transition: all 0.2s ease;
    border: 1px solid #e5e7eb;
}

.veiculo-list-item:hover {
    background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border-color: #cbd5e1;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #6c757d;
}
</style>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">🚗 Cadastrar Veículos</h2>
            <p class="text-muted">Depósito: <strong>{{ $deposito->nome }}</strong> - {{ $aeroporto->nome_aeroporto }}</p>
            <p class="text-muted small">Adicione os veículos um por um. Eles ficarão na lista até você finalizar.</p>
        </div>
        <div class="col text-end">
            <a href="{{ route('aeroportos.depositos.index', $aeroporto) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Coluna do formulário -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Adicionar Novo Veículo</h5>
                </div>
                <div class="card-body">
                    <form id="veiculoForm">
                        @csrf

                        <!-- Tipo de Veículo -->
                        <div class="mb-3">
                            <label for="tipo_veiculo" class="form-label fw-semibold">Tipo de Veículo *</label>
                            <select class="form-select" id="tipo_veiculo" name="tipo_veiculo" required>
                                <option value="">Selecione o tipo de veículo...</option>
                                @foreach($tiposVeiculos as $key => $tipo)
                                    <option value="{{ $key }}">
                                        {{ $tipo['nome'] }} - {{ $tipo['descricao'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Código do Veículo -->
                        <div class="mb-3">
                            <label for="codigo" class="form-label fw-semibold">Código do Veículo *</label>
                            <div class="position-relative">
                                <input type="text" 
                                       class="form-control" 
                                       id="codigo" 
                                       name="codigo" 
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
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label fw-semibold">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="disponivel" selected>✅ Disponível</option>
                                <option value="indisponivel">❌ Indisponível</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" id="addBtn">
                            <i class="bi bi-plus-circle"></i> Adicionar à Lista
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Coluna da lista de veículos -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-list-check"></i> Veículos a Cadastrar
                        <span class="badge bg-primary ms-2" id="totalCount">0</span>
                    </h5>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="limparListaBtn">
                        <i class="bi bi-trash"></i> Limpar Tudo
                    </button>
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    <div id="veiculosList">
                        <div class="empty-state">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mt-2 mb-0">Nenhum veículo adicionado ainda</p>
                            <small>Adicione veículos usando o formulário ao lado</small>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <button type="button" class="btn btn-success w-100" id="finalizarBtn">
                        <i class="bi bi-check-circle"></i> Finalizar Cadastro
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast para notificações -->
<div id="toastMessage" class="toast-notification" style="display: none;">
    <div class="alert alert-success shadow-lg mb-0">
        <i class="bi bi-check-circle-fill me-2"></i>
        <span id="toastText"></span>
    </div>
</div>

<script>
let checkCodigoTimeout = null;
let isCodigoValid = false;
let isChecking = false;

function showToast(message, isSuccess = true) {
    const toast = document.getElementById('toastMessage');
    const toastText = document.getElementById('toastText');
    const alertDiv = toast.querySelector('.alert');
    
    toastText.textContent = message;
    
    if (isSuccess) {
        alertDiv.className = 'alert alert-success shadow-lg mb-0';
    } else {
        alertDiv.className = 'alert alert-danger shadow-lg mb-0';
    }
    
    toast.style.display = 'block';
    
    setTimeout(() => {
        toast.style.display = 'none';
    }, 3000);
}

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
            
            updateAddButton();
        })
        .catch(error => {
            console.error('Erro ao verificar código:', error);
            spinner.classList.add('d-none');
            isChecking = false;
            feedbackDiv.innerHTML = '<span class="text-warning">⚠️ Não foi possível verificar disponibilidade</span>';
            isCodigoValid = true;
            updateAddButton();
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
    updateAddButton();
}

function updateAddButton() {
    const addBtn = document.getElementById('addBtn');
    const codigoValue = document.getElementById('codigo').value.trim();
    const tipoValue = document.getElementById('tipo_veiculo').value;
    
    addBtn.disabled = !(codigoValue !== '' && tipoValue !== '' && !isChecking && isCodigoValid);
}

function resetForm() {
    const codigoInput = document.getElementById('codigo');
    codigoInput.value = '';
    resetCodigoValidation();
    document.getElementById('tipo_veiculo').value = '';
    document.getElementById('status').value = 'disponivel';
    codigoInput.focus();
}

function adicionarVeiculo() {
    const form = document.getElementById('veiculoForm');
    const formData = new FormData(form);
    formData.append('ajax', '1');
    
    fetch('{{ route("aeroportos.depositos.veiculos.store", [$aeroporto, $deposito]) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('✓ Veículo adicionado à lista!');
            atualizarLista(data.carrinho);
            resetForm();
        } else {
            showToast('❌ ' + (data.message || 'Erro ao adicionar veículo'), false);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast('❌ Erro ao adicionar veículo. Verifique os dados.', false);
    });
}

function removerVeiculo(codigo) {
    fetch('{{ route("aeroportos.depositos.veiculos.remover-carrinho", [$aeroporto, $deposito]) }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ codigo: codigo })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('✓ Veículo removido da lista!');
            atualizarLista(data.carrinho);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast('❌ Erro ao remover veículo', false);
    });
}

function limparLista() {
    if (!confirm('Tem certeza que deseja limpar toda a lista?')) return;
    
    fetch('{{ route("aeroportos.depositos.veiculos.limpar-carrinho", [$aeroporto, $deposito]) }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('✓ Lista limpa com sucesso!');
            atualizarLista([]);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showToast('❌ Erro ao limpar lista', false);
    });
}

function finalizarCadastro() {
    if (!confirm('Deseja finalizar o cadastro e salvar todos os veículos?')) return;
    
    // Redirecionar diretamente para a rota de finalizar (método POST)
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("aeroportos.depositos.veiculos.finalizar", [$aeroporto, $deposito]) }}';
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    document.body.appendChild(form);
    form.submit();
}

function atualizarLista(veiculos) {
    const container = document.getElementById('veiculosList');
    const totalSpan = document.getElementById('totalCount');
    const total = veiculos.length;
    
    totalSpan.textContent = total;
    
    if (total === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="bi bi-inbox fs-1"></i>
                <p class="mt-2 mb-0">Nenhum veículo adicionado ainda</p>
                <small>Adicione veículos usando o formulário ao lado</small>
            </div>
        `;
        return;
    }
    
    let html = '';
    const statusMap = {
        'disponivel': { class: 'success', text: 'Disponível' },
        'indisponivel': { class: 'danger', text: 'Indisponível' }
    };
    
    veiculos.forEach((veiculo, index) => {
        const statusInfo = statusMap[veiculo.status] || statusMap['disponivel'];
        html += `
            <div class="veiculo-list-item">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-center" style="min-width: 50px;">
                            <span class="badge bg-secondary rounded-circle p-2">${index + 1}</span>
                        </div>
                        <div>
                            <strong>${veiculo.codigo}</strong>
                            <div>
                                <span class="badge bg-info">${veiculo.tipo_nome}</span>
                                <span class="badge bg-${statusInfo.class}">${statusInfo.text}</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removerVeiculo('${veiculo.codigo}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

document.addEventListener('DOMContentLoaded', function() {
    const codigoInput = document.getElementById('codigo');
    const tipoSelect = document.getElementById('tipo_veiculo');
    const addBtn = document.getElementById('addBtn');
    const limparListaBtn = document.getElementById('limparListaBtn');
    const finalizarBtn = document.getElementById('finalizarBtn');
    
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
    
    if (tipoSelect) {
        tipoSelect.addEventListener('change', updateAddButton);
    }
    
    if (addBtn) {
        addBtn.addEventListener('click', function(e) {
            e.preventDefault();
            adicionarVeiculo();
        });
    }
    
    if (limparListaBtn) {
        limparListaBtn.addEventListener('click', limparLista);
    }
    
    if (finalizarBtn) {
        finalizarBtn.addEventListener('click', finalizarCadastro);
    }
    
    updateAddButton();
});
</script>
@endsection