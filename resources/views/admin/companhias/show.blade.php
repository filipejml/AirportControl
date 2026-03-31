@extends('layouts.app')

@section('title', 'Detalhes da Companhia Aérea')

@section('content')
<style>
/* Estilos para os botões de ação */
.btn-action {
    padding: 0.5rem 1rem;
    font-size: 0.95rem;
    line-height: 1.5;
    border-radius: 0.375rem;
    min-width: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: all 0.2s ease;
}
.btn-action i {
    font-size: 1.1rem;
}
.btn-action span {
    display: inline-block;
}
.btn-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.btn-action-group {
    gap: 0.5rem !important;
}

/* Estilo para links de modelos */
.modelo-link {
    color: #0d6efd;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s ease;
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
}
.modelo-link:hover {
    color: #0a58ca;
    background-color: rgba(13, 110, 253, 0.1);
    text-decoration: underline;
    transform: translateX(2px);
}
.modelo-link i {
    font-size: 0.9rem;
    margin-right: 4px;
    opacity: 0.7;
}
.modelo-link:hover i {
    opacity: 1;
}

/* Estilo para card de informações adicionais */
.info-card {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: none;
    border-radius: 16px;
}
.info-card .card-body {
    padding: 1.25rem;
}
.info-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0;
}
.info-icon {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-radius: 10px;
    color: #3b82f6;
}
.info-label {
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
    margin-bottom: 2px;
}
.info-value {
    font-size: 0.9rem;
    font-weight: 600;
    color: #212529;
}

/* Ajustes responsivos */
@media (max-width: 768px) {
    .btn-action span {
        display: none;
    }
    .btn-action {
        min-width: 44px;
        padding: 0.5rem;
    }
}
</style>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="fw-bold">✈️ {{ $companhia->nome }}</h2>
            <p class="text-muted">Detalhes da companhia aérea</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('companhias.informacoes') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
            <a href="{{ route('companhias.edit', ['companhia' => $companhia, 'from' => 'show']) }}" class="btn btn-primary btn-action">
                <i class="bi bi-pencil"></i>
                <span>Editar</span>
            </a>
        </div>
    </div>

    <!-- Linha com estatísticas principais -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total de Aeronaves</h5>
                    <h2 class="display-4">{{ $companhia->aeronaves_count ?? $companhia->aeronaves->count() }}</h2>
                    <small>aeronaves cadastradas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Capacidade Total</h5>
                    <h2 class="display-4">{{ $companhia->aeronaves->sum('capacidade') }}</h2>
                    <small>passageiros</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Média de Capacidade</h5>
                    <h2 class="display-4">{{ $companhia->aeronaves->avg('capacidade') ? round($companhia->aeronaves->avg('capacidade')) : 0 }}</h2>
                    <small>passageiros por aeronave</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-secondary text-white">
                <div class="card-body">
                    <h5 class="card-title">Código</h5>
                    <h2 class="display-4">{{ $companhia->codigo ?? '—' }}</h2>
                    <small>código identificador</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Card com informações adicionais (data de cadastro) -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card info-card shadow-sm">
                <div class="card-body">
                    <h6 class="mb-3 text-muted">📋 Informações do Cadastro</h6>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-calendar-plus"></i>
                        </div>
                        <div>
                            <div class="info-label">Data de Cadastro</div>
                            <div class="info-value">{{ $companhia->created_at ? $companhia->created_at->format('d/m/Y \à\s H:i') : 'Data não disponível' }}</div>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div>
                            <div class="info-label">Última Atualização</div>
                            <div class="info-value">{{ $companhia->updated_at ? $companhia->updated_at->format('d/m/Y \à\s H:i') : 'Data não disponível' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Aeronaves -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">✈️ Aeronaves da Companhia</h5>
            @if($companhia->aeronaves->count() > 0)
                <span class="badge bg-primary rounded-pill px-3 py-2">
                    Total: {{ $companhia->aeronaves->count() }} aeronaves
                </span>
            @endif
        </div>
        <div class="card-body">
            @if($companhia->aeronaves->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                             <tr>
                                <th>ID</th>
                                <th>Modelo</th>
                                <th>Fabricante</th>
                                <th>Capacidade</th>
                                <th>Porte</th>
                                <th width="180">Ações</th>
                             </tr>
                        </thead>
                        <tbody>
                            @foreach($companhia->aeronaves as $aeronave)
                                 <tr>
                                    <td><span class="fw-semibold">#{{ $aeronave->id }}</span></td>
                                    <td>
                                        <a href="{{ route('aeronaves.show', $aeronave) }}" 
                                           class="modelo-link"
                                           title="Ver detalhes da aeronave {{ $aeronave->modelo }}">
                                            <i class="bi bi-airplane"></i>
                                            {{ $aeronave->modelo }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($aeronave->fabricante)
                                            <span class="text-muted">{{ $aeronave->fabricante->nome }}</span>
                                        @else
                                            <span class="badge bg-secondary">Não informado</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info rounded-pill px-3 py-2">
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
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2 btn-action-group">
                                            <a href="{{ route('aeronaves.edit', $aeronave) }}" 
                                               class="btn btn-primary btn-action"
                                               title="Editar aeronave">
                                                <i class="bi bi-pencil"></i>
                                                <span>Editar</span>
                                            </a>
                                            <form action="{{ route('aeronaves.destroy', $aeronave) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Tem certeza que deseja excluir a aeronave {{ $aeronave->modelo }}? Esta ação não pode ser desfeita.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-danger btn-action"
                                                        title="Excluir aeronave">
                                                    <i class="bi bi-trash"></i>
                                                    <span>Excluir</span>
                                                </button>
                                            </form>
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
                    <h5 class="text-muted mt-3">Nenhuma aeronave associada a esta companhia</h5>
                    <a href="{{ route('aeronaves.create') }}" class="btn btn-primary btn-action mt-3">
                        <i class="bi bi-plus-circle"></i>
                        <span>Cadastrar Nova Aeronave</span>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection