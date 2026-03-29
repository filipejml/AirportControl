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
use DateTime;

class CompanhiaAereaController extends Controller
{
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
                'nullable',
                'string',
                'max:10',
                Rule::unique('companhias_aereas', 'codigo')
            ],
            'aeronaves' => 'nullable|array'
        ], [
            'nome.unique' => 'Esta companhia aérea já está cadastrada no sistema.',
            'codigo.unique' => 'Este código já está sendo utilizado por outra companhia aérea.'
        ]);

        // Verificar se existem IDs disponíveis
        $availableId = $this->getAvailableId();
        
        if ($availableId) {
            // Se houver ID disponível, criar com ID específico
            $companhia = new CompanhiaAerea([
                'nome' => $request->nome,
                'codigo' => $request->codigo
            ]);
            $companhia->id = $availableId;
            $companhia->save();
        } else {
            // Se não houver IDs disponíveis, criar normalmente
            $companhia = CompanhiaAerea::create([
                'nome' => $request->nome,
                'codigo' => $request->codigo
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
        $companhia->load('aeronaves.fabricante');
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
                'nullable',
                'string',
                'max:10',
                Rule::unique('companhias_aereas', 'codigo')->ignore($companhia->id)
            ],
            'aeronaves' => 'nullable|array'
        ], [
            'nome.unique' => 'Esta companhia aérea já está cadastrada no sistema.',
            'codigo.unique' => 'Este código já está sendo utilizado por outra companhia aérea.'
        ]);

        $companhia->update([
            'nome' => $request->nome,
            'codigo' => $request->codigo
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
            ->withCount('aeronaves', 'voos');
        
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
                $query->orderBy('voos_count', 'desc');
                break;
            case 'mais_passageiros':
                $query->withSum('voos', 'total_passageiros')
                    ->orderBy('voos_sum_total_passageiros', 'desc');
                break;
            case 'melhor_objetivo':
                $query->withAvg('voos', 'nota_obj')
                    ->orderBy('voos_avg_nota_obj', 'desc');
                break;
            case 'melhor_pontualidade':
                $query->withAvg('voos', 'nota_pontualidade')
                    ->orderBy('voos_avg_nota_pontualidade', 'desc');
                break;
            case 'melhor_servicos':
                $query->withAvg('voos', 'nota_servicos')
                    ->orderBy('voos_avg_nota_servicos', 'desc');
                break;
            case 'melhor_patio':
                $query->withAvg('voos', 'nota_patio')
                    ->orderBy('voos_avg_nota_patio', 'desc');
                break;
            default:
                $query->orderBy('nome', 'asc');
                break;
        }
        
        $companhias = $query->get();
        
        // Calcular estatísticas para cada companhia
        foreach ($companhias as $companhia) {
            // Total de passageiros
            $companhia->total_passageiros = $companhia->voos()->sum('total_passageiros');
            
            // Médias das notas por categoria
            $companhia->nota_obj = $companhia->voos()->avg('nota_obj') ?? 0;
            $companhia->nota_pontualidade = $companhia->voos()->avg('nota_pontualidade') ?? 0;
            $companhia->nota_servicos = $companhia->voos()->avg('nota_servicos') ?? 0;
            $companhia->nota_patio = $companhia->voos()->avg('nota_patio') ?? 0;
            
            // Média geral (média das quatro notas)
            $medias = $companhia->voos()
                ->select(
                    DB::raw('COALESCE(AVG(nota_obj), 0) as obj'),
                    DB::raw('COALESCE(AVG(nota_pontualidade), 0) as pontualidade'),
                    DB::raw('COALESCE(AVG(nota_servicos), 0) as servicos'),
                    DB::raw('COALESCE(AVG(nota_patio), 0) as patio')
                )
                ->first();
            
            $companhia->media_notas = ($medias->obj + $medias->pontualidade + $medias->servicos + $medias->patio) / 4;
            
            // Aeroportos operados com contagem de voos
            $companhia->aeroportos_com_voos = $companhia->aeroportos->map(function($aeroporto) use ($companhia) {
                return [
                    'id' => $aeroporto->id,
                    'nome' => $aeroporto->nome_aeroporto,
                    'voos_count' => $aeroporto->voos()->where('companhia_aerea_id', $companhia->id)->count()
                ];
            });
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
        $mediaGeralNotas = $companhias->avg('media_notas') ?? 0;
        
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
        $totalVoos = $voosFiltrados->count();
        $totalPassageiros = $voosFiltrados->sum('total_passageiros');
        
        // Médias das notas com filtros
        $notaObj = $voosFiltrados->avg('nota_obj') ?? 0;
        $notaPontualidade = $voosFiltrados->avg('nota_pontualidade') ?? 0;
        $notaServicos = $voosFiltrados->avg('nota_servicos') ?? 0;
        $notaPatio = $voosFiltrados->avg('nota_patio') ?? 0;
        $mediaGeral = ($notaObj + $notaPontualidade + $notaServicos + $notaPatio) / 4;
        
        // Últimos voos com filtros (últimos 5)
        $ultimosVoos = $queryVoos->clone()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // TOTAL DE AERONAVES
        $totalAeronaves = $companhia->aeronaves()->count();
        
        // TOTAL DE AEROPORTOS
        $totalAeroportos = $companhia->aeroportos()->count();
        
        // Voos por aeroporto com filtros
        $voosPorAeroporto = collect();
        foreach ($companhia->aeroportos as $aeroporto) {
            $quantidadeVoos = $queryVoos->clone()
                ->where('aeroporto_id', $aeroporto->id)
                ->count();
            
            if ($quantidadeVoos > 0) {
                $voosPorAeroporto->put($aeroporto->nome_aeroporto, $quantidadeVoos);
            }
        }
        $voosPorAeroporto = $voosPorAeroporto->sortDesc();
        
        // Passageiros por aeroporto com filtros
        $passageirosPorAeroporto = collect();
        foreach ($companhia->aeroportos as $aeroporto) {
            $quantidadePassageiros = $queryVoos->clone()
                ->where('aeroporto_id', $aeroporto->id)
                ->sum('total_passageiros');
            
            if ($quantidadePassageiros > 0) {
                $passageirosPorAeroporto->put($aeroporto->nome_aeroporto, $quantidadePassageiros);
            }
        }
        $passageirosPorAeroporto = $passageirosPorAeroporto->sortDesc();
        
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
                $voosPorHorario[$voo->horario_voo]++;
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
        $totalVoos = $voos->count();
        $totalPassageiros = $voos->sum('total_passageiros');
        $totalAeronaves = $companhia->aeronaves()->count();
        $totalAeroportos = $companhia->aeroportos()->count();
        
        // Notas médias
        $notaObj = $voos->avg('nota_obj') ?? 0;
        $notaPontualidade = $voos->avg('nota_pontualidade') ?? 0;
        $notaServicos = $voos->avg('nota_servicos') ?? 0;
        $notaPatio = $voos->avg('nota_patio') ?? 0;
        $mediaGeral = ($notaObj + $notaPontualidade + $notaServicos + $notaPatio) / 4;
        
        // Data de geração do relatório
        $dataGeracao = Carbon::now()->format('d/m/Y H:i:s');
        
        // Dados para o gráfico de voos por aeroporto (top 5)
        $voosPorAeroporto = $voos->groupBy('aeroporto.nome_aeroporto')
            ->map(function($grupo) {
                return $grupo->count();
            })
            ->sortDesc()
            ->take(5);
        
        // Dados para o gráfico de voos por horário
        $horarios = ['EAM', 'AM', 'AN', 'PM', 'ALL'];
        $voosPorHorario = [];
        foreach ($horarios as $horario) {
            $voosPorHorario[$horario] = $voos->where('horario_voo', $horario)->count();
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
}