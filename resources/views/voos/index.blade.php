@extends('layouts.app')

@section('title', 'Gerenciamento de voos')

@push('styles')
<style>
    .flights-page {
        --flight-ink: #172033;
        --flight-muted: #667085;
        --flight-border: #e4e7ec;
        color: var(--flight-ink);
    }

    .flights-hero {
        position: relative;
        overflow: hidden;
        padding: clamp(1.5rem, 4vw, 2.5rem);
        border-radius: 1.5rem;
        color: #fff;
        background:
            radial-gradient(circle at 88% 15%, rgba(255,255,255,.16), transparent 24%),
            linear-gradient(125deg, #101828 0%, #1849a9 60%, #2e90fa 100%);
        box-shadow: 0 20px 45px rgba(16, 24, 40, .15);
    }

    .hero-symbol {
        display: grid;
        width: 3.75rem;
        height: 3.75rem;
        place-items: center;
        flex: 0 0 auto;
        border: 1px solid rgba(255,255,255,.28);
        border-radius: 1.1rem;
        background: rgba(255,255,255,.12);
        font-size: 1.55rem;
    }

    .metric-card {
        height: 100%;
        padding: 1.1rem 1.2rem;
        border: 1px solid var(--flight-border);
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 8px 22px rgba(16, 24, 40, .045);
    }

    .metric-icon {
        display: grid;
        width: 2.55rem;
        height: 2.55rem;
        place-items: center;
        flex: 0 0 auto;
        border-radius: .75rem;
        font-size: 1.05rem;
    }

    .metric-value {
        font-size: 1.55rem;
        font-weight: 750;
        line-height: 1;
        letter-spacing: -.03em;
    }

    .filter-card, .list-card {
        border: 1px solid var(--flight-border);
        border-radius: 1.15rem;
        background: #fff;
        box-shadow: 0 8px 24px rgba(16, 24, 40, .04);
    }

    .filter-card { padding: 1.25rem; }
    .list-card { overflow: hidden; }

    .filter-card .form-control,
    .filter-card .form-select {
        min-height: 2.75rem;
        border-color: #d0d5dd;
        border-radius: .75rem;
    }

    .search-control { position: relative; }
    .search-control > i {
        position: absolute;
        top: 50%;
        left: .9rem;
        z-index: 3;
        color: #98a2b3;
        transform: translateY(-50%);
    }
    .search-control .form-control { padding-left: 2.55rem; }

    .list-heading {
        padding: 1.15rem 1.3rem;
        border-bottom: 1px solid var(--flight-border);
    }

    .flights-table {
        min-width: 1200px;
        margin: 0;
    }

    .flights-table thead th {
        padding: .8rem .9rem;
        border-bottom-width: 1px;
        color: var(--flight-muted);
        background: #f9fafb;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
    }

    .flights-table tbody td {
        padding: .85rem .9rem;
        border-color: #f0f1f3;
        font-size: .82rem;
    }

    .flight-code {
        display: inline-flex;
        padding: .38rem .6rem;
        border-radius: .55rem;
        color: #155eef;
        background: #eff4ff;
        font-weight: 750;
        letter-spacing: .04em;
    }

    .rating {
        display: inline-grid;
        width: 2rem;
        height: 2rem;
        place-items: center;
        border-radius: 50%;
        font-size: .75rem;
        font-weight: 750;
    }

    .rating-good { color: #027a48; background: #ecfdf3; }
    .rating-medium { color: #b54708; background: #fffaeb; }
    .rating-low { color: #b42318; background: #fef3f2; }
    .rating-empty { color: #98a2b3; background: #f2f4f7; }

    .list-footer {
        padding: 1rem 1.25rem;
        border-top: 1px solid var(--flight-border);
        background: #fcfcfd;
    }

    .page-button {
        display: inline-flex;
        min-width: 2.35rem;
        min-height: 2.35rem;
        align-items: center;
        justify-content: center;
        border: 1px solid #d0d5dd;
        border-radius: .65rem;
        color: #344054;
        background: #fff;
        text-decoration: none;
    }
    .page-button:hover { color: #155eef; border-color: #84adff; background: #f5f8ff; }
    .page-button.disabled { color: #d0d5dd; pointer-events: none; }

    .empty-state { padding: 4.5rem 1rem; text-align: center; }
    .empty-icon {
        display: grid;
        width: 4rem;
        height: 4rem;
        margin: 0 auto 1rem;
        place-items: center;
        border-radius: 1.2rem;
        color: #155eef;
        background: #eff4ff;
        font-size: 1.6rem;
    }

    .loading-spinner {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        border: 2px solid rgba(0,0,0,.12);
        border-left-color: currentColor;
        border-radius: 50%;
        animation: spin .65s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    @media (max-width: 767.98px) {
        .flights-hero { border-radius: 1.15rem; }
        .hero-actions, .hero-actions .btn { width: 100%; }
    }
</style>
@endpush

@section('content')
@php
    $hasFilters = request()->filled('search') || request()->filled('tipo')
        || request()->filled('horario') || request()->filled('dias');
    $stats = $hasFilters ? $estatisticas['filtrados'] : [
        'total_voos' => $estatisticas['total_voos'],
        'total_passageiros' => $estatisticas['total_passageiros'],
        'media_pax_voo' => $estatisticas['media_pax_voo'],
    ];

    $ratingClass = fn ($value) => !$value
        ? 'rating-empty'
        : ($value >= 8 ? 'rating-good' : ($value >= 6 ? 'rating-medium' : 'rating-low'));
@endphp

<div class="flights-page pb-5">
    <header class="flights-hero mb-4">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
            <div class="d-flex align-items-center gap-3">
                <span class="hero-symbol" aria-hidden="true"><i class="bi bi-airplane-engines"></i></span>
                <div>
                    <div class="small text-white-50 fw-semibold text-uppercase mb-1">Operações</div>
                    <h1 class="h2 fw-bold mb-1">Gerenciamento de voos</h1>
                    <p class="mb-0 text-white-50">Consulte, avalie e mantenha os registros operacionais.</p>
                </div>
            </div>
            <div class="hero-actions d-flex flex-column flex-sm-row gap-2">
                @if(request('origem') === 'registros')
                    <a href="{{ route('registros') }}" class="btn btn-outline-light fw-semibold px-4 py-2">
                        <i class="bi bi-arrow-left me-1"></i> Voltar para registros
                    </a>
                @endif
                <a href="{{ route('voos.create') }}" class="btn btn-light text-primary fw-semibold px-4 py-2">
                    <i class="bi bi-plus-lg me-1"></i> Novo voo
                </a>
            </div>
        </div>
    </header>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    <section class="row g-3 mb-4" aria-label="Resumo de voos">
        <div class="col-6 col-xl-3">
            <div class="metric-card d-flex align-items-center gap-3">
                <span class="metric-icon text-primary bg-primary-subtle"><i class="bi bi-airplane"></i></span>
                <div><div class="metric-value">{{ number_format($stats['total_voos'], 0, ',', '.') }}</div><div class="small text-muted">{{ $hasFilters ? 'Voos filtrados' : 'Total de voos' }}</div></div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="metric-card d-flex align-items-center gap-3">
                <span class="metric-icon text-success bg-success-subtle"><i class="bi bi-people"></i></span>
                <div><div class="metric-value">{{ number_format($stats['total_passageiros'], 0, ',', '.') }}</div><div class="small text-muted">Passageiros</div></div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="metric-card d-flex align-items-center gap-3">
                <span class="metric-icon text-info bg-info-subtle"><i class="bi bi-person-check"></i></span>
                <div><div class="metric-value">{{ number_format($stats['media_pax_voo'], 0, ',', '.') }}</div><div class="small text-muted">Média por voo</div></div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="metric-card d-flex align-items-center gap-3">
                <span class="metric-icon text-warning bg-warning-subtle"><i class="bi bi-star-fill"></i></span>
                <div><div class="metric-value">{{ $estatisticas['media_geral_notas'] ? number_format($estatisticas['media_geral_notas'], 1, ',', '.') : '—' }}</div><div class="small text-muted">Média das notas</div></div>
            </div>
        </div>
    </section>

    <section class="filter-card mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
            <div>
                <h2 class="h5 fw-bold mb-1"><i class="bi bi-funnel me-1 text-primary"></i> Filtros</h2>
                <p class="small text-muted mb-0">Busque por voo, aeroporto, companhia ou aeronave.</p>
            </div>
            @if($hasFilters)
                <a href="{{ route('voos.index', request('origem') === 'registros' ? ['origem' => 'registros'] : []) }}" class="btn btn-sm btn-outline-secondary align-self-md-start">
                    <i class="bi bi-x-lg me-1"></i> Limpar filtros
                </a>
            @endif
        </div>
        <form id="filterForm" method="GET" action="{{ route('voos.index') }}" class="row g-3 align-items-end">
            <input type="hidden" name="per_page" value="{{ $perPage }}">
            @if(request('origem') === 'registros')
                <input type="hidden" name="origem" value="registros">
            @endif
            <div class="col-lg-4">
                <label for="searchInput" class="form-label small fw-semibold">Busca</label>
                <div class="search-control">
                    <i class="bi bi-search"></i>
                    <input type="search" class="form-control" name="search" id="searchInput"
                           placeholder="Número, aeroporto, companhia ou modelo"
                           value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <label for="filterTipo" class="form-label small fw-semibold">Tipo</label>
                <select class="form-select" name="tipo" id="filterTipo">
                    <option value="">Todos</option>
                    <option value="Regular" @selected(request('tipo') === 'Regular')>Regular</option>
                    <option value="Charter" @selected(request('tipo') === 'Charter')>Charter</option>
                </select>
            </div>
            <div class="col-6 col-lg-2">
                <label for="filterHorario" class="form-label small fw-semibold">Horário</label>
                <select class="form-select" name="horario" id="filterHorario">
                    <option value="">Todos</option>
                    <option value="EAM" @selected(request('horario') === 'EAM')>Madrugada</option>
                    <option value="AM" @selected(request('horario') === 'AM')>Manhã</option>
                    <option value="AN" @selected(request('horario') === 'AN')>Tarde</option>
                    <option value="PM" @selected(request('horario') === 'PM')>Noite</option>
                    <option value="ALL" @selected(request('horario') === 'ALL')>Diário</option>
                </select>
            </div>
            <div class="col-6 col-lg-2">
                <label for="filterData" class="form-label small fw-semibold">Período</label>
                <select class="form-select" name="dias" id="filterData">
                    <option value="">Todo período</option>
                    @foreach([7, 15, 30, 90] as $days)
                        <option value="{{ $days }}" @selected((int) request('dias') === $days)>Últimos {{ $days }} dias</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-lg-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i> Aplicar</button>
            </div>
        </form>
    </section>

    <section class="list-card">
        <div class="list-heading d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h2 class="h5 fw-bold mb-1">Registros de voo</h2>
                <p class="small text-muted mb-0">{{ number_format($voos->total(), 0, ',', '.') }} resultado(s) encontrado(s)</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-sm btn-outline-danger" id="btnExportarPDF">
                    <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnExportarCSV">
                    <i class="bi bi-filetype-csv me-1"></i> CSV
                </button>
            </div>
        </div>

        @if($voos->isEmpty())
            <div class="empty-state">
                <span class="empty-icon"><i class="bi bi-airplane"></i></span>
                <h3 class="h5 fw-bold">{{ $hasFilters ? 'Nenhum voo encontrado' : 'Nenhum voo cadastrado' }}</h3>
                <p class="text-muted">{{ $hasFilters ? 'Revise os filtros aplicados e tente novamente.' : 'Cadastre o primeiro voo para iniciar as operações.' }}</p>
                @if($hasFilters)
                    <a href="{{ route('voos.index', request('origem') === 'registros' ? ['origem' => 'registros'] : []) }}" class="btn btn-outline-primary">Limpar filtros</a>
                @else
                    <a href="{{ route('voos.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Cadastrar voo</a>
                @endif
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover flights-table align-middle" id="voosTable">
                    <thead>
                        <tr>
                            <th>Voo</th>
                            <th>Aeroporto</th>
                            <th>Companhia</th>
                            <th>Aeronave</th>
                            <th>Operação</th>
                            <th class="text-center">Quantidade</th>
                            <th class="text-end">Passageiros</th>
                            <th class="text-center">Obj.</th>
                            <th class="text-center">Pont.</th>
                            <th class="text-center">Serv.</th>
                            <th class="text-center">Pátio</th>
                            <th class="text-center">Média</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($voos as $voo)
                            @php
                                $porte = match($voo->tipo_aeronave) {
                                    'PC' => 'Pequeno',
                                    'MC' => 'Médio',
                                    'LC' => 'Grande',
                                    default => $voo->tipo_aeronave ?: '—',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('voos.show', $voo) }}" class="flight-code text-decoration-none">
                                        {{ $voo->id_voo }}
                                    </a>
                                    <div class="small text-muted mt-1">{{ $voo->created_at?->format('d/m/Y') }}</div>
                                </td>
                                <td>
                                    <strong>{{ $voo->aeroporto?->nome_aeroporto ?? 'Não informado' }}</strong>
                                </td>
                                <td>
                                    <strong>{{ $voo->companhiaAerea?->codigo ?? '—' }}</strong>
                                    <div class="small text-muted">{{ $voo->companhiaAerea?->nome }}</div>
                                </td>
                                <td>
                                    <strong>{{ $voo->aeronave?->modelo ?? 'Não informado' }}</strong>
                                    <div class="small text-muted">{{ $porte }}</div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill {{ $voo->tipo_voo === 'Regular' ? 'text-bg-success' : 'text-bg-info' }}">
                                        {{ $voo->tipo_voo }}
                                    </span>
                                    <div class="small text-muted mt-1">{{ $voo->horario_texto }}</div>
                                </td>
                                <td class="text-center fw-semibold">{{ number_format($voo->qtd_voos, 0, ',', '.') }}</td>
                                <td class="text-end fw-semibold">{{ number_format($voo->total_passageiros, 0, ',', '.') }}</td>
                                @foreach(['nota_obj', 'nota_pontualidade', 'nota_servicos', 'nota_patio'] as $field)
                                    <td class="text-center">
                                        <span class="rating {{ $ratingClass($voo->{$field}) }}">{{ $voo->{$field} ?: '—' }}</span>
                                    </td>
                                @endforeach
                                <td class="text-center">
                                    <span class="rating {{ $ratingClass($voo->media_notas) }}">
                                        {{ $voo->media_notas ? number_format($voo->media_notas, 1, ',', '.') : '—' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('voos.show', $voo) }}" class="btn btn-sm btn-outline-secondary" title="Ver voo">
                                            <i class="bi bi-eye"></i><span class="visually-hidden">Ver</span>
                                        </a>
                                        <a href="{{ route('voos.edit', $voo) }}" class="btn btn-sm btn-outline-primary" title="Editar voo">
                                            <i class="bi bi-pencil"></i><span class="visually-hidden">Editar</span>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Excluir voo"
                                                data-delete-flight data-flight-id="{{ $voo->id }}" data-flight-code="{{ $voo->id_voo }}">
                                            <i class="bi bi-trash"></i><span class="visually-hidden">Excluir</span>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $voo->id }}" action="{{ route('voos.destroy', $voo) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <footer class="list-footer d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div class="small text-muted">
                    Mostrando <strong>{{ $voos->firstItem() }}</strong>–<strong>{{ $voos->lastItem() }}</strong>
                    de <strong>{{ $voos->total() }}</strong>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <label class="d-flex align-items-center gap-2 small text-muted">
                        Por página
                        <select id="perPageSelect" class="form-select form-select-sm" style="width: 5rem">
                            @foreach([10, 25, 50, 100] as $size)
                                <option value="{{ $size }}" @selected($perPage === $size)>{{ $size }}</option>
                            @endforeach
                        </select>
                    </label>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ $voos->previousPageUrl() ?: '#' }}"
                           class="page-button {{ $voos->onFirstPage() ? 'disabled' : '' }}" aria-label="Página anterior">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                        <span class="small fw-semibold">Página {{ $voos->currentPage() }} de {{ $voos->lastPage() }}</span>
                        <a href="{{ $voos->nextPageUrl() ?: '#' }}"
                           class="page-button {{ $voos->hasMorePages() ? '' : 'disabled' }}" aria-label="Próxima página">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </footer>
        @endif
    </section>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h2 class="modal-title h5 fw-bold"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Excluir voo</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                Confirma a exclusão do voo <strong id="vooIdToDelete"></strong>? Esta ação não poderá ser desfeita.
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn"><i class="bi bi-trash me-1"></i> Excluir</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const filterForm = document.getElementById('filterForm');
    const searchInput = document.getElementById('searchInput');
    let searchTimer;

    ['filterTipo', 'filterHorario', 'filterData'].forEach((id) => {
        document.getElementById(id)?.addEventListener('change', () => filterForm.submit());
    });

    searchInput?.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => filterForm.submit(), 600);
    });

    document.getElementById('perPageSelect')?.addEventListener('change', (event) => {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', event.target.value);
        url.searchParams.delete('page');
        window.location.href = url.toString();
    });

    const exportFile = (route, button, loadingText) => {
        const params = new URLSearchParams(new FormData(filterForm));
        const original = button.innerHTML;
        button.disabled = true;
        button.innerHTML = `<span class="loading-spinner me-1"></span>${loadingText}`;
        window.location.href = `${route}?${params.toString()}`;
        setTimeout(() => {
            button.disabled = false;
            button.innerHTML = original;
        }, 1800);
    };

    const pdfButton = document.getElementById('btnExportarPDF');
    const csvButton = document.getElementById('btnExportarCSV');
    pdfButton?.addEventListener('click', () => exportFile(@json(route('voos.export.pdf')), pdfButton, 'Gerando...'));
    csvButton?.addEventListener('click', () => exportFile(@json(route('voos.export.csv')), csvButton, 'Exportando...'));

    const modalElement = document.getElementById('deleteModal');
    const deleteModal = modalElement ? new bootstrap.Modal(modalElement) : null;
    let deleteFormId = null;

    document.querySelectorAll('[data-delete-flight]').forEach((button) => {
        button.addEventListener('click', () => {
            deleteFormId = `delete-form-${button.dataset.flightId}`;
            document.getElementById('vooIdToDelete').textContent = button.dataset.flightCode;
            deleteModal?.show();
        });
    });

    document.getElementById('confirmDeleteBtn')?.addEventListener('click', () => {
        if (deleteFormId) document.getElementById(deleteFormId)?.submit();
    });
});
</script>
@endpush
