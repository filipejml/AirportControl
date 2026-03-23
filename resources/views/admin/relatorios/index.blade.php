{{-- resources/views/admin/relatorios/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Controle de Relatórios')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold">📊 Controle de Relatórios</h3>
            <p class="text-muted">Gerencie quais relatórios são visíveis para usuários comuns</p>
        </div>
        <a href="{{ route('admin.relatorios.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Novo Relatório
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th>Visível para Usuários</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($relatorios as $relatorio)
                            <tr>
                                <td>{{ $relatorio->id }}</td>
                                <td><strong>{{ $relatorio->nome }}</strong></td>
                                <td>{{ $relatorio->descricao ?? '—' }}</td>
                                <td>
                                    @if($relatorio->visivel_usuario)
                                        <span class="badge bg-success">✓ Visível</span>
                                    @else
                                        <span class="badge bg-secondary">✗ Oculto</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.relatorios.edit', $relatorio) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteModal{{ $relatorio->id }}">
                                        <i class="bi bi-trash"></i> Excluir
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal de confirmação de exclusão -->
                            <div class="modal fade" id="deleteModal{{ $relatorio->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Confirmar exclusão</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Tem certeza que deseja excluir o relatório <strong>{{ $relatorio->nome }}</strong>?</p>
                                            <p class="text-danger small">Esta ação não pode ser desfeita.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <form action="{{ route('admin.relatorios.destroy', $relatorio) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Excluir</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Nenhum relatório cadastrado.
                                    <a href="{{ route('admin.relatorios.create') }}" class="text-primary">Criar o primeiro relatório</a>
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