<?php

namespace App\Http\Controllers;

use App\Models\Aeroporto;
use App\Models\CompanhiaAerea;
use Illuminate\Http\Request;

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
        // Carrega as companhias com a contagem de aeronaves
        $aeroporto->load(['companhias' => function($query) {
            $query->withCount('aeronaves');
        }]);
        
        // Calcula estatísticas
        $totalCompanhias = $aeroporto->companhias->count();
        $totalAeronaves = $aeroporto->companhias->sum(function($companhia) {
            return $companhia->aeronaves_count ?? 0;
        });
        
        $mediaAeronaves = $totalCompanhias > 0 
            ? round($totalAeronaves / $totalCompanhias, 1)
            : 0;
        
        return view('admin.aeroportos.show', compact(
            'aeroporto', 
            'totalCompanhias',
            'totalAeronaves', 
            'mediaAeronaves'
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
    public function informacoes()
    {
        // Carrega os aeroportos com relacionamentos
        $aeroportos = Aeroporto::with(['companhias', 'voos.companhiaAerea'])
            ->withCount('companhias')
            ->get();
        
        // Calcula totais para cada aeroporto
        foreach ($aeroportos as $aeroporto) {
            $aeroporto->total_voos = $aeroporto->voos()->count();
            $aeroporto->total_passageiros = $aeroporto->voos()->sum('total_passageiros');
            $aeroporto->media_passageiros_por_voo = $aeroporto->total_voos > 0 
                ? $aeroporto->total_passageiros / $aeroporto->total_voos 
                : 0;
        }
        
        // Prepara dados para o JavaScript
        $aeroportosData = $aeroportos->map(function($a) {
            // Calcular melhores companhias por categoria para este aeroporto
            $melhoresCompanhias = [];
            $categorias = [
                'Objetivo' => 'nota_obj',
                'Pontualidade' => 'nota_pontualidade',
                'Servicos' => 'nota_servicos',
                'Patio' => 'nota_patio'
            ];
            
            foreach ($categorias as $categoria => $campoNota) {
                $melhorCompanhia = $a->voos()
                    ->with('companhiaAerea')
                    ->select('companhia_aerea_id')
                    ->selectRaw('AVG(' . $campoNota . ') as media_nota')
                    ->groupBy('companhia_aerea_id')
                    ->orderByRaw('AVG(' . $campoNota . ') DESC')
                    ->first();
                
                if ($melhorCompanhia && $melhorCompanhia->companhiaAerea) {
                    $melhoresCompanhias[$categoria] = [
                        'id' => $melhorCompanhia->companhia_aerea_id,
                        'nome' => $melhorCompanhia->companhiaAerea->nome,
                        'media' => $melhorCompanhia->media_nota ?? 0
                    ];
                } else {
                    $melhoresCompanhias[$categoria] = null;
                }
            }
            
            return [
                'id' => $a->id,
                'nome' => $a->nome_aeroporto,
                'companhias_count' => $a->companhias_count ?? 0,
                'total_voos' => $a->total_voos ?? 0,
                'total_passageiros' => $a->total_passageiros ?? 0,
                'media_passageiros_por_voo' => $a->media_passageiros_por_voo ?? 0,
                'media_notas' => $a->voos->avg('media_notas') ?? 0,
                'nota_obj' => $a->voos->avg('nota_obj') ?? 0,
                'nota_pontualidade' => $a->voos->avg('nota_pontualidade') ?? 0,
                'nota_servicos' => $a->voos->avg('nota_servicos') ?? 0,
                'nota_patio' => $a->voos->avg('nota_patio') ?? 0,
                'melhores_companhias' => $melhoresCompanhias,
                'companhias' => $a->companhias->map(function($c) use ($a) {
                    return [
                        'id' => $c->id,
                        'nome' => $c->nome,
                        'voos_count' => $a->voos()->where('companhia_aerea_id', $c->id)->count()
                    ];
                })
            ];
        });
        
        $companhias = CompanhiaAerea::orderBy('nome')->get();
        
        $totalAeroportos = $aeroportos->count();
        $totalVoos = $aeroportos->sum('total_voos');
        $totalPassageiros = $aeroportos->sum('total_passageiros');
        $mediaPassageirosPorVoo = $totalVoos > 0 ? $totalPassageiros / $totalVoos : 0;
        
        return view('aeroportos.informacoes', compact(
            'aeroportosData',
            'companhias',
            'totalAeroportos',
            'totalVoos',
            'totalPassageiros',
            'mediaPassageirosPorVoo'
        ));
    }
}