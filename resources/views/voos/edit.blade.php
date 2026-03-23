{{-- resources/views/voos/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Voo - Airport Manager')

@section('content')
<div class="container-fluid px-4">
    <!-- Cabeçalho com breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1">Editar Voo</h2>
                        <p class="text-muted mb-0">Atualize as informações do voo {{ $voo->id_voo }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensagens de Feedback -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeInDown" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-4"></i>
                <div>
                    <strong>Sucesso!</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeInDown" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
                <div>
                    <strong>Erro!</strong> {{ session('error') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeInDown" role="alert">
            <div class="d-flex align-items-start">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
                <div>
                    <strong>Erro de validação!</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Formulário de Edição -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('voos.update', $voo) }}" method="POST" id="formEditarVoo">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <!-- ID do Voo -->
                    <div class="col-md-6">
                        <label for="id_voo" class="form-label fw-semibold">
                            <i class="bi bi-tag me-1"></i>
                            ID do Voo <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="id_voo" 
                               id="id_voo" 
                               value="{{ old('id_voo', $voo->id_voo) }}" 
                               required
                               pattern="[A-Z]{2,4}-\d{4}"
                               placeholder="ex: GOL-1234"
                               class="form-control @error('id_voo') is-invalid @enderror">
                        <div class="form-text">Formato: Código da companhia (2-4 letras) + hífen + 4 números</div>
                        @error('id_voo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Aeroporto -->
                    <div class="col-md-6">
                        <label for="aeroporto_id" class="form-label fw-semibold">
                            <i class="bi bi-geo-alt me-1"></i>
                            Aeroporto <span class="text-danger">*</span>
                        </label>
                        <select name="aeroporto_id" id="aeroporto_id" required
                            class="form-select @error('aeroporto_id') is-invalid @enderror">
                            <option value="">Selecione um aeroporto</option>
                            @foreach($aeroportos as $aeroporto)
                                <option value="{{ $aeroporto->id }}" 
                                    {{ old('aeroporto_id', $voo->aeroporto_id) == $aeroporto->id ? 'selected' : '' }}>
                                    {{ $aeroporto->codigo_iata ?? $aeroporto->codigo_icao }} - {{ $aeroporto->nome_aeroporto }}
                                </option>
                            @endforeach
                        </select>
                        @error('aeroporto_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Companhia Aérea -->
                    <div class="col-md-6">
                        <label for="companhia_aerea_id" class="form-label fw-semibold">
                            <i class="bi bi-building me-1"></i>
                            Companhia Aérea <span class="text-danger">*</span>
                        </label>
                        <select name="companhia_aerea_id" id="companhia_aerea_id" required
                            class="form-select @error('companhia_aerea_id') is-invalid @enderror">
                            <option value="">Selecione uma companhia</option>
                            @foreach($companhias as $companhia)
                                <option value="{{ $companhia->id }}" 
                                    {{ old('companhia_aerea_id', $voo->companhia_aerea_id) == $companhia->id ? 'selected' : '' }}>
                                    {{ $companhia->codigo ?? '' }} - {{ $companhia->nome }}
                                </option>
                            @endforeach
                        </select>
                        @error('companhia_aerea_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Aeronave -->
                    <div class="col-md-6">
                        <label for="aeronave_id" class="form-label fw-semibold">
                            <i class="bi bi-airplane me-1"></i>
                            Aeronave <span class="text-danger">*</span>
                        </label>
                        <select name="aeronave_id" id="aeronave_id" required
                            class="form-select @error('aeronave_id') is-invalid @enderror">
                            <option value="">Selecione uma aeronave</option>
                        </select>
                        @error('aeronave_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        <!-- Informações da Aeronave -->
                        <div id="infoAeronave" class="mt-2 p-3 bg-light rounded-3 {{ $voo->aeronave_id ? '' : 'd-none' }}">
                            <div class="row g-2">
                                <div class="col-6">
                                    <small class="text-muted d-block">Capacidade</small>
                                    <strong id="capacidadeInfo" class="text-primary">{{ number_format($voo->qtd_passageiros, 0, ',', '.') }}</strong>
                                    <small> passageiros/voo</small>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Porte</small>
                                    <strong id="porteInfo" class="text-primary">
                                        @if($voo->tipo_aeronave == 'PC') Pequeno Porte
                                        @elseif($voo->tipo_aeronave == 'MC') Médio Porte
                                        @elseif($voo->tipo_aeronave == 'LC') Grande Porte
                                        @else - @endif
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tipo de Voo -->
                    <div class="col-md-6">
                        <label for="tipo_voo" class="form-label fw-semibold">
                            <i class="bi bi-tags me-1"></i>
                            Tipo de Voo <span class="text-danger">*</span>
                        </label>
                        <select name="tipo_voo" id="tipo_voo" required
                            class="form-select @error('tipo_voo') is-invalid @enderror">
                            <option value="Regular" {{ old('tipo_voo', $voo->tipo_voo) == 'Regular' ? 'selected' : '' }}>
                                Regular
                            </option>
                            <option value="Charter" {{ old('tipo_voo', $voo->tipo_voo) == 'Charter' ? 'selected' : '' }}>
                                Charter
                            </option>
                        </select>
                        @error('tipo_voo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Quantidade de Voos -->
                    <div class="col-md-6">
                        <label for="qtd_voos" class="form-label fw-semibold">
                            <i class="bi bi-sort-numeric-up me-1"></i>
                            Quantidade de Voos <span class="text-danger">*</span>
                        </label>
                        <input type="number" 
                               name="qtd_voos" 
                               id="qtd_voos" 
                               value="{{ old('qtd_voos', $voo->qtd_voos) }}" 
                               required 
                               min="1"
                               class="form-control @error('qtd_voos') is-invalid @enderror">
                        @error('qtd_voos')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Horário do Voo -->
                    <div class="col-md-6">
                        <label for="horario_voo" class="form-label fw-semibold">
                            <i class="bi bi-clock me-1"></i>
                            Horário do Voo <span class="text-danger">*</span>
                        </label>
                        <select name="horario_voo" id="horario_voo" required
                            class="form-select @error('horario_voo') is-invalid @enderror">
                            <option value="EAM" {{ old('horario_voo', $voo->horario_voo) == 'EAM' ? 'selected' : '' }}>
                                EAM (Early Morning - 00h às 06h)
                            </option>
                            <option value="AM" {{ old('horario_voo', $voo->horario_voo) == 'AM' ? 'selected' : '' }}>
                                AM (Morning - 06h às 12h)
                            </option>
                            <option value="AN" {{ old('horario_voo', $voo->horario_voo) == 'AN' ? 'selected' : '' }}>
                                AN (Afternoon - 12h às 18h)
                            </option>
                            <option value="PM" {{ old('horario_voo', $voo->horario_voo) == 'PM' ? 'selected' : '' }}>
                                PM (Evening - 18h às 00h)
                            </option>
                            <option value="ALL" {{ old('horario_voo', $voo->horario_voo) == 'ALL' ? 'selected' : '' }}>
                                ALL (Diário - Todos os horários)
                            </option>
                        </select>
                        @error('horario_voo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Resumo do Voo - Card de Informações -->
                    <div class="col-12">
                        <div class="card bg-primary bg-opacity-10 border-0">
                            <div class="card-body">
                                <h5 class="card-title text-primary mb-3">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Resumo do Voo
                                </h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <small class="text-muted d-block">Capacidade por Voo</small>
                                            <h4 class="mb-0 text-primary" id="resumoCapacidade">
                                                {{ number_format($voo->qtd_passageiros, 0, ',', '.') }}
                                            </h4>
                                            <small>passageiros</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <small class="text-muted d-block">Tipo da Aeronave</small>
                                            <h4 class="mb-0 text-primary" id="resumoTipoAeronave">
                                                @if($voo->tipo_aeronave == 'PC') Pequeno Porte
                                                @elseif($voo->tipo_aeronave == 'MC') Médio Porte
                                                @elseif($voo->tipo_aeronave == 'LC') Grande Porte
                                                @else - @endif
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <small class="text-muted d-block">Total de Passageiros</small>
                                            <h4 class="mb-0 text-primary" id="resumoTotal">
                                                {{ number_format($voo->total_passageiros, 0, ',', '.') }}
                                            </h4>
                                            <small>passageiros (total)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notas - Seção de Avaliações -->
                    <div class="col-12">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <i class="bi bi-star me-2 text-warning"></i>
                                    Notas (opcionais)
                                </h5>
                                <div class="row g-3">
                                    <!-- Nota Objetivo -->
                                    <div class="col-md-3">
                                        <label for="nota_obj" class="form-label fw-semibold">
                                            <i class="bi bi-bullseye me-1"></i>
                                            Nota Objetivo
                                        </label>
                                        <select name="nota_obj" id="nota_obj"
                                            class="form-select">
                                            <option value="">Não avaliado</option>
                                            <option value="A" {{ old('nota_obj', $voo->nota_obj_letra) == 'A' ? 'selected' : '' }}>
                                                A (Excelente - 10)
                                            </option>
                                            <option value="B" {{ old('nota_obj', $voo->nota_obj_letra) == 'B' ? 'selected' : '' }}>
                                                B (Muito Bom - 9)
                                            </option>
                                            <option value="C" {{ old('nota_obj', $voo->nota_obj_letra) == 'C' ? 'selected' : '' }}>
                                                C (Bom - 8)
                                            </option>
                                            <option value="D" {{ old('nota_obj', $voo->nota_obj_letra) == 'D' ? 'selected' : '' }}>
                                                D (Regular - 6)
                                            </option>
                                            <option value="E" {{ old('nota_obj', $voo->nota_obj_letra) == 'E' ? 'selected' : '' }}>
                                                E (Ruim - 4)
                                            </option>
                                            <option value="F" {{ old('nota_obj', $voo->nota_obj_letra) == 'F' ? 'selected' : '' }}>
                                                F (Péssimo - 2)
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Nota Pontualidade -->
                                    <div class="col-md-3">
                                        <label for="nota_pontualidade" class="form-label fw-semibold">
                                            <i class="bi bi-clock-history me-1"></i>
                                            Nota Pontualidade
                                        </label>
                                        <select name="nota_pontualidade" id="nota_pontualidade"
                                            class="form-select">
                                            <option value="">Não avaliado</option>
                                            <option value="A" {{ old('nota_pontualidade', $voo->nota_pontualidade_letra) == 'A' ? 'selected' : '' }}>
                                                A (Excelente - 10)
                                            </option>
                                            <option value="B" {{ old('nota_pontualidade', $voo->nota_pontualidade_letra) == 'B' ? 'selected' : '' }}>
                                                B (Muito Bom - 9)
                                            </option>
                                            <option value="C" {{ old('nota_pontualidade', $voo->nota_pontualidade_letra) == 'C' ? 'selected' : '' }}>
                                                C (Bom - 8)
                                            </option>
                                            <option value="D" {{ old('nota_pontualidade', $voo->nota_pontualidade_letra) == 'D' ? 'selected' : '' }}>
                                                D (Regular - 6)
                                            </option>
                                            <option value="E" {{ old('nota_pontualidade', $voo->nota_pontualidade_letra) == 'E' ? 'selected' : '' }}>
                                                E (Ruim - 4)
                                            </option>
                                            <option value="F" {{ old('nota_pontualidade', $voo->nota_pontualidade_letra) == 'F' ? 'selected' : '' }}>
                                                F (Péssimo - 2)
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Nota Serviços -->
                                    <div class="col-md-3">
                                        <label for="nota_servicos" class="form-label fw-semibold">
                                            <i class="bi bi-cup-straw me-1"></i>
                                            Nota Serviços
                                        </label>
                                        <select name="nota_servicos" id="nota_servicos"
                                            class="form-select">
                                            <option value="">Não avaliado</option>
                                            <option value="A" {{ old('nota_servicos', $voo->nota_servicos_letra) == 'A' ? 'selected' : '' }}>
                                                A (Excelente - 10)
                                            </option>
                                            <option value="B" {{ old('nota_servicos', $voo->nota_servicos_letra) == 'B' ? 'selected' : '' }}>
                                                B (Muito Bom - 9)
                                            </option>
                                            <option value="C" {{ old('nota_servicos', $voo->nota_servicos_letra) == 'C' ? 'selected' : '' }}>
                                                C (Bom - 8)
                                            </option>
                                            <option value="D" {{ old('nota_servicos', $voo->nota_servicos_letra) == 'D' ? 'selected' : '' }}>
                                                D (Regular - 6)
                                            </option>
                                            <option value="E" {{ old('nota_servicos', $voo->nota_servicos_letra) == 'E' ? 'selected' : '' }}>
                                                E (Ruim - 4)
                                            </option>
                                            <option value="F" {{ old('nota_servicos', $voo->nota_servicos_letra) == 'F' ? 'selected' : '' }}>
                                                F (Péssimo - 2)
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Nota Pátio -->
                                    <div class="col-md-3">
                                        <label for="nota_patio" class="form-label fw-semibold">
                                            <i class="bi bi-building me-1"></i>
                                            Nota Pátio
                                        </label>
                                        <select name="nota_patio" id="nota_patio"
                                            class="form-select">
                                            <option value="">Não avaliado</option>
                                            <option value="A" {{ old('nota_patio', $voo->nota_patio_letra) == 'A' ? 'selected' : '' }}>
                                                A (Excelente - 10)
                                            </option>
                                            <option value="B" {{ old('nota_patio', $voo->nota_patio_letra) == 'B' ? 'selected' : '' }}>
                                                B (Muito Bom - 9)
                                            </option>
                                            <option value="C" {{ old('nota_patio', $voo->nota_patio_letra) == 'C' ? 'selected' : '' }}>
                                                C (Bom - 8)
                                            </option>
                                            <option value="D" {{ old('nota_patio', $voo->nota_patio_letra) == 'D' ? 'selected' : '' }}>
                                                D (Regular - 6)
                                            </option>
                                            <option value="E" {{ old('nota_patio', $voo->nota_patio_letra) == 'E' ? 'selected' : '' }}>
                                                E (Ruim - 4)
                                            </option>
                                            <option value="F" {{ old('nota_patio', $voo->nota_patio_letra) == 'F' ? 'selected' : '' }}>
                                                F (Péssimo - 2)
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                    <a href="{{ route('voos.index') }}" class="btn btn-secondary btn-lg">
                        <i class="bi bi-x-circle me-2"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-warning btn-lg">
                        <i class="bi bi-pencil-square me-2"></i>
                        Atualizar Voo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Animações */
@import url('https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css');

/* Cards e formulários */
.card {
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #0d5c8b;
    box-shadow: 0 0 0 0.2rem rgba(13, 92, 139, 0.25);
}

/* Botões */
.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-2px);
}

/* Alertas */
.alert {
    border-radius: 12px;
}

/* Scroll suave */
html {
    scroll-behavior: smooth;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const companhiaSelect = document.getElementById('companhia_aerea_id');
    const aeronaveSelect = document.getElementById('aeronave_id');
    const qtdVoosInput = document.getElementById('qtd_voos');
    const resumoCapacidade = document.getElementById('resumoCapacidade');
    const resumoTotal = document.getElementById('resumoTotal');
    const resumoTipoAeronave = document.getElementById('resumoTipoAeronave');
    const capacidadeInfo = document.getElementById('capacidadeInfo');
    const porteInfo = document.getElementById('porteInfo');
    const infoAeronave = document.getElementById('infoAeronave');

    const porteTexto = {
        'PC': 'Pequeno Porte',
        'MC': 'Médio Porte',
        'LC': 'Grande Porte'
    };

    const currentAeronaveId = "{{ $voo->aeronave_id }}";

    function carregarAeronaves(companhiaId) {
        if (!companhiaId) {
            aeronaveSelect.innerHTML = '<option value="">Selecione uma aeronave</option>';
            infoAeronave.classList.add('d-none');
            return;
        }

        fetch(`/api/companhias/${companhiaId}/aeronaves`)
            .then(response => response.json())
            .then(aeronaves => {
                if (aeronaves.length === 0) {
                    aeronaveSelect.innerHTML = '<option value="">Nenhuma aeronave disponível</option>';
                    infoAeronave.classList.add('d-none');
                } else {
                    let options = '<option value="">Selecione uma aeronave</option>';
                    aeronaves.forEach(aeronave => {
                        const selected = currentAeronaveId == aeronave.id ? 'selected' : '';
                        options += `<option value="${aeronave.id}" 
                                         data-capacidade="${aeronave.capacidade}"
                                         data-porte="${aeronave.porte}"
                                         ${selected}>
                                    ${aeronave.modelo} - ${aeronave.fabricante?.nome || 'N/A'} (Cap: ${aeronave.capacidade} pax)
                                </option>`;
                    });
                    aeronaveSelect.innerHTML = options;
                    
                    if (currentAeronaveId) {
                        atualizarInfoAeronave();
                    }
                }
            })
            .catch(error => {
                console.error('Erro ao carregar aeronaves:', error);
                aeronaveSelect.innerHTML = '<option value="">Erro ao carregar aeronaves</option>';
            });
    }

    function atualizarInfoAeronave() {
        const selectedOption = aeronaveSelect.options[aeronaveSelect.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            const capacidade = selectedOption.dataset.capacidade;
            const porte = selectedOption.dataset.porte;
            
            if (capacidadeInfo && porteInfo) {
                capacidadeInfo.textContent = parseInt(capacidade).toLocaleString('pt-BR');
                porteInfo.textContent = porteTexto[porte] || porte;
                infoAeronave.classList.remove('d-none');
            }
            
            if (resumoCapacidade) {
                resumoCapacidade.textContent = parseInt(capacidade).toLocaleString('pt-BR');
            }
            
            if (resumoTipoAeronave) {
                resumoTipoAeronave.textContent = porteTexto[porte] || porte;
            }
            
            calcularTotalPassageiros();
        } else {
            infoAeronave.classList.add('d-none');
            resumoCapacidade.textContent = '0';
            resumoTipoAeronave.textContent = '-';
        }
    }

    function calcularTotalPassageiros() {
        const capacidade = parseInt(aeronaveSelect.options[aeronaveSelect.selectedIndex]?.dataset.capacidade || 0);
        const qtdVoos = parseInt(qtdVoosInput.value || 0);
        const total = capacidade * qtdVoos;
        resumoTotal.textContent = total.toLocaleString('pt-BR');
    }

    companhiaSelect.addEventListener('change', function() {
        carregarAeronaves(this.value);
    });
    
    aeronaveSelect.addEventListener('change', atualizarInfoAeronave);
    qtdVoosInput.addEventListener('input', calcularTotalPassageiros);

    // ID do voo formatting
    const idVooInput = document.getElementById('id_voo');
    idVooInput.addEventListener('input', function() {
        let valor = this.value.toUpperCase();
        valor = valor.replace(/[^A-Z0-9]/g, '');
        
        if (valor.length > 2 && !valor.includes('-')) {
            valor = valor.slice(0, 2) + '-' + valor.slice(2, 6);
        }
        
        if (valor.length > 7) {
            valor = valor.slice(0, 7);
        }
        
        this.value = valor;
    });

    // Carregar aeronaves iniciais
    if (companhiaSelect.value) {
        carregarAeronaves(companhiaSelect.value);
    }
});
</script>
@endsection