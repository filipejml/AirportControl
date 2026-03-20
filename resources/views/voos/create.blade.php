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

    <!-- Card do Último Voo Cadastrado -->
    @php
        $ultimoVoo = \App\Models\Voo::with(['aeroporto', 'companhiaAerea', 'aeronave'])
                        ->orderBy('created_at', 'desc')
                        ->first();
    @endphp

    @if($ultimoVoo)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-success shadow-sm">
                <div class="card-header bg-success text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-clock-history me-2 fs-5"></i>
                        <strong>Último Voo Cadastrado</strong>
                        <span class="ms-auto small">{{ $ultimoVoo->created_at->format('d/m/Y H:i:s') }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="bg-light rounded-circle p-3 d-inline-block mb-2">
                                    <i class="bi bi-airplane-fill text-primary fs-3"></i>
                                </div>
                                <h5 class="mb-0">{{ $ultimoVoo->id_voo }}</h5>
                                <small class="text-muted">ID do Voo</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-building me-2 text-primary"></i>
                                <div>
                                    <small class="text-muted d-block">Companhia</small>
                                    <strong>{{ $ultimoVoo->companhiaAerea->nome }}</strong>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-geo-alt me-2 text-primary"></i>
                                <div>
                                    <small class="text-muted d-block">Aeroporto</small>
                                    <strong>{{ $ultimoVoo->aeroporto->nome_aeroporto }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-airplane me-2 text-primary"></i>
                                <div>
                                    <small class="text-muted d-block">Aeronave</small>
                                    <strong>{{ $ultimoVoo->aeronave->modelo }}</strong>
                                    <small class="text-muted">({{ $ultimoVoo->aeronave->capacidade }} pax)</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-diagram-3 me-2 text-primary"></i>
                                <div>
                                    <small class="text-muted d-block">Tipo</small>
                                    <strong>
                                        @if($ultimoVoo->tipo_aeronave == 'PC')
                                            Pequeno Porte
                                        @elseif($ultimoVoo->tipo_aeronave == 'MC')
                                            Médio Porte
                                        @elseif($ultimoVoo->tipo_aeronave == 'LC')
                                            Grande Porte
                                        @else
                                            {{ $ultimoVoo->tipo_aeronave }}
                                        @endif
                                    </strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-sort-numeric-up me-2 text-primary"></i>
                                <div>
                                    <small class="text-muted d-block">Voos</small>
                                    <strong>{{ $ultimoVoo->qtd_voos }}x</strong>
                                    <small class="text-muted">({{ number_format($ultimoVoo->total_passageiros, 0, ',', '.') }} pax)</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-clock me-2 text-primary"></i>
                                <div>
                                    <small class="text-muted d-block">Horário</small>
                                    <strong>{{ $ultimoVoo->horario_voo }}</strong>
                                    <small class="text-muted">
                                        @if($ultimoVoo->horario_voo == 'EAM')
                                            (00h-06h)
                                        @elseif($ultimoVoo->horario_voo == 'AM')
                                            (06h-12h)
                                        @elseif($ultimoVoo->horario_voo == 'AN')
                                            (12h-18h)
                                        @elseif($ultimoVoo->horario_voo == 'PM')
                                            (18h-00h)
                                        @else
                                            (Diário)
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($ultimoVoo->media_notas)
                    <div class="row mt-3 pt-2 border-top">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-star-fill text-warning me-1"></i>
                                    <small class="text-muted">Avaliações:</small>
                                    @if($ultimoVoo->nota_obj)
                                        <span class="badge bg-info bg-opacity-10 text-info ms-2">Obj: {{ $ultimoVoo->nota_obj_letra }}</span>
                                    @endif
                                    @if($ultimoVoo->nota_pontualidade)
                                        <span class="badge bg-info bg-opacity-10 text-info">Pont: {{ $ultimoVoo->nota_pontualidade_letra }}</span>
                                    @endif
                                    @if($ultimoVoo->nota_servicos)
                                        <span class="badge bg-info bg-opacity-10 text-info">Serv: {{ $ultimoVoo->nota_servicos_letra }}</span>
                                    @endif
                                    @if($ultimoVoo->nota_patio)
                                        <span class="badge bg-info bg-opacity-10 text-info">Pátio: {{ $ultimoVoo->nota_patio_letra }}</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="badge bg-success rounded-pill fs-6 p-2">
                                        <i class="bi bi-calculator me-1"></i>
                                        Média: {{ number_format($ultimoVoo->media_notas, 1) }} ({{ $ultimoVoo->media_notas_letra }})
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Use os dados acima como referência para o novo cadastro
                        </small>
                        <a href="{{ route('voos.show', $ultimoVoo) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye me-1"></i>
                            Ver Detalhes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-secondary shadow-sm">
                <div class="card-body text-center py-3">
                    <i class="bi bi-info-circle me-2 text-secondary"></i>
                    <span class="text-secondary">Nenhum voo cadastrado ainda. Este será o primeiro!</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Resto do formulário igual ao anterior -->
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

                                <!-- Modelo da Aeronave -->
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

                                <!-- Tipo de Aeronave -->
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

<!-- CSS e Script permanecem iguais -->
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

    const porteTexto = {
        'PC': 'Pequeno Porte',
        'MC': 'Médio Porte',
        'LC': 'Grande Porte'
    };

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

    function limparCamposAeronave() {
        tipoAeronaveInput.value = '';
        capacidadeInput.value = '';
        totalPassageirosInput.value = '0';
    }

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

    function calcularTotalPassageiros() {
        const capacidade = parseInt(capacidadeInput.value || 0);
        const qtdVoos = parseInt(qtdVoosInput.value || 0);
        const total = capacidade * qtdVoos;
        totalPassageirosInput.value = total.toLocaleString('pt-BR');
    }

    // Validação do ID
    const idVooGroup = idVooInput.closest('.mb-3');
    let idVooFeedback = document.getElementById('idVooFeedback');
    
    if (!idVooFeedback) {
        idVooFeedback = document.createElement('div');
        idVooFeedback.id = 'idVooFeedback';
        idVooFeedback.className = 'form-text mt-1';
        idVooGroup.appendChild(idVooFeedback);
    }
    
    let timeoutId = null;

    function validarIdVoo(valor) {
        if (timeoutId) clearTimeout(timeoutId);
        
        if (!valor || valor.trim() === '') {
            idVooFeedback.innerHTML = '';
            idVooInput.classList.remove('is-valid', 'is-invalid');
            return false;
        }
        
        const formatoValido = /^[A-Z]{2,4}-\d{4}$/.test(valor);
        
        if (!formatoValido) {
            idVooFeedback.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1 text-warning"></i> Formato inválido. Use LL-NNNN ou LLLL-NNNN (ex: AA-1234 ou ASY-5678)';
            idVooFeedback.classList.add('text-warning');
            idVooFeedback.classList.remove('text-success', 'text-danger', 'text-info');
            idVooInput.classList.remove('is-valid');
            idVooInput.classList.add('is-invalid');
            return false;
        }
        
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
            });
        }, 500);
        
        return true;
    }

    idVooInput.addEventListener('input', function() {
        let valor = this.value.toUpperCase();
        this.value = valor;
        validarIdVoo(valor);
    });

    idVooInput.addEventListener('blur', function() {
        const valor = this.value;
        if (valor && !/^[A-Z]{2,4}-\d{4}$/.test(valor)) {
            idVooFeedback.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1 text-danger"></i> Formato inválido! Use LL-NNNN ou LLLL-NNNN (ex: AA-1234 ou ASY-5678)';
            idVooFeedback.classList.remove('text-success', 'text-warning', 'text-info');
            idVooFeedback.classList.add('text-danger');
            this.classList.add('is-invalid');
        }
    });

    companhiaSelect.addEventListener('change', function() {
        carregarAeronaves(this.value);
    });
    
    aeronaveSelect.addEventListener('change', atualizarInfoAeronave);
    qtdVoosInput.addEventListener('input', calcularTotalPassageiros);

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

    if (companhiaSelect.value) {
        carregarAeronaves(companhiaSelect.value);
    }

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