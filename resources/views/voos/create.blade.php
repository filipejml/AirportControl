{{-- resources/views/voos/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Cadastrar Voo - Airport Manager')

@section('content')
<div class="container-fluid px-4">
    <!-- Cabeçalho com breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('voos.index') }}" class="text-decoration-none">Voos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Novo Voo</li>
                </ol>
            </nav>
            
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                    <i class="bi bi-plus-circle-fill text-primary fs-1"></i>
                </div>
                <div>
                    <h2 class="fw-bold mb-1">Cadastrar Novo Voo</h2>
                    <p class="text-muted mb-0">Preencha os dados abaixo para registrar um novo voo no sistema</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form action="{{ route('voos.store') }}" method="POST" id="formVoo">
                        @csrf

                        <!-- Alertas de validação -->
                        @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
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

                        <!-- Seção: Informações Básicas -->
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
                                               pattern="[A-Za-z]{2}-\d{4}"
                                               placeholder="AA-1234"
                                               maxlength="7">
                                    </div>
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Formato: LL-NNNN (ex: AA-1234)
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

                        <!-- Seção: Companhia e Aeronave -->
                        <div class="mb-4">
                            <h5 class="fw-bold pb-2 border-bottom">
                                <i class="bi bi-building me-2 text-primary"></i>
                                Companhia e Aeronave
                            </h5>
                            
                            <div class="row mt-3">
                                <!-- Companhia Aérea -->
                                <div class="col-md-6 mb-3">
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

                                <!-- Modelo da Aeronave -->
                                <div class="col-md-6 mb-3">
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
                                            @foreach($aeronaves as $aeronave)
                                                <option value="{{ $aeronave->id }}" 
                                                        data-companhia="{{ $aeronave->companhias->pluck('id')->implode(',') }}"
                                                        data-porte="{{ $aeronave->porte }}"
                                                        data-capacidade="{{ $aeronave->capacidade }}"
                                                        data-fabricante="{{ $aeronave->fabricante->nome ?? 'N/A' }}"
                                                        {{ old('aeronave_id') == $aeronave->id ? 'selected' : '' }}>
                                                    {{ $aeronave->modelo }} - {{ $aeronave->fabricante->nome ?? 'N/A' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <!-- Card de informações da aeronave selecionada -->
                                    <div id="infoAeronaveCard" class="mt-2 p-3 bg-light rounded-3 d-none">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <i class="bi bi-airplane-fill text-primary fs-4"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-1 fw-bold" id="aeronaveModelo">-</h6>
                                                <div class="row small">
                                                    <div class="col-auto">
                                                        <span class="text-muted">Fabricante:</span>
                                                        <span class="fw-semibold ms-1" id="aeronaveFabricante">-</span>
                                                    </div>
                                                    <div class="col-auto">
                                                        <span class="text-muted">Capacidade:</span>
                                                        <span class="fw-semibold ms-1" id="aeronaveCapacidade">-</span>
                                                    </div>
                                                    <div class="col-auto">
                                                        <span class="text-muted">Porte:</span>
                                                        <span class="fw-semibold ms-1" id="aeronavePorte">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @error('aeronave_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Seção: Detalhes do Voo -->
                        <div class="mb-4">
                            <h5 class="fw-bold pb-2 border-bottom">
                                <i class="bi bi-calendar-check me-2 text-primary"></i>
                                Detalhes do Voo
                            </h5>
                            
                            <div class="row mt-3">
                                <!-- Quantidade de Voos -->
                                <div class="col-md-4 mb-3">
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
                                               value="{{ old('qtd_voos') }}" 
                                               required 
                                               min="1"
                                               placeholder="Ex: 5">
                                    </div>
                                    @error('qtd_voos')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Horário do Voo -->
                                <div class="col-md-4 mb-3">
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
                                            <option value="EAM" {{ old('horario_voo') == 'EAM' ? 'selected' : '' }}>EAM (Early Morning - 00h-06h)</option>
                                            <option value="AM" {{ old('horario_voo') == 'AM' ? 'selected' : '' }}>AM (Morning - 06h-12h)</option>
                                            <option value="AN" {{ old('horario_voo') == 'AN' ? 'selected' : '' }}>AN (Afternoon - 12h-18h)</option>
                                            <option value="PM" {{ old('horario_voo') == 'PM' ? 'selected' : '' }}>PM (Evening - 18h-00h)</option>
                                            <option value="ALL" {{ old('horario_voo') == 'ALL' ? 'selected' : '' }}>ALL (Diário)</option>
                                        </select>
                                    </div>
                                    @error('horario_voo')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Resumo (calculado) -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Resumo do Voo</label>
                                    <div class="bg-light p-3 rounded-3 h-100 d-flex align-items-center">
                                        <div class="w-100">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Passageiros/voo:</span>
                                                <span class="fw-bold" id="resumoCapacidade">0</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Total passageiros:</span>
                                                <span class="fw-bold text-primary" id="resumoTotal">0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Seção: Notas (Avaliações) -->
                        <div class="mb-4">
                            <h5 class="fw-bold pb-2 border-bottom">
                                <i class="bi bi-star me-2 text-primary"></i>
                                Avaliações (Opcional)
                            </h5>
                            
                            <div class="row mt-3">
                                <div class="col-12 mb-3">
                                    <div class="alert alert-info py-2">
                                        <i class="bi bi-info-circle me-2"></i>
                                        As notas são convertidas automaticamente: A=10, B=9, C=8, D=6, E=4, F=2
                                    </div>
                                </div>
                                
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

                        <!-- Barra de progresso do formulário -->
                        <div class="progress mb-4" style="height: 5px;" id="formProgress">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 0%;"></div>
                        </div>

                        <!-- Botões de ação -->
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
                                <button type="submit" class="btn btn-primary px-4" id="btnSubmit">
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
/* Animações e efeitos */
.card {
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 92, 139, 0.25);
}

.btn {
    transition: all 0.2s ease;
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

.btn-outline-warning:hover {
    transform: translateY(-1px);
}

/* Breadcrumb personalizado */
.breadcrumb-item a {
    color: #0d5c8b;
}

.breadcrumb-item.active {
    color: #6c757d;
}

/* Card de informações da aeronave */
#infoAeronaveCard {
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Badge de campo obrigatório */
.text-danger {
    font-size: 0.9em;
}

/* Responsividade */
@media (max-width: 768px) {
    .d-flex.align-items-center {
        flex-direction: column;
        text-align: center;
    }
    
    .bg-primary.bg-opacity-10 {
        margin-bottom: 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const companhiaSelect = document.getElementById('companhia_aerea_id');
    const aeronaveSelect = document.getElementById('aeronave_id');
    const infoAeronaveCard = document.getElementById('infoAeronaveCard');
    const qtdVoosInput = document.getElementById('qtd_voos');
    const resumoCapacidade = document.getElementById('resumoCapacidade');
    const resumoTotal = document.getElementById('resumoTotal');
    const form = document.getElementById('formVoo');
    const progressBar = document.querySelector('#formProgress .progress-bar');
    const btnLimpar = document.getElementById('btnLimpar');

    // Elementos do card de informações
    const aeronaveModelo = document.getElementById('aeronaveModelo');
    const aeronaveFabricante = document.getElementById('aeronaveFabricante');
    const aeronaveCapacidade = document.getElementById('aeronaveCapacidade');
    const aeronavePorte = document.getElementById('aeronavePorte');

    // Função para filtrar aeronaves por companhia
    function filtrarAeronaves() {
        const companhiaId = companhiaSelect.value;
        let temOpcaoVisivel = false;

        Array.from(aeronaveSelect.options).forEach(option => {
            if (option.value === '') return;

            const companhias = option.dataset.companhia ? option.dataset.companhia.split(',') : [];
            
            if (companhiaId && companhias.indexOf(companhiaId.toString()) === -1) {
                option.style.display = 'none';
                option.disabled = true;
            } else {
                option.style.display = '';
                option.disabled = false;
                temOpcaoVisivel = true;
            }
        });

        // Se não houver opções visíveis, mostrar mensagem
        if (!temOpcaoVisivel && companhiaId) {
            aeronaveSelect.innerHTML = '<option value="" disabled selected>Nenhuma aeronave disponível</option>';
        }
    }

    // Função para mostrar informações da aeronave selecionada
    function mostrarInfoAeronave() {
        const selectedOption = aeronaveSelect.options[aeronaveSelect.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            const modelo = selectedOption.text.split(' - ')[0];
            const fabricante = selectedOption.dataset.fabricante;
            const capacidade = selectedOption.dataset.capacidade;
            const porte = selectedOption.dataset.porte;
            
            let porteTexto = '';
            switch(porte) {
                case 'PC': porteTexto = 'Pequeno Porte'; break;
                case 'MC': porteTexto = 'Médio Porte'; break;
                case 'LC': porteTexto = 'Grande Porte'; break;
                default: porteTexto = porte;
            }
            
            aeronaveModelo.textContent = modelo;
            aeronaveFabricante.textContent = fabricante;
            aeronaveCapacidade.textContent = capacidade + ' pax';
            aeronavePorte.textContent = porteTexto;
            
            infoAeronaveCard.classList.remove('d-none');
            
            // Atualizar resumo
            resumoCapacidade.textContent = capacidade;
            calcularTotalPassageiros();
        } else {
            infoAeronaveCard.classList.add('d-none');
            resumoCapacidade.textContent = '0';
            resumoTotal.textContent = '0';
        }
    }

    // Função para calcular total de passageiros
    function calcularTotalPassageiros() {
        const capacidade = parseInt(aeronaveSelect.options[aeronaveSelect.selectedIndex]?.dataset.capacidade || 0);
        const qtdVoos = parseInt(qtdVoosInput.value || 0);
        const total = capacidade * qtdVoos;
        
        resumoTotal.textContent = total.toLocaleString('pt-BR');
    }

    // Função para atualizar barra de progresso
    function atualizarProgresso() {
        const campos = [
            'id_voo',
            'aeroporto_id',
            'companhia_aerea_id',
            'aeronave_id',
            'tipo_voo',
            'qtd_voos',
            'horario_voo'
        ];
        
        let preenchidos = 0;
        campos.forEach(campo => {
            const input = document.getElementById(campo);
            if (input && input.value) {
                preenchidos++;
            }
        });
        
        const progresso = (preenchidos / campos.length) * 100;
        progressBar.style.width = progresso + '%';
    }

    // Event listeners
    companhiaSelect.addEventListener('change', function() {
        filtrarAeronaves();
        atualizarProgresso();
    });
    
    aeronaveSelect.addEventListener('change', function() {
        mostrarInfoAeronave();
        atualizarProgresso();
    });
    
    qtdVoosInput.addEventListener('input', function() {
        calcularTotalPassageiros();
        atualizarProgresso();
    });

    // Adicionar listeners para todos os campos
    document.querySelectorAll('#formVoo input, #formVoo select').forEach(input => {
        input.addEventListener('change', atualizarProgresso);
        input.addEventListener('keyup', atualizarProgresso);
    });

    // Botão limpar
    btnLimpar.addEventListener('click', function(e) {
        e.preventDefault();
        form.reset();
        infoAeronaveCard.classList.add('d-none');
        resumoCapacidade.textContent = '0';
        resumoTotal.textContent = '0';
        progressBar.style.width = '0%';
    });

    // Validação do formato do ID do voo
    const idVooInput = document.getElementById('id_voo');
    idVooInput.addEventListener('input', function() {
        let valor = this.value.toUpperCase();
        if (valor.length > 2 && !valor.includes('-')) {
            valor = valor.slice(0, 2) + '-' + valor.slice(2);
        }
        this.value = valor;
    });

    // Executar na carga da página
    if (companhiaSelect.value) {
        filtrarAeronaves();
    }
    if (aeronaveSelect.value) {
        mostrarInfoAeronave();
    }
    if (qtdVoosInput.value) {
        calcularTotalPassageiros();
    }
    atualizarProgresso();
});
</script>
@endsection