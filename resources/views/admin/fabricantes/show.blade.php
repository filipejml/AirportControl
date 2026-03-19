@extends('layouts.app')

@section('title', 'Detalhes do Fabricante')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="fw-bold">🏭 {{ $fabricante->nome }}</h2>
            <p class="text-muted">
                @if($fabricante->pais_origem)
                    <i class="bi bi-geo-alt"></i> {{ $fabricante->pais_origem }}
                @else
                    País de origem não informado
                @endif
            </p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('fabricantes.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar para Lista
            </a>
            <a href="{{ route('fabricantes.edit', $fabricante) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Editar
            </a>
        </div>
    </div>

    <!-- Card com estatísticas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total de Aeronaves</h5>
                    <h2 class="display-4">{{ $fabricante->aeronaves_count }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Capacidade Total</h5>
                    <h2 class="display-4">{{ $fabricante->aeronaves->sum('capacidade') }}</h2>
                    <small>passageiros</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Média de Capacidade</h5>
                    <h2 class="display-4">{{ $fabricante->aeronaves->avg('capacidade') ? round($fabricante->aeronaves->avg('capacidade')) : 0 }}</h2>
                    <small>passageiros por aeronave</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Aeronaves -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">✈️ Modelos de Aeronaves do Fabricante</h5>
        </div>
        <div class="card-body">
            @if($fabricante->aeronaves->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Modelo</th>
                                <th>Capacidade</th>
                                <th>Porte</th>
                                <th>Companhias que operam</th>
                                <th>Data de Cadastro</th>
                                <th width="200">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fabricante->aeronaves as $aeronave)
                                <tr>
                                    <td>{{ $aeronave->id }}</td>
                                    <td class="fw-semibold">{{ $aeronave->modelo }}</td>
                                    <td>{{ $aeronave->capacidade }} passageiros</td>
                                    <td>
                                        @if($aeronave->porte == 'PC')
                                            <span class="badge bg-info">PC</span>
                                        @elseif($aeronave->porte == 'MC')
                                            <span class="badge bg-warning text-dark">MC</span>
                                        @elseif($aeronave->porte == 'LC')
                                            <span class="badge bg-danger">LC</span>
                                        @endif
                                    </td>
                                    <td>
                                        @foreach($aeronave->companhias as $companhia)
                                            <span class="badge bg-info text-dark mb-1">{{ $companhia->nome }}</span>
                                        @endforeach
                                    </td>
                                    <td>{{ $aeronave->created_at?->format('d/m/Y H:i') ?? 'Data não disponível' }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('aeronaves.edit', $aeronave) }}" 
                                               class="btn btn-sm btn-primary"
                                               title="Editar Aeronave">
                                                <i class="bi bi-pencil"></i> Editar
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger"
                                                    title="Excluir Aeronave"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteAeronaveModal{{ $aeronave->id }}">
                                                <i class="bi bi-trash"></i> Excluir
                                            </button>
                                        </div>

                                        <!-- Modal de confirmação de exclusão -->
                                        <div class="modal fade" id="deleteAeronaveModal{{ $aeronave->id }}" tabindex="-1">
                                            <!-- ... conteúdo do modal ... -->
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
                    <h5 class="text-muted mt-3">Nenhuma aeronave cadastrada para este fabricante</h5>
                    <a href="{{ route('aeronaves.create') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-plus-circle"></i> Cadastrar Nova Aeronave
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection