@extends('layouts.app')

@section('title', 'Registros do Sistema')

@section('content')
@php
    $modulos = [
        [
            'titulo' => 'Usuários',
            'descricao' => 'Contas, permissões e dados de acesso ao sistema.',
            'icone' => 'people-fill',
            'cor' => 'primary',
            'quantidade' => $estatisticas['usuarios'],
            'unidade' => 'usuários',
            'lista' => route('admin.users.index'),
            'novo' => route('admin.users.create'),
            'texto_novo' => 'Novo usuário',
        ],
        [
            'titulo' => 'Voos',
            'descricao' => 'Operações, passageiros, horários e avaliações.',
            'icone' => 'airplane-engines-fill',
            'cor' => 'success',
            'quantidade' => $estatisticas['voos'],
            'unidade' => 'voos',
            'lista' => route('voos.index'),
            'novo' => route('voos.create'),
            'texto_novo' => 'Novo voo',
        ],
        [
            'titulo' => 'Aeronaves',
            'descricao' => 'Modelos, capacidades, portes e fabricantes.',
            'icone' => 'airplane',
            'cor' => 'info',
            'quantidade' => $estatisticas['aeronaves'],
            'unidade' => 'modelos',
            'lista' => route('aeronaves.index'),
            'novo' => route('aeronaves.create'),
            'texto_novo' => 'Nova aeronave',
        ],
        [
            'titulo' => 'Fabricantes',
            'descricao' => 'Empresas responsáveis pelos modelos de aeronaves.',
            'icone' => 'tools',
            'cor' => 'warning',
            'quantidade' => $estatisticas['fabricantes'],
            'unidade' => 'fabricantes',
            'lista' => route('fabricantes.index'),
            'novo' => route('fabricantes.create'),
            'texto_novo' => 'Novo fabricante',
        ],
        [
            'titulo' => 'Companhias Aéreas',
            'descricao' => 'Empresas, frotas e vínculos operacionais.',
            'icone' => 'buildings',
            'cor' => 'danger',
            'quantidade' => $estatisticas['companhias'],
            'unidade' => 'companhias',
            'lista' => route('companhias.index'),
            'novo' => route('companhias.create'),
            'texto_novo' => 'Nova companhia',
        ],
        [
            'titulo' => 'Aeroportos',
            'descricao' => 'Aeroportos, companhias, depósitos e infraestrutura.',
            'icone' => 'geo-alt-fill',
            'cor' => 'primary',
            'quantidade' => $estatisticas['aeroportos'],
            'unidade' => 'aeroportos',
            'lista' => route('aeroportos.index'),
            'novo' => route('aeroportos.create.step1'),
            'texto_novo' => 'Novo aeroporto',
        ],
        [
            'titulo' => 'Depósitos',
            'descricao' => 'Estruturas de armazenamento e veículos aeroportuários.',
            'icone' => 'box-seam-fill',
            'cor' => 'secondary',
            'quantidade' => $estatisticas['depositos'],
            'unidade' => 'depósitos',
            'lista' => route('depositos.index'),
            'novo' => null,
            'texto_novo' => null,
        ],
        [
            'titulo' => 'Relatórios',
            'descricao' => 'Disponibilidade e controle dos relatórios gerenciais.',
            'icone' => 'file-earmark-bar-graph-fill',
            'cor' => 'success',
            'quantidade' => $estatisticas['relatorios'],
            'unidade' => 'relatórios',
            'lista' => route('admin.relatorios.index'),
            'novo' => null,
            'texto_novo' => null,
        ],
    ];
@endphp

<div class="records-page container py-4">
    <div class="records-header mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 position-relative">
            <div class="d-flex align-items-center gap-3">
                <div class="header-icon"><i class="bi bi-database-gear"></i></div>
                <div>
                    <span class="text-uppercase small fw-bold opacity-75 tracking-wide">Administração central</span>
                    <h1 class="h2 fw-bold mt-1 mb-1">Registros do Sistema</h1>
                    <p class="mb-0 opacity-75">Acesso rápido aos cadastros e módulos administrativos.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="search" id="buscaModulo" class="form-control border-start-0"
                       placeholder="Buscar módulo administrativo...">
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-end mb-3">
        <div>
            <h2 class="h5 fw-bold mb-1">Módulos administrativos</h2>
            <p class="small text-muted mb-0">Selecione uma área para visualizar ou cadastrar registros.</p>
        </div>
        <span class="badge bg-light text-dark border"><span id="modulosVisiveis">{{ count($modulos) }}</span> módulos</span>
    </div>

    <div class="row g-4" id="modulosContainer">
        @foreach($modulos as $modulo)
            <div class="col-md-6 col-lg-3 modulo-card" data-busca="{{ Str::lower($modulo['titulo'].' '.$modulo['descricao']) }}">
                <div class="module-card h-100">
                    <div class="module-accent bg-{{ $modulo['cor'] }}"></div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <div class="module-icon text-{{ $modulo['cor'] }} bg-{{ $modulo['cor'] }} bg-opacity-10">
                                <i class="bi bi-{{ $modulo['icone'] }}"></i>
                            </div>
                            <div class="module-count text-end">
                                <strong>{{ number_format($modulo['quantidade'], 0, ',', '.') }}</strong>
                                <small>{{ $modulo['unidade'] }}</small>
                            </div>
                        </div>
                        <h3 class="h5 fw-bold mb-2">{{ $modulo['titulo'] }}</h3>
                        <p class="text-muted small module-description">{{ $modulo['descricao'] }}</p>
                        <div class="d-flex gap-2 mt-auto">
                            <a href="{{ $modulo['lista'] }}" class="btn btn-outline-{{ $modulo['cor'] }} flex-grow-1">
                                <i class="bi bi-list-ul me-1"></i>Gerenciar
                            </a>
                            @if($modulo['novo'])
                                <a href="{{ $modulo['novo'] }}" class="btn btn-{{ $modulo['cor'] }}" title="{{ $modulo['texto_novo'] }}">
                                    <i class="bi bi-plus-lg"></i>
                                    <span class="visually-hidden">{{ $modulo['texto_novo'] }}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div id="semResultados" class="empty-state d-none">
        <i class="bi bi-search"></i>
        <h3 class="h5">Nenhum módulo encontrado</h3>
        <p class="text-muted mb-0">Tente buscar usando outro termo.</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
const buscaModulo = document.getElementById('buscaModulo');
const modulos = Array.from(document.querySelectorAll('.modulo-card'));

buscaModulo.addEventListener('input', function () {
    const termo = this.value.toLowerCase().trim();
    let visiveis = 0;

    modulos.forEach(modulo => {
        const mostrar = modulo.dataset.busca.includes(termo);
        modulo.classList.toggle('d-none', !mostrar);
        if (mostrar) visiveis++;
    });

    document.getElementById('modulosVisiveis').textContent = visiveis;
    document.getElementById('semResultados').classList.toggle('d-none', visiveis > 0);
});
</script>
@endpush

@push('styles')
<style>
.records-page { --records-navy: #102452; }
.tracking-wide { letter-spacing: .12em; }
.records-header { position: relative; padding: 1.8rem 2rem; overflow: hidden; color: #fff; border-radius: 16px; background: linear-gradient(135deg, #102452, #1767aa); box-shadow: 0 12px 30px rgba(16,36,82,.22); }
.records-header::after { position: absolute; width: 230px; height: 230px; right: -80px; top: -135px; border: 38px solid rgba(255,255,255,.06); border-radius: 50%; content: ""; }
.records-header > div { z-index: 1; }
.header-icon { display: grid; width: 58px; height: 58px; flex: 0 0 auto; place-items: center; color: var(--records-navy); border-radius: 14px; background: #ffc107; font-size: 1.5rem; }
.module-card { position: relative; display: flex; overflow: hidden; border: 0; border-radius: 13px; background: #fff; box-shadow: 0 .2rem .85rem rgba(0,0,0,.09); transition: transform .2s, box-shadow .2s; }
.module-card:hover { transform: translateY(-3px); box-shadow: 0 .8rem 1.5rem rgba(0,0,0,.13); }
.module-card .card-body { display: flex; flex-direction: column; }
.module-accent { width: 5px; flex: 0 0 auto; }
.module-icon { display: grid; width: 50px; height: 50px; place-items: center; border-radius: 12px; font-size: 1.35rem; }
.module-count strong, .module-count small { display: block; }
.module-count strong { font-size: 1.45rem; line-height: 1.1; }
.module-count small { color: #6c757d; font-size: .75rem; }
.module-description { min-height: 42px; }
.empty-state { padding: 4rem 1rem; text-align: center; }
.empty-state > i { display: block; margin-bottom: 1rem; color: #adb5bd; font-size: 3rem; }
@media (max-width: 767.98px) {
    .records-header { padding: 1.5rem; }
    .header-icon { width: 50px; height: 50px; }
}
</style>
@endpush
