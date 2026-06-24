<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h3 class="fw-bold">
                    <i class="bi bi-calendar3"></i> Movimentação por Período
                </h3>
                <p class="text-muted mb-0">
                    Acompanhe a evolução de voos e passageiros ao longo do tempo.
                </p>
                @include('relatorios.partials.status-badges', [
                    'relatorio' => $relatorio,
                    'class' => 'mt-2',
                ])
            </div>
            <button class="btn btn-success" id="exportarMovimentacaoCsv">
                <i class="bi bi-file-spreadsheet"></i> Exportar CSV
            </button>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <small>Períodos analisados</small>
                        <h3 class="mb-0" id="movTotalPeriodos">-</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <small>Total de voos</small>
                        <h3 class="mb-0" id="movTotalVoos">-</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <small>Total de passageiros</small>
                        <h3 class="mb-0" id="movTotalPassageiros">-</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark h-100">
                    <div class="card-body">
                        <small>Maior movimento</small>
                        <h5 class="mb-0" id="movMaiorMovimento">-</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="movAgrupamento" class="form-label fw-semibold">Agrupar por</label>
                        <select id="movAgrupamento" class="form-select">
                            <option value="dia">Dia</option>
                            <option value="semana">Semana</option>
                            <option value="mes" selected>Mês</option>
                            <option value="ano">Ano</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="movDataInicio" class="form-label fw-semibold">Data inicial</label>
                        <input type="date" id="movDataInicio" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label for="movDataFim" class="form-label fw-semibold">Data final</label>
                        <input type="date" id="movDataFim" class="form-control">
                    </div>
                    @include('relatorios.partials.filtros-globais', [
                        'ids' => [
                            'periodo' => 'movPeriodo',
                            'aeroporto' => 'movAeroporto',
                            'companhia' => 'movCompanhia',
                            'aeronave' => 'movAeronave',
                        ],
                    ])
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" id="limparMovimentacaoFiltros" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-eraser"></i> Limpar filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">Evolução da movimentação</h5>
                <div style="height: 340px;">
                    <canvas id="movimentacaoChart"></canvas>
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
                                    <th>Período</th>
                                    <th class="text-center">Voos</th>
                                    <th class="text-center">Passageiros</th>
                                    <th class="text-center">Pax/voo</th>
                                    <th class="text-center">Regular/Charter</th>
                                    <th class="text-center">Variação</th>
                                    <th class="text-center">Média</th>
                                </tr>
                            </thead>
                            <tbody id="movimentacaoResultado"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="row g-4" id="movimentacaoResultado"></div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
window.movimentacaoPorPeriodoConfig = {
    modoAdmin: @json($modoAdmin),
    apiUrl: @json(route('api.relatorios.movimentacao-por-periodo')),
};
</script>
<script src="{{ asset('js/relatorios/movimentacao-por-periodo.js') }}"></script>
@endpush
