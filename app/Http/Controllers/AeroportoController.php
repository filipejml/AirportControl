<?php
// app/Http/Controllers/AeroportoController.php

namespace App\Http\Controllers;

use App\Models\Aeroporto;
use App\Models\CompanhiaAerea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AeroportoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $aeroportos = Aeroporto::with('companhias')->get();
        return view('admin.aeroportos.index', compact('aeroportos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companhias = CompanhiaAerea::withCount('aeronaves')->get();
        return view('admin.aeroportos.create', compact('companhias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome_aeroporto' => 'required|string|max:255',
            'companhias' => 'nullable|array'
        ]);

        $aeroporto = Aeroporto::create([
            'nome_aeroporto' => $request->nome_aeroporto
        ]);

        if ($request->has('companhias')) {
            $aeroporto->companhias()->sync($request->companhias);
        }

        return redirect()->route('aeroportos.index')
            ->with('success', 'Aeroporto cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Aeroporto $aeroporto)
    {
        // Estatísticas gerais
        $totalVoos = $aeroporto->voos()->sum('qtd_voos');
        $totalPassageiros = $aeroporto->voos()->sum('total_passageiros');
        $mediaPassageirosPorVoo = $totalVoos > 0 ? round($totalPassageiros / $totalVoos, 0) : 0;
        $totalCompanhias = $aeroporto->companhias()->count();
        
        // Médias das notas (ponderadas por quantidade de voos)
        $notaObj = DB::table('voos')
            ->where('aeroporto_id', $aeroporto->id)
            ->whereNotNull('nota_obj')
            ->select(DB::raw('SUM(qtd_voos * nota_obj) / SUM(qtd_voos) as media'))
            ->value('media') ?? 0;
            
        $notaPontualidade = DB::table('voos')
            ->where('aeroporto_id', $aeroporto->id)
            ->whereNotNull('nota_pontualidade')
            ->select(DB::raw('SUM(qtd_voos * nota_pontualidade) / SUM(qtd_voos) as media'))
            ->value('media') ?? 0;
            
        $notaServicos = DB::table('voos')
            ->where('aeroporto_id', $aeroporto->id)
            ->whereNotNull('nota_servicos')
            ->select(DB::raw('SUM(qtd_voos * nota_servicos) / SUM(qtd_voos) as media'))
            ->value('media') ?? 0;
            
        $notaPatio = DB::table('voos')
            ->where('aeroporto_id', $aeroporto->id)
            ->whereNotNull('nota_patio')
            ->select(DB::raw('SUM(qtd_voos * nota_patio) / SUM(qtd_voos) as media'))
            ->value('media') ?? 0;
        
        // Dados para gráfico de voos por companhia
        $voosPorCompanhia = DB::table('voos')
            ->join('companhias_aereas', 'voos.companhia_aerea_id', '=', 'companhias_aereas.id')
            ->where('voos.aeroporto_id', $aeroporto->id)
            ->select('companhias_aereas.nome as companhia', DB::raw('SUM(voos.qtd_voos) as total_voos'))
            ->groupBy('companhias_aereas.id', 'companhias_aereas.nome')
            ->orderByDesc('total_voos')
            ->get();
        
        // Dados para gráfico de passageiros por companhia
        $passageirosPorCompanhia = DB::table('voos')
            ->join('companhias_aereas', 'voos.companhia_aerea_id', '=', 'companhias_aereas.id')
            ->where('voos.aeroporto_id', $aeroporto->id)
            ->select('companhias_aereas.nome as companhia', DB::raw('SUM(voos.total_passageiros) as total_passageiros'))
            ->groupBy('companhias_aereas.id', 'companhias_aereas.nome')
            ->orderByDesc('total_passageiros')
            ->get();
        
        // Dados para gráfico de voos por horário
        $voosPorHorario = DB::table('voos')
            ->where('aeroporto_id', $aeroporto->id)
            ->select('horario_voo', DB::raw('SUM(qtd_voos) as total_voos'))
            ->whereNotNull('horario_voo')
            ->groupBy('horario_voo')
            ->get();
        
        $horariosMap = [
            'EAM' => 'Early Morning',
            'AM' => 'Morning', 
            'AN' => 'Afternoon',
            'PM' => 'Evening',
            'ALL' => 'All Day'
        ];
        
        $horariosData = [];
        foreach ($horariosMap as $key => $label) {
            $found = $voosPorHorario->firstWhere('horario_voo', $key);
            $horariosData[$label] = $found ? $found->total_voos : 0;
        }
        
        // Dados para gráfico de voos por tipo
        $voosPorTipo = DB::table('voos')
            ->where('aeroporto_id', $aeroporto->id)
            ->select('tipo_voo', DB::raw('SUM(qtd_voos) as total_voos'))
            ->whereNotNull('tipo_voo')
            ->groupBy('tipo_voo')
            ->get();
        
        $tiposData = [
            'Regular' => 0,
            'Charter' => 0
        ];
        
        foreach ($voosPorTipo as $item) {
            if (isset($tiposData[$item->tipo_voo])) {
                $tiposData[$item->tipo_voo] = $item->total_voos;
            }
        }
        
        // Top companhias por nota (melhores avaliadas)
        $topCompanhiasNotas = DB::table('voos')
            ->join('companhias_aereas', 'voos.companhia_aerea_id', '=', 'companhias_aereas.id')
            ->where('voos.aeroporto_id', $aeroporto->id)
            ->select(
                'companhias_aereas.nome as companhia',
                DB::raw('AVG(voos.nota_obj) as nota_obj'),
                DB::raw('AVG(voos.nota_pontualidade) as nota_pontualidade'),
                DB::raw('AVG(voos.nota_servicos) as nota_servicos'),
                DB::raw('AVG(voos.nota_patio) as nota_patio'),
                DB::raw('(AVG(voos.nota_obj) + AVG(voos.nota_pontualidade) + AVG(voos.nota_servicos) + AVG(voos.nota_patio)) / 4 as media_geral')
            )
            ->groupBy('companhias_aereas.id', 'companhias_aereas.nome')
            ->havingRaw('media_geral > 0')
            ->orderByDesc('media_geral')
            ->limit(5)
            ->get();
        
        // Evolução mensal (últimos 12 meses)
        $evolucaoMensal = DB::table('voos')
            ->where('aeroporto_id', $aeroporto->id)
            ->where('created_at', '>=', now()->subMonths(12))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mes'),
                DB::raw('SUM(qtd_voos) as total_voos'),
                DB::raw('SUM(total_passageiros) as total_passageiros')
            )
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();
        
        return view('aeroportos.dashboard', compact(
            'aeroporto',
            'totalVoos',
            'totalPassageiros',
            'mediaPassageirosPorVoo',
            'totalCompanhias',
            'notaObj',
            'notaPontualidade',
            'notaServicos',
            'notaPatio',
            'voosPorCompanhia',
            'passageirosPorCompanhia',
            'horariosData',
            'tiposData',
            'topCompanhiasNotas',
            'evolucaoMensal'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Aeroporto $aeroporto)
    {
        $companhias = CompanhiaAerea::withCount('aeronaves')->get();
        $aeroporto->load('companhias');
        return view('admin.aeroportos.edit', compact('aeroporto', 'companhias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Aeroporto $aeroporto)
    {
        $request->validate([
            'nome_aeroporto' => 'required|string|max:255',
            'companhias' => 'nullable|array'
        ]);

        $aeroporto->update([
            'nome_aeroporto' => $request->nome_aeroporto
        ]);

        if ($request->has('companhias')) {
            $aeroporto->companhias()->sync($request->companhias);
        } else {
            $aeroporto->companhias()->detach();
        }

        return redirect()->route('aeroportos.index')
            ->with('success', 'Aeroporto atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aeroporto $aeroporto)
    {
        try {
            $aeroporto->companhias()->detach();
            $aeroporto->delete();
            
            return redirect()->route('aeroportos.index')
                ->with('success', 'Aeroporto excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('aeroportos.index')
                ->with('error', 'Erro ao excluir aeroporto: ' . $e->getMessage());
        }
    }

    /**
     * Check if airport name already exists
     */
    public function checkName(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255'
        ]);

        $nome = $request->nome;
        $airportId = $request->id;

        // Check if name exists excluding current airport when editing
        $query = Aeroporto::where('nome_aeroporto', $nome);
        
        if ($airportId) {
            $query->where('id', '!=', $airportId);
        }
        
        $exists = $query->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Este nome de aeroporto já está em uso.' : 'Nome disponível'
        ]);
    }

    /**
     * Display general information about airports
     */
    public function informacoes(Request $request)
    {
        $anoSelecionado = $request->get('ano', date('Y'));
        
        // Carrega os aeroportos com relacionamentos
        $aeroportos = Aeroporto::with(['companhias', 'voos.companhiaAerea'])
            ->withCount('companhias')
            ->get();
        
        // CORRIGIDO: usar sum('qtd_voos') ao invés de count()
        foreach ($aeroportos as $aeroporto) {
            // Soma a quantidade de voos (qtd_voos) em vez de contar registros
            $aeroporto->total_voos = $aeroporto->voos()->whereYear('created_at', $anoSelecionado)->sum('qtd_voos');
            $aeroporto->total_passageiros = $aeroporto->voos()->whereYear('created_at', $anoSelecionado)->sum('total_passageiros');
            $aeroporto->media_passageiros_por_voo = $aeroporto->total_voos > 0 
                ? $aeroporto->total_passageiros / $aeroporto->total_voos 
                : 0;
        }
        
        // DADOS PARA GRÁFICO SEMANAL DE PASSAGEIROS POR AEROPORTO
        $dadosSemanais = $this->getDadosPassageirosPorSemana($aeroportos, $anoSelecionado);
        
        // Prepara dados para o JavaScript
        $aeroportosData = $aeroportos->map(function($a) use ($anoSelecionado) {
            // Calcular melhores companhias por categoria para este aeroporto
            $melhoresCompanhias = [];
            $categorias = [
                'Objetivo' => 'nota_obj',
                'Pontualidade' => 'nota_pontualidade',
                'Servicos' => 'nota_servicos',
                'Patio' => 'nota_patio'
            ];
            
            foreach ($categorias as $categoria => $campoNota) {
                // CORRIGIDO: usar sum(qtd_voos * nota) / sum(qtd_voos) para média ponderada
                $melhorCompanhia = DB::table('voos')
                    ->join('companhias_aereas', 'voos.companhia_aerea_id', '=', 'companhias_aereas.id')
                    ->select(
                        'companhias_aereas.id',
                        'companhias_aereas.nome',
                        DB::raw('SUM(voos.qtd_voos * voos.' . $campoNota . ') / SUM(voos.qtd_voos) as media_nota')
                    )
                    ->where('voos.aeroporto_id', $a->id)
                    ->whereYear('voos.created_at', $anoSelecionado)
                    ->whereNotNull('voos.' . $campoNota)
                    ->groupBy('companhias_aereas.id', 'companhias_aereas.nome')
                    ->orderByDesc('media_nota')
                    ->first();
                
                if ($melhorCompanhia) {
                    $melhoresCompanhias[$categoria] = [
                        'id' => $melhorCompanhia->id,
                        'nome' => $melhorCompanhia->nome,
                        'media' => $melhorCompanhia->media_nota ?? 0
                    ];
                } else {
                    $melhoresCompanhias[$categoria] = null;
                }
            }
            
            // CORRIGIDO: calcular médias ponderadas por quantidade de voos
            $totalVoos = $a->total_voos;
            
            $notaObj = DB::table('voos')
                ->where('aeroporto_id', $a->id)
                ->whereYear('created_at', $anoSelecionado)
                ->whereNotNull('nota_obj')
                ->select(DB::raw('SUM(qtd_voos * nota_obj) / SUM(qtd_voos) as media'))
                ->value('media') ?? 0;
                
            $notaPontualidade = DB::table('voos')
                ->where('aeroporto_id', $a->id)
                ->whereYear('created_at', $anoSelecionado)
                ->whereNotNull('nota_pontualidade')
                ->select(DB::raw('SUM(qtd_voos * nota_pontualidade) / SUM(qtd_voos) as media'))
                ->value('media') ?? 0;
                
            $notaServicos = DB::table('voos')
                ->where('aeroporto_id', $a->id)
                ->whereYear('created_at', $anoSelecionado)
                ->whereNotNull('nota_servicos')
                ->select(DB::raw('SUM(qtd_voos * nota_servicos) / SUM(qtd_voos) as media'))
                ->value('media') ?? 0;
                
            $notaPatio = DB::table('voos')
                ->where('aeroporto_id', $a->id)
                ->whereYear('created_at', $anoSelecionado)
                ->whereNotNull('nota_patio')
                ->select(DB::raw('SUM(qtd_voos * nota_patio) / SUM(qtd_voos) as media'))
                ->value('media') ?? 0;
            
            // Média geral (média das 4 notas)
            $mediaNotas = 0;
            $countNotas = 0;
            if ($notaObj > 0) { $mediaNotas += $notaObj; $countNotas++; }
            if ($notaPontualidade > 0) { $mediaNotas += $notaPontualidade; $countNotas++; }
            if ($notaServicos > 0) { $mediaNotas += $notaServicos; $countNotas++; }
            if ($notaPatio > 0) { $mediaNotas += $notaPatio; $countNotas++; }
            $mediaNotas = $countNotas > 0 ? $mediaNotas / $countNotas : 0;
            
            return [
                'id' => $a->id,
                'nome' => $a->nome_aeroporto,
                'companhias_count' => $a->companhias_count ?? 0,
                'total_voos' => $a->total_voos ?? 0,
                'total_passageiros' => $a->total_passageiros ?? 0,
                'media_passageiros_por_voo' => $a->media_passageiros_por_voo ?? 0,
                'media_notas' => $mediaNotas,
                'nota_obj' => $notaObj,
                'nota_pontualidade' => $notaPontualidade,
                'nota_servicos' => $notaServicos,
                'nota_patio' => $notaPatio,
                'melhores_companhias' => $melhoresCompanhias,
                'companhias' => $a->companhias->map(function($c) use ($a, $anoSelecionado) {
                    // CORRIGIDO: contar quantidade de voos (soma do qtd_voos)
                    $totalVoosCompanhia = DB::table('voos')
                        ->where('aeroporto_id', $a->id)
                        ->where('companhia_aerea_id', $c->id)
                        ->whereYear('created_at', $anoSelecionado)
                        ->sum('qtd_voos');
                    
                    return [
                        'id' => $c->id,
                        'nome' => $c->nome,
                        'voos_count' => $totalVoosCompanhia
                    ];
                })
            ];
        });
        
        $companhias = CompanhiaAerea::orderBy('nome')->get();
        
        // CORRIGIDO: totais gerais usando sum(qtd_voos)
        $totalAeroportos = $aeroportos->count();
        $totalVoos = $aeroportos->sum('total_voos');
        $totalPassageiros = $aeroportos->sum('total_passageiros');
        $mediaPassageirosPorVoo = $totalVoos > 0 ? $totalPassageiros / $totalVoos : 0;
        
        // Anos disponíveis para o filtro
        $anosDisponiveis = DB::table('voos')
            ->select(DB::raw('DISTINCT YEAR(created_at) as ano'))
            ->orderBy('ano', 'desc')
            ->pluck('ano')
            ->toArray();
        
        if (empty($anosDisponiveis)) {
            $anosDisponiveis = [date('Y')];
        }
        
        return view('aeroportos.informacoes', compact(
            'aeroportosData',
            'companhias',
            'totalAeroportos',
            'totalVoos',
            'totalPassageiros',
            'mediaPassageirosPorVoo',
            'dadosSemanais',
            'anosDisponiveis',
            'anoSelecionado'
        ));
    }

    /**
     * Get weekly passenger data for all airports - Only show weeks with data
     */
    private function getDadosPassageirosPorSemana($aeroportos, $ano)
    {
        $semanasComDados = [];
        $dadosPorAeroporto = [];
        
        // Buscar todas as semanas que têm dados no ano
        $semanasExistentes = DB::table('voos')
            ->whereYear('created_at', $ano)
            ->select(DB::raw('YEARWEEK(created_at, 1) as semana_ano'), DB::raw('MIN(created_at) as data_referencia'))
            ->groupBy('semana_ano')
            ->orderBy('semana_ano', 'asc')
            ->get();
        
        if ($semanasExistentes->isEmpty()) {
            return ['semanas' => [], 'aeroportos' => []];
        }
        
        // Para cada semana com dados, criar o label
        foreach ($semanasExistentes as $semana) {
            $data = \Carbon\Carbon::parse($semana->data_referencia);
            $numeroSemana = $data->weekOfYear;
            $dataInicioSemana = $data->copy()->startOfWeek();
            $dataFimSemana = $data->copy()->endOfWeek();
            
            $semanaLabel = 'Sem ' . $numeroSemana . ' (' . $dataInicioSemana->format('d/m') . '-' . $dataFimSemana->format('d/m') . ')';
            $semanasComDados[] = [
                'label' => $semanaLabel,
                'semana_ano' => $semana->semana_ano,
                'data_inicio' => $dataInicioSemana,
                'data_fim' => $dataFimSemana
            ];
        }
        
        // Para cada aeroporto, buscar os dados apenas para as semanas que existem
        foreach ($aeroportos as $aeroporto) {
            $dadosSemanais = [];
            
            foreach ($semanasComDados as $semana) {
                $totalPassageiros = DB::table('voos')
                    ->where('aeroporto_id', $aeroporto->id)
                    ->whereYear('created_at', $ano)
                    ->whereBetween('created_at', [$semana['data_inicio'], $semana['data_fim']])
                    ->sum('total_passageiros');
                
                $dadosSemanais[] = $totalPassageiros;
            }
            
            // Só adicionar aeroportos que têm pelo menos um voo registrado no ano
            if (array_sum($dadosSemanais) > 0) {
                $dadosPorAeroporto[] = [
                    'nome' => $aeroporto->nome_aeroporto,
                    'dados' => $dadosSemanais,
                    'cor' => $this->gerarCorAleatoria(count($dadosPorAeroporto))
                ];
            }
        }
        
        return [
            'semanas' => array_column($semanasComDados, 'label'),
            'aeroportos' => $dadosPorAeroporto
        ];
    }

    /**
     * Generate random color for chart lines
     */
    private function gerarCorAleatoria($index)
    {
        $cores = [
            '#0d6efd', // azul
            '#198754', // verde
            '#dc3545', // vermelho
            '#ffc107', // amarelo
            '#6f42c1', // roxo
            '#fd7e14', // laranja
            '#20c997', // turquesa
            '#e83e8c', // rosa
            '#6610f2', // índigo
            '#d63384'  // magenta
        ];
        
        return $cores[$index % count($cores)];
    }
}