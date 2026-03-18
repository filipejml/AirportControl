@extends('layouts.app')

@section('title', 'Fabricantes')

@section('content')
<style>
.hover-primary:hover {
    color: #0d6efd !important;
    text-decoration: underline !important;
}
</style>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="fw-bold">🏭 Gerenciar Fabricantes</h2>
            <p class="text-muted">Lista de todos os fabricantes de aeronaves cadastrados</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('fabricantes.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Novo Fabricante
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Fabricante</th>
                            <th>País de Origem</th>
                            <th>Qtd. Aeronaves</th>
                            <th>Data de Cadastro</th>
                            <th width="200">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($fabricantes as $fabricante)
                            <tr>
                                <td>{{ $fabricante->id }}</td>
                                <td>
                                    <a href="{{ route('fabricantes.show', $fabricante) }}" 
                                       class="text-decoration-none fw-semibold text-dark hover-primary">
                                        {{ $fabricante->nome }}
                                    </a>
                                </td>
                                <td>
                                    @if($fabricante->pais_origem)
                                        <span class="badge bg-info text-dark">
                                            <i class="bi bi-geo-alt"></i> {{ $fabricante->pais_origem }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Não informado</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        {{ $fabricante->aeronaves_count ?? 0 }} aeronaves
                                    </span>
                                </td>
                                <td>{{ $fabricante->created_at?->format('d/m/Y H:i') ?? 'Data não disponível' }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('fabricantes.edit', $fabricante) }}" 
                                           class="btn btn-sm btn-primary"
                                           title="Editar Fabricante">
                                            <i class="bi bi-pencil"></i> Editar
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger"
                                                title="Excluir Fabricante"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal{{ $fabricante->id }}">
                                            <i class="bi bi-trash"></i> Excluir
                                        </button>
                                    </div>

                                    <!-- Modal de confirmação de exclusão -->
                                    <div class="modal fade" id="deleteModal{{ $fabricante->id }}" tabindex="-1">
                                        <!-- ... conteúdo do modal ... -->
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-exclamation-circle text-muted" style="font-size: 3rem;"></i>
                                    <h5 class="text-muted mt-3">Nenhum fabricante cadastrado ainda</h5>
                                    <p class="text-muted mb-3">Comece cadastrando o primeiro fabricante de aeronaves.</p>
                                    <a href="{{ route('fabricantes.create') }}" class="btn btn-primary">
                                        <i class="bi bi-plus-circle"></i> Cadastrar Primeiro Fabricante
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