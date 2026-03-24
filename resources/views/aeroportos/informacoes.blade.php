{{-- resources/views/aeroportos/informacoes.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="text-center mb-4">Informações Gerais dos Aeroportos</h1>
    
    {{-- Cards de Estatísticas Gerais --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body py-3">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <strong>Total de Aeroportos:</strong> 
                            <span class="badge bg-primary ms-4">{{ $totalAeroportos }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Total de Voos:</strong> 
                            <span class="badge bg-info ms-4">{{ number_format($totalVoos) }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Total de Passageiros:</strong> 
                            <span class="badge bg-success ms-4">{{ number_format($totalPassageiros) }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Média de Pax por Voo:</strong> 
                            <span class="badge bg-warning ms-4">{{ number_format($mediaPassageirosPorVoo, 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-funnel"></i> Filtrar por Companhia Aérea
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-outline-primary btn-sm filter-companhia" data-companhia="all">
                            Todos os Aeroportos
                        </button>
                        @foreach($companhias as $companhia)
                            <button class="btn btn-outline-secondary btn-sm filter-companhia" data-companhia="{{ $companhia->id }}">
                                {{ $companhia->nome }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-search"></i> Buscar por Aeroporto
                    </h5>
                </div>
                <div class="card-body">
                    <input type="text" id="searchAirport" class="form-control" placeholder="Digite o nome do aeroporto...">
                </div>
            </div>
        </div>
    </div>

    {{-- Seção dos cards dos aeroportos --}}
    <div class="row g-4">
        @foreach($aeroportosData as $aeroporto)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm airport-card" data-airport-id="{{ $aeroporto['id'] }}" data-nome="{{ strtolower($aeroporto['nome']) }}">
                    <div class="card-header bg-dark text-white text-center">
                        <h5 class="mb-0">{{ $aeroporto['nome'] }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center mb-3">
                            <div class="col">
                                <i class="bi bi-airplane-engines text-primary"></i>
                                <p>Total de Voos: <strong>{{ number_format($aeroporto['total_voos']) }}</strong></p>
                            </div>
                            <div class="col">
                                <i class="bi bi-people-fill text-success"></i>
                                <p>Total de Passageiros: <strong>{{ number_format($aeroporto['total_passageiros']) }}</strong></p>
                            </div>
                            <div class="col">
                                <i class="bi bi-building text-info"></i>
                                <p>Companhias: <strong>{{ $aeroporto['companhias_count'] }}</strong></p>
                            </div>
                        </div>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Objetivo 
                                <span class="badge bg-primary rounded-pill">{{ number_format($aeroporto['nota_obj'], 1) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Pontualidade 
                                <span class="badge bg-success rounded-pill">{{ number_format($aeroporto['nota_pontualidade'], 1) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Serviços 
                                <span class="badge bg-info rounded-pill">{{ number_format($aeroporto['nota_servicos'], 1) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Pátio 
                                <span class="badge bg-warning text-dark rounded-pill">{{ number_format($aeroporto['nota_patio'], 1) }}</span>
                            </li>
                        </ul>
                        @if($aeroporto['media_notas'] > 0)
                            <div class="text-center mt-3">
                                <span class="badge bg-secondary">
                                    <i class="bi bi-star-fill text-warning"></i> Média Geral: {{ number_format($aeroporto['media_notas'], 1) }}/10
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Card para as melhores companhias por categoria --}}
                @if(isset($aeroporto['melhores_companhias']) && count(array_filter($aeroporto['melhores_companhias'])) > 0)
                    <div class="card shadow-sm mt-3 border-primary">
                        <div class="card-header bg-primary text-white text-center">
                            <h6 class="mb-0">
                                <i class="bi bi-trophy-fill"></i> Melhores Companhias por Categoria
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                @foreach($aeroporto['melhores_companhias'] as $categoria => $companhia)
                                    @if($companhia)
                                        @php
                                            $badgeClass = match($categoria) {
                                                'Objetivo' => 'bg-primary',
                                                'Pontualidade' => 'bg-success',
                                                'Servicos' => 'bg-info',
                                                'Patio' => 'bg-warning text-dark',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="badge {{ $badgeClass }} me-2">{{ $categoria }}</span>
                                                {{ $companhia['nome'] }}
                                            </div>
                                            <span class="badge {{ $badgeClass }} rounded-pill">
                                                {{ number_format($companhia['media'], 1) }}
                                            </span>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>

{{-- Bootstrap Icons --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<script>
    // Dados dos aeroportos
    const airportsData = @json($aeroportosData);

    // Filtro por companhia
    document.querySelectorAll('.filter-companhia').forEach(btn => {
        btn.addEventListener('click', function() {
            const companhiaId = this.dataset.companhia;
            let visibleCount = 0;
            
            document.querySelectorAll('.airport-card').forEach(card => {
                if (companhiaId === 'all') {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    const airportId = card.dataset.airportId;
                    const airport = airportsData.find(a => a.id == airportId);
                    
                    if (airport && airport.companhias && airport.companhias.some(c => c.id == companhiaId)) {
                        card.style.display = '';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                }
            });
            
            // Atualizar botões ativos
            document.querySelectorAll('.filter-companhia').forEach(b => {
                b.classList.remove('btn-primary');
                b.classList.add('btn-outline-secondary');
            });
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-primary');
        });
    });

    // Busca por nome do aeroporto
    document.getElementById('searchAirport').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        
        document.querySelectorAll('.airport-card').forEach(card => {
            const nome = card.dataset.nome;
            if (nome.includes(searchTerm)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>

<p class="text-center text-muted mb-0">
    Desenvolvido por <strong>Filipe Lopes</strong>
</p>
@endsection