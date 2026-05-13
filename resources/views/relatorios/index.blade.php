{{-- resources/views/relatorios/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Relatórios Disponíveis')

@section('content')
<div class="container">
    <div class="mb-4">
        <h3 class="fw-bold">📊 Relatórios Disponíveis</h3>
        <p class="text-muted">
            @if(auth()->user()->tipo == 0)
                Visualizando todos os relatórios do sistema
            @else
                Visualizando relatórios disponíveis para usuários comuns
            @endif
        </p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        @forelse($relatorios as $relatorio)
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                <i class="bi bi-file-text fs-4 text-primary"></i>
                            </div>
                            <h5 class="card-title mb-0">{{ $relatorio->nome }}</h5>
                        </div>
                        @if($relatorio->descricao)
                            <p class="card-text text-muted">{{ $relatorio->descricao }}</p>
                        @endif
                        
                        @if($relatorio->tipo)
                            <div class="mt-2">
                                <span class="badge bg-secondary">
                                    <i class="bi bi-tag"></i> {{ ucfirst(str_replace('_', ' ', $relatorio->tipo)) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        @if($relatorio->tipo == 'companhias_por_aeroporto')
                            <a href="{{ route('relatorios.companhias-por-aeroporto') }}" 
                               class="btn btn-outline-primary btn-sm w-100">
                                <i class="bi bi-eye"></i> Visualizar Relatório
                            </a>
                        @else
                            <button class="btn btn-outline-secondary btn-sm w-100" disabled>
                                <i class="bi bi-clock"></i> Em breve
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    @if(auth()->user()->tipo == 0)
                        Nenhum relatório cadastrado no sistema.
                        <a href="{{ route('admin.relatorios.create') }}" class="alert-link">
                            Criar o primeiro relatório
                        </a>
                    @else
                        Nenhum relatório disponível no momento.
                    @endif
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection