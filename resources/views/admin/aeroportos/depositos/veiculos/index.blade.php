{{-- resources/views/admin/aeroportos/depositos/veiculos/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Veículos - ' . $deposito->nome)

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">🚗 Frota de Veículos</h2>
            <p class="text-muted">Depósito: {{ $deposito->nome }} - {{ $aeroporto->nome_aeroporto }}</p>
        </div>
        <div class="col text-end">
            <a href="{{ route('aeroportos.depositos.veiculos.create', [$aeroporto, $deposito]) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Novo Veículo
            </a>
            <a href="{{ route('aeroportos.depositos.index', $aeroporto) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    {{-- Cards de Resumo --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total de Veículos</h6>
                            <h2 class="mb-0">{{ $veiculos->total() }}</h2>
                        </div>
                        <i class="bi bi-truck fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Disponíveis</h6>
                            <h2 class="mb-0">{{ $deposito->veiculos->where('status', 'disponivel')->count() }}</h2>
                        </div>
                        <i class="bi bi-check-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Indisponíveis</h6>
                            <h2 class="mb-0">{{ $deposito->veiculos->where('status', 'indisponivel')->count() }}</h2>
                        </div>
                        <i class="bi bi-x-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tipo de Veículo</label>
                    <select name="tipo" class="form-select">
                        <option value="">Todos</option>
                        @foreach(\App\Models\Veiculo::TIPOS_VEICULOS as $key => $tipo)
                            <option value="{{ $key }}" {{ request('tipo') == $key ? 'selected' : '' }}>
                                {{ $tipo['nome'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="disponivel" {{ request('status') == 'disponivel' ? 'selected' : '' }}>Disponível</option>
                        <option value="indisponivel" {{ request('status') == 'indisponivel' ? 'selected' : '' }}>Indisponível</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabela de Veículos --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Tipo</th>
                            <th>Modelo / Fabricante</th>
                            <th>Ano</th>
                            <th>Capacidade</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($veiculos as $veiculo)
                            <tr>
                                <td><strong>{{ $veiculo->codigo }}</strong></td>
                                <td>
                                    <span class="badge bg-{{ $veiculo->tipo_cor }}">
                                        <i class="bi {{ $veiculo->tipo_icone }}"></i>
                                        {{ $veiculo->tipo_nome }}
                                    </span>
                                </td>
                                <td>
                                    {{ $veiculo->modelo ?? '-' }}<br>
                                    <small class="text-muted">{{ $veiculo->fabricante ?? '-' }}</small>
                                </td>
                                <td>{{ $veiculo->ano_fabricacao ?? '-' }}</td>
                                <td>
                                    @if($veiculo->capacidade_operacional)
                                        {{ number_format($veiculo->capacidade_operacional, 0, ',', '.') }}
                                        {{ $veiculo->unidade_capacidade ?? '' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($veiculo->status == 'disponivel')
                                        <span class="badge bg-success">✅ Disponível</span>
                                    @else
                                        <span class="badge bg-secondary">❌ Indisponível</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('aeroportos.depositos.veiculos.show', [$aeroporto, $deposito, $veiculo]) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Ver detalhes">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('aeroportos.depositos.veiculos.edit', [$aeroporto, $deposito, $veiculo]) }}" 
                                           class="btn btn-sm btn-outline-secondary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('aeroportos.depositos.veiculos.destroy', [$aeroporto, $deposito, $veiculo]) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('Tem certeza que deseja excluir este veículo?')" title="Excluir">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-car-front text-muted fs-1"></i>
                                    <h5 class="mt-2">Nenhum veículo encontrado</h5>
                                    <a href="{{ route('aeroportos.depositos.veiculos.create', [$aeroporto, $deposito]) }}" class="btn btn-primary mt-2">
                                        Cadastrar Primeiro Veículo
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="mt-3">
        {{ $veiculos->withQueryString()->links() }}
    </div>
</div>
@endsection