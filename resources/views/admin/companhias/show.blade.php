@extends('layouts.app')

@section('title', $companhia->nome . ' | Companhia aérea')

@push('styles')
<style>
    .company-page {
        --company-primary: #155eef;
        --company-ink: #172033;
        --company-muted: #667085;
        --company-border: #e4e7ec;
        color: var(--company-ink);
    }

    .company-hero {
        position: relative;
        overflow: hidden;
        padding: clamp(1.5rem, 4vw, 2.75rem);
        border: 0;
        border-radius: 1.5rem;
        background:
            radial-gradient(circle at 90% 10%, rgba(255,255,255,.18), transparent 25%),
            linear-gradient(125deg, #101828 0%, #1849a9 58%, #2e90fa 100%);
        box-shadow: 0 20px 45px rgba(16, 24, 40, .16);
    }

    .company-mark {
        display: grid;
        width: 4.25rem;
        height: 4.25rem;
        place-items: center;
        flex: 0 0 auto;
        border: 1px solid rgba(255,255,255,.3);
        border-radius: 1.25rem;
        color: #fff;
        background: rgba(255,255,255,.13);
        backdrop-filter: blur(8px);
        font-size: 1.8rem;
    }

    .company-code {
        display: inline-flex;
        align-items: center;
        padding: .35rem .7rem;
        border: 1px solid rgba(255,255,255,.28);
        border-radius: 999px;
        color: #fff;
        background: rgba(255,255,255,.12);
        font-size: .78rem;
        font-weight: 700;
        letter-spacing: .12em;
    }

    .hero-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        min-height: 2.75rem;
        border-radius: .8rem;
        font-weight: 600;
    }

    .hero-action-light {
        border-color: rgba(255,255,255,.35);
        color: #fff;
        background: rgba(255,255,255,.12);
    }

    .hero-action-light:hover {
        border-color: #fff;
        color: #101828;
        background: #fff;
    }

    .metric-card {
        height: 100%;
        padding: 1.25rem;
        border: 1px solid var(--company-border);
        border-radius: 1.1rem;
        background: #fff;
        box-shadow: 0 8px 24px rgba(16, 24, 40, .05);
    }

    .metric-icon {
        display: grid;
        width: 2.65rem;
        height: 2.65rem;
        place-items: center;
        border-radius: .8rem;
        font-size: 1.15rem;
    }

    .metric-value {
        margin-top: 1rem;
        font-size: clamp(1.65rem, 4vw, 2.15rem);
        font-weight: 750;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .metric-label {
        margin-top: .45rem;
        color: var(--company-muted);
        font-size: .84rem;
    }

    .section-card {
        overflow: hidden;
        border: 1px solid var(--company-border);
        border-radius: 1.25rem;
        background: #fff;
        box-shadow: 0 8px 24px rgba(16, 24, 40, .04);
    }

    .section-heading {
        padding: 1.25rem 1.35rem;
        border-bottom: 1px solid var(--company-border);
    }

    .section-kicker {
        color: var(--company-primary);
        font-size: .75rem;
        font-weight: 750;
        letter-spacing: .09em;
        text-transform: uppercase;
    }

    .company-table {
        margin: 0;
        min-width: 920px;
    }

    .company-table thead th {
        padding: .85rem 1rem;
        border-bottom-width: 1px;
        color: var(--company-muted);
        background: #f9fafb;
        font-size: .73rem;
        font-weight: 700;
        letter-spacing: .055em;
        text-transform: uppercase;
    }

    .company-table tbody td {
        padding: 1rem;
        border-color: #f0f1f3;
    }

    .aircraft-link {
        color: var(--company-ink);
        font-weight: 700;
        text-decoration: none;
    }

    .aircraft-link:hover { color: var(--company-primary); }

    .aircraft-avatar {
        display: grid;
        width: 2.5rem;
        height: 2.5rem;
        place-items: center;
        flex: 0 0 auto;
        border-radius: .75rem;
        color: var(--company-primary);
        background: #eff4ff;
    }

    .availability-switch .form-check-input {
        width: 2.75rem;
        height: 1.4rem;
        margin-top: 0;
        cursor: pointer;
    }

    .availability-switch .form-check-input:checked {
        border-color: #12b76a;
        background-color: #12b76a;
    }

    .aircraft-unavailable { background: #fffafa; }
    .aircraft-unavailable .aircraft-avatar {
        color: #98a2b3;
        background: #f2f4f7;
    }

    .aircraft-unavailable .aircraft-link { color: #667085; }

    .pending-row {
        box-shadow: inset 4px 0 #f79009;
        background: #fffcf5;
    }

    .floating-save {
        position: fixed;
        right: 1.5rem;
        bottom: 1.5rem;
        z-index: 1050;
        min-height: 3.25rem;
        padding-inline: 1.2rem;
        border: 0;
        border-radius: 999px;
        box-shadow: 0 12px 30px rgba(3, 152, 85, .28);
        font-weight: 700;
    }

    .empty-state {
        padding: 4rem 1rem;
        text-align: center;
    }

    @media (max-width: 767.98px) {
        .company-hero { border-radius: 1.15rem; }
        .company-mark { width: 3.5rem; height: 3.5rem; }
        .hero-actions { width: 100%; }
        .hero-actions .btn { flex: 1; }
        .floating-save { right: 1rem; bottom: 1rem; left: 1rem; width: calc(100% - 2rem); }
    }
</style>
@endpush

@section('content')
@php
    $totalAeronaves = $companhia->aeronaves->count();
    $aeronavesDisponiveis = $companhia->aeronaves->filter(
        fn ($aeronave) => (bool) ($aeronave->pivot->disponivel ?? true)
    )->count();
    $capacidadeTotal = $companhia->aeronaves->sum('capacidade');
    $capacidadeMedia = $totalAeronaves > 0 ? round($companhia->aeronaves->avg('capacidade')) : 0;
@endphp

<div class="company-page pb-5">
    <section class="company-hero mb-4 text-white">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
            <div class="d-flex align-items-center gap-3 gap-md-4">
                <div class="company-mark" aria-hidden="true">
                    <i class="bi bi-airplane-engines"></i>
                </div>
                <div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <span class="company-code">{{ $companhia->codigo ?? 'SEM CÓDIGO' }}</span>
                        <span class="small text-white-50">Companhia aérea #{{ $companhia->id }}</span>
                    </div>
                    <h1 class="h2 fw-bold mb-1">{{ $companhia->nome }}</h1>
                    <p class="mb-0 text-white-50">Visão geral da frota e disponibilidade operacional.</p>
                </div>
            </div>

            <div class="hero-actions d-flex flex-wrap gap-2">
                <a href="{{ route('companhias.index') }}" class="btn hero-action hero-action-light">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
                <a href="{{ route('companhias.dashboard', $companhia) }}" class="btn hero-action hero-action-light">
                    <i class="bi bi-bar-chart-line"></i> Dashboard
                </a>
                <a href="{{ route('companhias.edit', $companhia) }}" class="btn btn-light hero-action text-primary">
                    <i class="bi bi-pencil-square"></i> Editar companhia
                </a>
            </div>
        </div>
    </section>

    <section class="row g-3 mb-4" aria-label="Indicadores da companhia">
        <div class="col-6 col-xl-3">
            <div class="metric-card">
                <div class="metric-icon text-primary bg-primary-subtle"><i class="bi bi-airplane"></i></div>
                <div class="metric-value">{{ number_format($totalAeronaves, 0, ',', '.') }}</div>
                <div class="metric-label">Aeronaves associadas</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="metric-card">
                <div class="metric-icon text-success bg-success-subtle"><i class="bi bi-check2-circle"></i></div>
                <div class="metric-value">{{ number_format($aeronavesDisponiveis, 0, ',', '.') }}</div>
                <div class="metric-label">Disponíveis agora</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="metric-card">
                <div class="metric-icon text-info bg-info-subtle"><i class="bi bi-people"></i></div>
                <div class="metric-value">{{ number_format($capacidadeTotal, 0, ',', '.') }}</div>
                <div class="metric-label">Capacidade total</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="metric-card">
                <div class="metric-icon text-warning bg-warning-subtle"><i class="bi bi-speedometer2"></i></div>
                <div class="metric-value">{{ number_format($capacidadeMedia, 0, ',', '.') }}</div>
                <div class="metric-label">Média por aeronave</div>
            </div>
        </div>
    </section>

    <div class="row g-4">
        <div class="col-xl-9">
            <section class="section-card">
                <div class="section-heading d-flex flex-column flex-sm-row justify-content-between gap-3">
                    <div>
                        <div class="section-kicker mb-1">Frota</div>
                        <h2 class="h5 fw-bold mb-1">Aeronaves da companhia</h2>
                        <p class="small text-muted mb-0">Consulte a frota e ajuste sua disponibilidade operacional.</p>
                    </div>
                    @if($totalAeronaves > 0)
                        <span class="badge align-self-sm-center rounded-pill text-bg-light border px-3 py-2">
                            {{ $totalAeronaves }} {{ $totalAeronaves === 1 ? 'aeronave' : 'aeronaves' }}
                        </span>
                    @endif
                </div>

                @if($totalAeronaves > 0)
                    <div class="table-responsive">
                        <table class="table company-table align-middle" id="aeronavesTable">
                            <thead>
                                <tr>
                                    <th>Aeronave</th>
                                    <th>Fabricante</th>
                                    <th>Capacidade</th>
                                    <th>Porte</th>
                                    <th>Disponibilidade</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($companhia->aeronaves as $aeronave)
                                    @php
                                        $disponivel = (bool) ($aeronave->pivot->disponivel ?? true);
                                        $porte = match($aeronave->porte) {
                                            'PC' => ['Pequeno', 'text-bg-info'],
                                            'MC' => ['Médio', 'text-bg-warning'],
                                            'LC' => ['Grande', 'text-bg-danger'],
                                            default => [$aeronave->porte ?: 'Não informado', 'text-bg-secondary'],
                                        };
                                    @endphp
                                    <tr data-aeronave-id="{{ $aeronave->id }}"
                                        data-disponivel-original="{{ $disponivel ? 'true' : 'false' }}"
                                        class="{{ $disponivel ? '' : 'aircraft-unavailable' }}">
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="aircraft-avatar"><i class="bi bi-airplane"></i></span>
                                                <div>
                                                    <a href="{{ route('aeronaves.show', $aeronave) }}" class="aircraft-link">
                                                        {{ $aeronave->modelo }}
                                                    </a>
                                                    <div class="small text-muted">ID #{{ $aeronave->id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $aeronave->fabricante?->nome ?? 'Não informado' }}</td>
                                        <td><strong>{{ number_format($aeronave->capacidade, 0, ',', '.') }}</strong> <span class="small text-muted">passageiros</span></td>
                                        <td><span class="badge {{ $porte[1] }} rounded-pill">{{ $porte[0] }}</span></td>
                                        <td>
                                            <div class="availability-switch d-flex align-items-center gap-2">
                                                <div class="form-check form-switch m-0">
                                                    <input type="checkbox"
                                                           class="form-check-input disponivel-toggle"
                                                           id="disponivel_{{ $aeronave->id }}"
                                                           data-aeronave-id="{{ $aeronave->id }}"
                                                           data-companhia-id="{{ $companhia->id }}"
                                                           {{ $disponivel ? 'checked' : '' }}>
                                                </div>
                                                <label class="small fw-semibold status-label" for="disponivel_{{ $aeronave->id }}">
                                                    {{ $disponivel ? 'Disponível' : 'Indisponível' }}
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('aeronaves.edit', $aeronave) }}"
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="Editar {{ $aeronave->modelo }}">
                                                    <i class="bi bi-pencil"></i>
                                                    <span class="visually-hidden">Editar</span>
                                                </a>
                                                <form action="{{ route('aeronaves.destroy', $aeronave) }}" method="POST"
                                                      onsubmit="return confirm('Tem certeza que deseja excluir a aeronave {{ addslashes($aeronave->modelo) }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir {{ $aeronave->modelo }}">
                                                        <i class="bi bi-trash"></i>
                                                        <span class="visually-hidden">Excluir</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="metric-icon text-primary bg-primary-subtle mx-auto mb-3"><i class="bi bi-airplane"></i></div>
                        <h3 class="h5 fw-bold">Nenhuma aeronave associada</h3>
                        <p class="text-muted">Edite a companhia para relacionar aeronaves à sua frota.</p>
                        <a href="{{ route('companhias.edit', $companhia) }}" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i> Gerenciar frota
                        </a>
                    </div>
                @endif
            </section>
        </div>

        <aside class="col-xl-3">
            <section class="section-card p-4">
                <div class="section-kicker mb-1">Cadastro</div>
                <h2 class="h5 fw-bold mb-4">Informações</h2>

                <div class="d-flex gap-3 mb-4">
                    <span class="metric-icon text-primary bg-primary-subtle"><i class="bi bi-calendar-plus"></i></span>
                    <div>
                        <div class="small text-muted">Cadastrada em</div>
                        <strong>{{ $companhia->created_at?->format('d/m/Y') ?? 'Não disponível' }}</strong>
                        @if($companhia->created_at)
                            <div class="small text-muted">às {{ $companhia->created_at->format('H:i') }}</div>
                        @endif
                    </div>
                </div>

                <div class="d-flex gap-3">
                    <span class="metric-icon text-success bg-success-subtle"><i class="bi bi-arrow-repeat"></i></span>
                    <div>
                        <div class="small text-muted">Última atualização</div>
                        <strong>{{ $companhia->updated_at?->format('d/m/Y') ?? 'Não disponível' }}</strong>
                        @if($companhia->updated_at)
                            <div class="small text-muted">às {{ $companhia->updated_at->format('H:i') }}</div>
                        @endif
                    </div>
                </div>
            </section>
        </aside>
    </div>
</div>

<button type="button" id="floatingSaveBtn" class="btn btn-success floating-save d-none">
    <span class="save-default"><i class="bi bi-check2-circle me-2"></i>Salvar alterações (<span id="pendingCount">0</span>)</span>
    <span class="save-loading d-none"><span class="spinner-border spinner-border-sm me-2"></span>Salvando...</span>
</button>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const pendingChanges = new Map();
    const saveButton = document.getElementById('floatingSaveBtn');
    const pendingCount = document.getElementById('pendingCount');
    let isSaving = false;

    const setRowState = (row, available, pending = false) => {
        row.classList.toggle('aircraft-unavailable', !available);
        row.classList.toggle('pending-row', pending);
        row.querySelector('.status-label').textContent = available ? 'Disponível' : 'Indisponível';
    };

    const refreshSaveButton = () => {
        pendingCount.textContent = pendingChanges.size;
        saveButton.classList.toggle('d-none', pendingChanges.size === 0);
    };

    const notify = (message, type = 'success') => {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 shadow`;
        alert.style.zIndex = '2000';
        alert.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        document.body.appendChild(alert);
        setTimeout(() => alert.remove(), 3500);
    };

    document.querySelectorAll('.disponivel-toggle').forEach((toggle) => {
        toggle.addEventListener('change', () => {
            const row = toggle.closest('tr');
            const key = `${toggle.dataset.companhiaId}_${toggle.dataset.aeronaveId}`;
            const original = row.dataset.disponivelOriginal === 'true';

            if (toggle.checked === original) {
                pendingChanges.delete(key);
                setRowState(row, toggle.checked, false);
            } else {
                pendingChanges.set(key, {
                    companhiaId: toggle.dataset.companhiaId,
                    aeronaveId: toggle.dataset.aeronaveId,
                    disponivel: toggle.checked,
                });
                setRowState(row, toggle.checked, true);
            }

            refreshSaveButton();
        });
    });

    saveButton.addEventListener('click', async () => {
        if (isSaving || pendingChanges.size === 0) return;
        isSaving = true;
        saveButton.disabled = true;
        saveButton.querySelector('.save-default').classList.add('d-none');
        saveButton.querySelector('.save-loading').classList.remove('d-none');

        let saved = 0;
        let failed = 0;

        for (const [key, change] of [...pendingChanges]) {
            const row = document.querySelector(`tr[data-aeronave-id="${change.aeronaveId}"]`);

            try {
                const response = await fetch(
                    `/companhias/${change.companhiaId}/aeronaves/${change.aeronaveId}/disponibilidade`,
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': @json(csrf_token()),
                        },
                        body: JSON.stringify({ disponivel: change.disponivel }),
                    }
                );
                const result = await response.json();

                if (!response.ok || !result.success) throw new Error(result.message || 'Erro ao salvar');

                row.dataset.disponivelOriginal = String(change.disponivel);
                setRowState(row, change.disponivel, false);
                pendingChanges.delete(key);
                saved++;
            } catch (error) {
                const original = row.dataset.disponivelOriginal === 'true';
                row.querySelector('.disponivel-toggle').checked = original;
                setRowState(row, original, false);
                pendingChanges.delete(key);
                failed++;
            }
        }

        if (saved) notify(`${saved} alteração(ões) salva(s) com sucesso.`);
        if (failed) notify(`${failed} alteração(ões) não puderam ser salvas.`, 'danger');

        isSaving = false;
        saveButton.disabled = false;
        saveButton.querySelector('.save-default').classList.remove('d-none');
        saveButton.querySelector('.save-loading').classList.add('d-none');
        refreshSaveButton();
    });

    window.addEventListener('beforeunload', (event) => {
        if (pendingChanges.size === 0) return;
        event.preventDefault();
        event.returnValue = '';
    });
});
</script>
@endpush
