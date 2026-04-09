{{-- resources/views/admin/aeroportos/depositos/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detalhes do Depósito - ' . $deposito->nome)

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">🏢 {{ $deposito->nome }}</h2>
            <p class="text-muted">Detalhes do depósito</p>
        </div>
        <div class="col text-end">
            <a href="{{ route('aeroportos.depositos.index', $aeroporto) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
            <a href="{{ route('aeroportos.depositos.edit', [$aeroporto, $deposito]) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Editar
            </a>
        </div>
    </div>

    <!-- Informações do Depósito -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Código</h5>
                    <h3 class="mb-0">{{ $deposito->codigo }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Status</h5>
                    <h3 class="mb-0">
                        @if($deposito->status == 'ativo')
                            <i class="bi bi-check-circle"></i> Ativo
                        @elseif($deposito->status == 'manutencao')
                            <i class="bi bi-tools"></i> Manutenção
                        @else
                            <i class="bi bi-x-circle"></i> Inativo
                        @endif
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Capacidade</h5>
                    <h3 class="mb-0">{{ $deposito->capacidade_maxima ?? 'Ilimitada' }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">📋 Informações Detalhadas</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Localização:</th>
                            <td>{{ $deposito->localizacao ?? 'Não informada' }}</td>
                        </tr>
                        <tr>
                            <th>Área Total:</th>
                            <td>{{ $deposito->area_total ? number_format($deposito->area_total, 2) . ' m²' : 'Não informada' }}</td>
                        </tr>
                        <tr>
                            <th>Capacidade Máxima:</th>
                            <td>{{ $deposito->capacidade_maxima ?? 'Ilimitada' }} veículos</td>
                        </tr>
                        <tr>
                            <th>Ocupação:</th>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="me-2">{{ $estatisticas['total_veiculos'] }} / {{ $deposito->capacidade_maxima ?? '∞' }}</span>
                                    @if($deposito->capacidade_maxima)
                                        <div class="progress flex-grow-1" style="height: 8px;">
                                            <div class="progress-bar bg-{{ $estatisticas['total_veiculos'] / $deposito->capacidade_maxima >= 0.9 ? 'danger' : ($estatisticas['total_veiculos'] / $deposito->capacidade_maxima >= 0.7 ? 'warning' : 'success') }}" 
                                                 style="width: {{ ($estatisticas['total_veiculos'] / $deposito->capacidade_maxima) * 100 }}%"></div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>Data Cadastro:</th>
                            <td>{{ $deposito->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Última Atualização:</th>
                            <td>{{ $deposito->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @if($deposito->observacoes)
                        <tr>
                            <th>Observações:</th>
                            <td>{{ $deposito->observacoes }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">🚗 Estatísticas de Veículos</h5>
                    <a href="{{ route('aeroportos.depositos.veiculos.create', [$aeroporto, $deposito]) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle"></i> Novo Veículo
                    </a>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-4">
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <i class="bi bi-car-front fs-1 text-primary"></i>
                                <h3 class="mb-0 mt-2">{{ $estatisticas['total_veiculos'] }}</h3>
                                <small class="text-muted">Total de Veículos</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <i class="bi bi-check-circle fs-1 text-success"></i>
                                <h3 class="mb-0 mt-2">{{ $estatisticas['disponiveis'] }}</h3>
                                <small class="text-muted">Disponíveis</small>
                            </div>
                        </div>
                    </div>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <i class="bi bi-tools fs-1 text-warning"></i>
                                <h3 class="mb-0 mt-2">{{ $estatisticas['manutencao'] }}</h3>
                                <small class="text-muted">Em Manutenção</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <i class="bi bi-car-front-fill fs-1 text-info"></i>
                                <h3 class="mb-0 mt-2">{{ $estatisticas['por_tipo']->count() }}</h3>
                                <small class="text-muted">Tipos Diferentes</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Veículos -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">🚗 Veículos no Depósito</h5>
            <a href="{{ route('aeroportos.depositos.veiculos.index', [$aeroporto, $deposito]) }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-list"></i> Ver Todos
            </a>
        </div>
        <div class="card-body">
            @if($deposito->veiculos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Placa</th>
                                <th>Modelo</th>
                                <th>Marca</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($deposito->veiculos->take(5) as $veiculo)
                                <tr>
                                    <td><strong>{{ $veiculo->placa }}</strong></td>
                                    <td>{{ $veiculo->modelo }}</td>
                                    <td>{{ $veiculo->marca }}</td>
                                    <td><span class="badge bg-info">{{ ucfirst($veiculo->tipo) }}</span></td>
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
                                    <td>
                                        <a href="{{ route('aeroportos.depositos.veiculos.show', [$aeroporto, $deposito, $veiculo]) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($deposito->veiculos->count() > 5)
                    <div class="text-center mt-3">
                        <a href="{{ route('aeroportos.depositos.veiculos.index', [$aeroporto, $deposito]) }}" class="btn btn-link">
                            Ver todos os {{ $deposito->veiculos->count() }} veículos
                        </a>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="bi bi-car-front text-muted" style="font-size: 3rem;"></i>
                    <h5 class="text-muted mt-3">Nenhum veículo cadastrado neste depósito</h5>
                    <a href="{{ route('aeroportos.depositos.veiculos.create', [$aeroporto, $deposito]) }}" class="btn btn-primary mt-3">
                        <i class="bi bi-plus-circle"></i> Cadastrar Primeiro Veículo
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection