@extends('layouts.app')

@section('title', 'Cadastrar Fabricante')

@section('content')
<div class="manufacturer-create container py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="{{ route('fabricantes.index') }}">Fabricantes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Novo cadastro</li>
        </ol>
    </nav>

    <div class="page-header mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="header-icon"><i class="bi bi-plus-lg"></i></div>
            <div>
                <span class="text-uppercase small fw-bold opacity-75 tracking-wide">Cadastro do catálogo</span>
                <h1 class="h2 fw-bold mb-1">Novo Fabricante</h1>
                <p class="mb-0 opacity-75">Registre a empresa responsável pela produção das aeronaves.</p>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <div class="d-flex gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>
                    <strong>Revise os dados informados.</strong>
                    <ul class="mb-0 mt-1 ps-3">
                        @foreach($errors->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('fabricantes.store') }}" id="fabricanteForm">
        @csrf

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 px-4 pt-4 pb-2">
                        <h2 class="h5 fw-bold mb-1">Informações principais</h2>
                        <p class="small text-muted mb-0">Campos com <span class="text-danger">*</span> são obrigatórios.</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label for="nome" class="form-label fw-semibold">
                                Nome do fabricante <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white"><i class="bi bi-tools text-primary"></i></span>
                                <input type="text"
                                       class="form-control @error('nome') is-invalid @enderror"
                                       id="nome"
                                       name="nome"
                                       value="{{ old('nome') }}"
                                       placeholder="Ex.: Airbus, Boeing ou Embraer"
                                       maxlength="255"
                                       autocomplete="organization"
                                       autofocus
                                       required>
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <small class="text-muted">Use o nome oficial e evite abreviações.</small>
                                <small class="text-muted"><span id="nomeContador">0</span>/255</small>
                            </div>
                        </div>

                        <div>
                            <label for="pais_origem" class="form-label fw-semibold">País de origem</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-geo-alt text-success"></i></span>
                                <input type="text"
                                       class="form-control @error('pais_origem') is-invalid @enderror"
                                       id="pais_origem"
                                       name="pais_origem"
                                       value="{{ old('pais_origem') }}"
                                       placeholder="Ex.: Brasil"
                                       maxlength="255"
                                       list="paisesSugeridos"
                                       autocomplete="country-name">
                                @error('pais_origem')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <datalist id="paisesSugeridos">
                                <option value="Alemanha">
                                <option value="Brasil">
                                <option value="Canadá">
                                <option value="Estados Unidos">
                                <option value="França">
                                <option value="Itália">
                                <option value="Países Baixos">
                                <option value="Reino Unido">
                                <option value="Suécia">
                            </datalist>
                            <small class="text-muted d-block mt-2">Campo opcional. Informe o país onde o fabricante está sediado.</small>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top p-4">
                        <div class="d-flex flex-wrap justify-content-between gap-2">
                            <a href="{{ route('fabricantes.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary px-4" id="submitButton">
                                <i class="bi bi-check-circle me-1"></i>Salvar fabricante
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h6 fw-bold text-uppercase text-muted mb-3">Pré-visualização</h2>
                        <div class="preview-card">
                            <div class="preview-avatar" id="previewAvatar">NF</div>
                            <div class="min-width-0">
                                <strong id="previewNome" class="text-truncate d-block">Novo fabricante</strong>
                                <span class="small text-muted">
                                    <i class="bi bi-geo-alt me-1"></i><span id="previewPais">País não informado</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 helper-card">
                    <div class="card-body p-4">
                        <h2 class="h6 fw-bold"><i class="bi bi-lightbulb-fill text-warning me-2"></i>Depois do cadastro</h2>
                        <p class="small text-muted mb-3">O fabricante ficará disponível imediatamente no cadastro de aeronaves.</p>
                        <div class="helper-step">
                            <span>1</span><small>Cadastre o fabricante</small>
                        </div>
                        <div class="helper-step">
                            <span>2</span><small>Vincule modelos de aeronaves</small>
                        </div>
                        <div class="helper-step">
                            <span>3</span><small>Acompanhe os dados operacionais</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const nomeInput = document.getElementById('nome');
const paisInput = document.getElementById('pais_origem');
const form = document.getElementById('fabricanteForm');

function atualizarPreview() {
    const nome = nomeInput.value.trim();
    const pais = paisInput.value.trim();
    const iniciais = nome
        ? nome.split(/\s+/).slice(0, 2).map(parte => parte.charAt(0)).join('').toUpperCase()
        : 'NF';

    document.getElementById('previewNome').textContent = nome || 'Novo fabricante';
    document.getElementById('previewAvatar').textContent = iniciais;
    document.getElementById('previewPais').textContent = pais || 'País não informado';
    document.getElementById('nomeContador').textContent = nomeInput.value.length;
}

nomeInput.addEventListener('input', atualizarPreview);
paisInput.addEventListener('input', atualizarPreview);
form.addEventListener('submit', () => {
    const button = document.getElementById('submitButton');
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Salvando...';
});
atualizarPreview();
</script>
@endpush

@push('styles')
<style>
.manufacturer-create { --brand-navy: #102452; }
.tracking-wide { letter-spacing: .12em; }
.page-header { padding: 1.75rem 2rem; color: #fff; border-radius: 15px; background: linear-gradient(135deg, #102452, #1767aa); box-shadow: 0 10px 28px rgba(16,36,82,.2); }
.header-icon { display: grid; width: 56px; height: 56px; flex: 0 0 auto; place-items: center; color: var(--brand-navy); border-radius: 14px; background: #ffc107; font-size: 1.5rem; }
.input-group-text { min-width: 46px; justify-content: center; }
.preview-card { display: flex; padding: 1rem; gap: .8rem; align-items: center; border: 1px solid #e7ebef; border-radius: 12px; background: #f8f9fa; }
.preview-avatar { display: grid; width: 48px; height: 48px; flex: 0 0 auto; place-items: center; color: #0d6efd; border-radius: 12px; background: #e7f1ff; font-size: .9rem; font-weight: 800; }
.min-width-0 { min-width: 0; }
.helper-card { background: linear-gradient(145deg, #f8f9fa, #eef4fa); }
.helper-step { display: flex; margin-top: .7rem; gap: .65rem; align-items: center; }
.helper-step span { display: grid; width: 25px; height: 25px; place-items: center; color: #fff; border-radius: 50%; background: #0d6efd; font-size: .75rem; font-weight: 700; }
@media (max-width: 767.98px) {
    .page-header { padding: 1.5rem; }
    .header-icon { width: 48px; height: 48px; }
}
</style>
@endpush
