{{-- resources/views/voos/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Cadastrar Voo - Airport Manager')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center">
                <div>
                    <h2 class="fw-bold mb-1">Cadastrar Novo Voo</h2>
                    <p class="text-muted mb-0">Preencha os dados abaixo para registrar um novo voo no sistema</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="{{ route('voos.store') }}" method="POST" id="formVoo">
                        @csrf

                        @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Ops!</strong> Verifique os erros abaixo:
                            <ul class="mt-2 mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        <!-- Informações Básicas -->
                        <div class="mb-4">
                            <h5 class="fw-bold pb-2 border-bottom">
                                <i class="bi bi-info-circle me-2 text-primary"></i>
                                Informações Básicas
                            </h5>
                            
                            <div class="row mt-3">
                                <!-- ID do Voo -->
                                <div class="col-md-4 mb-3">
                                    <label for="id_voo" class="form-label fw-semibold">
                                        ID do Voo <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-tag"></i>
                                        </span>
                                        <input type="text" 
                                            class="form-control @error('id_voo') is-invalid @enderror" 
                                            id="id_voo" 
                                            name="id_voo" 
                                            value="{{ old('id_voo') }}" 
                                            required
                                            placeholder="Ex: AA-1234 ou ASY-5678"
                                            maxlength="9">
                                        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#codigosValidos" title="Ver códigos válidos">
                                            <i class="bi bi-info-circle"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Formato: LL-NNNN ou LLLL-NNNN (ex: AA-1234 ou ASY-5678) - Use letras maiúsculas
                                    </div>
                                    
                                    <!-- Lista de códigos válidos (colapsável) -->
                                    <div class="collapse mt-2" id="codigosValidos">
                                        <div class="card card-body bg-light">
                                            <small class="text-muted mb-2 fw-semibold">📋 Códigos de companhia válidos:</small>
                                            <div class="row">
                                                @php
                                                    $codigos = \App\Helpers\CompanhiaHelper::getCodigosValidos();
                                                    $chunks = array_chunk($codigos, ceil(count($codigos) / 4));
                                                @endphp
                                                @foreach($chunks as $chunk)
                                                    <div class="col-md-3">
                                                        @foreach($chunk as $codigo)
                                                            <small class="d-block mb-1">
                                                                <strong class="text-primary">{{ $codigo }}</strong> 
                                                                <span class="text-muted">-</span> 
                                                                {{ \App\Helpers\CompanhiaHelper::getNomeCompanhia($codigo) }}
                                                            </small>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @error('id_voo')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Aeroporto -->
                                <div class="col-md-4 mb-3">
                                    <label for="aeroporto_id" class="form-label fw-semibold">
                                        Aeroporto <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-geo-alt"></i>
                                        </span>
                                        <select class="form-select @error('aeroporto_id') is-invalid @enderror" 
                                                id="aeroporto_id" 
                                                name="aeroporto_id" 
                                                required>
                                            <option value="" disabled selected>Selecione um aeroporto</option>
                                            @foreach($aeroportos as $aeroporto)
                                                <option value="{{ $aeroporto->id }}" {{ old('aeroporto_id') == $aeroporto->id ? 'selected' : '' }}>
                                                    {{ $aeroporto->nome_aeroporto }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('aeroporto_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Tipo de Voo -->
                                <div class="col-md-4 mb-3">
                                    <label for="tipo_voo" class="form-label fw-semibold">
                                        Tipo de Voo <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-airplane"></i>
                                        </span>
                                        <select class="form-select @error('tipo_voo') is-invalid @enderror" 
                                                id="tipo_voo" 
                                                name="tipo_voo" 
                                                required>
                                            <option value="Regular" {{ old('tipo_voo') == 'Regular' ? 'selected' : '' }}>Regular</option>
                                            <option value="Charter" {{ old('tipo_voo') == 'Charter' ? 'selected' : '' }}>Charter</option>
                                        </select>
                                    </div>
                                    @error('tipo_voo')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Companhia e Aeronave -->
                        <div class="mb-4">
                            <h5 class="fw-bold pb-2 border-bottom">
                                <i class="bi bi-building me-2 text-primary"></i>
                                Companhia e Aeronave
                            </h5>
                            
                            <div class="row mt-3">
                                <!-- Companhia Aérea -->
                                <div class="col-md-4 mb-3">
                                    <label for="companhia_aerea_id" class="form-label fw-semibold">
                                        Companhia Aérea <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-building"></i>
                                        </span>
                                        <select class="form-select @error('companhia_aerea_id') is-invalid @enderror" 
                                                id="companhia_aerea_id" 
                                                name="companhia_aerea_id" 
                                                required>
                                            <option value="" disabled selected>Selecione uma companhia</option>
                                            @foreach($companhias as $companhia)
                                                <option value="{{ $companhia->id }}" {{ old('companhia_aerea_id') == $companhia->id ? 'selected' : '' }}>
                                                    {{ $companhia->nome }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('companhia_aerea_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Modelo da Aeronave (apenas o modelo) -->
                                <div class="col-md-4 mb-3">
                                    <label for="aeronave_id" class="form-label fw-semibold">
                                        Modelo da Aeronave <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-airplane"></i>
                                        </span>
                                        <select class="form-select @error('aeronave_id') is-invalid @enderror" 
                                                id="aeronave_id" 
                                                name="aeronave_id" 
                                                required>
                                            <option value="" disabled selected>Selecione uma aeronave</option>
                                        </select>
                                    </div>
                                    @error('aeronave_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Tipo de Aeronave (Porte - preenchido automaticamente) -->
                                <div class="col-md-4 mb-3">
                                    <label for="tipo_aeronave" class="form-label fw-semibold">
                                        Tipo de Aeronave <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-diagram-3"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control bg-light" 
                                               id="tipo_aeronave" 
                                               readonly
                                               placeholder="Selecione um modelo">
                                    </div>
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Preenchido automaticamente conforme o modelo selecionado
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detalhes do Voo -->
                        <div class="mb-4">
                            <h5 class="fw-bold pb-2 border-bottom">
                                <i class="bi bi-calendar-check me-2 text-primary"></i>
                                Detalhes do Voo
                            </h5>
                            
                            <!-- Primeira linha: Quantidade de Voos e Horário -->
                            <div class="row mt-3">
                                <div class="col-md-6 mb-3">
                                    <label for="qtd_voos" class="form-label fw-semibold">
                                        Quantidade de Voos <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-sort-numeric-up"></i>
                                        </span>
                                        <input type="number" 
                                               class="form-control @error('qtd_voos') is-invalid @enderror" 
                                               id="qtd_voos" 
                                               name="qtd_voos" 
                                               value="{{ old('qtd_voos', 1) }}" 
                                               required 
                                               min="1"
                                               placeholder="Ex: 5">
                                    </div>
                                    @error('qtd_voos')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="horario_voo" class="form-label fw-semibold">
                                        Horário do Voo <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-clock"></i>
                                        </span>
                                        <select class="form-select @error('horario_voo') is-invalid @enderror" 
                                                id="horario_voo" 
                                                name="horario_voo" 
                                                required>
                                            <option value="EAM" {{ old('horario_voo') == 'EAM' ? 'selected' : '' }}>EAM</option>
                                            <option value="AM" {{ old('horario_voo') == 'AM' ? 'selected' : '' }}>AM</option>
                                            <option value="AN" {{ old('horario_voo') == 'AN' ? 'selected' : '' }}>AN</option>
                                            <option value="PM" {{ old('horario_voo') == 'PM' ? 'selected' : '' }}>PM</option>
                                            <option value="ALL" {{ old('horario_voo') == 'ALL' ? 'selected' : '' }}>ALL</option>
                                        </select>
                                    </div>
                                    @error('horario_voo')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Segunda linha: Capacidade e Total de Passageiros -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="capacidade" class="form-label fw-semibold">
                                        Capacidade de Passageiros <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-people"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control bg-light" 
                                               id="capacidade" 
                                               readonly
                                               placeholder="Selecione um modelo">
                                        <span class="input-group-text bg-light">pax/voo</span>
                                    </div>
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Preenchido automaticamente conforme o modelo selecionado
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Total de Passageiros</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="bi bi-calculator"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control bg-light fw-bold text-primary" 
                                               id="total_passageiros" 
                                               readonly
                                               placeholder="0">
                                        <span class="input-group-text bg-light">passageiros</span>
                                    </div>
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Calculado automaticamente: Capacidade × Quantidade de Voos
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notas -->
                        <div class="mb-4">
                            <h5 class="fw-bold pb-2 border-bottom">
                                <i class="bi bi-star me-2 text-primary"></i>
                                Avaliações (Opcional)
                            </h5>
                            
                            <div class="row mt-3">
                                <div class="col-md-3 mb-3">
                                    <label for="nota_obj" class="form-label fw-semibold">Nota Objetivo</label>
                                    <select class="form-select" id="nota_obj" name="nota_obj">
                                        <option value="">Não avaliado</option>
                                        <option value="A" {{ old('nota_obj') == 'A' ? 'selected' : '' }}>A (Excelente - 10)</option>
                                        <option value="B" {{ old('nota_obj') == 'B' ? 'selected' : '' }}>B (Muito Bom - 9)</option>
                                        <option value="C" {{ old('nota_obj') == 'C' ? 'selected' : '' }}>C (Bom - 8)</option>
                                        <option value="D" {{ old('nota_obj') == 'D' ? 'selected' : '' }}>D (Regular - 6)</option>
                                        <option value="E" {{ old('nota_obj') == 'E' ? 'selected' : '' }}>E (Ruim - 4)</option>
                                        <option value="F" {{ old('nota_obj') == 'F' ? 'selected' : '' }}>F (Péssimo - 2)</option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="nota_pontualidade" class="form-label fw-semibold">Nota Pontualidade</label>
                                    <select class="form-select" id="nota_pontualidade" name="nota_pontualidade">
                                        <option value="">Não avaliado</option>
                                        <option value="A" {{ old('nota_pontualidade') == 'A' ? 'selected' : '' }}>A (Excelente - 10)</option>
                                        <option value="B" {{ old('nota_pontualidade') == 'B' ? 'selected' : '' }}>B (Muito Bom - 9)</option>
                                        <option value="C" {{ old('nota_pontualidade') == 'C' ? 'selected' : '' }}>C (Bom - 8)</option>
                                        <option value="D" {{ old('nota_pontualidade') == 'D' ? 'selected' : '' }}>D (Regular - 6)</option>
                                        <option value="E" {{ old('nota_pontualidade') == 'E' ? 'selected' : '' }}>E (Ruim - 4)</option>
                                        <option value="F" {{ old('nota_pontualidade') == 'F' ? 'selected' : '' }}>F (Péssimo - 2)</option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="nota_servicos" class="form-label fw-semibold">Nota Serviços</label>
                                    <select class="form-select" id="nota_servicos" name="nota_servicos">
                                        <option value="">Não avaliado</option>
                                        <option value="A" {{ old('nota_servicos') == 'A' ? 'selected' : '' }}>A (Excelente - 10)</option>
                                        <option value="B" {{ old('nota_servicos') == 'B' ? 'selected' : '' }}>B (Muito Bom - 9)</option>
                                        <option value="C" {{ old('nota_servicos') == 'C' ? 'selected' : '' }}>C (Bom - 8)</option>
                                        <option value="D" {{ old('nota_servicos') == 'D' ? 'selected' : '' }}>D (Regular - 6)</option>
                                        <option value="E" {{ old('nota_servicos') == 'E' ? 'selected' : '' }}>E (Ruim - 4)</option>
                                        <option value="F" {{ old('nota_servicos') == 'F' ? 'selected' : '' }}>F (Péssimo - 2)</option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="nota_patio" class="form-label fw-semibold">Nota Pátio</label>
                                    <select class="form-select" id="nota_patio" name="nota_patio">
                                        <option value="">Não avaliado</option>
                                        <option value="A" {{ old('nota_patio') == 'A' ? 'selected' : '' }}>A (Excelente - 10)</option>
                                        <option value="B" {{ old('nota_patio') == 'B' ? 'selected' : '' }}>B (Muito Bom - 9)</option>
                                        <option value="C" {{ old('nota_patio') == 'C' ? 'selected' : '' }}>C (Bom - 8)</option>
                                        <option value="D" {{ old('nota_patio') == 'D' ? 'selected' : '' }}>D (Regular - 6)</option>
                                        <option value="E" {{ old('nota_patio') == 'E' ? 'selected' : '' }}>E (Ruim - 4)</option>
                                        <option value="F" {{ old('nota_patio') == 'F' ? 'selected' : '' }}>F (Péssimo - 2)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('voos.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>
                                Cancelar
                            </a>
                            <div>
                                <button type="reset" class="btn btn-outline-warning me-2" id="btnLimpar">
                                    <i class="bi bi-eraser me-2"></i>
                                    Limpar
                                </button>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-check-circle me-2"></i>
                                    Cadastrar Voo
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 92, 139, 0.25);
}

.btn-primary {
    background-color: #0d5c8b;
    border-color: #0d5c8b;
}

.btn-primary:hover {
    background-color: #0a4a70;
    border-color: #0a4a70;
    transform: translateY(-1px);
}

input:read-only {
    cursor: not-allowed;
    background-color: #e9ecef;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const companhiaSelect = document.getElementById('companhia_aerea_id');
    const aeronaveSelect = document.getElementById('aeronave_id');
    const tipoAeronaveInput = document.getElementById('tipo_aeronave');
    const capacidadeInput = document.getElementById('capacidade');
    const qtdVoosInput = document.getElementById('qtd_voos');
    const totalPassageirosInput = document.getElementById('total_passageiros');
    const idVooInput = document.getElementById('id_voo');

    // Mapeamento de porte para descrição
    const porteTexto = {
        'PC': 'Pequeno Porte',
        'MC': 'Médio Porte',
        'LC': 'Grande Porte'
    };

    // Mapeamento de horários
    const horarioTexto = {
        'EAM': 'Early Morning (00h-06h)',
        'AM': 'Morning (06h-12h)',
        'AN': 'Afternoon (12h-18h)',
        'PM': 'Evening (18h-00h)',
        'ALL': 'Diário'
    };

    // Função para carregar aeronaves por companhia
    function carregarAeronaves(companhiaId) {
        if (!companhiaId) {
            aeronaveSelect.innerHTML = '<option value="" disabled selected>Selecione uma aeronave</option>';
            limparCamposAeronave();
            return;
        }

        fetch(`/api/companhias/${companhiaId}/aeronaves`)
            .then(response => response.json())
            .then(aeronaves => {
                if (aeronaves.length === 0) {
                    aeronaveSelect.innerHTML = '<option value="" disabled selected>Nenhuma aeronave disponível</option>';
                    limparCamposAeronave();
                } else {
                    let options = '<option value="" disabled selected>Selecione uma aeronave</option>';
                    aeronaves.forEach(aeronave => {
                        options += `<option value="${aeronave.id}" 
                                         data-capacidade="${aeronave.capacidade}"
                                         data-porte="${aeronave.porte}">
                                    ${aeronave.modelo}
                                </option>`;
                    });
                    aeronaveSelect.innerHTML = options;
                }
            })
            .catch(error => {
                console.error('Erro ao carregar aeronaves:', error);
                aeronaveSelect.innerHTML = '<option value="" disabled selected>Erro ao carregar aeronaves</option>';
            });
    }

    // Função para limpar os campos de aeronave
    function limparCamposAeronave() {
        tipoAeronaveInput.value = '';
        capacidadeInput.value = '';
        totalPassageirosInput.value = '0';
    }

    // Função para atualizar os campos conforme a aeronave selecionada
    function atualizarInfoAeronave() {
        const selectedOption = aeronaveSelect.options[aeronaveSelect.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            const capacidade = selectedOption.dataset.capacidade;
            const porte = selectedOption.dataset.porte;
            
            tipoAeronaveInput.value = porteTexto[porte] || porte;
            capacidadeInput.value = capacidade;
            
            calcularTotalPassageiros();
        } else {
            limparCamposAeronave();
        }
    }

    // Função para calcular total de passageiros
    function calcularTotalPassageiros() {
        const capacidade = parseInt(capacidadeInput.value || 0);
        const qtdVoos = parseInt(qtdVoosInput.value || 0);
        const total = capacidade * qtdVoos;
        totalPassageirosInput.value = total.toLocaleString('pt-BR');
    }

    // ============================================
    // VALIDAÇÃO DO ID DO VOO (COM CONVERSÃO PARA MAIÚSCULAS)
    // ============================================
    
    // Criar elemento de feedback para o ID do voo
    const idVooGroup = idVooInput.closest('.mb-3');
    let idVooFeedback = document.getElementById('idVooFeedback');
    
    if (!idVooFeedback) {
        idVooFeedback = document.createElement('div');
        idVooFeedback.id = 'idVooFeedback';
        idVooFeedback.className = 'form-text mt-1';
        idVooGroup.appendChild(idVooFeedback);
    }
    
    let timeoutId = null;

    // Função para validar o ID do voo
    function validarIdVoo(valor) {
        // Limpar timeout anterior
        if (timeoutId) {
            clearTimeout(timeoutId);
        }
        
        // Se o campo estiver vazio, limpar feedback
        if (!valor || valor.trim() === '') {
            idVooFeedback.innerHTML = '';
            idVooInput.classList.remove('is-valid', 'is-invalid');
            return false;
        }
        
        // Verificar formato básico (2-4 letras + hífen + 4 números)
        const formatoValido = /^[A-Z]{2,4}-\d{4}$/.test(valor);
        
        if (!formatoValido) {
            idVooFeedback.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1 text-warning"></i> Formato inválido. Use LL-NNNN ou LLLL-NNNN (ex: AA-1234 ou ASY-5678)';
            idVooFeedback.classList.add('text-warning');
            idVooFeedback.classList.remove('text-success', 'text-danger', 'text-info');
            idVooInput.classList.remove('is-valid');
            idVooInput.classList.add('is-invalid');
            return false;
        }
        
        // Formato válido, fazer requisição para verificar se o código existe
        timeoutId = setTimeout(() => {
            fetch('{{ route("verificar.id.voo") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ id_voo: valor })
            })
            .then(response => response.json())
            .then(data => {
                if (data.valid) {
                    const codigo = valor.split('-')[0];
                    idVooFeedback.innerHTML = '<i class="bi bi-check-circle-fill me-1 text-success"></i> ' + 
                        (data.companhia ? `✓ Código válido: ${data.companhia}` : '✓ Código válido!');
                    idVooFeedback.classList.remove('text-danger', 'text-warning', 'text-info');
                    idVooFeedback.classList.add('text-success');
                    idVooInput.classList.remove('is-invalid');
                    idVooInput.classList.add('is-valid');
                } else {
                    idVooFeedback.innerHTML = '<i class="bi bi-x-circle-fill me-1 text-danger"></i> ' + data.message;
                    idVooFeedback.classList.remove('text-success', 'text-warning', 'text-info');
                    idVooFeedback.classList.add('text-danger');
                    idVooInput.classList.remove('is-valid');
                    idVooInput.classList.add('is-invalid');
                }
            })
            .catch(error => {
                console.error('Erro ao validar ID:', error);
                idVooFeedback.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1 text-warning"></i> Erro ao validar. Tente novamente.';
                idVooFeedback.classList.add('text-warning');
            });
        }, 500);
        
        return true;
    }

    // Validação do ID do voo (converte para maiúsculas)
    idVooInput.addEventListener('input', function() {
        // Converte para maiúsculas automaticamente
        let valor = this.value.toUpperCase();
        
        // Atualiza o campo com o valor em maiúsculas
        this.value = valor;
        
        // Validar o ID
        validarIdVoo(valor);
    });

    // Validação ao perder o foco
    idVooInput.addEventListener('blur', function() {
        const valor = this.value;
        if (valor && !/^[A-Z]{2,4}-\d{4}$/.test(valor)) {
            idVooFeedback.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1 text-danger"></i> Formato inválido! Use LL-NNNN ou LLLL-NNNN (ex: AA-1234 ou ASY-5678)';
            idVooFeedback.classList.remove('text-success', 'text-warning', 'text-info');
            idVooFeedback.classList.add('text-danger');
            this.classList.add('is-invalid');
        }
    });

    // Event listeners para os outros campos
    companhiaSelect.addEventListener('change', function() {
        carregarAeronaves(this.value);
    });
    
    aeronaveSelect.addEventListener('change', atualizarInfoAeronave);
    qtdVoosInput.addEventListener('input', calcularTotalPassageiros);

    // Botão limpar
    const btnLimpar = document.getElementById('btnLimpar');
    if (btnLimpar) {
        btnLimpar.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('formVoo').reset();
            limparCamposAeronave();
            aeronaveSelect.innerHTML = '<option value="" disabled selected>Selecione uma aeronave</option>';
            totalPassageirosInput.value = '0';
            idVooFeedback.innerHTML = '';
            idVooInput.classList.remove('is-valid', 'is-invalid');
        });
    }

    // Se já houver uma companhia selecionada (em caso de erro), carregar aeronaves
    if (companhiaSelect.value) {
        carregarAeronaves(companhiaSelect.value);
    }

    // Adicionar tooltips para os horários
    const horarioSelect = document.getElementById('horario_voo');
    if (horarioSelect) {
        horarioSelect.addEventListener('change', function() {
            const horario = this.value;
            const tooltipText = horarioTexto[horario] || '';
            if (tooltipText) {
                this.setAttribute('title', tooltipText);
            }
        });
        // Trigger initial
        if (horarioSelect.value) {
            horarioSelect.setAttribute('title', horarioTexto[horarioSelect.value] || '');
        }
    }

    // Adicionar validação no submit
    const form = document.getElementById('formVoo');
    form.addEventListener('submit', function(e) {
        const idVoo = idVooInput.value;
        
        if (!idVoo || idVoo.trim() === '') {
            e.preventDefault();
            idVooFeedback.innerHTML = '<i class="bi bi-x-circle-fill me-1 text-danger"></i> O ID do voo é obrigatório!';
            idVooFeedback.classList.add('text-danger');
            idVooInput.classList.add('is-invalid');
            idVooInput.focus();
            return false;
        }
        
        const isValidFormat = /^[A-Z]{2,4}-\d{4}$/.test(idVoo);
        
        if (!isValidFormat) {
            e.preventDefault();
            idVooFeedback.innerHTML = '<i class="bi bi-x-circle-fill me-1 text-danger"></i> Formato inválido! O ID deve estar no formato LL-NNNN ou LLLL-NNNN (ex: AA-1234 ou ASY-5678)';
            idVooFeedback.classList.remove('text-success', 'text-warning', 'text-info');
            idVooFeedback.classList.add('text-danger');
            idVooInput.classList.add('is-invalid');
            idVooInput.focus();
            return false;
        }
        
        // Verificar se o ID foi validado pelo servidor
        if (!idVooInput.classList.contains('is-valid')) {
            e.preventDefault();
            idVooFeedback.innerHTML = '<i class="bi bi-x-circle-fill me-1 text-danger"></i> Por favor, aguarde a validação do código ou verifique se o código é válido.';
            idVooFeedback.classList.add('text-danger');
            return false;
        }
        
        return true;
    });
});
</script>
@endsection