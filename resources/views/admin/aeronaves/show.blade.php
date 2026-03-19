@extends('layouts.app')

@section('title', 'Detalhes da Aeronave')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Detalhes da Aeronave</h2>
            <p class="text-muted">Informações completas da aeronave selecionada</p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-transparent">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-airplane me-2"></i>{{ $aeronave->modelo }}
                </h5>
                <div>
                    <a href="{{ route('aeronaves.edit', $aeronave->id) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i> Editar
                    </a>
                    <a href="{{ route('aeronaves.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 200px;" class="text-muted fw-normal">ID:</th>
                            <td class="fw-semibold">{{ $aeronave->id }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal">Modelo:</th>
                            <td class="fw-semibold">{{ $aeronave->modelo }}</td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal">Fabricante:</th>
                            <td>
                                @if($aeronave->fabricante)
                                    <a href="{{ route('fabricantes.show', $aeronave->fabricante->id) }}" class="text-decoration-none">
                                        <i class="bi bi-building me-1"></i>{{ $aeronave->fabricante->nome }}
                                    </a>
                                    <small class="text-muted d-block">
                                        País: {{ $aeronave->fabricante->pais_origem ?? 'Não informado' }}
                                    </small>
                                @else
                                    <span class="badge bg-secondary">Fabricante não informado</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal">Capacidade:</th>
                            <td class="fw-semibold">{{ $aeronave->capacidade }} passageiros</td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal">Porte:</th>
                            <td>
                                @if($aeronave->porte == 'PC')
                                    <span class="badge bg-info fs-6 p-2">PC - Pequeno Porte (≤100 passageiros)</span>
                                @elseif($aeronave->porte == 'MC')
                                    <span class="badge bg-warning text-dark fs-6 p-2">MC - Médio Porte (101-299 passageiros)</span>
                                @elseif($aeronave->porte == 'LC')
                                    <span class="badge bg-danger fs-6 p-2">LC - Grande Porte (≥300 passageiros)</span>
                                @else
                                    <span class="badge bg-secondary">Não classificado</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal">Data de Cadastro:</th>
                            <td>
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ $aeronave->created_at?->format('d/m/Y') ?? 'Data não disponível' }}
                                <small class="text-muted d-block">
                                    {{ $aeronave->created_at?->format('H:i:s') ?? '' }}
                                </small>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-muted fw-normal">Última Atualização:</th>
                            <td>
                                <i class="bi bi-clock-history me-1"></i>
                                {{ $aeronave->updated_at?->format('d/m/Y H:i') ?? 'Não atualizada' }}
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Card com estatísticas rápidas -->
                <div class="col-md-4">
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <h6 class="card-title fw-semibold mb-3">
                                <i class="bi bi-info-circle me-1"></i>Informações Rápidas
                            </h6>
                            
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded me-2">
                                        <i class="bi bi-people-fill text-primary"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Capacidade Total</small>
                                        <span class="fw-bold">{{ $aeronave->capacidade }}</span>
                                        <small class="text-muted">passageiros</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-success bg-opacity-10 p-2 rounded me-2">
                                        <i class="bi bi-building text-success"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Fabricante</small>
                                        <span class="fw-bold">{{ $aeronave->fabricante?->nome ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning bg-opacity-10 p-2 rounded me-2">
                                        <i class="bi bi-airplane-fill text-warning"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Classificação</small>
                                        <span class="fw-bold">
                                            @if($aeronave->porte == 'PC') Pequeno Porte
                                            @elseif($aeronave->porte == 'MC') Médio Porte
                                            @elseif($aeronave->porte == 'LC') Grande Porte
                                            @else Não classificado
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Qualquer script específico para a página de detalhes, se necessário
document.addEventListener('DOMContentLoaded', function() {
    // Exemplo: console.log('Página de detalhes carregada');
});
</script>
@endpush

@endsection