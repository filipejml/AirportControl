<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h3 class="fw-bold">
                    <i class="bi bi-graph-up-arrow"></i> Desempenho das Companhias
                </h3>
                <p class="text-muted mb-0">
                    Compare volume operacional, cobertura e avaliações das companhias aéreas.
                </p>
                @include('relatorios.partials.status-badges', [
                    'relatorio' => $relatorio,
                    'class' => 'mt-2',
                ])
            </div>
            <button class="btn btn-success" id="exportarCsv">
                <i class="bi bi-file-spreadsheet"></i> Exportar CSV
            </button>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <small>Companhias com voos</small>
                        <h3 class="mb-0" id="totalCompanhias">-</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <small>Total de voos</small>
                        <h3 class="mb-0" id="totalVoos">-</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <small>Total de passageiros</small>
                        <h3 class="mb-0" id="totalPassageiros">-</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark h-100">
                    <div class="card-body">
                        <small>Média geral</small>
                        <h3 class="mb-0" id="mediaGeral">-</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="filtroPeriodo" class="form-label fw-semibold">Período</label>
                        <select id="filtroPeriodo" class="form-select">
                            <option value="">Todos os períodos</option>
                            <option value="hoje">Hoje</option>
                            <option value="semana">Esta semana</option>
                            <option value="mes">Este mês</option>
                            <option value="ano">Este ano</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="filtroCompanhia" class="form-label fw-semibold">Companhia</label>
                        <select id="filtroCompanhia" class="form-select">
                            <option value="">Todas as companhias</option>
                            @foreach($companhias as $companhia)
                                <option value="{{ $companhia->id }}">
                                    {{ $companhia->nome }}{{ $companhia->codigo ? " ({$companhia->codigo})" : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @include('relatorios.partials.filtros-globais', [
                        'ids' => [
                            'aeroporto' => 'filtroAeroporto',
                            'aeronave' => 'filtroAeronave',
                        ],
                    ])
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="button" id="limparFiltros" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-eraser"></i> Limpar filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @if($modoAdmin)
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-xl">
                            <thead class="table-light">
                                <tr>
                                    <th>Companhia</th>
                                    <th class="text-center">Voos</th>
                                    <th class="text-center">Passageiros</th>
                                    <th class="text-center">Pax/voo</th>
                                    <th class="text-center">Cobertura</th>
                                    <th class="text-center">Regular/Charter</th>
                                    <th class="text-center">Média</th>
                                </tr>
                            </thead>
                            <tbody id="resultadoRelatorio"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="row g-4" id="resultadoRelatorio"></div>
        @endif
    </div>
</div>

@push('scripts')
<script>
window.desempenhoCompanhiasConfig = {
    modoAdmin: @json($modoAdmin),
    apiUrl: @json(route('api.relatorios.desempenho-companhias')),
};
</script>
<script src="{{ asset('js/relatorios/desempenho-companhias.js') }}"></script>
@endpush
