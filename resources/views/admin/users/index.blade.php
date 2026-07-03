@extends('layouts.app')

@section('title', 'Gerenciar usuários')

@push('styles')
<style>
    .users-page {
        --users-ink: #172033;
        --users-muted: #667085;
        --users-border: #e4e7ec;
        color: var(--users-ink);
    }

    .users-hero {
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
        border: 1px solid var(--users-border);
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

    .users-card {
        overflow: hidden;
        border: 1px solid var(--users-border);
        border-radius: 1.15rem;
        background: #fff;
        box-shadow: 0 8px 24px rgba(16, 24, 40, .04);
    }

    .users-toolbar {
        padding: 1.2rem 1.3rem;
        border-bottom: 1px solid var(--users-border);
    }

    .users-toolbar .form-control,
    .users-toolbar .form-select {
        min-height: 2.7rem;
        border-color: #d0d5dd;
        border-radius: .75rem;
    }

    .search-field { position: relative; min-width: min(100%, 19rem); }
    .search-field i {
        position: absolute;
        top: 50%;
        left: .85rem;
        color: #98a2b3;
        transform: translateY(-50%);
    }
    .search-field .form-control { padding-left: 2.4rem; }

    .users-table {
        min-width: 850px;
        margin: 0;
    }

    .users-table thead th {
        padding: .8rem 1rem;
        border-bottom-width: 1px;
        color: var(--users-muted);
        background: #f9fafb;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
    }

    .users-table tbody td {
        padding: 1rem;
        border-color: #f0f1f3;
    }

    .user-avatar {
        display: grid;
        width: 2.7rem;
        height: 2.7rem;
        place-items: center;
        flex: 0 0 auto;
        border-radius: 50%;
        color: #155eef;
        background: #eff4ff;
        font-weight: 750;
        text-transform: uppercase;
    }

    .current-user { background: #f9fafb; }

    .users-footer {
        padding: 1rem 1.25rem;
        border-top: 1px solid var(--users-border);
        background: #fcfcfd;
    }

    .page-button {
        display: inline-grid;
        width: 2.35rem;
        height: 2.35rem;
        place-items: center;
        border: 1px solid #d0d5dd;
        border-radius: .65rem;
        color: #344054;
        background: #fff;
        text-decoration: none;
    }
    .page-button:hover { color: #155eef; border-color: #84adff; }
    .page-button.disabled { color: #d0d5dd; pointer-events: none; }

    .empty-state { padding: 4.5rem 1rem; text-align: center; }
    .empty-state i { color: #98a2b3; font-size: 2.5rem; }

    @media (max-width: 767.98px) {
        .users-hero { border-radius: 1.15rem; }
        .hero-action, .hero-action .btn { width: 100%; }
        .search-field { width: 100%; }
    }
</style>
@endpush

@section('content')
@php $hasFilters = request()->filled('search') || request()->filled('tipo'); @endphp

<div class="users-page pb-5">
    <header class="users-hero mb-4">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
            <div class="d-flex align-items-center gap-3">
                <span class="hero-symbol" aria-hidden="true"><i class="bi bi-people-fill"></i></span>
                <div>
                    <div class="small text-white-50 fw-semibold text-uppercase mb-1">Administração</div>
                    <h1 class="h2 fw-bold mb-1">Gerenciar usuários</h1>
                    <p class="mb-0 text-white-50">Controle contas, perfis e níveis de acesso ao sistema.</p>
                </div>
            </div>
            <div class="hero-action d-flex flex-column flex-sm-row gap-2">
                <a href="{{ route('registros') }}" class="btn btn-outline-light fw-semibold px-4 py-2">
                    <i class="bi bi-arrow-left me-1"></i> Voltar para registros
                </a>
                <a href="{{ route('admin.users.create') }}" class="btn btn-light text-primary fw-semibold px-4 py-2">
                    <i class="bi bi-person-plus me-1"></i> Novo usuário
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

    <section class="row g-3 mb-4" aria-label="Resumo de usuários">
        <div class="col-6 col-xl-3">
            <div class="metric-card d-flex align-items-center gap-3">
                <span class="metric-icon text-primary bg-primary-subtle"><i class="bi bi-people"></i></span>
                <div><div class="metric-value">{{ $estatisticas['total'] }}</div><div class="small text-muted">Usuários</div></div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="metric-card d-flex align-items-center gap-3">
                <span class="metric-icon text-danger bg-danger-subtle"><i class="bi bi-shield-lock"></i></span>
                <div><div class="metric-value">{{ $estatisticas['administradores'] }}</div><div class="small text-muted">Administradores</div></div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="metric-card d-flex align-items-center gap-3">
                <span class="metric-icon text-info bg-info-subtle"><i class="bi bi-person"></i></span>
                <div><div class="metric-value">{{ $estatisticas['comuns'] }}</div><div class="small text-muted">Usuários comuns</div></div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="metric-card d-flex align-items-center gap-3">
                <span class="metric-icon text-success bg-success-subtle"><i class="bi bi-person-check"></i></span>
                <div><div class="metric-value">{{ $estatisticas['recentes'] }}</div><div class="small text-muted">Novos em 30 dias</div></div>
            </div>
        </div>
    </section>

    <section class="users-card">
        <div class="users-toolbar">
            <form method="GET" action="{{ route('admin.users.index') }}"
                  class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
                <div>
                    <h2 class="h5 fw-bold mb-1">Usuários cadastrados</h2>
                    <p class="small text-muted mb-0">{{ $users->total() }} resultado(s) encontrado(s)</p>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <div class="search-field">
                        <i class="bi bi-search"></i>
                        <input type="search" name="search" class="form-control"
                               value="{{ request('search') }}" placeholder="Nome, usuário ou email">
                    </div>
                    <select name="tipo" class="form-select" aria-label="Filtrar por perfil">
                        <option value="">Todos os perfis</option>
                        <option value="0" @selected(request('tipo') === '0')>Administradores</option>
                        <option value="1" @selected(request('tipo') === '1')>Usuários comuns</option>
                    </select>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-funnel"></i><span class="visually-hidden">Filtrar</span></button>
                    @if($hasFilters)
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i><span class="visually-hidden">Limpar filtros</span></a>
                    @endif
                </div>
            </form>
        </div>

        @if($users->isEmpty())
            <div class="empty-state">
                <i class="bi bi-person-x d-block mb-3"></i>
                <h3 class="h5 fw-bold">Nenhum usuário encontrado</h3>
                <p class="text-muted">{{ $hasFilters ? 'Revise os filtros e tente novamente.' : 'Cadastre o primeiro usuário do sistema.' }}</p>
                @if($hasFilters)
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary">Limpar filtros</a>
                @else
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Novo usuário</a>
                @endif
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover users-table align-middle">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Identificação</th>
                            <th>Perfil</th>
                            <th>Cadastro</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr class="{{ auth()->id() === $user->id ? 'current-user' : '' }}">
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="user-avatar">{{ mb_substr($user->name, 0, 1) }}</span>
                                        <div>
                                            <strong>{{ $user->name }}</strong>
                                            @if(auth()->id() === $user->id)
                                                <span class="badge rounded-pill text-bg-light border ms-1">Você</span>
                                            @endif
                                            <div class="small text-muted">{{ '@' . $user->username }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $user->email }}</div>
                                    <div class="small text-muted">ID #{{ $user->id }}</div>
                                </td>
                                <td>
                                    @if((int) $user->tipo === 0)
                                        <span class="badge rounded-pill text-bg-danger"><i class="bi bi-shield-lock me-1"></i>Administrador</span>
                                    @else
                                        <span class="badge rounded-pill text-bg-info"><i class="bi bi-person me-1"></i>Usuário comum</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $user->created_at?->format('d/m/Y') }}</strong>
                                    <div class="small text-muted">às {{ $user->created_at?->format('H:i') }}</div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="Editar usuário">
                                            <i class="bi bi-pencil"></i><span class="visually-hidden">Editar</span>
                                        </a>
                                        @if(auth()->id() !== $user->id)
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                                  onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir usuário">
                                                    <i class="bi bi-trash"></i><span class="visually-hidden">Excluir</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <footer class="users-footer d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                <span class="small text-muted">
                    Mostrando {{ $users->firstItem() }}–{{ $users->lastItem() }} de {{ $users->total() }}
                </span>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ $users->previousPageUrl() ?: '#' }}"
                       class="page-button {{ $users->onFirstPage() ? 'disabled' : '' }}" aria-label="Página anterior">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                    <span class="small fw-semibold">Página {{ $users->currentPage() }} de {{ $users->lastPage() }}</span>
                    <a href="{{ $users->nextPageUrl() ?: '#' }}"
                       class="page-button {{ $users->hasMorePages() ? '' : 'disabled' }}" aria-label="Próxima página">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </footer>
        @endif
    </section>
</div>
@endsection
