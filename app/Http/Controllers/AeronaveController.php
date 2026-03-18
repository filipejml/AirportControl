<?php
// app/Http/Controllers/AeronaveController.php

namespace App\Http\Controllers;

use App\Models\Aeronave;
use App\Models\Fabricante;
use Illuminate\Http\Request;

class AeronaveController extends Controller
{
    public function index()
    {
        $aeronaves = Aeronave::with('fabricante')->get();
        return view('admin.aeronaves.index', compact('aeronaves'));
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
}