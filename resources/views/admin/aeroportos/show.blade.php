@extends('layouts.app')

@section('title', 'Detalhes do Aeroporto')

@section('content')
<style>
/* Estilos para os botões de ação */
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
.btn-action-group {
    gap: 0.5rem !important;
}

/* Estilo para links de companhias */
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
.companhia-link i {
    font-size: 0.9rem;
    margin-right: 4px;
    opacity: 0.7;
}
.companhia-link:hover i {
    opacity: 1;
}

/* Ajustes responsivos */
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
            <p class="text-muted">Detalhes do aeroporto</p>
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
        </div>
    </div>

    <!-- Card com estatísticas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total de Companhias</h5>
                    <h2 class="display-4">{{ $totalCompanhias ?? $aeroporto->companhias->count() }}</h2>
                    <small>operando no aeroporto</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total de Aeronaves</h5>
                    <h2 class="display-4">{{ $totalAeronaves }}</h2>
                    <small>operando no aeroporto</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Média de Aeronaves</h5>
                    <h2 class="display-4">{{ $mediaAeronaves }}</h2>
                    <small>por companhia</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Companhias -->
    <div class="card shadow-sm">
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
                                            {{ $companhia->aeronaves_count ?? 0 }} aeronaves
                                        </span>
                                    </td>
                                    <td>{{ $companhia->created_at?->format('d/m/Y H:i') ?? 'Data não disponível' }}</td>
                                    <td>
                                        <div class="d-flex gap-2 btn-action-group">
                                            <a href="{{ route('companhias.edit', $companhia) }}" 
                                               class="btn btn-primary btn-action"
                                               title="Editar companhia">
                                                <i class="bi bi-pencil"></i>
                                                <span>Editar</span>
                                            </a>
                                            <form action="{{ route('companhias.destroy', $companhia) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Tem certeza que deseja excluir a companhia {{ $companhia->nome }}? Esta ação não pode ser desfeita.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-danger btn-action"
                                                        title="Excluir companhia">
                                                    <i class="bi bi-trash"></i>
                                                    <span>Excluir</span>
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
                    <a href="{{ route('aeroportos.edit', $aeroporto) }}" class="btn btn-primary btn-action mt-3">
                        <i class="bi bi-pencil"></i>
                        <span>Associar Companhias</span>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection