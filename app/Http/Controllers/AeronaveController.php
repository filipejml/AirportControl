<?php
// app/Http/Controllers/AeronaveController.php

namespace App\Http\Controllers;

use App\Models\Aeronave;
use App\Models\Fabricante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;

class AeronaveController extends Controller
{
    public function index(Request $request)
    {
        // Get sorting parameters
        $sortField = $request->get('sort', 'id'); // default sort by id
        $sortDirection = $request->get('direction', 'asc'); // default direction asc
        
        // Define allowed sort fields to prevent SQL injection
        $allowedSortFields = ['id', 'modelo', 'fabricante', 'capacidade', 'porte', 'companhias_count'];
        
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'id';
        }
        
        // Define default sorting based on requirements
        // If no sort parameter is provided, apply specific default sorts
        if (!$request->has('sort')) {
            // No sort parameter - apply all default sorts
            $aeronaves = Aeronave::with('fabricante', 'companhias')
                ->withCount('companhias')
                ->orderBy('id', 'asc') // Default: sort by ID
                ->get()
                ->sortBy(function ($aeronave) {
                    // Sort by fabricante name A-Z
                    return $aeronave->fabricante ? $aeronave->fabricante->nome : '';
                })
                ->sortByDesc('capacidade') // Then by capacidade descending
                ->sortBy('porte') // Then by porte A-Z
                ->sortByDesc('companhias_count'); // Finally by companhias count descending
                
            // Re-index the collection to maintain proper ordering
            $aeronaves = $aeronaves->values();
        } else {
            // User clicked on a sort header - apply single sort
            if ($sortField == 'fabricante') {
                // Sort by fabricante name
                $aeronaves = Aeronave::with('fabricante', 'companhias')
                    ->withCount('companhias')
                    ->get()
                    ->sortBy(function ($aeronave) {
                        return $aeronave->fabricante ? $aeronave->fabricante->nome : '';
                    });
                    
                if ($sortDirection == 'desc') {
                    $aeronaves = $aeronaves->reverse();
                }
            } elseif ($sortField == 'companhias_count') {
                // Sort by companhias count
                $aeronaves = Aeronave::with('fabricante', 'companhias')
                    ->withCount('companhias')
                    ->get()
                    ->sortBy('companhias_count');
                    
                if ($sortDirection == 'desc') {
                    $aeronaves = $aeronaves->reverse();
                }
            } else {
                // Sort by regular database fields
                $aeronaves = Aeronave::with('fabricante', 'companhias')
                    ->withCount('companhias')
                    ->orderBy($sortField, $sortDirection)
                    ->get();
            }
        }
        
        return view('admin.aeronaves.index', compact('aeronaves', 'sortField', 'sortDirection'));
    }

    // NOVO MÉTODO SHOW
    public function show(Aeronave $aeronave)
    {
        // Carrega o relacionamento com fabricante
        $aeronave->load('fabricante', 'companhias');
        
        return view('admin.aeronaves.show', compact('aeronave'));
    }

    public function create()
    {
        $fabricantes = Fabricante::orderBy('nome')->get();
        return view('admin.aeronaves.create', compact('fabricantes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'modelo' => 'required|string|max:255',
            'capacidade' => 'required|integer|min:1',
            'fabricante_id' => 'required|exists:fabricantes,id'
        ]);

        $aeronave = Aeronave::create($request->all());

        return redirect()->route('aeronaves.index')
            ->with('success', "Aeronave cadastrada com sucesso! Porte: {$aeronave->porte_descricao}");
    }

    public function edit(Aeronave $aeronave)
    {
        $fabricantes = Fabricante::orderBy('nome')->get();
        return view('admin.aeronaves.edit', compact('aeronave', 'fabricantes'));
    }

    public function update(Request $request, Aeronave $aeronave)
    {
        $request->validate([
            'modelo' => 'required|string|max:255',
            'capacidade' => 'required|integer|min:1',
            'fabricante_id' => 'required|exists:fabricantes,id'
        ]);

        $aeronave->update($request->all());

        return redirect()->route('aeronaves.index')
            ->with('success', "Aeronave atualizada com sucesso! Porte: {$aeronave->porte_descricao}");
    }

    public function destroy(Aeronave $aeronave)
    {
        $aeronave->delete();

        return redirect()->route('aeronaves.index')
            ->with('success', 'Aeronave excluída com sucesso!');
    }

    // Verifica se o modelo existe no cadastrado de aeronaves (usado para validação AJAX)
    public function verificarModelo(Request $request)
    {
        $request->validate([
            'modelo' => 'required|string|max:255'
        ]);
        
        $modelo = $request->input('modelo');
        $excluirId = $request->input('excluir_id'); // Para edição, ignorar o próprio registro
        
        $query = Aeronave::where('modelo', $modelo);
        
        if ($excluirId) {
            $query->where('id', '!=', $excluirId);
        }
        
        $existe = $query->exists();
        
        return response()->json([
            'existe' => $existe,
            'message' => $existe ? 'Este modelo de aeronave já está cadastrado!' : null
        ]);
    }

    /**
     * Display general information about aircrafts
     */
    public function informacoes()
    {
        // Buscar todas as aeronaves com seus relacionamentos
        $aeronaves = Aeronave::with(['fabricante', 'voos.companhiaAerea'])
            ->withCount('voos')
            ->get();
        
        // Organizar dados por modelo
        $modelosComDados = [];
        
        foreach ($aeronaves as $aeronave) {
            $modelo = $aeronave->modelo;
            
            // CORRIGIDO: Usar sum('qtd_voos') ao invés de count()
            $totalVoos = $aeronave->voos()->sum('qtd_voos');
            $totalPassageiros = $aeronave->voos()->sum('total_passageiros');
            
            // CORRIGIDO: Calcular médias ponderadas por quantidade de voos
            $mediaObjetivo = DB::table('voos')
                ->where('aeronave_id', $aeronave->id)
                ->whereNotNull('nota_obj')
                ->select(DB::raw('SUM(qtd_voos * nota_obj) / SUM(qtd_voos) as media'))
                ->value('media') ?? 0;
                
            $mediaPontualidade = DB::table('voos')
                ->where('aeronave_id', $aeronave->id)
                ->whereNotNull('nota_pontualidade')
                ->select(DB::raw('SUM(qtd_voos * nota_pontualidade) / SUM(qtd_voos) as media'))
                ->value('media') ?? 0;
                
            $mediaServicos = DB::table('voos')
                ->where('aeronave_id', $aeronave->id)
                ->whereNotNull('nota_servicos')
                ->select(DB::raw('SUM(qtd_voos * nota_servicos) / SUM(qtd_voos) as media'))
                ->value('media') ?? 0;
                
            $mediaPatio = DB::table('voos')
                ->where('aeronave_id', $aeronave->id)
                ->whereNotNull('nota_patio')
                ->select(DB::raw('SUM(qtd_voos * nota_patio) / SUM(qtd_voos) as media'))
                ->value('media') ?? 0;
            
            $modelosComDados[$modelo] = [
                'id' => $aeronave->id,
                'fabricante' => $aeronave->fabricante->nome ?? 'N/A',
                'capacidade' => $aeronave->capacidade,
                'porte' => $aeronave->porte_descricao,
                'total_voos' => $totalVoos,
                'total_passageiros' => $totalPassageiros,
                'media_objetivo' => $mediaObjetivo ? round($mediaObjetivo, 1) : 0,
                'media_pontualidade' => $mediaPontualidade ? round($mediaPontualidade, 1) : 0,
                'media_servicos' => $mediaServicos ? round($mediaServicos, 1) : 0,
                'media_patio' => $mediaPatio ? round($mediaPatio, 1) : 0,
                'tem_dados' => $totalVoos > 0
            ];
        }
        
        return view('aeronaves.informacoes', compact('modelosComDados'));
    }

    /**
     * Display dashboard for a specific aircraft
     */
    public function dashboard(Request $request, Aeronave $aeronave)
    {
        // Carregar relacionamentos necessários
        $aeronave->load(['fabricante', 'companhias']);
        
        // Filtros
        $companhiaSelecionada = $request->get('companhia', 'geral');
        $periodoSelecionado = $request->get('periodo', 'geral');
        $semanaSelecionada = $request->get('semana');
        $anoFiltro = $request->get('ano');
        $mesSelecionado = $request->get('mes');
        $anoSelecionado = $request->get('ano_selecionado');
        
        // Query base para voos com esta aeronave
        $queryVoos = $aeronave->voos()->with(['companhiaAerea', 'aeroporto']);
        
        // Aplicar filtro de companhia
        if ($companhiaSelecionada !== 'geral') {
            $queryVoos->where('companhia_aerea_id', $companhiaSelecionada);
        }
        
        // Aplicar filtro de período
        if ($periodoSelecionado !== 'geral') {
            switch ($periodoSelecionado) {
                case 'semanal':
                    if ($semanaSelecionada) {
                        list($ano, $semana) = explode('-W', $semanaSelecionada);
                        $dataInicio = (new DateTime())->setISODate($ano, $semana)->format('Y-m-d 00:00:00');
                        $dataFim = (new DateTime())->setISODate($ano, $semana)->modify('+6 days')->format('Y-m-d 23:59:59');
                        $queryVoos->whereBetween('created_at', [$dataInicio, $dataFim]);
                    }
                    break;
                case 'mensal':
                    if ($anoFiltro && $mesSelecionado) {
                        $dataInicio = "{$anoFiltro}-{$mesSelecionado}-01 00:00:00";
                        $dataFim = date('Y-m-t 23:59:59', strtotime($dataInicio));
                        $queryVoos->whereBetween('created_at', [$dataInicio, $dataFim]);
                    }
                    break;
                case 'anual':
                    if ($anoSelecionado) {
                        $dataInicio = "{$anoSelecionado}-01-01 00:00:00";
                        $dataFim = "{$anoSelecionado}-12-31 23:59:59";
                        $queryVoos->whereBetween('created_at', [$dataInicio, $dataFim]);
                    }
                    break;
            }
        }
        
        // Obter voos filtrados
        $voosFiltrados = $queryVoos->get();
        
        // Estatísticas básicas com filtros
        $totalVoos = $voosFiltrados->count();
        $totalPassageiros = $voosFiltrados->sum('total_passageiros');
        
        // Médias das notas com filtros
        $notaObj = $voosFiltrados->avg('nota_obj') ?? 0;
        $notaPontualidade = $voosFiltrados->avg('nota_pontualidade') ?? 0;
        $notaServicos = $voosFiltrados->avg('nota_servicos') ?? 0;
        $notaPatio = $voosFiltrados->avg('nota_patio') ?? 0;
        
        // Últimos voos (últimos 5)
        $ultimosVoos = $queryVoos->clone()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Total de companhias que utilizam esta aeronave
        $totalCompanhias = $aeronave->companhias()->count();
        
        // Total de aeroportos atendidos
        $totalAeroportos = $voosFiltrados->pluck('aeroporto_id')->unique()->count();
        
        // Voos por companhia
        $voosPorCompanhia = collect();
        foreach ($aeronave->companhias as $companhia) {
            $quantidadeVoos = $queryVoos->clone()
                ->where('companhia_aerea_id', $companhia->id)
                ->count();
            
            if ($quantidadeVoos > 0) {
                $voosPorCompanhia->put($companhia->nome, $quantidadeVoos);
            }
        }
        $voosPorCompanhia = $voosPorCompanhia->sortDesc();
        
        // Passageiros por companhia
        $passageirosPorCompanhia = collect();
        foreach ($aeronave->companhias as $companhia) {
            $quantidadePassageiros = $queryVoos->clone()
                ->where('companhia_aerea_id', $companhia->id)
                ->sum('total_passageiros');
            
            if ($quantidadePassageiros > 0) {
                $passageirosPorCompanhia->put($companhia->nome, $quantidadePassageiros);
            }
        }
        $passageirosPorCompanhia = $passageirosPorCompanhia->sortDesc();
        
        // Companhias disponíveis para filtro
        $companhiasDisponiveis = $aeronave->companhias;
        
        // Semanas disponíveis para filtro (últimas 52 semanas)
        $semanasDisponiveis = collect();
        for ($i = 0; $i < 52; $i++) {
            $data = now()->subWeeks($i);
            $semanasDisponiveis->push((object)[
                'semana' => $data->format('Y-\WW'),
                'numero_semana' => $data->weekOfYear,
                'ano' => $data->year
            ]);
        }
        $semanasDisponiveis = $semanasDisponiveis->unique('semana');
        
        // Anos disponíveis para filtro
        $anosDisponiveis = $aeronave->voos()
            ->select(DB::raw('DISTINCT YEAR(created_at) as ano'))
            ->orderBy('ano', 'desc')
            ->pluck('ano')
            ->toArray();
        
        return view('aeronaves.dashboard', compact(
            'aeronave',
            'totalVoos',
            'totalPassageiros',
            'totalCompanhias',
            'totalAeroportos',
            'notaObj',
            'notaPontualidade',
            'notaServicos',
            'notaPatio',
            'ultimosVoos',
            'companhiasDisponiveis',
            'semanasDisponiveis',
            'anosDisponiveis',
            'companhiaSelecionada',
            'periodoSelecionado',
            'semanaSelecionada',
            'anoFiltro',
            'mesSelecionado',
            'anoSelecionado',
            'voosPorCompanhia',
            'passageirosPorCompanhia'
        ));
    }

    /**
     * Display ranking and general data about aircrafts
     */
    public function ranking()
    {
        // Buscar todas as aeronaves com seus relacionamentos
        $aeronaves = Aeronave::with(['fabricante', 'voos.companhiaAerea'])
            ->withCount('voos')
            ->get();
        
        $rankings = [];
        
        foreach ($aeronaves as $aeronave) {
            $totalVoos = $aeronave->voos()->sum('qtd_voos');
            $totalPassageiros = $aeronave->voos()->sum('total_passageiros');
            
            // Contar quantos registros de voo (não a quantidade total)
            $numeroRegistrosVoos = $aeronave->voos()->count();
            
            // Só incluir aeronaves com MÍNIMO 3 REGISTROS de voo para cálculos de nota
            // (alinhado com a lógica do Dashboard)
            if ($numeroRegistrosVoos < 3) {
                // Adiciona com notas zeradas, mas marca como sem dados suficientes
                $rankings[] = [
                    'id' => $aeronave->id,
                    'modelo' => $aeronave->modelo,
                    'fabricante' => $aeronave->fabricante->nome ?? 'N/A',
                    'capacidade' => $aeronave->capacidade,
                    'porte' => $aeronave->porte_descricao,
                    'total_voos' => $totalVoos,
                    'total_passageiros' => $totalPassageiros,
                    'media_objetivo' => 0,
                    'media_pontualidade' => 0,
                    'media_servicos' => 0,
                    'media_patio' => 0,
                    'nota_geral' => 0,
                    'tem_dados' => $totalVoos > 0,
                    'dados_suficientes' => false
                ];
                continue;
            }
            
            // Médias simples das notas (igual ao Dashboard)
            $mediaObjetivo = DB::table('voos')
                ->where('aeronave_id', $aeronave->id)
                ->whereNotNull('nota_obj')
                ->select(DB::raw('AVG(nota_obj) as media'))
                ->value('media') ?? 0;
                
            $mediaPontualidade = DB::table('voos')
                ->where('aeronave_id', $aeronave->id)
                ->whereNotNull('nota_pontualidade')
                ->select(DB::raw('AVG(nota_pontualidade) as media'))
                ->value('media') ?? 0;
                
            $mediaServicos = DB::table('voos')
                ->where('aeronave_id', $aeronave->id)
                ->whereNotNull('nota_servicos')
                ->select(DB::raw('AVG(nota_servicos) as media'))
                ->value('media') ?? 0;
                
            $mediaPatio = DB::table('voos')
                ->where('aeronave_id', $aeronave->id)
                ->whereNotNull('nota_patio')
                ->select(DB::raw('AVG(nota_patio) as media'))
                ->value('media') ?? 0;
            
            // Nota média geral (média das 4 notas)
            $notaGeral = ($mediaObjetivo + $mediaPontualidade + $mediaServicos + $mediaPatio) / 4;
            
            $rankings[] = [
                'id' => $aeronave->id,
                'modelo' => $aeronave->modelo,
                'fabricante' => $aeronave->fabricante->nome ?? 'N/A',
                'capacidade' => $aeronave->capacidade,
                'porte' => $aeronave->porte_descricao,
                'total_voos' => $totalVoos,
                'total_passageiros' => $totalPassageiros,
                'media_objetivo' => $mediaObjetivo ? round($mediaObjetivo, 1) : 0,
                'media_pontualidade' => $mediaPontualidade ? round($mediaPontualidade, 1) : 0,
                'media_servicos' => $mediaServicos ? round($mediaServicos, 1) : 0,
                'media_patio' => $mediaPatio ? round($mediaPatio, 1) : 0,
                'nota_geral' => $notaGeral ? round($notaGeral, 1) : 0,
                'tem_dados' => $totalVoos > 0,
                'dados_suficientes' => true
            ];
        }
        
        // Filtrar APENAS aeronaves com dados suficientes (3+ registros) para os rankings de nota
        $aeronavesComDadosSuficientes = collect($rankings)->filter(function($item) {
            return $item['dados_suficientes'] === true && $item['nota_geral'] > 0;
        })->values();
        
        // Se não houver aeronaves com dados suficientes, mostrar mensagem apropriada
        if ($aeronavesComDadosSuficientes->isEmpty()) {
            $rankingsPorNota = collect();
            $rankingsObjetivo = collect();
            $rankingsPontualidade = collect();
            $rankingsServicos = collect();
            $rankingsPatio = collect();
            $rankingsPorVoos = collect($rankings)->sortByDesc('total_voos')->values();
            $rankingsPorPassageiros = collect($rankings)->sortByDesc('total_passageiros')->values();
            $rankingsPorCapacidade = collect($rankings)->sortByDesc('capacidade')->values();
            
            $estatisticas = [
                'total_aeronaves' => $aeronaves->count(),
                'total_fabricantes' => $aeronaves->pluck('fabricante.nome')->unique()->count(),
                'total_voos_geral' => collect($rankings)->sum('total_voos'),
                'total_passageiros_geral' => collect($rankings)->sum('total_passageiros'),
                'media_nota_geral' => 0,
                'aeronaves_com_dados' => collect($rankings)->where('tem_dados', true)->count(),
                'aeronaves_com_dados_suficientes' => 0,
                'porte_pequeno' => collect($rankings)->where('porte', 'Pequeno Porte (≤100)')->count(),
                'porte_medio' => collect($rankings)->where('porte', 'Médio Porte (101-299)')->count(),
                'porte_grande' => collect($rankings)->where('porte', 'Grande Porte (≥300)')->count(),
                'aviso_sem_dados' => true
            ];
        } else {
            // Ordenar rankings por nota geral (decrescente) COM DESEMPATE por total de voos
            $rankingsPorNota = $aeronavesComDadosSuficientes->sortByDesc(function($item) {
                return [$item['nota_geral'], $item['total_voos']];
            })->values();
            
            // Rankings por categoria individual COM DESEMPATE por total de voos
            $rankingsObjetivo = $aeronavesComDadosSuficientes->sortByDesc(function($item) {
                return [$item['media_objetivo'], $item['total_voos']];
            })->values();
            
            $rankingsPontualidade = $aeronavesComDadosSuficientes->sortByDesc(function($item) {
                return [$item['media_pontualidade'], $item['total_voos']];
            })->values();
            
            $rankingsServicos = $aeronavesComDadosSuficientes->sortByDesc(function($item) {
                return [$item['media_servicos'], $item['total_voos']];
            })->values();
            
            $rankingsPatio = $aeronavesComDadosSuficientes->sortByDesc(function($item) {
                return [$item['media_patio'], $item['total_voos']];
            })->values();
            
            // Rankings sem filtro de dados suficientes (mostra todas)
            $rankingsPorVoos = collect($rankings)->sortByDesc('total_voos')->values();
            $rankingsPorPassageiros = collect($rankings)->sortByDesc('total_passageiros')->values();
            $rankingsPorCapacidade = collect($rankings)->sortByDesc('capacidade')->values();
            
            // Estatísticas gerais
            $estatisticas = [
                'total_aeronaves' => $aeronaves->count(),
                'total_fabricantes' => $aeronaves->pluck('fabricante.nome')->unique()->count(),
                'total_voos_geral' => collect($rankings)->sum('total_voos'),
                'total_passageiros_geral' => collect($rankings)->sum('total_passageiros'),
                'media_nota_geral' => $aeronavesComDadosSuficientes->avg('nota_geral'),
                'aeronaves_com_dados' => collect($rankings)->where('tem_dados', true)->count(),
                'aeronaves_com_dados_suficientes' => $aeronavesComDadosSuficientes->count(),
                'porte_pequeno' => collect($rankings)->where('porte', 'Pequeno Porte (≤100)')->count(),
                'porte_medio' => collect($rankings)->where('porte', 'Médio Porte (101-299)')->count(),
                'porte_grande' => collect($rankings)->where('porte', 'Grande Porte (≥300)')->count(),
                'aviso_sem_dados' => false
            ];
        }
        
        // Melhor em cada categoria (APENAS com dados suficientes)
        $melhorNotaGeral = $rankingsPorNota->isNotEmpty() ? $rankingsPorNota->first() : null;
        $piorNotaGeral = $rankingsPorNota->isNotEmpty() ? $rankingsPorNota->last() : null;
        
        $maisVoos = $rankingsPorVoos->isNotEmpty() ? $rankingsPorVoos->first() : null;
        $menosVoos = $rankingsPorVoos->isNotEmpty() ? $rankingsPorVoos->last() : null;
        
        $maisPassageiros = $rankingsPorPassageiros->isNotEmpty() ? $rankingsPorPassageiros->first() : null;
        $menosPassageiros = $rankingsPorPassageiros->isNotEmpty() ? $rankingsPorPassageiros->last() : null;
        
        $maiorCapacidade = $rankingsPorCapacidade->isNotEmpty() ? $rankingsPorCapacidade->first() : null;
        $menorCapacidade = $rankingsPorCapacidade->isNotEmpty() ? $rankingsPorCapacidade->last() : null;
        
        return view('aeronaves.ranking', compact(
            'rankingsPorNota',
            'rankingsObjetivo',
            'rankingsPontualidade',
            'rankingsServicos',
            'rankingsPatio',
            'rankingsPorVoos',
            'rankingsPorPassageiros',
            'rankingsPorCapacidade',
            'estatisticas',
            'melhorNotaGeral',
            'piorNotaGeral',
            'maisVoos',
            'menosVoos',
            'maisPassageiros',
            'menosPassageiros',
            'maiorCapacidade',
            'menorCapacidade'
        ));
    }
}