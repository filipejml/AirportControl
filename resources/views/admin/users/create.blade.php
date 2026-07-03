@extends('layouts.app')

@section('title', 'Novo usuário')

@push('styles')
<style>
    .user-create-page {
        --user-ink: #172033;
        --user-muted: #667085;
        --user-border: #e4e7ec;
        color: var(--user-ink);
    }

    .user-create-hero {
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

    .hero-back {
        border-color: rgba(255,255,255,.35);
        color: #fff;
        background: rgba(255,255,255,.1);
        font-weight: 600;
    }
    .hero-back:hover { color: #101828; border-color: #fff; background: #fff; }

    .user-form-shell {
        overflow: hidden;
        border: 1px solid var(--user-border);
        border-radius: 1.2rem;
        background: #fff;
        box-shadow: 0 10px 28px rgba(16, 24, 40, .06);
    }

    .form-content { padding: clamp(1.25rem, 3vw, 2rem); }

    .section-heading {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin-bottom: 1.5rem;
    }

    .section-number {
        display: grid;
        width: 2.25rem;
        height: 2.25rem;
        place-items: center;
        flex: 0 0 auto;
        border-radius: .7rem;
        color: #155eef;
        background: #eff4ff;
        font-weight: 750;
    }

    .user-create-page .form-control,
    .user-create-page .form-select,
    .user-create-page .input-group-text,
    .user-create-page .password-toggle {
        min-height: 3rem;
        border-color: #d0d5dd;
    }

    .user-create-page .form-control,
    .user-create-page .form-select { border-radius: .85rem; }

    .user-create-page .form-control:focus,
    .user-create-page .form-select:focus {
        border-color: #84adff;
        box-shadow: 0 0 0 .22rem rgba(21, 94, 239, .1);
    }

    .password-group .form-control { border-radius: .85rem 0 0 .85rem; }
    .password-toggle {
        border: 1px solid #d0d5dd;
        border-left: 0;
        border-radius: 0 .85rem .85rem 0;
        color: #667085;
        background: #fff;
    }

    .profile-option {
        position: relative;
        display: block;
        height: 100%;
        padding: 1rem;
        border: 1px solid var(--user-border);
        border-radius: .9rem;
        cursor: pointer;
        transition: border-color .15s ease, background .15s ease;
    }

    .profile-option:has(input:checked) {
        border-color: #155eef;
        background: #f5f8ff;
        box-shadow: inset 0 0 0 1px #155eef;
    }

    .profile-icon {
        display: grid;
        width: 2.5rem;
        height: 2.5rem;
        place-items: center;
        border-radius: .75rem;
    }

    .form-actions {
        padding: 1rem clamp(1.25rem, 3vw, 2rem);
        border-top: 1px solid var(--user-border);
        background: #fcfcfd;
    }

    @media (max-width: 767.98px) {
        .user-create-hero { border-radius: 1.15rem; }
        .hero-back { width: 100%; }
        .form-actions .btn { flex: 1; }
    }
</style>
@endpush

@section('content')
<div class="user-create-page pb-5">
    <header class="user-create-hero mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
            <div class="d-flex align-items-center gap-3">
                <span class="hero-symbol" aria-hidden="true"><i class="bi bi-person-plus-fill"></i></span>
                <div>
                    <div class="small text-white-50 fw-semibold text-uppercase mb-1">Administração</div>
                    <h1 class="h2 fw-bold mb-1">Criar novo usuário</h1>
                    <p class="mb-0 text-white-50">Cadastre uma conta e defina seu nível de acesso.</p>
                </div>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn hero-back px-3 py-2">
                <i class="bi bi-arrow-left me-1"></i> Voltar para usuários
            </a>
        </div>
    </header>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Revise os dados informados.</strong>
            <ul class="mb-0 mt-2 ps-4">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.store') }}" method="POST" class="user-form-shell">
        @csrf

        <div class="form-content">
            <section class="mb-5">
                <div class="section-heading">
                    <span class="section-number">1</span>
                    <div>
                        <h2 class="h5 fw-bold mb-1">Identificação</h2>
                        <p class="small text-muted mb-0">Informações pessoais e credenciais de acesso.</p>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <label for="name" class="form-label fw-semibold">Nome completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}"
                               placeholder="Ex.: Maria da Silva" required autocomplete="name">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-lg-6">
                        <label for="username" class="form-label fw-semibold">Nome de usuário <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror"
                               id="username" name="username" value="{{ old('username') }}"
                               placeholder="Ex.: maria.silva" required autocomplete="username">
                        @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email') }}"
                               placeholder="nome@exemplo.com" required autocomplete="email">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-lg-6">
                        <label for="password" class="form-label fw-semibold">Senha <span class="text-danger">*</span></label>
                        <div class="input-group password-group">
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" minlength="6"
                                   required autocomplete="new-password">
                            <button type="button" class="password-toggle px-3" data-password-target="password" aria-label="Exibir senha">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="form-text">Use pelo menos 6 caracteres.</div>
                        @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-lg-6">
                        <label for="password_confirmation" class="form-label fw-semibold">Confirmar senha <span class="text-danger">*</span></label>
                        <div class="input-group password-group">
                            <input type="password" class="form-control"
                                   id="password_confirmation" name="password_confirmation"
                                   minlength="6" required autocomplete="new-password">
                            <button type="button" class="password-toggle px-3" data-password-target="password_confirmation" aria-label="Exibir confirmação de senha">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <div class="section-heading">
                    <span class="section-number">2</span>
                    <div>
                        <h2 class="h5 fw-bold mb-1">Nível de acesso</h2>
                        <p class="small text-muted mb-0">Defina quais recursos estarão disponíveis para esta conta.</p>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="profile-option">
                            <div class="d-flex align-items-start gap-3">
                                <span class="profile-icon text-info bg-info-subtle"><i class="bi bi-person"></i></span>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between gap-2">
                                        <strong>Usuário comum</strong>
                                        <input class="form-check-input" type="radio" name="tipo" value="1" @checked(old('tipo', '1') === '1')>
                                    </div>
                                    <p class="small text-muted mb-0 mt-1">Consulta dashboards, informações e relatórios disponíveis.</p>
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label class="profile-option">
                            <div class="d-flex align-items-start gap-3">
                                <span class="profile-icon text-danger bg-danger-subtle"><i class="bi bi-shield-lock"></i></span>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between gap-2">
                                        <strong>Administrador</strong>
                                        <input class="form-check-input" type="radio" name="tipo" value="0" @checked(old('tipo') === '0')>
                                    </div>
                                    <p class="small text-muted mb-0 mt-1">Acesso completo aos cadastros e configurações administrativas.</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
                @error('tipo')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
            </section>
        </div>

        <div class="form-actions d-flex justify-content-end gap-2">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary px-4">
                <i class="bi bi-arrow-left me-1"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-person-check me-1"></i> Criar usuário
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('[data-password-target]').forEach((button) => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.passwordTarget);
        const visible = input.type === 'text';
        input.type = visible ? 'password' : 'text';
        button.querySelector('i').className = `bi ${visible ? 'bi-eye' : 'bi-eye-slash'}`;
        button.setAttribute('aria-label', visible ? 'Exibir senha' : 'Ocultar senha');
    });
});
</script>
@endpush
