@extends('layouts.app')

@section('title', 'Controle de Relatórios')

@section('content')
<div class="container">
    <div class="mb-4">
        <h3 class="fw-bold">📊 Controle de Relatórios</h3>
        <p class="text-muted mb-0">
            Habilite quais relatórios estruturados na aplicação ficam visíveis para usuários comuns.
        </p>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nome</th>
                            <th>Tipo</th>
                            <th>Descrição</th>
                            <th class="text-center">Visível para usuários</th>
                            <th class="text-end">Visualização</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($relatorios as $relatorio)
                            <tr>
                                <td><strong>{{ $relatorio->nome }}</strong></td>
                                <td>
                                    <span class="badge bg-info text-dark">{{ $relatorio->tipo }}</span>
                                </td>
                                <td>{{ Str::limit($relatorio->descricao, 70) ?: '—' }}</td>
                                <td class="text-center">
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input
                                            type="checkbox"
                                            class="form-check-input toggle-visibilidade"
                                            id="toggle_{{ $relatorio->id }}"
                                            data-url="{{ route('admin.relatorios.toggle-visibilidade', $relatorio) }}"
                                            aria-label="Alterar visibilidade de {{ $relatorio->nome }}"
                                            {{ $relatorio->visivel_usuario ? 'checked' : '' }}
                                        >
                                    </div>
                                </td>
                                <td class="text-end">
                                    @if($relatorio->admin_route)
                                        <a href="{{ route($relatorio->admin_route) }}" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-eye"></i> Ver
                                        </a>
                                    @else
                                        <span class="text-muted small">Indisponível</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Nenhum relatório estruturado foi registrado na aplicação.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = '{{ csrf_token() }}';

    function updateToggleColor(toggle) {
        const color = toggle.checked ? '#198754' : '#dc3545';
        toggle.style.backgroundColor = color;
        toggle.style.borderColor = color;
    }

    function showToast(type, message) {
        let container = document.querySelector('.toast-container');

        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        container.appendChild(toast);
        const instance = new bootstrap.Toast(toast, { delay: 3000 });
        toast.addEventListener('hidden.bs.toast', () => toast.remove());
        instance.show();
    }

    document.querySelectorAll('.toggle-visibilidade').forEach(toggle => {
        updateToggleColor(toggle);

        toggle.addEventListener('change', async function () {
            const requestedState = this.checked;
            this.disabled = true;
            this.style.opacity = '0.6';

            try {
                const response = await fetch(this.dataset.url, {
                    method: 'PATCH',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ visivel_usuario: requestedState }),
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Não foi possível atualizar a visibilidade.');
                }

                updateToggleColor(this);
                showToast(
                    requestedState ? 'success' : 'warning',
                    requestedState
                        ? 'Relatório visível para usuários comuns.'
                        : 'Relatório oculto para usuários comuns.'
                );
            } catch (error) {
                this.checked = !requestedState;
                updateToggleColor(this);
                showToast('danger', error.message || 'Erro de conexão. Tente novamente.');
            } finally {
                this.disabled = false;
                this.style.opacity = '1';
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.toggle-visibilidade {
    width: 50px;
    height: 25px;
    cursor: pointer;
    transition: transform 0.2s ease;
}

.toggle-visibilidade:hover {
    transform: scale(1.05);
}

.toggle-visibilidade:disabled {
    cursor: wait;
}

.toast-container {
    z-index: 1080;
}
</style>
@endpush
@endsection
