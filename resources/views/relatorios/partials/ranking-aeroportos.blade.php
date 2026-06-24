<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h3 class="fw-bold">
                    <i class="bi bi-trophy"></i> Ranking de Aeroportos
                </h3>
                <p class="text-muted mb-0">
                    Compare movimentação, cobertura e desempenho operacional dos aeroportos.
                </p>
            </div>
            <button class="btn btn-success" id="exportarRankingCsv">
                <i class="bi bi-file-spreadsheet"></i> Exportar CSV
            </button>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <small>Aeroportos no ranking</small>
                        <h3 class="mb-0" id="rankTotalAeroportos">-</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <small>Total de voos</small>
                        <h3 class="mb-0" id="rankTotalVoos">-</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <small>Total de passageiros</small>
                        <h3 class="mb-0" id="rankTotalPassageiros">-</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark h-100">
                    <div class="card-body">
                        <small>Líder atual</small>
                        <h5 class="mb-0" id="rankLider">-</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="rankPeriodo" class="form-label fw-semibold">Período</label>
                        <select id="rankPeriodo" class="form-select">
                            <option value="">Todos os períodos</option>
                            <option value="hoje">Hoje</option>
                            <option value="semana">Esta semana</option>
                            <option value="mes">Este mês</option>
                            <option value="ano">Este ano</option>
                        </select>
                    </div>
                    @include('relatorios.partials.filtros-globais', [
                        'ids' => [
                            'aeroporto' => 'rankAeroporto',
                            'companhia' => 'rankCompanhia',
                            'aeronave' => 'rankAeronave',
                        ],
                    ])
                    <div class="col-md-4">
                        <label for="rankOrdenacao" class="form-label fw-semibold">Critério do ranking</label>
                        <select id="rankOrdenacao" class="form-select">
                            <option value="total_voos">Total de voos</option>
                            <option value="total_passageiros">Total de passageiros</option>
                            <option value="media_passageiros_por_voo">Passageiros por voo</option>
                            <option value="total_companhias">Companhias ativas</option>
                            <option value="media_geral">Avaliação geral</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="button" id="limparRankingFiltros" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-eraser"></i> Limpar filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">Top aeroportos</h5>
                <div style="height: 340px;">
                    <canvas id="rankingAeroportosChart"></canvas>
                </div>
            </div>
        </div>

        @if($modoAdmin)
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">Posição</th>
                                    <th>Aeroporto</th>
                                    <th class="text-center">Voos</th>
                                    <th class="text-center">Passageiros</th>
                                    <th class="text-center">Pax/voo</th>
                                    <th class="text-center">Cobertura</th>
                                    <th class="text-center">Média</th>
                                </tr>
                            </thead>
                            <tbody id="rankingAeroportosResultado"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="row g-4" id="rankingAeroportosResultado"></div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
window.rankingAeroportosConfig = {
    modoAdmin: @json($modoAdmin),
    apiUrl: @json(route('api.relatorios.ranking-aeroportos')),
};
</script>
<script src="{{ asset('js/relatorios/ranking-aeroportos.js') }}"></script>
@endpush
