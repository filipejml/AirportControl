@extends('layouts.app')

@section('title', 'Nova companhia aérea')

@push('styles')
<style>
    .company-form-page {
        --form-ink: #172033;
        --form-muted: #667085;
        --form-border: #e4e7ec;
        color: var(--form-ink);
    }

    .form-hero {
        position: relative;
        overflow: hidden;
        padding: clamp(1.5rem, 4vw, 2.4rem);
        border-radius: 1.5rem;
        color: #fff;
        background:
            radial-gradient(circle at 88% 15%, rgba(255,255,255,.16), transparent 24%),
            linear-gradient(125deg, #101828 0%, #1849a9 60%, #2e90fa 100%);
        box-shadow: 0 20px 45px rgba(16, 24, 40, .15);
    }

    .hero-icon {
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

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        border-color: rgba(255,255,255,.35);
        color: #fff;
        background: rgba(255,255,255,.1);
        font-weight: 600;
    }

    .back-button:hover {
        border-color: #fff;
        color: #101828;
        background: #fff;
    }

    .form-card {
        overflow: hidden;
        border: 1px solid var(--form-border);
        border-radius: 1.25rem;
        background: #fff;
        box-shadow: 0 8px 25px rgba(16, 24, 40, .05);
    }

    .form-section {
        padding: clamp(1.25rem, 3vw, 2rem);
    }

    .form-section + .form-section { border-top: 1px solid var(--form-border); }

    .step-number {
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

    .company-form-page .form-control {
        min-height: 3rem;
        padding: .7rem .95rem;
        border-color: #d0d5dd;
        border-radius: .8rem;
    }

    .company-form-page .form-control:focus {
        border-color: #84adff;
        box-shadow: 0 0 0 .22rem rgba(21, 94, 239, .1);
    }

    .field-status {
        position: absolute;
        top: 50%;
        right: .9rem;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .aircraft-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(215px, 1fr));
        gap: 1rem;
    }

    .aircraft-card {
        position: relative;
        min-height: 9.5rem;
        padding: 1.1rem;
        border: 1px solid var(--form-border);
        border-radius: 1rem;
        background: #fff;
        cursor: pointer;
        transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
        user-select: none;
    }

    .aircraft-card:hover {
        border-color: #84adff;
        box-shadow: 0 10px 24px rgba(16, 24, 40, .08);
        transform: translateY(-2px);
    }

    .aircraft-card:focus-visible {
        outline: 3px solid rgba(21, 94, 239, .22);
        outline-offset: 2px;
    }

    .aircraft-card.selected {
        border-color: #155eef;
        background: #f5f8ff;
        box-shadow: inset 0 0 0 1px #155eef;
    }

    .selection-check {
        display: grid;
        width: 1.65rem;
        height: 1.65rem;
        place-items: center;
        border: 1px solid #d0d5dd;
        border-radius: 50%;
        color: transparent;
        background: #fff;
        transition: all .18s ease;
    }

    .aircraft-card.selected .selection-check {
        border-color: #155eef;
        color: #fff;
        background: #155eef;
    }

    .aircraft-icon {
        display: grid;
        width: 2.5rem;
        height: 2.5rem;
        place-items: center;
        border-radius: .75rem;
        color: #155eef;
        background: #eff4ff;
    }

    .selection-summary {
        padding: .65rem .9rem;
        border-radius: .75rem;
        color: #344054;
        background: #f2f4f7;
        font-size: .85rem;
        font-weight: 600;
    }

    .form-actions {
        position: sticky;
        bottom: 0;
        z-index: 5;
        padding: 1rem clamp(1.25rem, 3vw, 2rem);
        border-top: 1px solid var(--form-border);
        background: rgba(255,255,255,.94);
        backdrop-filter: blur(10px);
    }

    @media (max-width: 767.98px) {
        .form-hero { border-radius: 1.15rem; }
        .back-button { width: 100%; justify-content: center; }
        .form-actions .btn { flex: 1; }
    }
</style>
@endpush

@section('content')
<div class="company-form-page pb-5">
    <header class="form-hero mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
            <div class="d-flex align-items-center gap-3">
                <span class="hero-icon" aria-hidden="true"><i class="bi bi-building-add"></i></span>
                <div>
                    <div class="small text-white-50 fw-semibold text-uppercase mb-1">Novo cadastro</div>
                    <h1 class="h2 fw-bold mb-1">Cadastrar companhia aérea</h1>
                    <p class="mb-0 text-white-50">Informe os dados principais e monte a frota inicial.</p>
                </div>
            </div>
            <a href="{{ route('companhias.index') }}" class="btn back-button px-3 py-2">
                <i class="bi bi-arrow-left"></i> Voltar para companhias
            </a>
        </div>
    </header>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm" role="alert">
            <div class="d-flex gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>
                    <strong>Revise os dados informados.</strong>
                    <ul class="mb-0 mt-1 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('companhias.store') }}" id="companhiaForm" class="form-card">
        @csrf

        <section class="form-section">
            <div class="d-flex gap-3 mb-4">
                <span class="step-number">1</span>
                <div>
                    <h2 class="h5 fw-bold mb-1">Identificação</h2>
                    <p class="small text-muted mb-0">Dados usados para identificar a companhia no sistema.</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <label for="nome" class="form-label fw-semibold">Nome da companhia <span class="text-danger">*</span></label>
                    <div class="position-relative">
                        <input type="text" class="form-control pe-5 @error('nome') is-invalid @enderror"
                               id="nome" name="nome" value="{{ old('nome') }}"
                               placeholder="Ex.: Azul Linhas Aéreas" maxlength="255"
                               required autocomplete="off">
                        <span class="field-status">
                            <span class="spinner-border spinner-border-sm text-primary d-none" id="nomeSpinner"></span>
                            <i class="bi bi-check-circle-fill text-success d-none" id="nomeCheckIcon"></i>
                            <i class="bi bi-x-circle-fill text-danger d-none" id="nomeXIcon"></i>
                        </span>
                    </div>
                    <div id="nomeFeedback" class="form-text">Use o nome comercial completo.</div>
                </div>

                <div class="col-lg-4">
                    <label for="codigo" class="form-label fw-semibold">Código <span class="text-danger">*</span></label>
                    <div class="position-relative">
                        <input type="text" class="form-control pe-5 text-uppercase @error('codigo') is-invalid @enderror"
                               id="codigo" name="codigo" value="{{ old('codigo') }}"
                               placeholder="Ex.: AZUL" minlength="2" maxlength="4"
                               pattern="[A-Za-z]{2,4}" required autocomplete="off">
                        <span class="field-status">
                            <span class="spinner-border spinner-border-sm text-primary d-none" id="codigoSpinner"></span>
                            <i class="bi bi-check-circle-fill text-success d-none" id="codigoCheckIcon"></i>
                            <i class="bi bi-x-circle-fill text-danger d-none" id="codigoXIcon"></i>
                        </span>
                    </div>
                    <div id="codigoFeedback" class="form-text">Informe de 2 a 4 letras.</div>
                </div>
            </div>
        </section>

        <section class="form-section">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
                <div class="d-flex gap-3">
                    <span class="step-number">2</span>
                    <div>
                        <h2 class="h5 fw-bold mb-1">Frota inicial <span class="text-muted fw-normal">(opcional)</span></h2>
                        <p class="small text-muted mb-0">Selecione as aeronaves que serão vinculadas à companhia.</p>
                    </div>
                </div>
                @if($aeronaves->isNotEmpty())
                    <div class="d-flex flex-wrap gap-2 align-self-md-start">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllBtn">
                            <i class="bi bi-check-all me-1"></i> Selecionar todas
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllBtn">
                            Limpar seleção
                        </button>
                    </div>
                @endif
            </div>

            @if($aeronaves->isNotEmpty())
                <div class="aircraft-grid" id="aeronavesContainer">
                    @foreach($aeronaves as $aeronave)
                        @php $selected = in_array($aeronave->id, old('aeronaves', [])); @endphp
                        <div class="aircraft-card {{ $selected ? 'selected' : '' }}"
                             data-aircraft-card role="checkbox" tabindex="0"
                             aria-checked="{{ $selected ? 'true' : 'false' }}">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                <span class="aircraft-icon"><i class="bi bi-airplane"></i></span>
                                <span class="selection-check"><i class="bi bi-check-lg"></i></span>
                            </div>
                            <h3 class="h6 fw-bold mb-2">{{ $aeronave->modelo }}</h3>
                            <div class="small text-muted mb-1">{{ $aeronave->fabricante?->nome ?? 'Fabricante não informado' }}</div>
                            <div class="small"><strong>{{ number_format($aeronave->capacidade, 0, ',', '.') }}</strong> passageiros</div>
                            <input type="checkbox" name="aeronaves[]" class="aeronave-checkbox visually-hidden"
                                   value="{{ $aeronave->id }}" {{ $selected ? 'checked' : '' }}>
                        </div>
                    @endforeach
                </div>
                <div class="selection-summary d-inline-flex align-items-center gap-2 mt-3" aria-live="polite">
                    <i class="bi bi-info-circle"></i>
                    <span><span id="countNumber">0</span> aeronave(s) selecionada(s)</span>
                </div>
            @else
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Não há aeronaves cadastradas.
                    <a href="{{ route('aeronaves.create') }}" class="alert-link">Cadastrar uma aeronave</a>
                </div>
            @endif
        </section>

        <div class="form-actions d-flex justify-content-end gap-2">
            <a href="{{ route('companhias.index') }}" class="btn btn-outline-secondary px-4">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
            <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                <i class="bi bi-check2-circle me-1"></i> Cadastrar companhia
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('companhiaForm');
    const nomeInput = document.getElementById('nome');
    const codigoInput = document.getElementById('codigo');
    const submitButton = document.getElementById('submitBtn');
    const cards = [...document.querySelectorAll('[data-aircraft-card]')];
    let nameValid = false;
    let codeValid = false;
    let checkingName = false;
    let checkingCode = false;
    let nameTimer;
    let codeTimer;

    const updateSelectedCount = () => {
        const counter = document.getElementById('countNumber');
        if (counter) counter.textContent = cards.filter(card => card.classList.contains('selected')).length;
    };

    const setCard = (card, selected) => {
        card.classList.toggle('selected', selected);
        card.setAttribute('aria-checked', String(selected));
        card.querySelector('.aeronave-checkbox').checked = selected;
        updateSelectedCount();
    };

    cards.forEach((card) => {
        card.addEventListener('click', () => setCard(card, !card.classList.contains('selected')));
        card.addEventListener('keydown', (event) => {
            if (!['Enter', ' '].includes(event.key)) return;
            event.preventDefault();
            setCard(card, !card.classList.contains('selected'));
        });
    });

    document.getElementById('selectAllBtn')?.addEventListener('click', () => cards.forEach(card => setCard(card, true)));
    document.getElementById('deselectAllBtn')?.addEventListener('click', () => cards.forEach(card => setCard(card, false)));
    updateSelectedCount();

    const updateSubmit = () => {
        const filled = nomeInput.value.trim() && codigoInput.value.trim();
        submitButton.disabled = !filled || checkingName || checkingCode || !nameValid || !codeValid;
    };

    const setValidation = (field, state, message) => {
        const input = field === 'nome' ? nomeInput : codigoInput;
        document.getElementById(`${field}Spinner`).classList.add('d-none');
        document.getElementById(`${field}CheckIcon`).classList.toggle('d-none', state !== 'valid');
        document.getElementById(`${field}XIcon`).classList.toggle('d-none', state !== 'invalid');
        input.classList.toggle('is-valid', state === 'valid');
        input.classList.toggle('is-invalid', state === 'invalid');
        document.getElementById(`${field}Feedback`).innerHTML = message;
    };

    const checkUnique = (field, value, route) => {
        const isName = field === 'nome';
        if (!value) {
            if (isName) nameValid = false; else codeValid = false;
            setValidation(field, 'idle', isName ? 'Use o nome comercial completo.' : 'Informe de 2 a 4 letras.');
            updateSubmit();
            return;
        }

        if (!isName && !/^[A-Z]{2,4}$/.test(value)) {
            codeValid = false;
            setValidation(field, 'invalid', '<span class="text-danger">O código deve conter de 2 a 4 letras.</span>');
            updateSubmit();
            return;
        }

        if (isName) checkingName = true; else checkingCode = true;
        document.getElementById(`${field}Spinner`).classList.remove('d-none');
        setValidation(field, 'checking', '<span class="text-muted">Verificando disponibilidade...</span>');
        document.getElementById(`${field}Spinner`).classList.remove('d-none');
        updateSubmit();

        const data = new FormData();
        data.append(field, value);

        fetch(route, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': @json(csrf_token()), 'Accept': 'application/json' },
            body: data,
        })
            .then(response => response.json())
            .then(data => {
                const valid = !data.exists;
                if (isName) nameValid = valid; else codeValid = valid;
                setValidation(
                    field,
                    valid ? 'valid' : 'invalid',
                    valid
                        ? '<span class="text-success">Disponível para cadastro.</span>'
                        : `<span class="text-danger">${data.message || 'Já está em uso.'}</span>`
                );
            })
            .catch(() => {
                if (isName) nameValid = true; else codeValid = true;
                setValidation(field, 'idle', '<span class="text-warning">Não foi possível verificar agora; o servidor validará ao salvar.</span>');
            })
            .finally(() => {
                if (isName) checkingName = false; else checkingCode = false;
                updateSubmit();
            });
    };

    nomeInput.addEventListener('input', () => {
        clearTimeout(nameTimer);
        nameValid = false;
        updateSubmit();
        nameTimer = setTimeout(
            () => checkUnique('nome', nomeInput.value.trim(), @json(route('companhias.check-name'))),
            450
        );
    });

    codigoInput.addEventListener('input', () => {
        codigoInput.value = codigoInput.value.toUpperCase().replace(/[^A-Z]/g, '').slice(0, 4);
        clearTimeout(codeTimer);
        codeValid = false;
        updateSubmit();
        codeTimer = setTimeout(
            () => checkUnique('codigo', codigoInput.value.trim(), @json(route('companhias.check-code'))),
            450
        );
    });

    if (nomeInput.value.trim()) checkUnique('nome', nomeInput.value.trim(), @json(route('companhias.check-name')));
    if (codigoInput.value.trim()) {
        codigoInput.value = codigoInput.value.toUpperCase();
        checkUnique('codigo', codigoInput.value.trim(), @json(route('companhias.check-code')));
    }
    updateSubmit();

    form.addEventListener('submit', (event) => {
        if (!nameValid || !codeValid || checkingName || checkingCode) {
            event.preventDefault();
            nomeInput.focus();
        }
    });
});
</script>
@endpush
