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

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Data Cadastro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($veiculos as $veiculo)
                            <tr>
                                <td><strong>{{ $veiculo->codigo }}</strong></td>
                                <td>
                                    <span class="badge bg-primary">
                                        <i class="bi {{ $veiculo->tipo_icone }}"></i>
                                        {{ $veiculo->tipo_nome }}
                                    </span>
                                </td>
                                <td>
                                    @if($veiculo->status == 'disponivel')
                                        <span class="badge bg-success">✅ Disponível</span>
                                    @else
                                        <span class="badge bg-secondary">❌ Indisponível</span>
                                    @endif
                                </td>
                                <td>{{ $veiculo->created_at->format('d/m/Y H:i') }}</td>
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
                                <td colspan="5" class="text-center py-5">
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