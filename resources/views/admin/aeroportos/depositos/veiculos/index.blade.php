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
            <a href="{{ route('aeroportos.depositos.show', [$aeroporto, $deposito]) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="disponivel" {{ request('status') == 'disponivel' ? 'selected' : '' }}>Disponível</option>
                        <option value="em_uso" {{ request('status') == 'em_uso' ? 'selected' : '' }}>Em Uso</option>
                        <option value="manutencao" {{ request('status') == 'manutencao' ? 'selected' : '' }}>Manutenção</option>
                        <option value="inativo" {{ request('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="search" class="form-control" placeholder="Código, placa, modelo..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Cards de Resumo --}}
    <div class="row mb-4">
        <div class="col-md-3">
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
        <div class="col-md-3">
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
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Em Manutenção</h6>
                            <h2 class="mb-0">{{ $deposito->veiculos->where('status', 'manutencao')->count() }}</h2>
                        </div>
                        <i class="bi bi-tools fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Certificados a Vencer</h6>
                            <h2 class="mb-0">{{ $deposito->veiculos->filter(fn($v) => $v->dias_para_vencimento_certificado <= 30 && $v->dias_para_vencimento_certificado > 0)->count() }}</h2>
                        </div>
                        <i class="bi bi-calendar-exclamation fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
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
                            <th>Modelo/Fabricante</th>
                            <th>Placa</th>
                            <th>Capacidade</th>
                            <th>Horímetro</th>
                            <th>Status</th>
                            <th>Próx. Manut.</th>
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
                                <td>{{ $veiculo->placa ?? '-' }}</td>
                                <td>
                                    @if($veiculo->capacidade_operacional)
                                        {{ number_format($veiculo->capacidade_operacional, 0, ',', '.') }}
                                        {{ $veiculo->unidade_capacidade ?? '' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ number_format($veiculo->horimetro, 0, ',', '.') }} h</td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'disponivel' => 'success',
                                            'em_uso' => 'warning',
                                            'manutencao' => 'danger',
                                            'inativo' => 'secondary'
                                        ];
                                        $statusLabels = [
                                            'disponivel' => 'Disponível',
                                            'em_uso' => 'Em Uso',
                                            'manutencao' => 'Manutenção',
                                            'inativo' => 'Inativo'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$veiculo->status] }}">
                                        {{ $statusLabels[$veiculo->status] }}
                                    </span>
                                    @if($veiculo->precisa_manutencao)
                                        <span class="badge bg-danger">⚠️ Urgente</span>
                                    @endif
                                </td>
                                <td>
                                    @if($veiculo->proxima_manutencao)
                                        <span class="{{ $veiculo->proxima_manutencao->isPast() ? 'text-danger' : 'text-muted' }}">
                                            {{ $veiculo->proxima_manutencao->format('d/m/Y') }}
                                        </span>
                                    @else
                                        -
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
                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                onclick="registrarManutencao({{ $veiculo->id }})" title="Registrar Manutenção">
                                            <i class="bi bi-tools"></i>
                                        </button>
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
                                <td colspan="9" class="text-center py-5">
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

{{-- Modal para registrar manutenção --}}
<div class="modal fade" id="manutencaoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Manutenção</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="manutencaoForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="_method" value="PUT">
                    <div class="mb-3">
                        <label class="form-label">Descrição da Manutenção</label>
                        <textarea name="descricao" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Horímetro Atual (horas)</label>
                        <input type="number" name="horimetro" class="form-control" step="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function registrarManutencao(veiculoId) {
    const modal = new bootstrap.Modal(document.getElementById('manutencaoModal'));
    const form = document.getElementById('manutencaoForm');
    const url = "{{ route('aeroportos.depositos.veiculos.manutencao', [$aeroporto, $deposito, '']) }}/" + veiculoId;
    form.action = url;
    modal.show();
}
</script>
@endsection