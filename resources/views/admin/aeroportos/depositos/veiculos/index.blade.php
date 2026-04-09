{{-- resources/views/admin/aeroportos/depositos/veiculos/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Veículos - ' . $deposito->nome)

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">🚗 Veículos - {{ $deposito->nome }}</h2>
            <p class="text-muted">Gerencie os veículos do depósito</p>
        </div>
        <div class="col text-end">
            <a href="{{ route('aeroportos.depositos.veiculos.create', [$aeroporto, $deposito]) }}" 
               class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Novo Veículo
            </a>
            <a href="{{ route('aeroportos.depositos.index', $aeroporto) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Placa</th>
                            <th>Modelo/Marca</th>
                            <th>Ano</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Quilometragem</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($veiculos as $veiculo)
                            <tr>
                                <td><strong>{{ $veiculo->placa }}</strong></td>
                                <td>{{ $veiculo->modelo }}<br><small>{{ $veiculo->marca }}</small></td>
                                <td>{{ $veiculo->ano }}</td>
                                <td>
                                    <span class="badge bg-info">{{ ucfirst($veiculo->tipo) }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'disponivel' => 'success',
                                            'em_uso' => 'warning',
                                            'manutencao' => 'danger',
                                            'inativo' => 'secondary'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$veiculo->status] }}">
                                        {{ ucfirst(str_replace('_', ' ', $veiculo->status)) }}
                                    </span>
                                </td>
                                <td>{{ number_format($veiculo->quilometragem, 0, ',', '.') }} km</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('aeroportos.depositos.veiculos.show', [$aeroporto, $deposito, $veiculo]) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('aeroportos.depositos.veiculos.edit', [$aeroporto, $deposito, $veiculo]) }}" 
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('aeroportos.depositos.veiculos.destroy', [$aeroporto, $deposito, $veiculo]) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('Tem certeza?')">
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
                                    <h5 class="mt-2">Nenhum veículo cadastrado</h5>
                                    <a href="{{ route('aeroportos.depositos.veiculos.create', [$aeroporto, $deposito]) }}" 
                                       class="btn btn-primary mt-2">
                                        Cadastrar Primeiro Veículo
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $veiculos->links() }}
        </div>
    </div>
</div>
@endsection