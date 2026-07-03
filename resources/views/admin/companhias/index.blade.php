@extends('layouts.app')

@section('title', 'Companhias aéreas')

@push('styles')
<style>
    .companies-page {
        --companies-ink: #172033;
        --companies-muted: #667085;
        --companies-border: #e4e7ec;
        color: var(--companies-ink);
    }

    .companies-header {
        position: relative;
        overflow: hidden;
        padding: clamp(1.5rem, 4vw, 2.5rem);
        border-radius: 1.5rem;
        color: #fff;
        background:
            radial-gradient(circle at 88% 18%, rgba(255,255,255,.16), transparent 24%),
            linear-gradient(125deg, #101828 0%, #1849a9 60%, #2e90fa 100%);
        box-shadow: 0 20px 45px rgba(16, 24, 40, .15);
    }

    .header-icon {
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

    .summary-card {
        height: 100%;
        padding: 1.1rem 1.2rem;
        border: 1px solid var(--companies-border);
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 8px 22px rgba(16, 24, 40, .045);
    }

    .summary-icon {
        display: grid;
        width: 2.55rem;
        height: 2.55rem;
        place-items: center;
        border-radius: .75rem;
        font-size: 1.1rem;
    }

    .summary-value {
        font-size: 1.65rem;
        font-weight: 750;
        line-height: 1;
        letter-spacing: -.03em;
    }

    .list-card {
        overflow: hidden;
        border: 1px solid var(--companies-border);
        border-radius: 1.25rem;
        background: #fff;
        box-shadow: 0 8px 24px rgba(16, 24, 40, .04);
    }

    .toolbar {
        padding: 1.2rem 1.3rem;
        border-bottom: 1px solid var(--companies-border);
    }

    .search-box {
        position: relative;
        width: min(100%, 390px);
    }

    .search-box i {
        position: absolute;
        top: 50%;
        left: .9rem;
        color: #98a2b3;
        transform: translateY(-50%);
    }

    .search-box .form-control {
        min-height: 2.75rem;
        padding-left: 2.6rem;
        border-color: var(--companies-border);
        border-radius: .8rem;
    }

    .search-box .form-control:focus {
        border-color: #84adff;
        box-shadow: 0 0 0 .2rem rgba(21, 94, 239, .1);
    }

    .companies-table {
        min-width: 820px;
        margin: 0;
    }

    .companies-table thead th {
        padding: .85rem 1rem;
        border-bottom-width: 1px;
        color: var(--companies-muted);
        background: #f9fafb;
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .055em;
        text-transform: uppercase;
    }

    .companies-table tbody td {
        padding: 1rem;
        border-color: #f0f1f3;
    }

    .company-avatar {
        display: grid;
        width: 2.75rem;
        height: 2.75rem;
        place-items: center;
        flex: 0 0 auto;
        border-radius: .85rem;
        color: #155eef;
        background: #eff4ff;
        font-size: 1.1rem;
    }

    .company-name {
        color: var(--companies-ink);
        font-weight: 700;
        text-decoration: none;
    }

    .company-name:hover { color: #155eef; }

    .code-badge {
        display: inline-flex;
        align-items: center;
        min-width: 3.5rem;
        justify-content: center;
        padding: .4rem .65rem;
        border: 1px solid #d0d5dd;
        border-radius: .6rem;
        color: #344054;
        background: #f9fafb;
        font-size: .78rem;
        font-weight: 750;
        letter-spacing: .08em;
    }

    .availability-bar {
        width: 7rem;
        height: .4rem;
        overflow: hidden;
        border-radius: 999px;
        background: #eaecf0;
    }

    .availability-bar > span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: #12b76a;
    }

    .empty-state {
        padding: 4.5rem 1rem;
        text-align: center;
    }

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
        .companies-header { border-radius: 1.15rem; }
        .header-actions, .header-actions .btn { width: 100%; }
        .toolbar .search-box { width: 100%; }
    }
</style>
@endpush

@section('content')
@php
    $totalCompanhias = $companhias->count();
    $totalAeronaves = $companhias->sum('aeronaves_count');
    $totalDisponiveis = $companhias->sum('aeronaves_disponiveis_count');
    $companhiasComFrota = $companhias->where('aeronaves_count', '>', 0)->count();
@endphp

<div class="companies-page pb-5">
    <header class="companies-header mb-4">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
            <div class="d-flex align-items-center gap-3">
                <span class="header-icon" aria-hidden="true"><i class="bi bi-buildings"></i></span>
                <div>
                    <div class="small text-white-50 fw-semibold text-uppercase mb-1">Administração</div>
                    <h1 class="h2 fw-bold mb-1">Companhias aéreas</h1>
                    <p class="mb-0 text-white-50">Gerencie cadastros, frotas e disponibilidade operacional.</p>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('companhias.create') }}" class="btn btn-light text-primary fw-semibold px-4 py-2">
                    <i class="bi bi-plus-lg me-1"></i> Nova companhia
                </a>
            </div>
        </div>
    </header>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    @endif

    <section class="row g-3 mb-4" aria-label="Resumo das companhias">
        <div class="col-6 col-xl-3">
            <div class="summary-card d-flex align-items-center gap-3">
                <span class="summary-icon text-primary bg-primary-subtle"><i class="bi bi-buildings"></i></span>
                <div><div class="summary-value">{{ $totalCompanhias }}</div><div class="small text-muted">Companhias</div></div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="summary-card d-flex align-items-center gap-3">
                <span class="summary-icon text-info bg-info-subtle"><i class="bi bi-airplane"></i></span>
                <div><div class="summary-value">{{ $totalAeronaves }}</div><div class="small text-muted">Aeronaves</div></div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="summary-card d-flex align-items-center gap-3">
                <span class="summary-icon text-success bg-success-subtle"><i class="bi bi-check2-circle"></i></span>
                <div><div class="summary-value">{{ $totalDisponiveis }}</div><div class="small text-muted">Disponíveis</div></div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="summary-card d-flex align-items-center gap-3">
                <span class="summary-icon text-warning bg-warning-subtle"><i class="bi bi-diagram-3"></i></span>
                <div><div class="summary-value">{{ $companhiasComFrota }}</div><div class="small text-muted">Com frota</div></div>
            </div>
        </div>
    </section>

    <section class="list-card">
        <div class="toolbar d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h2 class="h5 fw-bold mb-1">Companhias cadastradas</h2>
                <p class="small text-muted mb-0"><span id="visibleCount">{{ $totalCompanhias }}</span> resultado(s) exibido(s)</p>
            </div>
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="search" id="companySearch" class="form-control"
                       placeholder="Buscar por nome, código ou ID"
                       aria-label="Buscar companhias">
            </div>
        </div>

        @if($totalCompanhias > 0)
            <div class="table-responsive">
                <table class="table companies-table align-middle" id="companiesTable">
                    <thead>
                        <tr>
                            <th>Companhia</th>
                            <th>Código</th>
                            <th>Frota</th>
                            <th>Disponibilidade</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($companhias as $companhia)
                            @php
                                $total = (int) $companhia->aeronaves_count;
                                $disponiveis = (int) $companhia->aeronaves_disponiveis_count;
                                $percentual = $total > 0 ? round(($disponiveis / $total) * 100) : 0;
                            @endphp
                            <tr data-search="{{ \Illuminate\Support\Str::lower($companhia->id . ' ' . $companhia->nome . ' ' . $companhia->codigo) }}">
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="company-avatar"><i class="bi bi-airplane-engines"></i></span>
                                        <div>
                                            <a href="{{ route('companhias.show', $companhia) }}" class="company-name">
                                                {{ $companhia->nome }}
                                            </a>
                                            <div class="small text-muted">ID #{{ $companhia->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($companhia->codigo)
                                        <span class="code-badge">{{ $companhia->codigo }}</span>
                                    @else
                                        <span class="small text-muted">Não informado</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $total }}</strong>
                                    <span class="small text-muted">{{ $total === 1 ? 'aeronave' : 'aeronaves' }}</span>
                                </td>
                                <td>
                                    @if($total > 0)
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="availability-bar" aria-hidden="true">
                                                <span style="width: {{ $percentual }}%"></span>
                                            </div>
                                            <span class="small fw-semibold">{{ $disponiveis }}/{{ $total }}</span>
                                        </div>
                                    @else
                                        <span class="badge rounded-pill text-bg-light border">Sem frota</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('companhias.show', $companhia) }}"
                                           class="btn btn-sm btn-outline-secondary" title="Ver detalhes">
                                            <i class="bi bi-eye"></i><span class="visually-hidden">Ver detalhes</span>
                                        </a>
                                        <a href="{{ route('companhias.edit', $companhia) }}"
                                           class="btn btn-sm btn-outline-primary" title="Editar companhia">
                                            <i class="bi bi-pencil"></i><span class="visually-hidden">Editar</span>
                                        </a>
                                        <form action="{{ route('companhias.destroy', $companhia) }}" method="POST"
                                              onsubmit="return confirm('Tem certeza que deseja excluir esta companhia? Esta ação não pode ser desfeita.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir companhia">
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
            <div id="searchEmpty" class="empty-state d-none">
                <span class="empty-icon"><i class="bi bi-search"></i></span>
                <h3 class="h5 fw-bold">Nenhuma companhia encontrada</h3>
                <p class="text-muted mb-0">Tente buscar usando outro nome, código ou ID.</p>
            </div>
        @else
            <div class="empty-state">
                <span class="empty-icon"><i class="bi bi-buildings"></i></span>
                <h3 class="h5 fw-bold">Nenhuma companhia cadastrada</h3>
                <p class="text-muted">Cadastre a primeira companhia para começar a montar as frotas.</p>
                <a href="{{ route('companhias.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Cadastrar companhia
                </a>
            </div>
        @endif
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const search = document.getElementById('companySearch');
    const table = document.getElementById('companiesTable');
    if (!search || !table) return;

    const rows = [...table.querySelectorAll('tbody tr')];
    const visibleCount = document.getElementById('visibleCount');
    const emptyState = document.getElementById('searchEmpty');

    search.addEventListener('input', () => {
        const term = search.value.trim().toLocaleLowerCase('pt-BR');
        let count = 0;

        rows.forEach((row) => {
            const visible = row.dataset.search.includes(term);
            row.classList.toggle('d-none', !visible);
            if (visible) count++;
        });

        visibleCount.textContent = count;
        table.parentElement.classList.toggle('d-none', count === 0);
        emptyState.classList.toggle('d-none', count !== 0);
    });
});
</script>
@endpush
