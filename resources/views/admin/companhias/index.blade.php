@extends('layouts.app')

@section('title', 'Companhias Aéreas')

@section('content')
<style>
.hover-primary:hover {
    color: #0d6efd !important;
    text-decoration: underline !important;
    cursor: pointer;
}
.btn-action {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    line-height: 1.5;
    border-radius: 0.25rem;
    min-width: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}
.btn-action i {
    font-size: 1rem;
}
.btn-action span {
    display: inline-block;
}
@media (max-width: 768px) {
    .btn-action span {
        display: none;
    }
    .btn-action {
        min-width: 38px;
    }
}
</style>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="fw-bold">✈️ Gerenciar Companhias Aéreas</h2>
            <p class="text-muted">Lista de todas as companhias aéreas cadastradas</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('companhias.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nova Companhia
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                         <tr>
                            <th>ID</th>
                            <th>Companhia</th>
                            <th>Código</th>
                            <th>Qtd. Aeronaves</th>
                            <th width="180">Ações</th>
                         </tr>
                    </thead>
                    <tbody>
                        @forelse($companhias as $companhia)
                             <tr>
                                <td><span class="fw-semibold">#{{ $companhia->id }}</span></td>
                                <td>
                                    <a href="{{ route('companhias.show', $companhia->id) }}" 
                                       class="text-decoration-none fw-semibold text-dark hover-primary">
                                        {{ $companhia->nome }}
                                    </a>
                                </td>
                                <td>
                                    @if($companhia->codigo)
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">
                                            <i class="bi bi-tag"></i> {{ $companhia->codigo }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary rounded-pill px-3 py-2">
                                        {{ $companhia->aeronaves_count ?? 0 }} aeronaves
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('companhias.edit', $companhia->id) }}" 
                                           class="btn btn-primary btn-action"
                                           title="Editar Companhia">
                                            <i class="bi bi-pencil"></i>
                                            <span>Editar</span>
                                        </a>
                                        
                                        <!-- Formulário de exclusão direto (sem modal) -->
                                        <form action="{{ route('companhias.destroy', $companhia->id) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Tem certeza que deseja excluir a companhia {{ $companhia->nome }}? Esta ação não pode ser desfeita.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-danger btn-action"
                                                    title="Excluir Companhia">
                                                <i class="bi bi-trash"></i>
                                                <span>Excluir</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                             </tr>
                        @empty
                             <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="bi bi-exclamation-circle text-muted" style="font-size: 3rem;"></i>
                                    <h5 class="text-muted mt-3">Nenhuma companhia aérea cadastrada ainda</h5>
                                    <p class="text-muted mb-3">Comece cadastrando a primeira companhia aérea.</p>
                                    <a href="{{ route('companhias.create') }}" class="btn btn-primary btn-lg">
                                        <i class="bi bi-plus-circle"></i> Cadastrar Primeira Companhia
                                    </a>
                                </td>
                             </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection