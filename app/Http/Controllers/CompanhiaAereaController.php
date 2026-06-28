<?php

namespace App\Http\Controllers;

use App\Models\Aeronave;
use App\Models\CompanhiaAerea;
use App\Models\Aeroporto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use App\Services\PeriodoFiltroService;
use App\Services\VooMetricasService;
use App\Services\CompanhiaRankingService;

class CompanhiaAereaController extends Controller
{
    public function ranking(Request $request, CompanhiaRankingService $rankingService)
    {
        $filters = PeriodoFiltroService::filtrosDetalhadosFromRequest($request);
        $rankings = $rankingService->generateRankings($filters);

        return view('companhias.ranking', [
            'rankingsPorNota' => $rankings['rankings_por_nota'],
            'rankingsObjetivo' => $rankings['rankings_objetivo'],
            'rankingsPontualidade' => $rankings['rankings_pontualidade'],
            'rankingsServicos' => $rankings['rankings_servicos'],
            'rankingsPatio' => $rankings['rankings_patio'],
            'rankingsPorVoos' => $rankings['rankings_por_voos'],
            'rankingsPorPassageiros' => $rankings['rankings_por_passageiros'],
            'estatisticas' => $rankings['estatisticas'],
            'periodoSelecionado' => $filters['periodo'],
            'semanaSelecionada' => $filters['semana'],
            'anoFiltro' => $filters['ano'],
            'mesSelecionado' => $filters['mes'],
            'anoSelecionado' => $filters['ano_selecionado'],
            'semanasDisponiveis' => PeriodoFiltroService::semanasDisponiveis(),
            'anosDisponiveis' => CompanhiaAerea::query()
                ->join('voos', 'companhias_aereas.id', '=', 'voos.companhia_aerea_id')
                ->whereNotNull('voos.created_at')
                ->pluck('voos.created_at')
                ->map(fn ($data) => Carbon::parse($data)->year)
                ->unique()
                ->sortDesc()
                ->values()
                ->all(),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companhias = CompanhiaAerea::withCount('aeronaves')->get();
        return view('admin.companhias.index', compact('companhias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $aeronaves = Aeronave::with('fabricante')->get();
        return view('admin.companhias.create', compact('aeronaves'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('companhias_aereas', 'nome')->where(function ($query) {
                    return $query->whereNotNull('id');
                })
            ],
            'codigo' => [
                'required', // TORNAR OBRIGATÓRIO
                'string',
                'max:10',
                Rule::unique('companhias_aereas', 'codigo')
            ],
            'aeronaves' => 'nullable|array'
        ], [
            'nome.unique' => 'Esta companhia aérea já está cadastrada no sistema.',
            'codigo.required' => 'O código da companhia é obrigatório.',
            'codigo.unique' => 'Este código já está sendo utilizado por outra companhia aérea.'
        ]);

        // Verificar se existem IDs disponíveis
        $availableId = $this->getAvailableId();
        
        if ($availableId) {
            // Se houver ID disponível, criar com ID específico
            $companhia = new CompanhiaAerea([
                'nome' => $request->nome,
                'codigo' => strtoupper($request->codigo) // Converter para maiúsculo
            ]);
            $companhia->id = $availableId;
            $companhia->save();
        } else {
            // Se não houver IDs disponíveis, criar normalmente
            $companhia = CompanhiaAerea::create([
                'nome' => $request->nome,
                'codigo' => strtoupper($request->codigo) // Converter para maiúsculo
            ]);
        }

        if ($request->has('aeronaves')) {
            $companhia->aeronaves()->sync($request->aeronaves);
        }

        return redirect()->route('companhias.index')
            ->with('success', 'Companhia aérea cadastrada com sucesso! (ID: ' . $companhia->id . ')');
    }

    /**
     * Display the specified resource.
     */
    public function show(CompanhiaAerea $companhia)
    {
        // Carregar aeronaves com o pivot incluindo o campo disponivel
        $companhia->load(['aeronaves' => function($query) {
            $query->withPivot('disponivel');
        }, 'aeronaves.fabricante']);
        
        return view('admin.companhias.show', compact('companhia'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CompanhiaAerea $companhia)
    {
        $aeronaves = Aeronave::with('fabricante')->get();
        $companhia->load('aeronaves');
        return view('admin.companhias.edit', compact('companhia', 'aeronaves'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CompanhiaAerea $companhia)
    {
        $request->validate([
            'nome' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('companhias_aereas', 'nome')->ignore($companhia->id)
            ],
            'codigo' => [
                'required', // TORNAR OBRIGATÓRIO
                'string',
                'max:10',
                Rule::unique('companhias_aereas', 'codigo')->ignore($companhia->id)
            ],
            'aeronaves' => 'nullable|array'
        ], [
            'nome.unique' => 'Esta companhia aérea já está cadastrada no sistema.',
            'codigo.required' => 'O código da companhia é obrigatório.',
            'codigo.unique' => 'Este código já está sendo utilizado por outra companhia aérea.'
        ]);

        $companhia->update([
            'nome' => $request->nome,
            'codigo' => strtoupper($request->codigo) // Converter para maiúsculo
        ]);

        if ($request->has('aeronaves')) {
            $companhia->aeronaves()->sync($request->aeronaves);
        } else {
            $companhia->aeronaves()->detach();
        }

        return redirect()->route('companhias.index')
            ->with('success', 'Companhia aérea atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CompanhiaAerea $companhia)
    {
        try {
            // Armazenar o ID antes de deletar
            $deletedId = $companhia->id;
            
            $companhia->aeronaves()->detach();
            $companhia->delete();
            
            return redirect()->route('companhias.index')
                ->with('success', 'Companhia aérea excluída com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('companhias.index')
                ->with('error', 'Erro ao excluir companhia aérea: ' . $e->getMessage());
        }
    }

    /**
     * Check if company name already exists (AJAX endpoint)
     */
    public function checkName(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'id' => 'nullable|integer' // Para edição, ignorar o próprio registro
        ]);

        $query = CompanhiaAerea::where('nome', $request->nome);
        
        // Se for edição, ignorar o registro atual
        if ($request->has('id') && $request->id) {
            $query->where('id', '!=', $request->id);
        }
        
        $exists = $query->exists();
        
        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Esta companhia aérea já está cadastrada' : 'Nome disponível'
        ]);
    }

    // Verificar se o código da companhia aérea já existe (AJAX endpoint)
    public function checkCode(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:10',
            'id' => 'nullable|integer' // Para edição, ignorar o próprio registro
        ]);

        $query = CompanhiaAerea::where('codigo', strtoupper($request->codigo));
        
        // Se for edição, ignorar o registro atual
        if ($request->has('id') && $request->id) {
            $query->where('id', '!=', $request->id);
        }
        
        $exists = $query->exists();
        
        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Este código já está sendo utilizado por outra companhia aérea' : 'Código disponível'
        ]);
    }

    /**
     * Get the smallest available ID that is not in use
     */
    private function getAvailableId()
    {
        // Pegar todos os IDs existentes
        $existingIds = CompanhiaAerea::pluck('id')->toArray();
        
        if (empty($existingIds)) {
            return 1;
        }
        
        // Procurar o menor ID disponível
        $maxId = max($existingIds);
        
        for ($i = 1; $i <= $maxId; $i++) {
            if (!in_array($i, $existingIds)) {
                return $i;
            }
        }
        
        // Se todos os IDs até o máximo estão ocupados, retorna o próximo
        return $maxId + 1;
    }

    /**
     * Display general information about airlines
     */
    public function informacoes(Request $request)
    {
        $query = CompanhiaAerea::with(['aeronaves', 'aeroportos', 'voos'])
            ->withCount('aeronaves')
            ->withSum('voos', 'qtd_voos')
            ->withSum('voos', 'total_passageiros');
        
        // Aplicar filtro por companhia (selecionar uma companhia específica)
        if ($request->filled('companhia')) {
            $query->where('id', $request->companhia);
        }
        
        // Aplicar filtro por aeroporto
        if ($request->filled('aeroporto')) {
            $query->whereHas('aeroportos', function ($q) use ($request) {
                $q->where('aeroportos.id', $request->aeroporto);
            });
        }
        
        // Aplicar ordenação
        switch ($request->get('ordenacao')) {
            case 'nome_az':
                $query->orderBy('nome', 'asc');
                break;
            case 'nome_za':
                $query->orderBy('nome', 'desc');
                break;
            case 'mais_voos':
                $query->orderBy('voos_sum_qtd_voos', 'desc');
                break;
            case 'mais_passageiros':
                $query->orderBy('voos_sum_total_passageiros', 'desc');
                break;
            case 'melhor_objetivo':
                break;
            case 'melhor_pontualidade':
                break;
            case 'melhor_servicos':
                break;
            case 'melhor_patio':
                break;
            default:
                $query->orderBy('nome', 'asc');
                break;
        }
        
        $companhias = $query->get();
        
        // Calcular estatísticas para cada companhia
        foreach ($companhias as $companhia) {
            $companhia->voos_count = (int) ($companhia->voos_sum_qtd_voos ?? 0);
            $companhia->total_passageiros = (int) ($companhia->voos_sum_total_passageiros ?? 0);

            // Médias das notas por categoria
            $companhia->nota_obj = VooMetricasService::mediaPonderada($companhia->voos, 'nota_obj');
            $companhia->nota_pontualidade = VooMetricasService::mediaPonderada($companhia->voos, 'nota_pontualidade');
            $companhia->nota_servicos = VooMetricasService::mediaPonderada($companhia->voos, 'nota_servicos');
            $companhia->nota_patio = VooMetricasService::mediaPonderada($companhia->voos, 'nota_patio');
            $companhia->media_notas = VooMetricasService::mediaGeral($companhia->voos);
            
            // Aeroportos operados com contagem de voos
            $voosPorAeroporto = $companhia->voos->groupBy('aeroporto_id');
            $companhia->aeroportos_com_voos = $companhia->aeroportos->map(function($aeroporto) use ($voosPorAeroporto) {
                return [
                    'id' => $aeroporto->id,
                    'nome' => $aeroporto->nome_aeroporto,
                    'voos_count' => (int) ($voosPorAeroporto->get($aeroporto->id)?->sum('qtd_voos') ?? 0)
                ];
            });
        }

        $campoOrdenacao = match ($request->get('ordenacao')) {
            'melhor_objetivo' => 'nota_obj',
            'melhor_pontualidade' => 'nota_pontualidade',
            'melhor_servicos' => 'nota_servicos',
            'melhor_patio' => 'nota_patio',
            default => null,
        };

        if ($campoOrdenacao) {
            $companhias = $companhias->sortByDesc($campoOrdenacao)->values();
        }
        
        // Buscar todos os aeroportos para os filtros
        $aeroportos = Aeroporto::orderBy('nome_aeroporto')->get();
        
        // Preparar dados das companhias para JavaScript (se necessário)
        $companiesData = $companhias->map(function($c) {
            return [
                'id' => $c->id,
                'nome' => $c->nome,
                'codigo' => $c->codigo,
                'aeronaves_count' => $c->aeronaves_count,
                'voos_count' => $c->voos_count,
                'total_passageiros' => $c->total_passageiros,
                'media_notas' => $c->media_notas,
                'nota_obj' => $c->nota_obj,
                'nota_pontualidade' => $c->nota_pontualidade,
                'nota_servicos' => $c->nota_servicos,
                'nota_patio' => $c->nota_patio,
                'aeroportos' => $c->aeroportos_com_voos
            ];
        });
        
        // Totais (considerando os filtros aplicados)
        $totalCompanhias = $companhias->count();
        $totalVoos = $companhias->sum('voos_count');
        $totalPassageiros = $companhias->sum('total_passageiros');
        
        // Média geral de notas (considerando os filtros aplicados)
        $mediaGeralNotas = VooMetricasService::mediaGeral(
            $companhias->flatMap->voos
        );
        
        return view('companhias.informacoes', compact(
            'companhias',
            'aeroportos',
            'companiesData',
            'totalCompanhias',
            'totalVoos',
            'totalPassageiros',
            'mediaGeralNotas'
        ));
    }

    /**
     * Display a public dashboard for the airline
     */
    public function dashboard(Request $request, CompanhiaAerea $companhia)
    {
        // Carregar relacionamentos necessários
        $companhia->load(['aeronaves', 'aeroportos', 'voos']);
        
        // Filtros
        $aeroportoSelecionado = $request->get('aeroporto', 'geral');
        $periodoSelecionado = $request->get('periodo', 'geral');
        $semanaSelecionada = $request->get('semana');
        $anoFiltro = $request->get('ano');
        $mesSelecionado = $request->get('mes');
        $anoSelecionado = $request->get('ano_selecionado');
        
        // Query base para voos com filtros
        $queryVoos = $companhia->voos()->with('aeroporto');
        
        // Aplicar filtro de aeroporto
        if ($aeroportoSelecionado !== 'geral') {
            $queryVoos->whereHas('aeroporto', function($q) use ($aeroportoSelecionado) {
                $q->where('nome_aeroporto', $aeroportoSelecionado);
            });
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
        $totalVoos = $voosFiltrados->sum('qtd_voos');
        $totalPassageiros = $voosFiltrados->sum('total_passageiros');
        
        // Médias das notas com filtros
        $notaObj = VooMetricasService::mediaPonderada($voosFiltrados, 'nota_obj');
        $notaPontualidade = VooMetricasService::mediaPonderada($voosFiltrados, 'nota_pontualidade');
        $notaServicos = VooMetricasService::mediaPonderada($voosFiltrados, 'nota_servicos');
        $notaPatio = VooMetricasService::mediaPonderada($voosFiltrados, 'nota_patio');
        $mediaGeral = VooMetricasService::mediaGeral($voosFiltrados);
        
        // Últimos voos com filtros (últimos 5)
        $ultimosVoos = $voosFiltrados->sortByDesc('created_at')->take(5)->values();
        
        // TOTAL DE AERONAVES
        $totalAeronaves = $companhia->aeronaves->count();
        
        // TOTAL DE AEROPORTOS
        $totalAeroportos = $companhia->aeroportos->count();
        
        // Voos por aeroporto com filtros
        $voosPorAeroporto = $voosFiltrados
            ->groupBy('aeroporto.nome_aeroporto')
            ->map(fn ($items) => $items->sum('qtd_voos'))
            ->filter(fn ($quantidade) => $quantidade > 0)
            ->sortDesc();
        
        // Passageiros por aeroporto com filtros
        $passageirosPorAeroporto = $voosFiltrados
            ->groupBy('aeroporto.nome_aeroporto')
            ->map(fn ($items) => $items->sum('total_passageiros'))
            ->filter(fn ($quantidade) => $quantidade > 0)
            ->sortDesc();
        
        // Voos por Horário
        $voosPorHorario = [
            'EAM' => 0, // Early Morning (00h-06h)
            'AM' => 0,  // Morning (06h-12h)
            'AN' => 0,  // Afternoon (12h-18h)
            'PM' => 0,  // Evening (18h-00h)
            'ALL' => 0  // Diário
        ];
        
        foreach ($voosFiltrados as $voo) {
            if (isset($voosPorHorario[$voo->horario_voo])) {
                $voosPorHorario[$voo->horario_voo] += (int) $voo->qtd_voos;
            }
        }
        
        // Passageiros por Horário
        $passageirosPorHorario = [
            'EAM' => 0,
            'AM' => 0,
            'AN' => 0,
            'PM' => 0,
            'ALL' => 0
        ];
        
        foreach ($voosFiltrados as $voo) {
            if (isset($passageirosPorHorario[$voo->horario_voo])) {
                $passageirosPorHorario[$voo->horario_voo] += $voo->total_passageiros;
            }
        }
        
        // Aeronaves da frota
        $aeronaves = $companhia->aeronaves()
            ->with('fabricante')
            ->get();
        
        // Dados para os filtros
        $aeroportosDisponiveis = $companhia->aeroportos->pluck('nome_aeroporto')->unique()->values()->toArray();
        
        // Semanas disponíveis para filtro (últimos 52 semanas)
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
        $anosDisponiveis = $companhia->voos()
            ->select(DB::raw('DISTINCT YEAR(created_at) as ano'))
            ->orderBy('ano', 'desc')
            ->pluck('ano')
            ->toArray();
        
        return view('companhias.dashboard', compact(
            'companhia',
            'totalVoos',
            'totalPassageiros',
            'totalAeronaves',
            'totalAeroportos',
            'notaObj',
            'notaPontualidade',
            'notaServicos',
            'notaPatio',
            'mediaGeral',
            'ultimosVoos',
            'aeronaves',
            'aeroportosDisponiveis',
            'semanasDisponiveis',
            'anosDisponiveis',
            'aeroportoSelecionado',
            'periodoSelecionado',
            'semanaSelecionada',
            'anoFiltro',
            'mesSelecionado',
            'anoSelecionado',
            'voosPorAeroporto',
            'passageirosPorAeroporto',
            'voosPorHorario',
            'passageirosPorHorario'
        ));
    }

    /**
     * Export all flights of the airline to PDF
     */
    public function exportVoosPdf(CompanhiaAerea $companhia)
    {
        // Carregar todos os voos da companhia com seus relacionamentos
        $voos = $companhia->voos()
            ->with(['aeroporto', 'aeronave.fabricante'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Estatísticas para o relatório
        $totalVoos = $voos->sum('qtd_voos');
        $totalPassageiros = $voos->sum('total_passageiros');
        $totalAeronaves = $companhia->aeronaves()->count();
        $totalAeroportos = $companhia->aeroportos()->count();
        
        // Notas médias
        $notaObj = VooMetricasService::mediaPonderada($voos, 'nota_obj');
        $notaPontualidade = VooMetricasService::mediaPonderada($voos, 'nota_pontualidade');
        $notaServicos = VooMetricasService::mediaPonderada($voos, 'nota_servicos');
        $notaPatio = VooMetricasService::mediaPonderada($voos, 'nota_patio');
        $mediaGeral = VooMetricasService::mediaGeral($voos);
        
        // Data de geração do relatório
        $dataGeracao = Carbon::now()->format('d/m/Y H:i:s');
        
        // Dados para o gráfico de voos por aeroporto (top 5)
        $voosPorAeroporto = $voos->groupBy('aeroporto.nome_aeroporto')
            ->map(function($grupo) {
                return $grupo->sum('qtd_voos');
            })
            ->sortDesc()
            ->take(5);
        
        // Dados para o gráfico de voos por horário
        $horarios = ['EAM', 'AM', 'AN', 'PM', 'ALL'];
        $voosPorHorario = [];
        foreach ($horarios as $horario) {
            $voosPorHorario[$horario] = $voos->where('horario_voo', $horario)->sum('qtd_voos');
        }
        
        // Gerar o PDF
        $pdf = Pdf::loadView('pdf.voos_companhia', compact(
            'companhia',
            'voos',
            'totalVoos',
            'totalPassageiros',
            'totalAeronaves',
            'totalAeroportos',
            'notaObj',
            'notaPontualidade',
            'notaServicos',
            'notaPatio',
            'mediaGeral',
            'dataGeracao',
            'voosPorAeroporto',
            'voosPorHorario'
        ));
        
        // Configurar o PDF
        $pdf->setPaper('A4', 'landscape');
        
        // Nome do arquivo
        $nomeArquivo = 'voos_' . preg_replace('/[^a-z0-9]/i', '_', $companhia->nome) . '.pdf';
        
        return $pdf->download($nomeArquivo);
    }

    /**
     * Update the availability of an aircraft for the airline
     */
    public function atualizarDisponibilidade(Request $request, CompanhiaAerea $companhia, Aeronave $aeronave)
    {
        try {
            $disponivel = $request->input('disponivel', false);
            
            $companhia->atualizarDisponibilidadeAeronave($aeronave->id, $disponivel);
            
            return response()->json([
                'success' => true,
                'message' => $disponivel ? 'Aeronave disponível para voos!' : 'Aeronave indisponível para voos.',
                'disponivel' => $disponivel
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar disponibilidade: ' . $e->getMessage()
            ], 500);
        }
    }   
}
