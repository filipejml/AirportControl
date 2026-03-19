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
}