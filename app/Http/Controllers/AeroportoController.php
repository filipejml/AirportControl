<?php
// app/Http/Controllers/AeroportoController.php

namespace App\Http\Controllers;

use App\Models\Aeroporto;
use App\Models\CompanhiaAerea;
use App\Models\Deposito;
use App\Models\Veiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AeroportoController extends Controller
{

    /**
     * STEP 1: Informações básicas do aeroporto
     */
    public function createStep1()
    {
        // Limpar sessão anterior se existir
        Session::forget('aeroporto_em_criacao');
        
        // Carregar companhias para o formulário (igual ao create antigo)
        $companhias = CompanhiaAerea::withCount('aeronaves')->get();
        
        return view('admin.aeroportos.wizard.step1', compact('companhias'));
    }

    /**
     * Store Step 1 - Salva informações básicas
     */
    public function storeStep1(Request $request)
    {
        $request->validate([
            'nome_aeroporto' => 'required|string|max:255|unique:aeroportos,nome_aeroporto',
            'companhias' => 'nullable|array'
        ]);

        // Criar aeroporto
        $aeroporto = Aeroporto::create([
            'nome_aeroporto' => $request->nome_aeroporto
        ]);

        // Associar companhias selecionadas
        if ($request->has('companhias')) {
            $aeroporto->companhias()->sync($request->companhias);
        }

        // Armazenar ID na sessão para uso nos próximos passos
        Session::put('aeroporto_em_criacao', $aeroporto->id);

        return redirect()->route('aeroportos.create.step2', $aeroporto)
            ->with('success', 'Aeroporto criado com sucesso! Agora adicione os depósitos.');
    }

    /**
     * STEP 2: Cadastro de Depósitos
     */
    public function createStep2(Aeroporto $aeroporto)
    {
        // Verificar se o aeroporto pertence à criação atual
        if (Session::get('aeroporto_em_criacao') != $aeroporto->id) {
            return redirect()->route('aeroportos.create.step1')
                ->with('error', 'Sessão expirada. Comece novamente.');
        }

        $depositos = $aeroporto->depositos()->get();
        
        return view('admin.aeroportos.wizard.step2', compact('aeroporto', 'depositos'));
    }

    /**
     * Store Step 2 - Salva depósitos
     */
    public function storeStep2(Request $request, Aeroporto $aeroporto)
    {
        // Validar se é para pular ou salvar
        if ($request->has('skip')) {
            return redirect()->route('aeroportos.create.step3', $aeroporto);
        }

        // Se não tem depósitos para salvar, também pula
        if (empty($request->depositos)) {
            return redirect()->route('aeroportos.create.step3', $aeroporto);
        }

        // Validar os dados dos depósitos
        $request->validate([
            'depositos.*.nome' => 'required|string|max:255',
            'depositos.*.capacidade_maxima' => 'nullable|integer|min:0',
            'depositos.*.observacoes' => 'nullable|string'
        ]);

        // Salvar cada depósito
        foreach ($request->depositos as $depositoData) {
            $depositoData['status'] = 'ativo';
            $aeroporto->depositos()->create($depositoData);
        }

        // Redirecionar para o próximo passo
        return redirect()->route('aeroportos.create.step3', $aeroporto)
            ->with('success', 'Depósitos adicionados com sucesso!');
    }

    /**
     * STEP 3: Cadastro de Veículos por depósito
     */
    public function createStep3(Aeroporto $aeroporto)
    {
        // Verificar se o aeroporto pertence à criação atual
        if (Session::get('aeroporto_em_criacao') != $aeroporto->id) {
            return redirect()->route('aeroportos.create.step1')
                ->with('error', 'Sessão expirada. Comece novamente.');
        }

        $depositos = $aeroporto->depositos()->with('veiculos')->get();
        
        return view('admin.aeroportos.wizard.step3', compact('aeroporto', 'depositos'));
    }

    /**
     * Store Step 3 - Salva veículos
     */
    public function storeStep3(Request $request, Aeroporto $aeroporto)
    {
        // Se for pular, finalizar
        if ($request->has('skip')) {
            Session::forget('aeroporto_em_criacao');
            return redirect()->route('aeroportos.show', $aeroporto)
                ->with('success', 'Aeroporto cadastrado com sucesso!');
        }

        $request->validate([
            'veiculos' => 'array',
            'veiculos.*.deposito_id' => 'required|exists:depositos,id',
            'veiculos.*.codigo' => 'required|string|max:50|unique:veiculos,codigo',
            'veiculos.*.tipo_veiculo' => 'required|in:' . implode(',', array_keys(Veiculo::TIPOS_VEICULOS)),
            'veiculos.*.modelo' => 'nullable|string|max:100',
            'veiculos.*.fabricante' => 'nullable|string|max:100',
            'veiculos.*.ano_fabricacao' => 'nullable|integer|min:1900|max:' . date('Y'),
            'veiculos.*.capacidade_operacional' => 'nullable|numeric|min:0',
        ]);

        $unidadeMap = [
            'esteira_bagagem' => 'kg',
            'caminhao_combustivel' => 'litros',
            'carrinho_bagagem' => 'unidades',
            'caminhao_pushback' => 'toneladas',
            'caminhao_escada' => 'metros',
            'caminhao_limpeza' => 'litros'
        ];

        foreach ($request->veiculos as $veiculoData) {
            $deposito = Deposito::find($veiculoData['deposito_id']);
            
            // Verificar capacidade do depósito
            if (!$deposito->hasEspacoDisponivel()) {
                continue; // Pular se não tem espaço
            }
            
            $veiculoData['unidade_capacidade'] = $unidadeMap[$veiculoData['tipo_veiculo']] ?? null;
            $veiculoData['status'] = 'disponivel';
            
            $deposito->veiculos()->create($veiculoData);
        }

        // Limpar sessão
        Session::forget('aeroporto_em_criacao');

        return redirect()->route('aeroportos.show', $aeroporto)
            ->with('success', 'Aeroporto cadastrado com todos os veículos!');
    }

    public function getVeiculoTemplate(Request $request)
    {
        $depositoId = $request->deposito_id;
        $index = $request->index;
        
        return view('admin.aeroportos.wizard.partials.veiculo-form', compact('depositoId', 'index'))->render();
    }

    public function checkVeiculoCodigo(Request $request)
    {
        $request->validate(['codigo' => 'required|string']);
        
        $exists = Veiculo::where('codigo', $request->codigo)->exists();
        
        return response()->json(['exists' => $exists]);
    }


    /**
     * Display a listing of the resource (ADMIN)
     */
    public function index()
    {
        $aeroportos = Aeroporto::with('companhias')->get();
        return view('admin.aeroportos.index', compact('aeroportos'));
    }

    /**
     * Store a newly created resource in storage (ADMIN)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome_aeroporto' => 'required|string|max:255|unique:aeroportos,nome_aeroporto',
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
     * Display the specified resource (ADMIN) - CORRIGIDO
     */
    public function show(Aeroporto $aeroporto)
    {
        // Carregar os relacionamentos necessários para o ADMIN
        $aeroporto->load(['companhias', 'depositos.veiculos']);
        
        // Retornar a view ADMIN (não a de usuário comum)
        return view('admin.aeroportos.show', compact('aeroporto'));
    }

    /**
     * Show the form for editing the specified resource (ADMIN)
     */
    public function edit(Aeroporto $aeroporto)
    {
        $companhias = CompanhiaAerea::withCount('aeronaves')->get();
        $aeroporto->load('companhias');
        return view('admin.aeroportos.edit', compact('aeroporto', 'companhias'));
    }

    /**
     * Update the specified resource in storage (ADMIN)
     */
    public function update(Request $request, Aeroporto $aeroporto)
    {
        $request->validate([
            'nome_aeroporto' => 'required|string|max:255|unique:aeroportos,nome_aeroporto,' . $aeroporto->id,
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
     * Remove the specified resource from storage (ADMIN)
     */
    public function destroy(Aeroporto $aeroporto)
    {
        try {
            // Verificar se tem voos
            if ($aeroporto->voos()->exists()) {
                return redirect()->route('aeroportos.index')
                    ->with('error', 'Não é possível excluir um aeroporto que possui voos cadastrados.');
            }
            
            $aeroporto->companhias()->detach();
            $aeroporto->depositos()->delete(); // Remove depósitos e veículos (cascade)
            $aeroporto->delete();
            
            return redirect()->route('aeroportos.index')
                ->with('success', 'Aeroporto excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('aeroportos.index')
                ->with('error', 'Erro ao excluir aeroporto: ' . $e->getMessage());
        }
    }

    /**
     * Display dashboard for common user (USUÁRIO COMUM)
     */
    public function dashboard(Aeroporto $aeroporto)
    {
        // Carregar depósitos e veículos para exibir no dashboard
        $aeroporto->load(['depositos.veiculos']);
        
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
        
        // Retornar a view de USUÁRIO COMUM (dashboard)
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
     * Display general information about airports (USUÁRIO COMUM)
     */
    public function informacoes(Request $request)
    {
        $anoSelecionado = $request->get('ano', date('Y'));
        
        // Carrega os aeroportos com relacionamentos
        $aeroportos = Aeroporto::with(['companhias', 'voos.companhiaAerea'])
            ->withCount('companhias')
            ->get();
        
        // Calcular totais para cada aeroporto
        foreach ($aeroportos as $aeroporto) {
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
            
            // Calcular médias ponderadas
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
            
            // Média geral
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
        
        $totalAeroportos = $aeroportos->count();
        $totalVoos = $aeroportos->sum('total_voos');
        $totalPassageiros = $aeroportos->sum('total_passageiros');
        $mediaPassageirosPorVoo = $totalVoos > 0 ? $totalPassageiros / $totalVoos : 0;
        
        $anosDisponiveis = DB::table('voos')
            ->select(DB::raw('DISTINCT YEAR(created_at) as ano'))
            ->orderBy('ano', 'desc')
            ->pluck('ano')
            ->toArray();
        
        if (empty($anosDisponiveis)) {
            $anosDisponiveis = [date('Y')];
        }
        
        // Retornar a view de USUÁRIO COMUM (informacoes)
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
     * Check if airport name already exists (AJAX - ADMIN)
     */
    public function checkName(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255'
        ]);

        $nome = $request->nome;
        $airportId = $request->id;

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
     * Get weekly passenger data for all airports - Only show weeks with data
     */
    private function getDadosPassageirosPorSemana($aeroportos, $ano)
    {
        $semanasComDados = [];
        $dadosPorAeroporto = [];
        
        $semanasExistentes = DB::table('voos')
            ->whereYear('created_at', $ano)
            ->select(DB::raw('YEARWEEK(created_at, 1) as semana_ano'), DB::raw('MIN(created_at) as data_referencia'))
            ->groupBy('semana_ano')
            ->orderBy('semana_ano', 'asc')
            ->get();
        
        if ($semanasExistentes->isEmpty()) {
            return ['semanas' => [], 'aeroportos' => []];
        }
        
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
            '#0d6efd', '#198754', '#dc3545', '#ffc107', '#6f42c1',
            '#fd7e14', '#20c997', '#e83e8c', '#6610f2', '#d63384'
        ];
        
        return $cores[$index % count($cores)];
    }
}