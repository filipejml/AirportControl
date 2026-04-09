{{-- resources/views/admin/aeroportos/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Aeroportos')

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
            <h2 class="fw-bold">🛫 Gerenciar Aeroportos</h2>
            <p class="text-muted">Lista de todos os aeroportos cadastrados</p>
        </div>
        <div class="col-md-4 text-end">
            {{-- ALTERADO: usar a nova rota do wizard --}}
            <a href="{{ route('aeroportos.create.step1') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Novo Aeroporto
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
                            <th>Aeroporto</th>
                            <th>Companhias Atendidas</th>
                            <th>Depósitos</th>
                            <th>Veículos</th>
                            <th>Data de Cadastro</th>
                            <th width="200">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aeroportos as $aeroporto)
                            <tr>
                                <td>{{ $aeroporto->id }}</td>
                                <td>
                                    <a href="{{ route('aeroportos.show', $aeroporto) }}" 
                                       class="text-decoration-none fw-semibold text-dark hover-primary">
                                        {{ $aeroporto->nome_aeroporto }}
                                    </a>
                                </td>
                                <td>
                                    @if($aeroporto->companhias->count() > 0)
                                        <span class="badge bg-primary">{{ $aeroporto->companhias->count() }} companhia(s)</span>
                                    @else
                                        <span class="badge bg-secondary">Nenhuma companhia</span>
                                    @endif
                                </td>
                                <td>
                                    @if($aeroporto->depositos->count() > 0)
                                        <span class="badge bg-info">{{ $aeroporto->depositos->count() }} depósito(s)</span>
                                    @else
                                        <span class="badge bg-secondary">Nenhum depósito</span>
                                    @endif
                                </td>
                                <td>
                                    @if($aeroporto->veiculos->count() > 0)
                                        <span class="badge bg-success">{{ $aeroporto->veiculos->count() }} veículo(s)</span>
                                    @else
                                        <span class="badge bg-secondary">Nenhum veículo</span>
                                    @endif
                                </td>
                                <td>{{ $aeroporto->created_at?->format('d/m/Y H:i') ?? 'Data não disponível' }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('aeroportos.show', $aeroporto) }}" 
                                           class="btn btn-sm btn-info"
                                           title="Ver Detalhes">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('aeroportos.edit', $aeroporto) }}" 
                                           class="btn btn-sm btn-primary"
                                           title="Editar Aeroporto">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="{{ route('aeroportos.depositos.index', $aeroporto) }}" 
                                           class="btn btn-sm btn-warning"
                                           title="Gerenciar Depósitos">
                                            <i class="bi bi-building"></i>
                                        </a>
                                        
                                        <form action="{{ route('aeroportos.destroy', $aeroporto) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Tem certeza que deseja excluir o aeroporto {{ $aeroporto->nome_aeroporto }}? Esta ação não pode ser desfeita.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-danger"
                                                    title="Excluir Aeroporto">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-exclamation-circle text-muted" style="font-size: 3rem;"></i>
                                    <h5 class="text-muted mt-3">Nenhum aeroporto cadastrado ainda</h5>
                                    <p class="text-muted mb-3">Comece cadastrando o primeiro aeroporto.</p>
                                    <a href="{{ route('aeroportos.create.step1') }}" class="btn btn-primary">
                                        <i class="bi bi-plus-circle"></i> Cadastrar Primeiro Aeroporto
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