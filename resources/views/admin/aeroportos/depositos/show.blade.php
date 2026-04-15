{{-- resources/views/admin/aeroportos/depositos/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detalhes do Depósito - ' . $deposito->nome)

@section('content')
<style>
/* Estilos modernos para os cards de estatísticas */
.stats-card {
    background: white;
    border-radius: 20px;
    padding: 1.25rem 1rem;
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

.stats-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.stats-icon {
    width: 48px;
    height: 48px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
}

.stats-value {
    font-size: 1.75rem;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 0.25rem;
}

.stats-label {
    font-size: 0.85rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 500;
}

.stats-sub {
    font-size: 0.7rem;
    color: #adb5bd;
    margin-top: 0.25rem;
}

@media (max-width: 768px) {
    .stats-value {
        font-size: 1.5rem;
    }
    .stats-icon {
        width: 40px;
        height: 40px;
        font-size: 1.25rem;
    }
}
</style>

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

    <!-- Cards de Estatísticas - Layout Moderno -->
    <div class="row g-4 mb-5">
        <div class="col-md-3 col-sm-6">
            <div class="stats-card">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="stats-label">Status</div>
                        <div class="stats-value" style="font-size: 1.2rem;">
                            @if($deposito->status == 'ativo')
                                <span class="text-success">✓ Ativo</span>
                            @elseif($deposito->status == 'manutencao')
                                <span class="text-warning">🔧 Manutenção</span>
                            @else
                                <span class="text-danger">✗ Inativo</span>
                            @endif
                        </div>
                        <div class="stats-sub">situação atual</div>
                    </div>
                    <div class="stats-icon bg-{{ $deposito->status == 'ativo' ? 'success' : ($deposito->status == 'manutencao' ? 'warning' : 'danger') }} bg-opacity-10 text-{{ $deposito->status == 'ativo' ? 'success' : ($deposito->status == 'manutencao' ? 'warning' : 'danger') }}">
                        <i class="bi bi-{{ $deposito->status == 'ativo' ? 'check-circle' : ($deposito->status == 'manutencao' ? 'tools' : 'x-circle') }}"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6">
            <div class="stats-card">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="stats-label">Capacidade</div>
                        <div class="stats-value">{{ $deposito->capacidade_maxima ?? '∞' }}</div>
                        <div class="stats-sub">veículos no máximo</div>
                    </div>
                    <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6">
            <div class="stats-card">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="stats-label">Veículos</div>
                        <div class="stats-value">{{ $estatisticas['total_veiculos'] }}</div>
                        <div class="stats-sub">cadastrados no depósito</div>
                    </div>
                    <div class="stats-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-car-front"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6">
            <div class="stats-card">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="stats-label">Cadastro</div>
                        <div class="stats-value" style="font-size: 1.2rem; font-weight: 600;">
                            {{ $deposito->created_at->format('d/m/Y') }}
                        </div>
                        <div class="stats-sub">data de criação</div>
                    </div>
                    <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-calendar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Segunda linha de cards - Disponibilidade -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stats-card">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="stats-label">Disponíveis</div>
                        <div class="stats-value text-success">{{ $estatisticas['disponiveis'] }}</div>
                        <div class="stats-sub">veículos em operação</div>
                    </div>
                    <div class="stats-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress" style="height: 6px;">
                        @php
                            $percentDisponiveis = $estatisticas['total_veiculos'] > 0 ? ($estatisticas['disponiveis'] / $estatisticas['total_veiculos']) * 100 : 0;
                        @endphp
                        <div class="progress-bar bg-success" style="width: {{ $percentDisponiveis }}%"></div>
                    </div>
                    <small class="text-muted">{{ number_format($percentDisponiveis, 1) }}% do total</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="stats-card">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="stats-label">Indisponíveis</div>
                        <div class="stats-value text-danger">{{ $estatisticas['indisponiveis'] }}</div>
                        <div class="stats-sub">veículos fora de operação</div>
                    </div>
                    <div class="stats-icon bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress" style="height: 6px;">
                        @php
                            $percentIndisponiveis = $estatisticas['total_veiculos'] > 0 ? ($estatisticas['indisponiveis'] / $estatisticas['total_veiculos']) * 100 : 0;
                        @endphp
                        <div class="progress-bar bg-danger" style="width: {{ $percentIndisponiveis }}%"></div>
                    </div>
                    <small class="text-muted">{{ number_format($percentIndisponiveis, 1) }}% do total</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="stats-card">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <div class="stats-label">Ocupação</div>
                        <div class="stats-value">
                            @php
                                $ocupacao = $deposito->capacidade_maxima ? ($estatisticas['total_veiculos'] / $deposito->capacidade_maxima) * 100 : 0;
                            @endphp
                            {{ number_format($ocupacao, 1) }}%
                        </div>
                        <div class="stats-sub">
                            {{ $estatisticas['total_veiculos'] }} / {{ $deposito->capacidade_maxima ?? '∞' }}
                        </div>
                    </div>
                    <div class="stats-icon bg-{{ $ocupacao >= 90 ? 'danger' : ($ocupacao >= 70 ? 'warning' : 'primary') }} bg-opacity-10 text-{{ $ocupacao >= 90 ? 'danger' : ($ocupacao >= 70 ? 'warning' : 'primary') }}">
                        <i class="bi bi-pie-chart"></i>
                    </div>
                </div>
                @if($deposito->capacidade_maxima)
                    <div class="mt-3">
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-{{ $ocupacao >= 90 ? 'danger' : ($ocupacao >= 70 ? 'warning' : 'primary') }}" 
                                 style="width: {{ min(100, $ocupacao) }}%"></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tipos de Veículos -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">🚗 Distribuição por Tipo de Veículo</h5>
            <a href="{{ route('aeroportos.depositos.veiculos.create', [$aeroporto, $deposito]) }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle"></i> Novo Veículo
            </a>
        </div>
        <div class="card-body">
            @if($estatisticas['por_tipo']->count() > 0)
                <div class="row g-3">
                    @foreach($estatisticas['por_tipo'] as $tipo => $quantidade)
                        @php
                            $tipoInfo = \App\Models\Veiculo::TIPOS_VEICULOS[$tipo] ?? ['nome' => ucfirst($tipo), 'icone' => 'bi-truck'];
                            $percentual = $estatisticas['total_veiculos'] > 0 ? ($quantidade / $estatisticas['total_veiculos']) * 100 : 0;
                        @endphp
                        <div class="col-md-4 col-lg-3">
                            <div class="border rounded p-3 h-100">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <i class="bi {{ $tipoInfo['icone'] }} fs-2 text-primary"></i>
                                    <div>
                                        <h6 class="mb-0">{{ $tipoInfo['nome'] }}</h6>
                                        <small class="text-muted">{{ $quantidade }} veículo(s)</small>
                                    </div>
                                </div>
                                <div class="progress" style="height: 4px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $percentual }}%"></div>
                                </div>
                                <small class="text-muted">{{ number_format($percentual, 1) }}% do total</small>
                            </div>
                        </div>
                    @endforeach
                </div>
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

    <!-- Lista de Veículos Recentes -->
    @if($deposito->veiculos->count() > 0)
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">📋 Últimos Veículos Cadastrados</h5>
            <a href="{{ route('aeroportos.depositos.veiculos.index', [$aeroporto, $deposito]) }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-list"></i> Ver Todos
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Tipo</th>
                            <th>Quantidade</th>
                            <th>Status</th>
                            <th>Data Cadastro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deposito->veiculos->sortByDesc('created_at')->take(10) as $veiculo)
                            <tr>
                                <td><strong>{{ $veiculo->codigo }}</strong></td>
                                <td>
                                    <span class="badge bg-primary">
                                        <i class="bi {{ \App\Models\Veiculo::TIPOS_VEICULOS[$veiculo->tipo_veiculo]['icone'] ?? 'bi-truck' }}"></i>
                                        {{ $veiculo->tipo_nome }}
                                    </span>
                                </td>
                                <td>{{ $veiculo->quantidade }} unidade(s)</td>
                                <td>
                                    @if($veiculo->status == 'disponivel')
                                        <span class="badge bg-success">Disponível</span>
                                    @else
                                        <span class="badge bg-secondary">Indisponível</span>
                                    @endif
                                </td>
                                <td>{{ $veiculo->created_at->format('d/m/Y H:i') }}</td>
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
        </div>
    </div>
    @endif
</div>
@endsection