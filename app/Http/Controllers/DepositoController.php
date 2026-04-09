<?php
// app/Http/Controllers/DepositoController.php

namespace App\Http\Controllers;

use App\Models\Aeroporto;
use App\Models\Deposito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepositoController extends Controller
{
    public function index(Aeroporto $aeroporto)
    {
        $depositos = $aeroporto->depositos()->withCount('veiculos')->get();
        return view('admin.aeroportos.depositos.index', compact('aeroporto', 'depositos'));
    }

    public function create(Aeroporto $aeroporto)
    {
        return view('admin.aeroportos.depositos.create', compact('aeroporto'));
    }

    public function store(Request $request, Aeroporto $aeroporto)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'codigo' => 'required|string|max:50|unique:depositos,codigo',
            'localizacao' => 'nullable|string|max:500',
            'area_total' => 'nullable|numeric|min:0',
            'capacidade_maxima' => 'nullable|integer|min:0',
            'status' => 'required|in:ativo,inativo,manutencao',
            'observacoes' => 'nullable|string'
        ]);

        $deposito = $aeroporto->depositos()->create($validated);

        return redirect()->route('aeroportos.depositos.index', $aeroporto)
            ->with('success', 'Depósito criado com sucesso!');
    }

    public function show(Aeroporto $aeroporto, Deposito $deposito)
    {
        $deposito->load(['veiculos' => function($query) {
            $query->orderBy('status')->orderBy('modelo');
        }]);
        
        $estatisticas = [
            'total_veiculos' => $deposito->veiculos->count(),
            'disponiveis' => $deposito->veiculos->where('status', 'disponivel')->count(),
            'em_uso' => $deposito->veiculos->where('status', 'em_uso')->count(),
            'manutencao' => $deposito->veiculos->where('status', 'manutencao')->count(),
            'por_tipo' => $deposito->veiculos->groupBy('tipo')->map->count(),
        ];
        
        return view('admin.aeroportos.depositos.show', compact('aeroporto', 'deposito', 'estatisticas'));
    }

    public function edit(Aeroporto $aeroporto, Deposito $deposito)
    {
        return view('admin.aeroportos.depositos.edit', compact('aeroporto', 'deposito'));
    }

    public function update(Request $request, Aeroporto $aeroporto, Deposito $deposito)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'codigo' => 'required|string|max:50|unique:depositos,codigo,' . $deposito->id,
            'localizacao' => 'nullable|string|max:500',
            'area_total' => 'nullable|numeric|min:0',
            'capacidade_maxima' => 'nullable|integer|min:0',
            'status' => 'required|in:ativo,inativo,manutencao',
            'observacoes' => 'nullable|string'
        ]);

        $deposito->update($validated);

        return redirect()->route('aeroportos.depositos.index', $aeroporto)
            ->with('success', 'Depósito atualizado com sucesso!');
    }

    public function destroy(Aeroporto $aeroporto, Deposito $deposito)
    {
        try {
            $deposito->delete();
            return redirect()->route('aeroportos.depositos.index', $aeroporto)
                ->with('success', 'Depósito excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('aeroportos.depositos.index', $aeroporto)
                ->with('error', 'Erro ao excluir depósito: ' . $e->getMessage());
        }
    }

    public function checkCodigo(Request $request)
    {
        $request->validate(['codigo' => 'required|string']);
        
        $exists = Deposito::where('codigo', $request->codigo)
            ->when($request->id, fn($q) => $q->where('id', '!=', $request->id))
            ->exists();
            
        return response()->json(['exists' => $exists]);
    }
}