<?php
// app/Http/Controllers/FabricanteController.php

namespace App\Http\Controllers;

use App\Models\Fabricante;
use Illuminate\Http\Request;

class FabricanteController extends Controller
{
    public function index()
    {
        $fabricantes = Fabricante::withCount('aeronaves')->orderBy('nome')->get();
        return view('admin.fabricantes.index', compact('fabricantes'));
    }

    public function create()
    {
        return view('admin.fabricantes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:fabricantes',
            'pais_origem' => 'nullable|string|max:255'
        ]);

        $fabricante = Fabricante::create($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'fabricante' => $fabricante
            ]);
        }

        return redirect()->route('fabricantes.index')
            ->with('success', 'Fabricante cadastrado com sucesso!');
    }

    public function show(Fabricante $fabricante)
    {
        // Carregar as aeronaves e suas companhias
        $fabricante->load(['aeronaves.companhias']);
        $fabricante->loadCount('aeronaves');
        
        return view('admin.fabricantes.show', compact('fabricante'));
    }

    public function edit(Fabricante $fabricante)
    {
        return view('admin.fabricantes.edit', compact('fabricante'));
    }

    public function update(Request $request, Fabricante $fabricante)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:fabricantes,nome,' . $fabricante->id,
            'pais_origem' => 'nullable|string|max:255'
        ]);

        $fabricante->update($request->all());

        return redirect()->route('fabricantes.index')
            ->with('success', 'Fabricante atualizado com sucesso!');
    }

    public function destroy(Fabricante $fabricante)
    {
        // Verificar se existem aeronaves vinculadas
        if ($fabricante->aeronaves()->count() > 0) {
            return redirect()->route('fabricantes.index')
                ->with('error', 'Não é possível excluir este fabricante pois existem aeronaves vinculadas a ele.');
        }

        $fabricante->delete();

        return redirect()->route('fabricantes.index')
            ->with('success', 'Fabricante excluído com sucesso!');
    }
}