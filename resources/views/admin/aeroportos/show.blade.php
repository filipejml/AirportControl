{{-- resources/views/admin/aeroportos/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detalhes do Aeroporto')

@section('content')
<style>
.btn-action {
    padding: 0.5rem 1rem;
    font-size: 0.95rem;
    line-height: 1.5;
    border-radius: 0.375rem;
    min-width: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.2s ease;
}
.btn-action i {
    font-size: 1.1rem;
}
.btn-action span {
    display: inline-block;
}
.btn-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.companhia-link {
    color: #0d6efd;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s ease;
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
}
.companhia-link:hover {
    color: #0a58ca;
    background-color: rgba(13, 110, 253, 0.1);
    text-decoration: underline;
    transform: translateX(2px);
}
@media (max-width: 768px) {
    .btn-action span {
        display: none;
    }
    .btn-action {
        min-width: 44px;
        padding: 0.5rem;
    }
}
</style>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="fw-bold">🛫 {{ $aeroporto->nome_aeroporto }}</h2>
            <p class="text-muted">Detalhes do aeroporto - Visualização Administrativa</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('aeroportos.index') }}" class="btn btn-outline-secondary btn-action">
                <i class="bi bi-arrow-left"></i>
                <span>Voltar</span>
            </a>
            <a href="{{ route('aeroportos.edit', $aeroporto) }}" class="btn btn-primary btn-action">
                <i class="bi bi-pencil"></i>
                <span>Editar</span>
            </a>
            <form action="{{ route('aeroportos.destroy', $aeroporto) }}" 
                  method="POST" 
                  class="d-inline"
                  onsubmit="return confirm('Tem certeza que deseja excluir este aeroporto?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-action">
                    <i class="bi bi-trash"></i>
                    <span>Excluir</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Card com estatísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total de Companhias</h5>
                    <h2 class="display-4">{{ $aeroporto->companhias->count() }}</h2>
                    <small>operando no aeroporto</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total de Depósitos</h5>
                    <h2 class="display-4">{{ $aeroporto->depositos->count() }}</h2>
                    <small>depósitos cadastrados</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Total de Veículos</h5>
                    <h2 class="display-4">{{ $aeroporto->veiculos->count() }}</h2>
                    <small>veículos nos depósitos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Data Cadastro</h5>
                    <h5 class="mb-0">{{ $aeroporto->created_at?->format('d/m/Y') ?? 'N/A' }}</h5>
                    <small>criado em</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Companhias -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">✈️ Companhias que Operam neste Aeroporto</h5>
            @if($aeroporto->companhias->count() > 0)
                <span class="badge bg-primary rounded-pill px-3 py-2">
                    Total: {{ $aeroporto->companhias->count() }} companhias
                </span>
            @endif
        </div>
        <div class="card-body">
            @if($aeroporto->companhias->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Companhia</th>
                                <th>Qtd. Aeronaves</th>
                                <th>Data de Cadastro</th>
                                <th width="180">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($aeroporto->companhias as $companhia)
                                <tr>
                                    <td><span class="fw-semibold">#{{ $companhia->id }}</span></td>
                                    <td>
                                        <a href="{{ route('companhias.show', $companhia) }}" 
                                           class="companhia-link"
                                           title="Ver detalhes da companhia {{ $companhia->nome }}">
                                            <i class="bi bi-building"></i>
                                            {{ $companhia->nome }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary rounded-pill px-3 py-2">
                                            {{ $companhia->aeronaves_count ?? $companhia->aeronaves->count() }} aeronaves
                                        </span>
                                    </td>
                                    <td>{{ $companhia->created_at?->format('d/m/Y H:i') ?? 'Data não disponível' }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('companhias.edit', $companhia) }}" 
                                               class="btn btn-sm btn-primary"
                                               title="Editar companhia">
                                                <i class="bi bi-pencil"></i> Editar
                                            </a>
                                            <form action="{{ route('companhias.destroy', $companhia) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Tem certeza que deseja excluir a companhia {{ $companhia->nome }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i> Excluir
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-exclamation-circle text-muted" style="font-size: 3rem;"></i>
                    <h5 class="text-muted mt-3">Nenhuma companhia associada a este aeroporto</h5>
                    <a href="{{ route('aeroportos.edit', $aeroporto) }}" class="btn btn-primary mt-3">
                        <i class="bi bi-pencil"></i> Associar Companhias
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Depósitos e Veículos -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">🏢 Depósitos e Veículos</h5>
            <div>
                <a href="{{ route('aeroportos.depositos.create', $aeroporto) }}" class="btn btn-sm btn-success me-2">
                    <i class="bi bi-plus-circle"></i> Novo Depósito
                </a>
                <a href="{{ route('aeroportos.depositos.index', $aeroporto) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-box"></i> Gerenciar Depósitos
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($aeroporto->depositos->count() > 0)
                <div class="row">
                    @foreach($aeroporto->depositos as $deposito)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $deposito->nome }}</h6>
                                        <small class="text-muted">Código: {{ $deposito->codigo }}</small>
                                    </div>
                                    <span class="badge bg-{{ $deposito->status === 'ativo' ? 'success' : ($deposito->status === 'manutencao' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($deposito->status) }}
                                    </span>
                                </div>
                                
                                <p class="mb-2 mt-2 small">
                                    <i class="bi bi-geo-alt"></i> {{ $deposito->localizacao ?? 'Localização não informada' }}
                                </p>
                                
                                <div class="mt-2">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <small class="text-muted">Total</small>
                                            <strong class="d-block">{{ $deposito->veiculos->count() }}</strong>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted">Disponíveis</small>
                                            <strong class="d-block text-success">
                                                {{ $deposito->veiculos->where('status', 'disponivel')->count() }}
                                            </strong>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted">Em Uso</small>
                                            <strong class="d-block text-warning">
                                                {{ $deposito->veiculos->where('status', 'em_uso')->count() }}
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <a href="{{ route('aeroportos.depositos.show', [$aeroporto, $deposito]) }}" 
                                       class="btn btn-sm btn-outline-primary w-100">
                                        <i class="bi bi-eye"></i> Ver Detalhes
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-box text-muted" style="font-size: 3rem;"></i>
                    <h5 class="text-muted mt-3">Nenhum depósito cadastrado</h5>
                    <p class="text-muted">Crie depósitos para armazenar os veículos do aeroporto.</p>
                    <a href="{{ route('aeroportos.depositos.create', $aeroporto) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Criar Primeiro Depósito
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection