@extends('layouts.app')

@section('title', 'Informações de Fabricantes - Airport Manager')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <span class="text-uppercase text-primary fw-bold small tracking-wide">Catálogo de produção</span>
            <h1 class="h2 fw-bold mt-1 mb-1">
                <i class="bi bi-tools me-2"></i>Informações de Fabricantes
            </h1>
            <p class="text-muted mb-0">Modelos, capacidade e movimentação das aeronaves por fabricante.</p>
        </div>
        <a href="{{ route('fabricantes.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-gear me-1"></i> Gerenciar fabricantes
        </a>
    </div>

    <div class="row g-3 mb-4">
        @foreach([
            ['Fabricantes', $estatisticas['total_fabricantes'], 'tools', 'primary'],
            ['Países de origem', $estatisticas['total_paises'], 'globe-americas', 'success'],
            ['Modelos cadastrados', $estatisticas['total_modelos'], 'airplane', 'warning'],
            ['Total de voos', $estatisticas['total_voos'], 'graph-up-arrow', 'info'],
        ] as [$rotulo, $valor, $icone, $cor])
            <div class="col-sm-6 col-xl-3">
                <div class="summary-card border-start border-4 border-{{ $cor }}">
                    <div>
                        <span class="text-muted small">{{ $rotulo }}</span>
                        <strong>{{ number_format($valor, 0, ',', '.') }}</strong>
                    </div>
                    <i class="bi bi-{{ $icone }} text-{{ $cor }}"></i>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-5">
                    <label for="buscaFabricante" class="form-label small fw-semibold text-muted">Fabricante</label>
                    <input type="search" id="buscaFabricante" class="form-control" placeholder="Buscar por nome...">
                </div>
                <div class="col-md-4">
                    <label for="filtroPais" class="form-label small fw-semibold text-muted">País de origem</label>
                    <select id="filtroPais" class="form-select">
                        <option value="">Todos os países</option>
                        @foreach($fabricantes->pluck('pais_origem')->unique()->sort() as $pais)
                            <option value="{{ Str::lower($pais) }}">{{ $pais }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="ordenacao" class="form-label small fw-semibold text-muted">Ordenar</label>
                    <select id="ordenacao" class="form-select">
                        <option value="nome">Nome (A–Z)</option>
                        <option value="modelos">Mais modelos</option>
                        <option value="voos">Mais voos</option>
                        <option value="passageiros">Mais passageiros</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4" id="fabricantesContainer">
        @forelse($fabricantes as $fabricante)
            <div class="col-md-6 col-xl-4 fabricante-card"
                 data-nome="{{ Str::lower($fabricante['nome']) }}"
                 data-pais="{{ Str::lower($fabricante['pais_origem']) }}"
                 data-modelos="{{ $fabricante['total_modelos'] }}"
                 data-voos="{{ $fabricante['total_voos'] }}"
                 data-passageiros="{{ $fabricante['total_passageiros'] }}">
                <div class="card border-0 shadow-sm h-100 manufacturer-card">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <div class="d-flex justify-content-between gap-3">
                            <div>
                                <h2 class="h5 fw-bold mb-1">{{ $fabricante['nome'] }}</h2>
                                <span class="text-muted small"><i class="bi bi-geo-alt me-1"></i>{{ $fabricante['pais_origem'] }}</span>
                            </div>
                            <span class="manufacturer-icon"><i class="bi bi-tools"></i></span>
                        </div>
                    </div>
                    <div class="card-body px-4">
                        <div class="row g-2 text-center mb-3">
                            <div class="col-4">
                                <div class="metric"><strong>{{ $fabricante['total_modelos'] }}</strong><small>Modelos</small></div>
                            </div>
                            <div class="col-4">
                                <div class="metric"><strong>{{ number_format($fabricante['total_voos'], 0, ',', '.') }}</strong><small>Voos</small></div>
                            </div>
                            <div class="col-4">
                                <div class="metric"><strong>{{ number_format($fabricante['total_companhias'], 0, ',', '.') }}</strong><small>Companhias</small></div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between py-2 border-top">
                            <span class="text-muted">Passageiros</span>
                            <strong>{{ number_format($fabricante['total_passageiros'], 0, ',', '.') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-top">
                            <span class="text-muted">Nota geral</span>
                            <strong>{{ $fabricante['nota_geral'] > 0 ? number_format($fabricante['nota_geral'], 1, ',', '.') : '—' }}</strong>
                        </div>
                        <div class="border-top pt-3">
                            <span class="small text-muted d-block mb-2">Médias por tipo</span>
                            <div class="row g-2 text-center">
                                @foreach([
                                    ['Objetivo', 'objetivo', 'bullseye', 'primary'],
                                    ['Pontualidade', 'pontualidade', 'stopwatch', 'success'],
                                    ['Serviços', 'servicos', 'bell', 'info'],
                                    ['Pátio', 'patio', 'buildings', 'warning'],
                                ] as [$rotulo, $campo, $icone, $cor])
                                    <div class="col-6">
                                        <div class="rating-metric">
                                            <i class="bi bi-{{ $icone }} text-{{ $cor }}"></i>
                                            <small>{{ $rotulo }}</small>
                                            <strong>
                                                {{ $fabricante['medias_por_tipo'][$campo] > 0
                                                    ? number_format($fabricante['medias_por_tipo'][$campo], 1, ',', '.')
                                                    : '—' }}
                                            </strong>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-3">
                            <span class="small text-muted d-block mb-2">Modelos</span>
                            <span class="badge bg-light text-dark border">
                                {{ $fabricante['total_modelos'] }}
                                {{ $fabricante['total_modelos'] === 1 ? 'modelo cadastrado' : 'modelos cadastrados' }}
                            </span>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 px-4 pb-4">
                        <a href="{{ route('fabricantes.show', $fabricante['id']) }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-eye me-1"></i>Ver detalhes
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center text-muted py-5">Nenhum fabricante cadastrado.</div>
        @endforelse
    </div>

    <div id="semResultados" class="text-center text-muted py-5 d-none">Nenhum fabricante encontrado.</div>
</div>
@endsection

@push('scripts')
<script>
const container = document.getElementById('fabricantesContainer');
const cards = Array.from(document.querySelectorAll('.fabricante-card'));

function atualizarFabricantes() {
    const busca = document.getElementById('buscaFabricante').value.toLowerCase();
    const pais = document.getElementById('filtroPais').value;
    const ordem = document.getElementById('ordenacao').value;
    let visiveis = 0;

    cards.forEach(card => {
        const mostrar = card.dataset.nome.includes(busca) && (!pais || card.dataset.pais === pais);
        card.classList.toggle('d-none', !mostrar);
        if (mostrar) visiveis++;
    });

    cards.sort((a, b) => {
        if (ordem === 'nome') return a.dataset.nome.localeCompare(b.dataset.nome);
        return Number(b.dataset[ordem]) - Number(a.dataset[ordem]);
    }).forEach(card => container.appendChild(card));

    document.getElementById('semResultados').classList.toggle('d-none', visiveis > 0);
}

document.getElementById('buscaFabricante').addEventListener('input', atualizarFabricantes);
document.getElementById('filtroPais').addEventListener('change', atualizarFabricantes);
document.getElementById('ordenacao').addEventListener('change', atualizarFabricantes);
</script>
@endpush

@push('styles')
<style>
.tracking-wide { letter-spacing: .12em; }
.summary-card { display: flex; min-height: 115px; padding: 1.25rem; align-items: center; justify-content: space-between; border-radius: 10px; background: #fff; box-shadow: 0 .125rem .5rem rgba(0,0,0,.08); }
.summary-card strong { display: block; margin-top: .25rem; font-size: 1.8rem; }
.summary-card > i { font-size: 2.25rem; opacity: .75; }
.manufacturer-card { transition: transform .2s, box-shadow .2s; }
.manufacturer-card:hover { transform: translateY(-3px); box-shadow: 0 .75rem 1.5rem rgba(0,0,0,.12) !important; }
.manufacturer-icon { display: grid; width: 42px; height: 42px; place-items: center; border-radius: 10px; color: #0d6efd; background: #e7f1ff; font-size: 1.2rem; }
.metric { padding: .75rem .25rem; border-radius: 8px; background: #f8f9fa; }
.metric strong, .metric small { display: block; }
.metric small { color: #6c757d; font-size: .75rem; }
.rating-metric { display: grid; grid-template-columns: auto 1fr auto; gap: .4rem; padding: .55rem; align-items: center; border-radius: 8px; background: #f8f9fa; }
.rating-metric small { color: #6c757d; text-align: left; }
</style>
@endpush
