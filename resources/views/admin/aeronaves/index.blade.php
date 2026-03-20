@extends('layouts.app')

@section('title', 'Aeronaves')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="fw-bold">Gerenciar Aeronaves</h2>
            <p class="text-muted">Lista de todas as aeronaves cadastradas no sistema</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('aeronaves.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nova Aeronave
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <a href="{{ route('aeronaves.index', ['sort' => 'id', 'direction' => ($sortField == 'id' && $sortDirection == 'asc') ? 'desc' : 'asc']) }}" 
                                   class="text-decoration-none text-dark d-flex align-items-center">
                                    ID
                                    @if($sortField == 'id')
                                        <i class="bi bi-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up ms-1 text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('aeronaves.index', ['sort' => 'modelo', 'direction' => ($sortField == 'modelo' && $sortDirection == 'asc') ? 'desc' : 'asc']) }}" 
                                   class="text-decoration-none text-dark d-flex align-items-center">
                                    Modelo
                                    @if($sortField == 'modelo')
                                        <i class="bi bi-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up ms-1 text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('aeronaves.index', ['sort' => 'fabricante', 'direction' => ($sortField == 'fabricante' && $sortDirection == 'asc') ? 'desc' : 'asc']) }}" 
                                   class="text-decoration-none text-dark d-flex align-items-center">
                                    Fabricante
                                    @if($sortField == 'fabricante')
                                        <i class="bi bi-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up ms-1 text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('aeronaves.index', ['sort' => 'capacidade', 'direction' => ($sortField == 'capacidade' && $sortDirection == 'asc') ? 'desc' : 'asc']) }}" 
                                   class="text-decoration-none text-dark d-flex align-items-center">
                                    Capacidade
                                    @if($sortField == 'capacidade')
                                        <i class="bi bi-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up ms-1 text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('aeronaves.index', ['sort' => 'porte', 'direction' => ($sortField == 'porte' && $sortDirection == 'asc') ? 'desc' : 'asc']) }}" 
                                   class="text-decoration-none text-dark d-flex align-items-center">
                                    Porte
                                    @if($sortField == 'porte')
                                        <i class="bi bi-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up ms-1 text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('aeronaves.index', ['sort' => 'companhias_count', 'direction' => ($sortField == 'companhias_count' && $sortDirection == 'asc') ? 'desc' : 'asc']) }}" 
                                   class="text-decoration-none text-dark d-flex align-items-center">
                                    Companhias
                                    @if($sortField == 'companhias_count')
                                        <i class="bi bi-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up ms-1 text-muted"></i>
                                    @endif
                                </a>
                            </th>
                            <th width="150">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aeronaves as $aeronave)
                            <tr>
                                <td>{{ $aeronave->id }}</td>
                                <td class="fw-semibold">
                                    <a href="{{ route('aeronaves.show', $aeronave->id) }}" class="text-decoration-none text-dark">
                                        {{ $aeronave->modelo }}
                                    </a>
                                </td>
                                <td>
                                    @if($aeronave->fabricante)
                                        <a href="{{ route('fabricantes.show', $aeronave->fabricante) }}" 
                                           class="text-decoration-none">
                                            {{ $aeronave->fabricante->nome }}
                                        </a>
                                    @else
                                        <span class="badge bg-secondary">Não informado</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        {{ $aeronave->capacidade }} passageiros
                                    </span>
                                </td>
                                <td>
                                    @if($aeronave->porte == 'PC')
                                        <span class="badge bg-info">PC - Pequeno Porte</span>
                                    @elseif($aeronave->porte == 'MC')
                                        <span class="badge bg-warning text-dark">MC - Médio Porte</span>
                                    @elseif($aeronave->porte == 'LC')
                                        <span class="badge bg-danger">LC - Grande Porte</span>
                                    @else
                                        <span class="badge bg-secondary">Não classificado</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $totalCompanhias = $aeronave->companhias->count();
                                    @endphp
                                    @if($totalCompanhias > 0)
                                        <span class="badge bg-primary">
                                            <i class="bi bi-building"></i> {{ $totalCompanhias }} 
                                            {{ $totalCompanhias == 1 ? 'companhia' : 'companhias' }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-building"></i> Nenhuma
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('aeronaves.edit', $aeronave->id) }}" 
                                            class="btn btn-outline-primary"
                                            title="Editar">
                                                <i class="bi bi-pencil me-1"></i> Editar
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-danger"
                                                title="Excluir"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal{{ $aeronave->id }}">
                                            <i class="bi bi-trash me-1"></i> Excluir
                                        </button>
                                    </div>

                                    <!-- Modal de confirmação de exclusão -->
                                    <div class="modal fade" id="deleteModal{{ $aeronave->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Confirmar Exclusão</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Tem certeza que deseja excluir a aeronave <strong>{{ $aeronave->modelo }}</strong>?</p>
                                                    <p class="text-danger mb-0"><small>Esta ação não poderá ser desfeita.</small></p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <form action="{{ route('aeronaves.destroy', $aeronave) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Confirmar Exclusão</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="bi bi-exclamation-circle text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2">Nenhuma aeronave cadastrada ainda.</p>
                                    <a href="{{ route('aeronaves.create') }}" class="btn btn-primary btn-sm">
                                        Cadastrar primeira aeronave
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