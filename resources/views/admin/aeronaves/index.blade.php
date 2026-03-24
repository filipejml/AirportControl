@extends('layouts.app')

@section('title', 'Aeronaves')

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
            <h2 class="fw-bold">✈️ Gerenciar Aeronaves</h2>
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
                            <th>Modelo</th>
                            <th>Fabricante</th>
                            <th>Capacidade</th>
                            <th>Porte</th>
                            <th>Companhias</th>
                            <th width="180">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aeronaves as $aeronave)
                            <tr>
                                <td><span class="fw-semibold">#{{ $aeronave->id }}</span></td>
                                <td>
                                    <a href="{{ route('aeronaves.show', $aeronave->id) }}" 
                                       class="text-decoration-none fw-semibold text-dark hover-primary">
                                        {{ $aeronave->modelo }}
                                    </a>
                                </td>
                                <td>
                                    @if($aeronave->fabricante)
                                        <a href="{{ route('fabricantes.show', $aeronave->fabricante) }}" 
                                           class="text-decoration-none text-dark hover-primary">
                                            {{ $aeronave->fabricante->nome }}
                                        </a>
                                    @else
                                        <span class="badge bg-secondary">Não informado</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-success rounded-pill px-3 py-2">
                                        {{ $aeronave->capacidade }} passageiros
                                    </span>
                                </td>
                                <td>
                                    @if($aeronave->porte == 'PC')
                                        <span class="badge bg-info rounded-pill px-3 py-2">PC - Pequeno Porte</span>
                                    @elseif($aeronave->porte == 'MC')
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">MC - Médio Porte</span>
                                    @elseif($aeronave->porte == 'LC')
                                        <span class="badge bg-danger rounded-pill px-3 py-2">LC - Grande Porte</span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">Não classificado</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $totalCompanhias = $aeronave->companhias->count();
                                    @endphp
                                    @if($totalCompanhias > 0)
                                        <span class="badge bg-primary rounded-pill px-3 py-2">
                                            <i class="bi bi-building"></i> {{ $totalCompanhias }} 
                                            {{ $totalCompanhias == 1 ? 'companhia' : 'companhias' }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">
                                            <i class="bi bi-building"></i> Nenhuma
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('aeronaves.edit', $aeronave->id) }}" 
                                           class="btn btn-primary btn-action"
                                           title="Editar Aeronave">
                                            <i class="bi bi-pencil"></i>
                                            <span>Editar</span>
                                        </a>
                                        
                                        <!-- Formulário de exclusão direto (sem modal) -->
                                        <form action="{{ route('aeronaves.destroy', $aeronave->id) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Tem certeza que deseja excluir a aeronave {{ $aeronave->modelo }}? Esta ação não pode ser desfeita.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-danger btn-action"
                                                    title="Excluir Aeronave">
                                                <i class="bi bi-trash"></i>
                                                <span>Excluir</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-exclamation-circle text-muted" style="font-size: 3rem;"></i>
                                    <h5 class="text-muted mt-3">Nenhuma aeronave cadastrada ainda</h5>
                                    <p class="text-muted mb-3">Comece cadastrando a primeira aeronave.</p>
                                    <a href="{{ route('aeronaves.create') }}" class="btn btn-primary btn-lg">
                                        <i class="bi bi-plus-circle"></i> Cadastrar Primeira Aeronave
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