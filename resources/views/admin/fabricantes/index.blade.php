@extends('layouts.app')

@section('title', 'Gerenciar Fabricantes')

@section('content')
@php
    $totalModelos = $fabricantes->sum('aeronaves_count');
    $paisesInformados = $fabricantes->pluck('pais_origem')->filter()->unique()->sort()->values();
    $comModelos = $fabricantes->where('aeronaves_count', '>', 0)->count();
@endphp

<div class="manufacturers-index container py-4">
    <div class="page-header mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 position-relative">
            <div>
                <span class="text-uppercase small fw-bold opacity-75 tracking-wide">Administração do catálogo</span>
                <h1 class="h2 fw-bold mt-1 mb-1"><i class="bi bi-tools me-2"></i>Gerenciar Fabricantes</h1>
                <p class="mb-0 opacity-75">Consulte e mantenha os fabricantes de aeronaves.</p>
            </div>
            <a href="{{ route('fabricantes.create') }}" class="btn btn-warning fw-semibold">
                <i class="bi bi-plus-circle me-1"></i>Novo fabricante
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        @foreach([
            ['Fabricantes', $fabricantes->count(), 'tools', 'primary'],
            ['Países', $paisesInformados->count(), 'globe-americas', 'success'],
            ['Modelos vinculados', $totalModelos, 'airplane', 'info'],
            ['Com modelos', $comModelos, 'check-circle', 'warning'],
        ] as [$rotulo, $valor, $icone, $cor])
            <div class="col-6 col-xl-3">
                <div class="summary-card">
                    <div class="summary-icon text-{{ $cor }} bg-{{ $cor }} bg-opacity-10">
                        <i class="bi bi-{{ $icone }}"></i>
                    </div>
                    <div>
                        <span>{{ $rotulo }}</span>
                        <strong>{{ number_format($valor, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="buscaFabricante" class="form-label small fw-semibold text-muted">Buscar fabricante</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="search" id="buscaFabricante" class="form-control border-start-0" placeholder="Digite o nome do fabricante...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="filtroPais" class="form-label small fw-semibold text-muted">País de origem</label>
                    <select id="filtroPais" class="form-select">
                        <option value="">Todos os países</option>
                        @foreach($paisesInformados as $pais)
                            <option value="{{ Str::lower($pais) }}">{{ $pais }}</option>
                        @endforeach
                        <option value="nao-informado">Não informado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="ordenacao" class="form-label small fw-semibold text-muted">Ordenar por</label>
                    <select id="ordenacao" class="form-select">
                        <option value="nome">Nome (A–Z)</option>
                        <option value="nome-desc">Nome (Z–A)</option>
                        <option value="modelos">Mais modelos</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-header bg-white border-0 p-4 pb-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h5 fw-bold mb-1">Fabricantes cadastrados</h2>
                    <p class="small text-muted mb-0"><span id="totalVisivel">{{ $fabricantes->count() }}</span> registros exibidos</p>
                </div>
                <a href="{{ route('fabricantes.informacoes') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-bar-chart me-1"></i>Visão geral
                </a>
            </div>
        </div>

        @if($fabricantes->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Fabricante</th>
                            <th>País de origem</th>
                            <th class="text-center">Modelos</th>
                            <th>Data de cadastro</th>
                            <th class="text-end pe-4">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="fabricantesTabela">
                        @foreach($fabricantes as $fabricante)
                            <tr class="fabricante-row"
                                data-nome="{{ Str::lower($fabricante->nome) }}"
                                data-pais="{{ $fabricante->pais_origem ? Str::lower($fabricante->pais_origem) : 'nao-informado' }}"
                                data-modelos="{{ $fabricante->aeronaves_count ?? 0 }}">
                                <td class="ps-4">
                                    <a href="{{ route('fabricantes.show', $fabricante) }}" class="manufacturer-link">
                                        <span class="manufacturer-avatar">{{ Str::upper(Str::substr($fabricante->nome, 0, 2)) }}</span>
                                        <span>
                                            <strong>{{ $fabricante->nome }}</strong>
                                            <small>ID #{{ $fabricante->id }}</small>
                                        </span>
                                    </a>
                                </td>
                                <td>
                                    @if($fabricante->pais_origem)
                                        <span><i class="bi bi-geo-alt text-primary me-1"></i>{{ $fabricante->pais_origem }}</span>
                                    @else
                                        <span class="text-muted"><i class="bi bi-dash-circle me-1"></i>Não informado</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="model-count">{{ $fabricante->aeronaves_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <span class="d-block">{{ $fabricante->created_at?->format('d/m/Y') ?? '—' }}</span>
                                    <small class="text-muted">{{ $fabricante->created_at?->format('H:i') }}</small>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('fabricantes.show', $fabricante) }}" class="btn btn-outline-primary" title="Visualizar">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('fabricantes.edit', $fabricante) }}" class="btn btn-outline-secondary" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" title="Excluir"
                                                data-bs-toggle="modal" data-bs-target="#deleteModal{{ $fabricante->id }}"
                                                @disabled(($fabricante->aeronaves_count ?? 0) > 0)>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    <div class="modal fade text-start" id="deleteModal{{ $fabricante->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-body p-4 text-center">
                                                    <div class="delete-icon"><i class="bi bi-exclamation-triangle"></i></div>
                                                    <h3 class="h5 fw-bold">Excluir fabricante?</h3>
                                                    <p class="text-muted">
                                                        O fabricante <strong>{{ $fabricante->nome }}</strong> será removido permanentemente.
                                                    </p>
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <form action="{{ route('fabricantes.destroy', $fabricante) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Excluir</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="semResultados" class="empty-state d-none">
                <i class="bi bi-search"></i>
                <h3 class="h5">Nenhum fabricante encontrado</h3>
                <p class="text-muted mb-0">Altere os filtros para visualizar outros registros.</p>
            </div>
        @else
            <div class="empty-state">
                <i class="bi bi-tools"></i>
                <h3 class="h5">Nenhum fabricante cadastrado</h3>
                <p class="text-muted">Comece adicionando o primeiro fabricante.</p>
                <a href="{{ route('fabricantes.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Cadastrar fabricante
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
const tabela = document.getElementById('fabricantesTabela');
const linhas = tabela ? Array.from(tabela.querySelectorAll('.fabricante-row')) : [];

function atualizarTabela() {
    const busca = document.getElementById('buscaFabricante').value.toLowerCase();
    const pais = document.getElementById('filtroPais').value;
    const ordem = document.getElementById('ordenacao').value;
    let visiveis = 0;

    linhas.forEach(linha => {
        const mostrar = linha.dataset.nome.includes(busca) && (!pais || linha.dataset.pais === pais);
        linha.classList.toggle('d-none', !mostrar);
        if (mostrar) visiveis++;
    });

    linhas.sort((a, b) => {
        if (ordem === 'modelos') return Number(b.dataset.modelos) - Number(a.dataset.modelos) || a.dataset.nome.localeCompare(b.dataset.nome);
        if (ordem === 'nome-desc') return b.dataset.nome.localeCompare(a.dataset.nome);
        return a.dataset.nome.localeCompare(b.dataset.nome);
    }).forEach(linha => tabela.appendChild(linha));

    document.getElementById('totalVisivel').textContent = visiveis;
    document.getElementById('semResultados').classList.toggle('d-none', visiveis > 0);
}

document.getElementById('buscaFabricante')?.addEventListener('input', atualizarTabela);
document.getElementById('filtroPais')?.addEventListener('change', atualizarTabela);
document.getElementById('ordenacao')?.addEventListener('change', atualizarTabela);
</script>
@endpush

@push('styles')
<style>
.manufacturers-index { --brand-navy: #102452; }
.tracking-wide { letter-spacing: .12em; }
.page-header { padding: 1.75rem 2rem; overflow: hidden; color: #fff; border-radius: 15px; background: linear-gradient(135deg, #102452, #1767aa); box-shadow: 0 10px 28px rgba(16,36,82,.2); }
.summary-card { display: flex; min-height: 105px; padding: 1.15rem; gap: .9rem; align-items: center; border-radius: 11px; background: #fff; box-shadow: 0 .125rem .65rem rgba(0,0,0,.08); }
.summary-icon { display: grid; width: 46px; height: 46px; flex: 0 0 auto; place-items: center; border-radius: 11px; font-size: 1.25rem; }
.summary-card span, .summary-card strong { display: block; }
.summary-card span { color: #6c757d; font-size: .8rem; }
.summary-card strong { font-size: 1.6rem; line-height: 1.15; }
.manufacturer-link { display: flex; align-items: center; gap: .75rem; color: #212529; text-decoration: none; }
.manufacturer-link:hover strong { color: #0d6efd; }
.manufacturer-link small { display: block; color: #6c757d; font-weight: 400; }
.manufacturer-avatar { display: grid; width: 40px; height: 40px; place-items: center; color: #0d6efd; border-radius: 10px; background: #e7f1ff; font-size: .78rem; font-weight: 800; }
.model-count { display: inline-grid; min-width: 36px; height: 30px; padding: 0 .5rem; place-items: center; color: #0d6efd; border-radius: 15px; background: #e7f1ff; font-weight: 800; }
.empty-state { padding: 4rem 1rem; text-align: center; }
.empty-state > i { display: block; margin-bottom: 1rem; color: #adb5bd; font-size: 3rem; }
.delete-icon { display: grid; width: 64px; height: 64px; margin: 0 auto 1rem; place-items: center; color: #dc3545; border-radius: 50%; background: #f8d7da; font-size: 1.75rem; }
.btn:disabled { cursor: not-allowed; opacity: .4; }
@media (max-width: 767.98px) {
    .page-header { padding: 1.5rem; }
    .summary-card { min-height: 92px; }
}
</style>
@endpush
