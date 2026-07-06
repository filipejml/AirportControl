@extends('layouts.app')

@section('title', 'Detalhes do Voo - Airport Manager')

@push('styles')
<style>
    .flight-details {
        --flight-border: #e4e7ec;
        --flight-muted: #667085;
    }

    .flight-details .page-header,
    .flight-details .details-card {
        border: 1px solid var(--flight-border);
        border-radius: 1.25rem;
        background: #fff;
        box-shadow: 0 10px 30px rgba(16, 24, 40, .06);
    }

    .flight-details .page-header {
        padding: clamp(1.25rem, 3vw, 2rem);
        color: #fff;
        background: linear-gradient(125deg, #101828 0%, #1849a9 65%, #2e90fa 100%);
    }

    .flight-details .details-card {
        overflow: hidden;
    }

    .flight-details .section-title {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--flight-border);
        font-size: 1rem;
        font-weight: 700;
    }

    .flight-details .info-item {
        height: 100%;
        padding: 1rem;
        border: 1px solid var(--flight-border);
        border-radius: .9rem;
        background: #f9fafb;
    }

    .flight-details .info-label {
        margin-bottom: .35rem;
        color: var(--flight-muted);
        font-size: .75rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .flight-details .info-value {
        margin: 0;
        color: #172033;
        font-size: 1.05rem;
        font-weight: 600;
    }

    .flight-details .score-card {
        height: 100%;
        padding: 1rem;
        border-radius: .9rem;
        background: #eff6ff;
        text-align: center;
    }
</style>
@endpush

@section('content')
@php
    $horarioLabels = [
        'EAM' => 'Madrugada (00h–06h)',
        'AM' => 'Manhã (06h–12h)',
        'AN' => 'Tarde (12h–18h)',
        'PM' => 'Noite (18h–00h)',
        'ALL' => 'Diário',
    ];

    $porteLabels = [
        'PC' => 'Pequeno Porte',
        'MC' => 'Médio Porte',
        'LC' => 'Grande Porte',
    ];

    $avaliacoes = [
        ['label' => 'Objetivo', 'nota' => $voo->nota_obj, 'letra' => $voo->nota_obj_letra],
        ['label' => 'Pontualidade', 'nota' => $voo->nota_pontualidade, 'letra' => $voo->nota_pontualidade_letra],
        ['label' => 'Serviços', 'nota' => $voo->nota_servicos, 'letra' => $voo->nota_servicos_letra],
        ['label' => 'Pátio', 'nota' => $voo->nota_patio, 'letra' => $voo->nota_patio_letra],
    ];
@endphp

<div class="flight-details pb-4">
    <header class="page-header mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2 text-white-50">
                    <i class="bi bi-airplane"></i>
                    <span>Registro de voo</span>
                </div>
                <h1 class="h2 fw-bold mb-1">Detalhes do voo {{ $voo->id_voo }}</h1>
                <p class="mb-0 text-white-50">Consulte as informações operacionais e avaliações deste registro.</p>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('voos.edit', $voo) }}" class="btn btn-warning">
                    <i class="bi bi-pencil-square me-1"></i> Editar
                </a>
                <a href="{{ route('voos.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>
    </header>

    <section class="details-card mb-4">
        <div class="section-title">
            <i class="bi bi-info-circle me-2 text-primary"></i>Informações do voo
        </div>
        <div class="p-3 p-md-4">
            <div class="row g-3">
                <div class="col-sm-6 col-lg-4">
                    <div class="info-item">
                        <div class="info-label">ID do voo</div>
                        <p class="info-value font-monospace">{{ $voo->id_voo }}</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="info-item">
                        <div class="info-label">Aeroporto</div>
                        <p class="info-value">{{ $voo->aeroporto?->nome_aeroporto ?? 'Não informado' }}</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="info-item">
                        <div class="info-label">Companhia aérea</div>
                        <p class="info-value">{{ $voo->companhiaAerea?->nome ?? 'Não informada' }}</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="info-item">
                        <div class="info-label">Aeronave</div>
                        <p class="info-value">
                            {{ $voo->aeronave?->modelo ?? 'Não informada' }}
                            @if($voo->aeronave?->fabricante?->nome)
                                <span class="text-muted fw-normal">({{ $voo->aeronave->fabricante->nome }})</span>
                            @endif
                        </p>
                        @if($voo->aeronave)
                            <small class="text-muted">
                                {{ number_format($voo->aeronave->capacidade, 0, ',', '.') }} passageiros ·
                                {{ $porteLabels[$voo->aeronave->porte] ?? $voo->aeronave->porte }}
                            </small>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="info-item">
                        <div class="info-label">Tipo de voo</div>
                        <p class="info-value">
                            <span class="badge {{ $voo->tipo_voo === 'Regular' ? 'text-bg-success' : 'text-bg-primary' }}">
                                {{ $voo->tipo_voo }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="info-item">
                        <div class="info-label">Horário</div>
                        <p class="info-value">{{ $horarioLabels[$voo->horario_voo] ?? $voo->horario_voo }}</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="info-item">
                        <div class="info-label">Quantidade de voos</div>
                        <p class="info-value">{{ number_format($voo->qtd_voos, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="info-item">
                        <div class="info-label">Passageiros por voo</div>
                        <p class="info-value">{{ number_format($voo->qtd_passageiros, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="info-item">
                        <div class="info-label">Total de passageiros</div>
                        <p class="info-value text-primary">{{ number_format($voo->total_passageiros, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="info-item">
                        <div class="info-label">Data de cadastro</div>
                        <p class="info-value">{{ $voo->created_at?->format('d/m/Y H:i') ?? 'Não informada' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if(collect($avaliacoes)->contains(fn ($avaliacao) => $avaliacao['nota'] !== null))
        <section class="details-card">
            <div class="section-title">
                <i class="bi bi-star me-2 text-warning"></i>Avaliações
            </div>
            <div class="p-3 p-md-4">
                <div class="row g-3">
                    @foreach($avaliacoes as $avaliacao)
                        @if($avaliacao['nota'] !== null)
                            <div class="col-6 col-lg-3">
                                <div class="score-card">
                                    <div class="info-label">{{ $avaliacao['label'] }}</div>
                                    <div class="fs-3 fw-bold text-primary">{{ $avaliacao['letra'] }}</div>
                                    <small class="text-muted">{{ number_format($avaliacao['nota'], 1, ',', '.') }} pontos</small>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                @if($voo->media_notas !== null)
                    <div class="alert alert-primary d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mt-3 mb-0">
                        <div>
                            <div class="fw-semibold">Média geral</div>
                            <small>Média aritmética das avaliações</small>
                        </div>
                        <div class="text-sm-end">
                            <span class="fs-3 fw-bold">{{ number_format($voo->media_notas, 1, ',', '.') }}</span>
                            <span class="badge text-bg-primary ms-2">{{ $voo->media_notas_letra }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endif
</div>
@endsection
