<?php
// app/Http/Controllers/AeronaveController.php

namespace App\Http\Controllers;

use App\Models\Aeronave;
use App\Models\Fabricante;
use Illuminate\Http\Request;

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
}