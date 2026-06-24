<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h3 class="fw-bold">
                    <i class="bi bi-speedometer2"></i> Ocupação dos Voos
                </h3>
                <p class="text-muted mb-0">
                    Compare passageiros transportados com a capacidade oferecida pelas aeronaves.
                </p>
                @include('relatorios.partials.status-badges', [
                    'relatorio' => $relatorio,
                    'class' => 'mt-2',
                ])
            </div>
            <button class="btn btn-success" id="exportarOcupacaoCsv">
                <i class="bi bi-file-spreadsheet"></i> Exportar CSV
            </button>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <small>Total de voos</small>
                        <h3 class="mb-0" id="ocupTotalVoos">-</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <small>Passageiros</small>
                        <h3 class="mb-0" id="ocupTotalPassageiros">-</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <small>Assentos ofertados</small>
                        <h3 class="mb-0" id="ocupAssentos">-</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark h-100">
                    <div class="card-body">
                        <small>Ocupação geral</small>
                        <h3 class="mb-0" id="ocupTaxaGeral">-</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="ocupPeriodo" class="form-label fw-semibold">Período</label>
                        <select id="ocupPeriodo" class="form-select">
                            <option value="">Todos os períodos</option>
                            <option value="hoje">Hoje</option>
                            <option value="semana">Esta semana</option>
                            <option value="mes">Este mês</option>
                            <option value="ano">Este ano</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="ocupCompanhia" class="form-label fw-semibold">Companhia</label>
                        <select id="ocupCompanhia" class="form-select">
                            <option value="">Todas</option>
                            @foreach($companhias as $companhia)
                                <option value="{{ $companhia->id }}">{{ $companhia->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    @include('relatorios.partials.filtros-globais', [
                        'ids' => [
                            'aeronave' => 'ocupAeronave',
                        ],
                    ])
                    <div class="col-md-3">
                        <label for="ocupAeroporto" class="form-label fw-semibold">Aeroporto</label>
                        <select id="ocupAeroporto" class="form-select">
                            <option value="">Todos</option>
                            @foreach($aeroportos as $aeroporto)
                                <option value="{{ $aeroporto->id }}">{{ $aeroporto->nome_aeroporto }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="ocupFaixa" class="form-label fw-semibold">Faixa de ocupação</label>
                        <select id="ocupFaixa" class="form-select">
                            <option value="">Todas</option>
                            <option value="baixa">Baixa (&lt; 50%)</option>
                            <option value="media">Média (50%–74,9%)</option>
                            <option value="alta">Alta (75%–99,9%)</option>
                            <option value="lotado">Lotado (≥ 100%)</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="button" id="limparOcupacaoFiltros" class="btn btn-outline-secondary">
                            <i class="bi bi-eraser"></i> Limpar filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-5">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">Distribuição por faixa</h5>
                        <div style="height: 300px;">
                            <canvas id="ocupacaoFaixasChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="card-title">Maiores taxas de ocupação</h5>
                        <div style="height: 300px;">
                            <canvas id="ocupacaoRankingChart"></canvas>
                        </div>
                    </div>
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
                                    <th>Voo</th>
                                    <th>Companhia/Aeroporto</th>
                                    <th>Aeronave</th>
                                    <th class="text-center">Voos</th>
                                    <th class="text-center">Passageiros</th>
                                    <th class="text-center">Assentos</th>
                                    <th class="text-center">Ocupação</th>
                                </tr>
                            </thead>
                            <tbody id="ocupacaoResultado"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="row g-4" id="ocupacaoResultado"></div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
window.ocupacaoVoosConfig = {
    modoAdmin: @json($modoAdmin),
    apiUrl: @json(route('api.relatorios.ocupacao-voos')),
};
</script>
<script src="{{ asset('js/relatorios/ocupacao-voos.js') }}"></script>
@endpush
