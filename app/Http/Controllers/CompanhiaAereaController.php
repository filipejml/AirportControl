<?php

namespace App\Http\Controllers;

use App\Models\Aeronave;
use App\Models\CompanhiaAerea;
use App\Models\Aeroporto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
}