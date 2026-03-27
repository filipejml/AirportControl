<?php
// app/Http/Controllers/AeronaveController.php

namespace App\Http\Controllers;

use App\Models\Aeronave;
use App\Models\Fabricante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            
            // Calcular médias das notas dos voos
            $totalVoos = $aeronave->voos->count();
            $totalPassageiros = $aeronave->voos->sum('qtd_passageiros');
            $mediaObjetivo = $aeronave->voos->avg('nota_obj');
            $mediaPontualidade = $aeronave->voos->avg('nota_pontualidade');
            $mediaServicos = $aeronave->voos->avg('nota_servicos');
            $mediaPatio = $aeronave->voos->avg('nota_patio');
            
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
                'tem_dados' => $totalVoos > 0 // Campo corrigido para 'tem_dados'
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
}