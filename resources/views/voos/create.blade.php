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

    {{-- Card do Último Voo Cadastrado (Colapsável) --}}
    @php
    $ultimoVoo = \App\Models\Voo::with([
        'aeroporto' => function($query) {
            $query->select('id', 'nome_aeroporto');
        },
        'companhiaAerea' => function($query) {
            $query->select('id', 'nome');
        },
        'aeronave' => function($query) {
            $query->select('id', 'modelo', 'capacidade', 'porte');
        }
    ])
    ->select([
        'id', 'id_voo', 'aeroporto_id', 'companhia_aerea_id', 
        'aeronave_id', 'tipo_aeronave', 'qtd_voos', 'total_passageiros',
        'horario_voo', 'nota_obj', 'nota_pontualidade', 'nota_servicos',
        'nota_patio', 'media_notas', 'qtd_passageiros', 'created_at', 'tipo_voo'
    ])
    ->orderBy('created_at', 'desc')
    ->first();
    @endphp

    @if($ultimoVoo)
    <div class="card mb-4 border-success shadow-sm">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center cursor-pointer"
             data-bs-toggle="collapse" 
             data-bs-target="#ultimoVooCollapse" 
             aria-expanded="false" 
             aria-controls="ultimoVooCollapse">
            <div class="d-flex align-items-center">
                <i class="bi bi-clock-history me-2 fs-5"></i>
                <h5 class="mb-0 fw-semibold">Último Voo Cadastrado</h5>
            </div>
            <div class="d-flex align-items-center">
                <small class="text-white-50 me-3">
                    <i class="bi bi-calendar me-1"></i>
                    {{ $ultimoVoo->created_at->diffForHumans() }}
                </small>
                <i class="bi bi-chevron-down collapse-icon"></i>
            </div>
        </div>
        
        {{-- Resumo (sempre visível quando colapsado) --}}
        <div id="ultimoVooResumo">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="bi bi-airplane-fill text-success fs-4"></i>
                        </div>
                        <div>
                            <strong class="text-dark fs-5">{{ $ultimoVoo->id_voo }}</strong>
                            <div class="text-muted small">
                                <span class="me-3">
                                    <i class="bi bi-building me-1"></i>{{ $ultimoVoo->companhiaAerea->nome ?? 'N/A' }}
                                </span>
                                <span class="me-3">
                                    <i class="bi bi-airplane me-1"></i>{{ $ultimoVoo->aeronave->modelo ?? 'N/A' }}
                                </span>
                                <span class="badge bg-info bg-opacity-10 text-info">
                                    <i class="bi bi-tag me-1"></i>{{ $ultimoVoo->tipo_voo }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="text-success fw-bold fs-5">
                            {{ number_format($ultimoVoo->total_passageiros, 0, ',', '.') }} 
                            <small class="text-muted fw-normal fs-6">passageiros</small>
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-geo-alt me-1"></i>{{ $ultimoVoo->aeroporto->nome_aeroporto ?? 'N/A' }} 
                            • 
                            <i class="bi bi-clock me-1"></i>{{ $ultimoVoo->horario_voo }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Detalhes (expansível) --}}
        <div class="collapse" id="ultimoVooCollapse" data-bs-parent=".card">
            <div class="card-body border-top">
                <div class="row g-4">
                    <div class="col-md-2 text-center">
                        <div class="bg-light p-3 rounded-3">
                            <i class="bi bi-airplane-engines fs-1 text-success"></i>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <strong class="text-muted d-block mb-1">
                                    <i class="bi bi-tag me-1"></i>ID do Voo
                                </strong>
                                <span class="fs-5 fw-semibold text-dark">{{ $ultimoVoo->id_voo }}</span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <strong class="text-muted d-block mb-1">
                                    <i class="bi bi-building me-1"></i>Companhia
                                </strong>
                                <span class="text-dark">{{ $ultimoVoo->companhiaAerea->nome ?? 'N/A' }}</span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <strong class="text-muted d-block mb-1">
                                    <i class="bi bi-airplane me-1"></i>Modelo
                                </strong>
                                <span class="text-dark">{{ $ultimoVoo->aeronave->modelo ?? 'N/A' }}</span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <strong class="text-muted d-block mb-1">
                                    <i class="bi bi-geo-alt me-1"></i>Aeroporto
                                </strong>
                                <span class="text-dark">{{ $ultimoVoo->aeroporto->nome_aeroporto ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-3 mb-3">
                                <strong class="text-muted d-block mb-1">
                                    <i class="bi bi-diagram-3 me-1"></i>Tipo de Voo
                                </strong>
                                <span class="badge bg-info">{{ $ultimoVoo->tipo_voo }}</span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <strong class="text-muted d-block mb-1">
                                    <i class="bi bi-box me-1"></i>Porte Aeronave
                                </strong>
                                <span class="badge bg-secondary">
                                    @switch($ultimoVoo->tipo_aeronave)
                                        @case('PC') Pequeno Porte @break
                                        @case('MC') Médio Porte @break
                                        @case('LC') Grande Porte @break
                                        @default {{ $ultimoVoo->tipo_aeronave }}
                                    @endswitch
                                </span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <strong class="text-muted d-block mb-1">
                                    <i class="bi bi-people me-1"></i>Capacidade
                                </strong>
                                <span class="text-dark">{{ number_format($ultimoVoo->qtd_passageiros, 0, ',', '.') }} pax/voo</span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <strong class="text-muted d-block mb-1">
                                    <i class="bi bi-sort-numeric-up me-1"></i>Qtd. Voos
                                </strong>
                                <span class="text-dark">{{ $ultimoVoo->qtd_voos }}x</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <strong class="text-muted d-block mb-1">
                                    <i class="bi bi-clock me-1"></i>Horário
                                </strong>
                                <span class="badge bg-warning text-dark">
                                    {{ $ultimoVoo->horario_voo }}
                                </span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <strong class="text-muted d-block mb-1">
                                    <i class="bi bi-calculator me-1"></i>Total Pax
                                </strong>
                                <span class="text-dark fw-semibold">{{ number_format($ultimoVoo->total_passageiros, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        
                        @if($ultimoVoo->nota_obj || $ultimoVoo->nota_pontualidade || $ultimoVoo->nota_servicos || $ultimoVoo->nota_patio)
                        <div class="row mt-2 pt-2 border-top">
                            <div class="col-12">
                                <strong class="text-muted d-block mb-2">
                                    <i class="bi bi-star-fill me-1 text-warning"></i>Avaliações
                                </strong>
                                <div class="d-flex flex-wrap gap-3">
                                    @if($ultimoVoo->nota_obj)
                                    <div class="text-center">
                                        <small class="text-muted d-block">Objetivo</small>
                                        <span class="badge bg-primary fs-6 px-3 py-2">{{ $ultimoVoo->nota_obj }}/10</span>
                                        <small class="text-muted d-block">
                                            @php
                                                $mapaLetra = [10 => 'A', 9 => 'B', 8 => 'C', 6 => 'D', 4 => 'E', 2 => 'F'];
                                                $notaLetraObj = $mapaLetra[$ultimoVoo->nota_obj] ?? '';
                                            @endphp
                                            {{ $notaLetraObj }}
                                        </small>
                                    </div>
                                    @endif
                                    @if($ultimoVoo->nota_pontualidade)
                                    <div class="text-center">
                                        <small class="text-muted d-block">Pontualidade</small>
                                        <span class="badge bg-primary fs-6 px-3 py-2">{{ $ultimoVoo->nota_pontualidade }}/10</span>
                                        <small class="text-muted d-block">
                                            @php
                                                $notaLetraPont = $mapaLetra[$ultimoVoo->nota_pontualidade] ?? '';
                                            @endphp
                                            {{ $notaLetraPont }}
                                        </small>
                                    </div>
                                    @endif
                                    @if($ultimoVoo->nota_servicos)
                                    <div class="text-center">
                                        <small class="text-muted d-block">Serviços</small>
                                        <span class="badge bg-primary fs-6 px-3 py-2">{{ $ultimoVoo->nota_servicos }}/10</span>
                                        <small class="text-muted d-block">
                                            @php
                                                $notaLetraServ = $mapaLetra[$ultimoVoo->nota_servicos] ?? '';
                                            @endphp
                                            {{ $notaLetraServ }}
                                        </small>
                                    </div>
                                    @endif
                                    @if($ultimoVoo->nota_patio)
                                    <div class="text-center">
                                        <small class="text-muted d-block">Pátio</small>
                                        <span class="badge bg-primary fs-6 px-3 py-2">{{ $ultimoVoo->nota_patio }}/10</span>
                                        <small class="text-muted d-block">
                                            @php
                                                $notaLetraPatio = $mapaLetra[$ultimoVoo->nota_patio] ?? '';
                                            @endphp
                                            {{ $notaLetraPatio }}
                                        </small>
                                    </div>
                                    @endif
                                    @if($ultimoVoo->media_notas)
                                    <div class="text-center ms-auto">
                                        <small class="text-muted d-block">Média Geral</small>
                                        <span class="badge bg-success fs-6 px-3 py-2">
                                            {{ number_format($ultimoVoo->media_notas, 1) }}/10
                                        </span>
                                        <small class="text-muted d-block">
                                            @php
                                                $mediaLetra = match(true) {
                                                    $ultimoVoo->media_notas >= 9 => 'A',
                                                    $ultimoVoo->media_notas >= 8 => 'B',
                                                    $ultimoVoo->media_notas >= 7 => 'C',
                                                    $ultimoVoo->media_notas >= 5 => 'D',
                                                    $ultimoVoo->media_notas >= 3 => 'E',
                                                    default => 'F'
                                                };
                                            @endphp
                                            {{ $mediaLetra }}
                                        </small>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-footer bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                <small class="text-muted">
                    <i class="bi bi-calendar-check me-1"></i>
                    Cadastrado em: {{ $ultimoVoo->created_at->format('d/m/Y H:i:s') }}
                    <span class="mx-2">•</span>
                    <i class="bi bi-arrow-repeat me-1"></i>
                    {{ $ultimoVoo->qtd_voos }} voos registrados
                </small>
                <div>
                    <a href="{{ route('voos.show', $ultimoVoo) }}" class="btn btn-sm btn-outline-primary me-2">
                        <i class="bi bi-eye me-1"></i>
                        Ver Detalhes
                    </a>
                    <a href="{{ route('voos.create') }}" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-plus-circle me-1"></i>
                        Novo Voo
                    </a>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="card mb-4 border-secondary shadow-sm">
        <div class="card-body text-center py-4">
            <i class="bi bi-info-circle fs-3 text-secondary mb-2 d-block"></i>
            <span class="text-secondary">Nenhum voo cadastrado ainda. Este será o primeiro!</span>
            <div class="mt-3">
                <a href="{{ route('voos.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>
                    Cadastrar Primeiro Voo
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Formulário de Criação -->
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
                                <!-- Aeroporto e ID do Voo na mesma linha -->
                                <div class="col-md-6 mb-3">
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

                                <div class="col-md-6 mb-3">
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
                                    </div>
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Formato: LL-NNNN ou LLLL-NNNN (ex: AA-1234 ou ASY-5678) - Use letras maiúsculas
                                    </div>
                                    
                                    @error('id_voo')
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
                                        </select>
                                    </div>
                                    @error('aeronave_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <!-- Tipo de Voo e Tipo de Aeronave na mesma linha -->
                                <div class="col-md-6 mb-3">
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

                                <div class="col-md-6 mb-3">
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
                                        <option value="A" {{ old('nota_obj') == 'A' ? 'selected' : '' }}>A</option>
                                        <option value="B" {{ old('nota_obj') == 'B' ? 'selected' : '' }}>B</option>
                                        <option value="C" {{ old('nota_obj') == 'C' ? 'selected' : '' }}>C</option>
                                        <option value="D" {{ old('nota_obj') == 'D' ? 'selected' : '' }}>D</option>
                                        <option value="E" {{ old('nota_obj') == 'E' ? 'selected' : '' }}>E</option>
                                        <option value="F" {{ old('nota_obj') == 'F' ? 'selected' : '' }}>F</option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="nota_pontualidade" class="form-label fw-semibold">Nota Pontualidade</label>
                                    <select class="form-select" id="nota_pontualidade" name="nota_pontualidade">
                                        <option value="">Não avaliado</option>
                                        <option value="A" {{ old('nota_pontualidade') == 'A' ? 'selected' : '' }}>A</option>
                                        <option value="B" {{ old('nota_pontualidade') == 'B' ? 'selected' : '' }}>B</option>
                                        <option value="C" {{ old('nota_pontualidade') == 'C' ? 'selected' : '' }}>C</option>
                                        <option value="D" {{ old('nota_pontualidade') == 'D' ? 'selected' : '' }}>D</option>
                                        <option value="E" {{ old('nota_pontualidade') == 'E' ? 'selected' : '' }}>E</option>
                                        <option value="F" {{ old('nota_pontualidade') == 'F' ? 'selected' : '' }}>F</option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="nota_servicos" class="form-label fw-semibold">Nota Serviços</label>
                                    <select class="form-select" id="nota_servicos" name="nota_servicos">
                                        <option value="">Não avaliado</option>
                                        <option value="A" {{ old('nota_servicos') == 'A' ? 'selected' : '' }}>A</option>
                                        <option value="B" {{ old('nota_servicos') == 'B' ? 'selected' : '' }}>B</option>
                                        <option value="C" {{ old('nota_servicos') == 'C' ? 'selected' : '' }}>C</option>
                                        <option value="D" {{ old('nota_servicos') == 'D' ? 'selected' : '' }}>D</option>
                                        <option value="E" {{ old('nota_servicos') == 'E' ? 'selected' : '' }}>E</option>
                                        <option value="F" {{ old('nota_servicos') == 'F' ? 'selected' : '' }}>F</option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="nota_patio" class="form-label fw-semibold">Nota Pátio</label>
                                    <select class="form-select" id="nota_patio" name="nota_patio">
                                        <option value="">Não avaliado</option>
                                        <option value="A" {{ old('nota_patio') == 'A' ? 'selected' : '' }}>A</option>
                                        <option value="B" {{ old('nota_patio') == 'B' ? 'selected' : '' }}>B</option>
                                        <option value="C" {{ old('nota_patio') == 'C' ? 'selected' : '' }}>C</option>
                                        <option value="D" {{ old('nota_patio') == 'D' ? 'selected' : '' }}>D</option>
                                        <option value="E" {{ old('nota_patio') == 'E' ? 'selected' : '' }}>E</option>
                                        <option value="F" {{ old('nota_patio') == 'F' ? 'selected' : '' }}>F</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="d-flex justify-content-center gap-3 align-items-center">
                            <button type="submit" class="btn btn-primary px-5 py-2">
                                <i class="bi bi-check-circle me-2"></i>
                                Cadastrar Voo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    select.form-control, 
    select.form-select {
        -webkit-appearance: menulist;
        -moz-appearance: menulist;
        appearance: menulist;
        background-image: none;
        padding-right: 2.5rem;
    }
    
    .collapse-icon {
        transition: transform 0.3s ease;
        font-size: 1.2rem;
    }
    
    .collapsed .collapse-icon {
        transform: rotate(-90deg);
    }
    
    .cursor-pointer {
        cursor: pointer;
    }
    
    .card-header {
        transition: background-color 0.2s ease;
    }
    
    .card-header:hover {
        background-color: #0a6b4e !important;
    }
    
    .collapse {
        transition: all 0.3s ease;
    }
    
    #ultimoVooResumo {
        transition: all 0.2s ease;
    }
    
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
    
    .alert {
        animation: slideDown 0.3s ease;
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

    let preenchimentoAutomaticoAtivo = true;
    let ultimoCodigoProcessado = '';
    let timeoutId = null;

    // ============================================
    // FUNCIONALIDADE DE EXPANDIR/RECOLHER DO CARD
    // ============================================
    const collapseElement = document.getElementById('ultimoVooCollapse');
    const headerElement = document.querySelector('.card-header[data-bs-target="#ultimoVooCollapse"]');
    
    if (collapseElement && headerElement) {
        const iconElement = headerElement.querySelector('.collapse-icon');
        
        const savedState = localStorage.getItem('ultimoVooCollapseState');
        if (savedState === 'expanded') {
            collapseElement.classList.add('show');
            if (headerElement.classList) {
                headerElement.classList.remove('collapsed');
            }
            if (iconElement) {
                iconElement.style.transform = 'rotate(0deg)';
            }
        } else if (savedState === 'collapsed') {
            collapseElement.classList.remove('show');
            if (headerElement.classList) {
                headerElement.classList.add('collapsed');
            }
            if (iconElement) {
                iconElement.style.transform = 'rotate(-90deg)';
            }
        } else {
            collapseElement.classList.remove('show');
            if (headerElement.classList) {
                headerElement.classList.add('collapsed');
            }
            if (iconElement) {
                iconElement.style.transform = 'rotate(-90deg)';
            }
            localStorage.setItem('ultimoVooCollapseState', 'collapsed');
        }
        
        collapseElement.addEventListener('show.bs.collapse', function () {
            if (headerElement.classList) {
                headerElement.classList.remove('collapsed');
            }
            if (iconElement) {
                iconElement.style.transform = 'rotate(0deg)';
            }
            localStorage.setItem('ultimoVooCollapseState', 'expanded');
        });
        
        collapseElement.addEventListener('hide.bs.collapse', function () {
            if (headerElement.classList) {
                headerElement.classList.add('collapsed');
            }
            if (iconElement) {
                iconElement.style.transform = 'rotate(-90deg)';
            }
            localStorage.setItem('ultimoVooCollapseState', 'collapsed');
        });
    }

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
                                    ${aeronave.modelo} - ${aeronave.fabricante?.nome || 'N/A'} (Cap: ${aeronave.capacidade} pax)
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

    function mostrarFeedback(mensagem, tipo, container, autoRemover = true) {
        const tipos = {
            success: { class: 'alert-success', icon: 'check-circle-fill' },
            danger: { class: 'alert-danger', icon: 'exclamation-triangle-fill' },
            warning: { class: 'alert-warning', icon: 'exclamation-triangle-fill' },
            info: { class: 'alert-info', icon: 'info-circle-fill' }
        };
        
        const config = tipos[tipo] || tipos.info;
        
        const feedbackDiv = document.createElement('div');
        feedbackDiv.className = `alert ${config.class} alert-dismissible fade show mt-2 py-2`;
        feedbackDiv.style.fontSize = '0.875rem';
        feedbackDiv.innerHTML = `
            <i class="bi bi-${config.icon} me-1"></i>
            ${mensagem}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        `;
        
        const existingAlerts = container.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());
        
        container.appendChild(feedbackDiv);
        
        if (autoRemover) {
            setTimeout(() => {
                if (feedbackDiv.parentNode) {
                    feedbackDiv.remove();
                }
            }, 5000);
        }
        
        return feedbackDiv;
    }

    // ============================================
    // VALIDAÇÃO DO ID DO VOO - CORRIGIDA
    // ============================================
    const idVooGroup = idVooInput.closest('.mb-3');
    let idVooFeedback = document.getElementById('idVooFeedback');
    
    if (!idVooFeedback) {
        idVooFeedback = document.createElement('div');
        idVooFeedback.id = 'idVooFeedback';
        idVooFeedback.className = 'form-text mt-1';
        idVooGroup.appendChild(idVooFeedback);
    }

    function validarIdVoo(valor) {
        if (timeoutId) clearTimeout(timeoutId);
        
        if (!valor || valor.trim() === '') {
            idVooFeedback.innerHTML = '';
            idVooInput.classList.remove('is-valid', 'is-invalid');
            return false;
        }
        
        // FORMATO CORRETO: 2-4 letras maiúsculas + hífen + 4 dígitos
        const formatoValido = /^[A-Z]{2,4}-\d{4}$/.test(valor);
        
        if (!formatoValido) {
            idVooFeedback.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1 text-warning"></i> Formato inválido. Use LL-NNNN ou LLLL-NNNN (ex: AA-1234 ou ASY-5678)';
            idVooFeedback.classList.add('text-warning');
            idVooFeedback.classList.remove('text-success', 'text-danger', 'text-info');
            idVooInput.classList.remove('is-valid');
            idVooInput.classList.add('is-invalid');
            return false;
        }
        
        // EXTRAIR O CÓDIGO DO ID DO VOO
        const codigoExtraido = valor.split('-')[0];
        
        // LISTA DE CÓDIGOS VÁLIDOS (mesma do CompanhiaHelper.php)
        const codigosValidos = [
            'PL', 'PP', 'FT', 'GK', 'AO', 'WW', 'AA', 'SK', 'RA', 'ASY', 'OB', 'JP', 
            'VI', 'PN', 'BV', 'OT', 'SPY', 'AW', 'AK', 'VAII', 'CA', 'WAT', 'TAL', 
            'HW', 'KW', 'FA', 'MAA', 'EX', 'SCA', 'RBA', 'CN', 'SG', 'AM', 'CZ', 
            'PF', 'RS', 'RX', 'AS', 'RE'
        ];
        
        // MAPA DE NOMES DAS COMPANHIAS
        const nomesCompanhias = {
            'PL': 'Prosperity Lines', 'PP': 'Pop! Airline', 'FT': 'Fast Travel', 'GK': 'Gluck Airlines', 'AO': 'Air Odysseia',
            'WW': 'World Wide', 'AA': 'Alpha', 'SK': 'Skyways','RA': 'Ryoko Airlines', 'ASY': 'AraSky', 'OB': 'Outback',
            'JP': 'Jurassic Pax', 'VI': 'Vahana Indonesia', 'PN': 'Península', 'BV': 'Bon Voyage', 'OT': 'Orient', 
            'SPY': 'Stellar Party', 'AW': 'Aerowings', 'AK': 'Air Kiwi','VAII': 'Vintage Airline II', 'CA': 'ChallengAir',
            'WAT': 'WAT', 'TAL': 'TAL', 'HW': 'Hallowings', 'KW': 'Air Kiwi','FA': 'Flyair', 'MAA': 'Maasai Airways',
            'EX': 'Evish Xmas', 'SCA': 'Santa Claus', 'RBA': 'Ryukyu by AJA', 'CN': 'CloudNine','SG': 'Singapura Airlines',
            'AM': 'American Airways', 'CZ': 'China Southern', 'PF': 'PayrionFestival', 'RS': 'Royal Skyways', 'RX': 'Riyadh Air',
            'AS': 'Asfar', 'RE': 'Reis'
        };
        
        // VALIDAÇÃO LOCAL IMEDIATA
        const codigoValidoLocal = codigosValidos.includes(codigoExtraido);
        
        if (!codigoValidoLocal) {
            idVooFeedback.innerHTML = `<i class="bi bi-x-circle-fill me-1 text-danger"></i> Código "${codigoExtraido}" inválido! Use um dos códigos válidos.`;
            idVooFeedback.classList.remove('text-success', 'text-warning');
            idVooFeedback.classList.add('text-danger');
            idVooInput.classList.remove('is-valid');
            idVooInput.classList.add('is-invalid');
            return false;
        }
        
        // SE O CÓDIGO É VÁLIDO LOCALMENTE, MOSTRA FEEDBACK IMEDIATO
        const nomeCompanhia = nomesCompanhias[codigoExtraido];
        let mensagem = `<i class="bi bi-check-circle-fill me-1 text-success"></i> ✓ Código válido: ${nomeCompanhia}`;
        
        // TENTA SELECIONAR A COMPANHIA AUTOMATICAMENTE (se existir no select)
        const companhiaSelect = document.getElementById('companhia_aerea_id');
        let companhiaEncontrada = false;
        
        if (companhiaSelect) {
            for (let i = 0; i < companhiaSelect.options.length; i++) {
                const option = companhiaSelect.options[i];
                const optionText = option.text.toUpperCase();
                if (optionText.includes(nomeCompanhia.toUpperCase()) || 
                    optionText.includes(codigoExtraido)) {
                    if (preenchimentoAutomaticoAtivo && companhiaSelect.value != option.value) {
                        companhiaSelect.value = option.value;
                        const changeEvent = new Event('change', { bubbles: true });
                        companhiaSelect.dispatchEvent(changeEvent);
                        mensagem += `<br><small class="text-success"><i class="bi bi-building me-1"></i> Companhia "${option.text}" selecionada!</small>`;
                    }
                    companhiaEncontrada = true;
                    break;
                }
            }
            
            if (!companhiaEncontrada && preenchimentoAutomaticoAtivo) {
                mensagem += `<br><small class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i> Companhia "${nomeCompanhia}" não cadastrada. Cadastre-a primeiro.</small>`;
            }
        }
        
        idVooFeedback.innerHTML = mensagem;
        idVooFeedback.classList.remove('text-danger', 'text-warning');
        idVooFeedback.classList.add('text-success');
        idVooInput.classList.remove('is-invalid');
        idVooInput.classList.add('is-valid');
        
        // FAZ A VALIDAÇÃO AJAX EM BACKGROUND (apenas para verificar duplicidade)
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
                if (!data.valid) {
                    idVooFeedback.innerHTML = `<i class="bi bi-x-circle-fill me-1 text-danger"></i> ${data.message}`;
                    idVooFeedback.classList.remove('text-success');
                    idVooFeedback.classList.add('text-danger');
                    idVooInput.classList.remove('is-valid');
                    idVooInput.classList.add('is-invalid');
                }
            })
            .catch(error => {
                console.error('Erro na validação AJAX:', error);
                // Não altera o status se o AJAX falhar, pois já validou localmente
            });
        }, 300);
        
        return true;
    }

    // MÁSCARA CORRIGIDA PARA O ID DO VOO
    idVooInput.addEventListener('input', function(e) {
        let valor = this.value.toUpperCase();
        
        // Remover caracteres que não são letras ou números
        valor = valor.replace(/[^A-Z0-9]/g, '');
        
        // Se o valor está vazio, limpar e retornar
        if (valor.length === 0) {
            this.value = '';
            validarIdVoo('');
            return;
        }
        
        // Separar a parte das letras e números
        let matchLetras = valor.match(/^([A-Z]+)/);
        let matchNumeros = valor.match(/\d+$/);
        
        let letras = matchLetras ? matchLetras[1] : '';
        let numeros = matchNumeros ? matchNumeros[0] : '';
        
        // Limitar letras entre 2 e 4 caracteres
        if (letras.length > 4) {
            letras = letras.substring(0, 4);
        }
        
        // Limitar números a 4 dígitos
        if (numeros.length > 4) {
            numeros = numeros.substring(0, 4);
        }
        
        // Construir o valor formatado
        let novoValor = '';
        if (letras.length >= 2) {
            novoValor = letras;
            
            // Adicionar hífen se tiver pelo menos 2 letras e números ou se o usuário digitou o hífen
            if (numeros.length > 0) {
                novoValor += '-' + numeros;
            } else if (this.value.includes('-') && letras.length >= 2) {
                novoValor += '-';
            }
        } else if (letras.length > 0) {
            novoValor = letras;
        }
        
        // Se ainda não tem letras suficientes, manter o que foi digitado
        if (novoValor === '' && valor.length > 0) {
            novoValor = valor;
        }
        
        this.value = novoValor;
        validarIdVoo(novoValor);
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

    // Adicionar checkbox para controlar o preenchimento automático
    const toggleAutoFill = document.createElement('div');
    toggleAutoFill.className = 'form-check mt-1';
    toggleAutoFill.innerHTML = `
        <input class="form-check-input" type="checkbox" id="toggleAutoFill" checked>
        <label class="form-check-label small text-muted" for="toggleAutoFill">
            <i class="bi bi-magic"></i> Preencher companhia automaticamente ao digitar o ID
        </label>
    `;
    idVooGroup.appendChild(toggleAutoFill);

    document.getElementById('toggleAutoFill').addEventListener('change', function(e) {
        preenchimentoAutomaticoAtivo = e.target.checked;
        const status = e.target.checked ? 'ativado' : 'desativado';
        mostrarFeedback(
            `Preenchimento automático ${status}.`,
            e.target.checked ? 'info' : 'warning',
            idVooGroup,
            true
        );
    });

    // Eventos dos selects
    companhiaSelect.addEventListener('change', function() {
        carregarAeronaves(this.value);
    });
    
    aeronaveSelect.addEventListener('change', atualizarInfoAeronave);
    qtdVoosInput.addEventListener('input', calcularTotalPassageiros);

    // Carregar aeronaves iniciais se houver companhia selecionada
    if (companhiaSelect.value) {
        carregarAeronaves(companhiaSelect.value);
    }

    // Validação do formulário antes do envio
    const form = document.getElementById('formVoo');
    form.addEventListener('submit', function(e) {
        const idVoo = idVooInput.value;
        
        if (!idVoo || idVoo.trim() === '') {
            e.preventDefault();
            idVooFeedback.innerHTML = '<i class="bi bi-x-circle-fill me-1 text-danger"></i> O ID do voo é obrigatório!';
            idVooFeedback.classList.add('text-danger');
            idVooInput.classList.add('is-invalid');
            idVooInput.focus();
            
            mostrarFeedback(
                'Por favor, preencha o ID do voo.',
                'danger',
                idVooGroup,
                true
            );
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
            
            mostrarFeedback(
                'Formato de ID inválido. Use o formato correto (ex: AA-1234)',
                'danger',
                idVooGroup,
                true
            );
            return false;
        }
        
        if (!idVooInput.classList.contains('is-valid')) {
            e.preventDefault();
            
            if (!idVooFeedback.innerHTML.includes('Código de companhia inválido')) {
                idVooFeedback.innerHTML = '<i class="bi bi-x-circle-fill me-1 text-danger"></i> Por favor, aguarde a validação do código ou verifique se o código é válido.';
                idVooFeedback.classList.add('text-danger');
            }
            
            mostrarFeedback(
                'Aguardando validação do código do voo. Por favor, aguarde.',
                'warning',
                idVooGroup,
                true
            );
            return false;
        }
        
        if (!companhiaSelect.value) {
            e.preventDefault();
            mostrarFeedback(
                'Por favor, selecione uma companhia aérea.',
                'danger',
                companhiaSelect.closest('.mb-3'),
                true
            );
            companhiaSelect.focus();
            return false;
        }
        
        if (!aeronaveSelect.value) {
            e.preventDefault();
            mostrarFeedback(
                'Por favor, selecione uma aeronave.',
                'danger',
                aeronaveSelect.closest('.mb-3'),
                true
            );
            aeronaveSelect.focus();
            return false;
        }
        
        return true;
    });

    // Tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Feedback de sucesso após cadastro
    if (sessionStorage.getItem('voo_cadastrado')) {
        mostrarFeedback(
            sessionStorage.getItem('voo_mensagem') || 'Voo cadastrado com sucesso!',
            'success',
            document.querySelector('.card-body'),
            true
        );
        sessionStorage.removeItem('voo_cadastrado');
        sessionStorage.removeItem('voo_mensagem');
    }
    
    qtdVoosInput.addEventListener('change', function() {
        if (this.value < 1) this.value = 1;
        calcularTotalPassageiros();
    });
    
    // Dica para o usuário sobre os códigos válidos
    const exibirDicaCodigos = () => {
        const dicaDiv = document.createElement('div');
        dicaDiv.className = 'alert alert-info alert-dismissible fade show mt-2 py-2';
        dicaDiv.style.fontSize = '0.875rem';
        dicaDiv.innerHTML = `
            <i class="bi bi-info-circle-fill me-1"></i>
            <strong>Dica:</strong> Os códigos de companhia válidos são: 
            PL, PP, FT, GK, AO, WW, AA, SK, RA, ASY, OB, JP, VI, PN, BV, OT, SPY, AW, AK, VAII, CA, WAT, TAL, HW, KW, FA, MAA, EX, SCA, RBA, CN, SG, AM, CZ, PF, RS, RX, AS, RE
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        `;
        idVooGroup.appendChild(dicaDiv);
        
        setTimeout(() => {
            if (dicaDiv.parentNode) dicaDiv.remove();
        }, 8000);
    };
    
    if (!sessionStorage.getItem('dica_codigos_mostrada')) {
        setTimeout(exibirDicaCodigos, 1000);
        sessionStorage.setItem('dica_codigos_mostrada', 'true');
    }
});
</script>
@endsection