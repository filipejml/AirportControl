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
        $aeroportos = Aeroporto::with(['companhias', 'voos.companhia'])
            ->withCount('companhias')
            ->get();
        
        // Calculate totals for each airport
        foreach ($aeroportos as $aeroporto) {
            $aeroporto->total_voos = $aeroporto->voos()->count();
            $aeroporto->total_passageiros = $aeroporto->voos()->sum('total_passageiros');
            $aeroporto->media_passageiros_por_voo = $aeroporto->total_voos > 0 
                ? $aeroporto->total_passageiros / $aeroporto->total_voos 
                : 0;
        }
        
        $companhias = CompanhiaAerea::orderBy('nome')->get();
        
        // Totals
        $totalAeroportos = $aeroportos->count();
        $totalVoos = $aeroportos->sum('total_voos');
        $totalPassageiros = $aeroportos->sum('total_passageiros');
        $mediaPassageirosPorVoo = $totalVoos > 0 ? $totalPassageiros / $totalVoos : 0;
        
        // Statistics by time of day
        $horarioStats = [
            'EAM' => 0, // 00-06
            'AM' => 0,  // 06-12
            'AN' => 0,  // 12-18
            'PM' => 0   // 18-00
        ];
        
        foreach ($aeroportos as $aeroporto) {
            $stats = $aeroporto->voos()
                ->selectRaw('horario_voo, COUNT(*) as total')
                ->groupBy('horario_voo')
                ->pluck('total', 'horario_voo')
                ->toArray();
            
            foreach ($horarioStats as $key => $value) {
                $horarioStats[$key] += $stats[$key] ?? 0;
            }
        }
        
        return view('aeroportos.informacoes', compact(
            'aeroportos',
            'companhias',
            'totalAeroportos',
            'totalVoos',
            'totalPassageiros',
            'mediaPassageirosPorVoo',
            'horarioStats'
        ));
    }

}