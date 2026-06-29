@extends('layouts.app')

@section('title', 'Depósitos - Airport Manager')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <span class="text-uppercase text-primary fw-bold small">Recursos aeroportuários</span>
            <h1 class="h2 fw-bold mb-1 mt-1">
                <i class="bi bi-box-seam me-2"></i>Depósitos
            </h1>
            <p class="text-muted mb-0">Visão geral dos depósitos e veículos por aeroporto.</p>
        </div>
        <a href="{{ route('aeroportos.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-geo-alt me-1"></i> Gerenciar aeroportos
        </a>
    </div>

    <div class="row g-3 mb-4">
        @foreach([
            ['Aeroportos', $estatisticas['total_aeroportos'], 'geo-alt-fill', 'primary'],
            ['Com depósitos', $estatisticas['aeroportos_com_depositos'], 'buildings', 'success'],
            ['Total de depósitos', $estatisticas['total_depositos'], 'box-seam', 'warning'],
            ['Total de veículos', $estatisticas['total_veiculos'], 'truck', 'info'],
        ] as [$rotulo, $valor, $icone, $cor])
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-{{ $cor }}">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted">{{ $rotulo }}</div>
                            <div class="h2 fw-bold mb-0">{{ number_format($valor, 0, ',', '.') }}</div>
                        </div>
                        <i class="bi bi-{{ $icone }} fs-1 text-{{ $cor }} opacity-75"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Aeroporto</th>
                        <th class="text-center">Depósitos</th>
                        <th class="text-center">Veículos</th>
                        <th>Detalhamento</th>
                        <th class="text-end pe-4">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($aeroportos as $aeroporto)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $aeroporto->nome_aeroporto }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary rounded-pill">{{ $aeroporto->depositos_count }}</span>
                            </td>
                            <td class="text-center">
                                {{ number_format($aeroporto->depositos->sum('veiculos_count'), 0, ',', '.') }}
                            </td>
                            <td>
                                @forelse($aeroporto->depositos as $deposito)
                                    <span class="badge bg-light text-dark border me-1 mb-1">
                                        {{ $deposito->nome }}: {{ $deposito->veiculos_count }}
                                    </span>
                                @empty
                                    <span class="text-muted small">Nenhum depósito cadastrado</span>
                                @endforelse
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('aeroportos.depositos.index', $aeroporto) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Gerenciar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                Nenhum aeroporto cadastrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
