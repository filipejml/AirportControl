@extends('layouts.app')

@section('title', 'Gerenciar aeronaves')

@push('styles')
<style>
    .aircraft-page {
        --aircraft-ink: #172033;
        --aircraft-muted: #667085;
        --aircraft-border: #e4e7ec;
        color: var(--aircraft-ink);
    }

    .aircraft-hero {
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
        border: 1px solid var(--aircraft-border);
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
    }

    .aircraft-list {
        overflow: hidden;
        border: 1px solid var(--aircraft-border);
        border-radius: 1.15rem;
        background: #fff;
        box-shadow: 0 8px 24px rgba(16, 24, 40, .04);
    }

    .list-toolbar {
        padding: 1.2rem 1.3rem;
        border-bottom: 1px solid var(--aircraft-border);
    }

    .list-toolbar .form-control,
    .list-toolbar .form-select {
        min-height: 2.75rem;
        border-color: #d0d5dd;
        border-radius: .75rem;
    }

    .search-box { position: relative; width: min(100%, 22rem); }
    .search-box i {
        position: absolute;
        top: 50%;
        left: .9rem;
        color: #98a2b3;
        transform: translateY(-50%);
    }
    .search-box .form-control { padding-left: 2.5rem; }

    .aircraft-table {
        min-width: 920px;
        margin: 0;
    }

    .aircraft-table thead th {
        padding: .8rem 1rem;
        border-bottom-width: 1px;
        color: var(--aircraft-muted);
        background: #f9fafb;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
    }

    .aircraft-table tbody td {
        padding: 1rem;
        border-color: #f0f1f3;
    }

    .aircraft-avatar {
        display: grid;
        width: 2.7rem;
        height: 2.7rem;
        place-items: center;
        flex: 0 0 auto;
        border-radius: .85rem;
        color: #155eef;
        background: #eff4ff;
        font-size: 1.1rem;
    }

    .aircraft-name {
        color: var(--aircraft-ink);
        font-weight: 700;
        text-decoration: none;
    }
    .aircraft-name:hover { color: #155eef; }

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

    @media (max-width: 767.98px) {
        .aircraft-hero { border-radius: 1.15rem; }
        .hero-actions, .hero-actions .btn, .search-box { width: 100%; }
    }
</style>
@endpush

@section('content')
@php
    $totalAeronaves = $aeronaves->count();
    $capacidadeTotal = $aeronaves->sum('capacidade');
    $fabricantesCount = $aeronaves->pluck('fabricante_id')->filter()->unique()->count();
    $vinculadas = $aeronaves->filter(fn ($aeronave) => $aeronave->companhias->isNotEmpty())->count();
@endphp

<div class="aircraft-page pb-5">
    <header class="aircraft-hero mb-4">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
            <div class="d-flex align-items-center gap-3">
                <span class="hero-symbol" aria-hidden="true"><i class="bi bi-airplane"></i></span>
                <div>
                    <div class="small text-white-50 fw-semibold text-uppercase mb-1">Administração</div>
                    <h1 class="h2 fw-bold mb-1">Gerenciar aeronaves</h1>
                    <p class="mb-0 text-white-50">Consulte modelos, fabricantes, capacidades e vínculos operacionais.</p>
                </div>
            </div>
            <div class="hero-actions d-flex flex-column flex-sm-row gap-2">
                @if(request('origem') === 'registros')
                    <a href="{{ route('registros') }}" class="btn btn-outline-light fw-semibold px-4 py-2">
                        <i class="bi bi-arrow-left me-1"></i> Voltar para registros
                    </a>
                @endif
                <a href="{{ route('aeronaves.create') }}" class="btn btn-light text-primary fw-semibold px-4 py-2">
                    <i class="bi bi-plus-lg me-1"></i> Nova aeronave
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

    <section class="row g-3 mb-4" aria-label="Resumo de aeronaves">
        <div class="col-6 col-xl-3">
            <div class="metric-card d-flex align-items-center gap-3">
                <span class="metric-icon text-primary bg-primary-subtle"><i class="bi bi-airplane"></i></span>
                <div><div class="metric-value">{{ $totalAeronaves }}</div><div class="small text-muted">Modelos</div></div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="metric-card d-flex align-items-center gap-3">
                <span class="metric-icon text-success bg-success-subtle"><i class="bi bi-people"></i></span>
                <div><div class="metric-value">{{ number_format($capacidadeTotal, 0, ',', '.') }}</div><div class="small text-muted">Capacidade total</div></div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="metric-card d-flex align-items-center gap-3">
                <span class="metric-icon text-info bg-info-subtle"><i class="bi bi-tools"></i></span>
                <div><div class="metric-value">{{ $fabricantesCount }}</div><div class="small text-muted">Fabricantes</div></div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="metric-card d-flex align-items-center gap-3">
                <span class="metric-icon text-warning bg-warning-subtle"><i class="bi bi-buildings"></i></span>
                <div><div class="metric-value">{{ $vinculadas }}</div><div class="small text-muted">Com companhia</div></div>
            </div>
        </div>
    </section>

    <section class="aircraft-list">
        <div class="list-toolbar d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3">
            <div>
                <h2 class="h5 fw-bold mb-1">Aeronaves cadastradas</h2>
                <p class="small text-muted mb-0"><span id="aircraftVisibleCount">{{ $totalAeronaves }}</span> resultado(s) exibido(s)</p>
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="search" id="aircraftSearch" class="form-control"
                           placeholder="Buscar modelo ou fabricante" aria-label="Buscar aeronaves">
                </div>
                <select id="aircraftSizeFilter" class="form-select" aria-label="Filtrar por porte">
                    <option value="">Todos os portes</option>
                    <option value="PC">Pequeno porte</option>
                    <option value="MC">Médio porte</option>
                    <option value="LC">Grande porte</option>
                </select>
            </div>
        </div>

        @if($aeronaves->isEmpty())
            <div class="empty-state">
                <span class="empty-icon"><i class="bi bi-airplane"></i></span>
                <h3 class="h5 fw-bold">Nenhuma aeronave cadastrada</h3>
                <p class="text-muted">Cadastre o primeiro modelo para começar a montar as frotas.</p>
                <a href="{{ route('aeronaves.create') }}" class="btn btn-primary">Nova aeronave</a>
            </div>
        @else
            <div class="table-responsive" id="aircraftTableWrapper">
                <table class="table table-hover aircraft-table align-middle">
                    <thead>
                        <tr>
                            <th>Aeronave</th>
                            <th>Fabricante</th>
                            <th>Capacidade</th>
                            <th>Porte</th>
                            <th>Companhias</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($aeronaves as $aeronave)
                            @php
                                $porte = match($aeronave->porte) {
                                    'PC' => ['Pequeno', 'text-bg-info'],
                                    'MC' => ['Médio', 'text-bg-warning'],
                                    'LC' => ['Grande', 'text-bg-danger'],
                                    default => ['Não classificado', 'text-bg-secondary'],
                                };
                            @endphp
                            <tr data-aircraft-row data-size="{{ $aeronave->porte }}"
                                data-search="{{ Str::lower($aeronave->modelo . ' ' . ($aeronave->fabricante?->nome ?? '')) }}">
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="aircraft-avatar"><i class="bi bi-airplane"></i></span>
                                        <div>
                                            <a href="{{ route('aeronaves.show', $aeronave) }}" class="aircraft-name">{{ $aeronave->modelo }}</a>
                                            <div class="small text-muted">ID #{{ $aeronave->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($aeronave->fabricante)
                                        <a href="{{ route('fabricantes.show', $aeronave->fabricante) }}" class="text-decoration-none text-dark fw-semibold">
                                            {{ $aeronave->fabricante->nome }}
                                        </a>
                                    @else
                                        <span class="text-muted">Não informado</span>
                                    @endif
                                </td>
                                <td><strong>{{ number_format($aeronave->capacidade, 0, ',', '.') }}</strong> <span class="small text-muted">passageiros</span></td>
                                <td><span class="badge rounded-pill {{ $porte[1] }}">{{ $porte[0] }}</span></td>
                                <td>
                                    <span class="badge rounded-pill text-bg-light border">
                                        <i class="bi bi-buildings me-1"></i>{{ $aeronave->companhias->count() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('aeronaves.show', $aeronave) }}" class="btn btn-sm btn-outline-secondary" title="Ver aeronave">
                                            <i class="bi bi-eye"></i><span class="visually-hidden">Ver</span>
                                        </a>
                                        <a href="{{ route('aeronaves.edit', $aeronave) }}" class="btn btn-sm btn-outline-primary" title="Editar aeronave">
                                            <i class="bi bi-pencil"></i><span class="visually-hidden">Editar</span>
                                        </a>
                                        <form action="{{ route('aeronaves.destroy', $aeronave) }}" method="POST"
                                              onsubmit="return confirm('Tem certeza que deseja excluir esta aeronave?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir aeronave">
                                                <i class="bi bi-trash"></i><span class="visually-hidden">Excluir</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="aircraftSearchEmpty" class="empty-state d-none">
                <span class="empty-icon"><i class="bi bi-search"></i></span>
                <h3 class="h5 fw-bold">Nenhuma aeronave encontrada</h3>
                <p class="text-muted mb-0">Tente outro modelo, fabricante ou porte.</p>
            </div>
        @endif
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const search = document.getElementById('aircraftSearch');
    const size = document.getElementById('aircraftSizeFilter');
    const rows = [...document.querySelectorAll('[data-aircraft-row]')];
    if (!search || !size || rows.length === 0) return;

    const update = () => {
        const term = search.value.trim().toLocaleLowerCase('pt-BR');
        let visible = 0;

        rows.forEach((row) => {
            const show = row.dataset.search.includes(term) && (!size.value || row.dataset.size === size.value);
            row.classList.toggle('d-none', !show);
            if (show) visible++;
        });

        document.getElementById('aircraftVisibleCount').textContent = visible;
        document.getElementById('aircraftTableWrapper').classList.toggle('d-none', visible === 0);
        document.getElementById('aircraftSearchEmpty').classList.toggle('d-none', visible !== 0);
    };

    search.addEventListener('input', update);
    size.addEventListener('change', update);
});
</script>
@endpush
