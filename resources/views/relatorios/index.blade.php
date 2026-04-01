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
                        <h5 class="card-title">{{ $relatorio->nome }}</h5>
                        @if($relatorio->descricao)
                            <p class="card-text text-muted">{{ $relatorio->descricao }}</p>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <a href="#" class="btn btn-outline-primary btn-sm w-100">
                            Visualizar Relatório
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    @if(auth()->user()->tipo == 0)
                        Nenhum relatório cadastrado no sistema.
                    @else
                        Nenhum relatório disponível no momento.
                    @endif
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection