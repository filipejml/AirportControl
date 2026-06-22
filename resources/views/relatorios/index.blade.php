@extends('layouts.app')

@section('title', 'Relatórios Disponíveis')

@section('content')
<div class="container">
    <div class="mb-4">
        <h3 class="fw-bold">📊 Relatórios Disponíveis</h3>
        <p class="text-muted">Consulte os relatórios habilitados pela administração.</p>
    </div>

    <div class="row g-4">
        @forelse($relatorios as $relatorio)
            @if($relatorio->route !== '#')
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                    @if($relatorio->tipo === \App\Models\Relatorio::TIPO_COMPANHIAS_POR_AEROPORTO)
                                        <i class="bi bi-building fs-4 text-primary"></i>
                                    @elseif($relatorio->tipo === \App\Models\Relatorio::TIPO_VOOS_POR_AEROPORTO)
                                        <i class="bi bi-airplane fs-4 text-primary"></i>
                                    @elseif($relatorio->tipo === \App\Models\Relatorio::TIPO_DESEMPENHO_COMPANHIAS)
                                        <i class="bi bi-graph-up-arrow fs-4 text-primary"></i>
                                    @elseif($relatorio->tipo === \App\Models\Relatorio::TIPO_MOVIMENTACAO_POR_PERIODO)
                                        <i class="bi bi-calendar3 fs-4 text-primary"></i>
                                    @endif
                                </div>
                                <h5 class="card-title mb-0">{{ $relatorio->nome }}</h5>
                            </div>

                            @if($relatorio->descricao)
                                <p class="card-text text-muted">{{ $relatorio->descricao }}</p>
                            @endif
                        </div>
                        <div class="card-footer bg-transparent border-top-0">
                            <a href="{{ route($relatorio->route) }}" class="btn btn-outline-primary btn-sm w-100">
                                <i class="bi bi-eye"></i> Visualizar relatório
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center mb-0">
                    Nenhum relatório está disponível no momento.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
