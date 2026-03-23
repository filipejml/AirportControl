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
            'aeronaves' => 'nullable|array'
        ], [
            'nome.unique' => 'Esta companhia aérea já está cadastrada no sistema.'
        ]);

        // Verificar se existem IDs disponíveis
        $availableId = $this->getAvailableId();
        
        if ($availableId) {
            // Se houver ID disponível, criar com ID específico
            $companhia = new CompanhiaAerea([
                'nome' => $request->nome
            ]);
            $companhia->id = $availableId;
            $companhia->save();
        } else {
            // Se não houver IDs disponíveis, criar normalmente
            $companhia = CompanhiaAerea::create([
                'nome' => $request->nome
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
            'aeronaves' => 'nullable|array'
        ], [
            'nome.unique' => 'Esta companhia aérea já está cadastrada no sistema.'
        ]);

        $companhia->update([
            'nome' => $request->nome
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
    public function informacoes()
    {
        $companhias = CompanhiaAerea::with(['aeronaves', 'aeroportos', 'voos'])
            ->withCount('aeronaves', 'voos')
            ->get();
        
        // Calculate total passengers and ratings for each company
        foreach ($companhias as $companhia) {
            $companhia->total_passageiros = $companhia->voos()->sum('total_passageiros');
            
            // Calculate average ratings for each category
            $companhia->nota_obj = $companhia->voos()->avg('nota_obj') ?? 0;
            $companhia->nota_pontualidade = $companhia->voos()->avg('nota_pontualidade') ?? 0;
            $companhia->nota_servicos = $companhia->voos()->avg('nota_servicos') ?? 0;
            $companhia->nota_patio = $companhia->voos()->avg('nota_patio') ?? 0;
            
            // Calculate overall average rating (média das quatro notas)
            $medias = $companhia->voos()
                ->select(
                    DB::raw('AVG(nota_obj) as obj'),
                    DB::raw('AVG(nota_pontualidade) as pontualidade'),
                    DB::raw('AVG(nota_servicos) as servicos'),
                    DB::raw('AVG(nota_patio) as patio')
                )
                ->first();
            
            $companhia->media_notas = ($medias->obj + $medias->pontualidade + $medias->servicos + $medias->patio) / 4;
            
            // Calculate aeroportos count with voos count for each
            $companhia->aeroportos_com_voos = $companhia->aeroportos->map(function($aeroporto) use ($companhia) {
                return [
                    'id' => $aeroporto->id,
                    'nome' => $aeroporto->nome_aeroporto,
                    'voos_count' => $aeroporto->voos()->where('companhia_aerea_id', $companhia->id)->count()
                ];
            });
        }
        
        $aeroportos = Aeroporto::with('companhias')->get();
        
        // Prepare companies data for JavaScript
        $companiesData = $companhias->map(function($c) {
            return [
                'id' => $c->id,
                'nome' => $c->nome,
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
        
        // Totals
        $totalCompanhias = $companhias->count();
        $totalVoos = $companhias->sum('voos_count');
        $totalPassageiros = $companhias->sum('total_passageiros');
        
        // Global average ratings
        $mediaGeralNotas = 0;
        $totalNotas = 0;
        
        foreach ($companhias as $companhia) {
            if ($companhia->media_notas) {
                $mediaGeralNotas += $companhia->media_notas;
                $totalNotas++;
            }
        }
        
        $mediaGeralNotas = $totalNotas > 0 ? $mediaGeralNotas / $totalNotas : 0;
        
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