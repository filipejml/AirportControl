<?php
// app/Http/Controllers/VeiculoController.php

namespace App\Http\Controllers;

use App\Models\Aeroporto;
use App\Models\Deposito;
use App\Models\Veiculo;
use Illuminate\Http\Request;

class VeiculoController extends Controller
{
    public function index(Aeroporto $aeroporto, Deposito $deposito)
    {
        $veiculos = $deposito->veiculos()->paginate(15);
        return view('admin.aeroportos.depositos.veiculos.index', compact('aeroporto', 'deposito', 'veiculos'));
    }

    public function create(Aeroporto $aeroporto, Deposito $deposito)
    {
        return view('admin.aeroportos.depositos.veiculos.create', compact('aeroporto', 'deposito'));
    }

    public function store(Request $request, Aeroporto $aeroporto, Deposito $deposito)
    {
        $validated = $request->validate([
            'placa' => 'required|string|max:10|unique:veiculos,placa',
            'modelo' => 'required|string|max:100',
            'marca' => 'required|string|max:100',
            'ano' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'cor' => 'nullable|string|max:50',
            'tipo' => 'required|in:carro,caminhao,onibus,van,utilitario,outro',
            'status' => 'required|in:disponivel,em_uso,manutencao,inativo',
            'quilometragem' => 'nullable|integer|min:0',
            'capacidade_passageiros' => 'nullable|integer|min:0',
            'carga_maxima' => 'nullable|numeric|min:0',
            'data_aquisicao' => 'nullable|date',
            'ultima_manutencao' => 'nullable|date',
            'proxima_manutencao' => 'nullable|date|after:ultima_manutencao',
            'observacoes' => 'nullable|string'
        ]);

        // Verificar capacidade do depósito
        if (!$deposito->hasEspacoDisponivel()) {
            return back()->with('error', 'Depósito está com capacidade máxima atingida!')
                ->withInput();
        }

        $veiculo = $deposito->veiculos()->create($validated);

        return redirect()->route('aeroportos.depositos.veiculos.index', [$aeroporto, $deposito])
            ->with('success', 'Veículo cadastrado com sucesso!');
    }

    public function show(Aeroporto $aeroporto, Deposito $deposito, Veiculo $veiculo)
    {
        return view('admin.aeroportos.depositos.veiculos.show', compact('aeroporto', 'deposito', 'veiculo'));
    }

    public function edit(Aeroporto $aeroporto, Deposito $deposito, Veiculo $veiculo)
    {
        return view('admin.aeroportos.depositos.veiculos.edit', compact('aeroporto', 'deposito', 'veiculo'));
    }

    public function update(Request $request, Aeroporto $aeroporto, Deposito $deposito, Veiculo $veiculo)
    {
        $validated = $request->validate([
            'placa' => 'required|string|max:10|unique:veiculos,placa,' . $veiculo->id,
            'modelo' => 'required|string|max:100',
            'marca' => 'required|string|max:100',
            'ano' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'cor' => 'nullable|string|max:50',
            'tipo' => 'required|in:carro,caminhao,onibus,van,utilitario,outro',
            'status' => 'required|in:disponivel,em_uso,manutencao,inativo',
            'quilometragem' => 'nullable|integer|min:0',
            'capacidade_passageiros' => 'nullable|integer|min:0',
            'carga_maxima' => 'nullable|numeric|min:0',
            'data_aquisicao' => 'nullable|date',
            'ultima_manutencao' => 'nullable|date',
            'proxima_manutencao' => 'nullable|date',
            'observacoes' => 'nullable|string'
        ]);

        $veiculo->update($validated);

        return redirect()->route('aeroportos.depositos.veiculos.index', [$aeroporto, $deposito])
            ->with('success', 'Veículo atualizado com sucesso!');
    }

    public function destroy(Aeroporto $aeroporto, Deposito $deposito, Veiculo $veiculo)
    {
        try {
            $veiculo->delete();
            return redirect()->route('aeroportos.depositos.veiculos.index', [$aeroporto, $deposito])
                ->with('success', 'Veículo excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('aeroportos.depositos.veiculos.index', [$aeroporto, $deposito])
                ->with('error', 'Erro ao excluir veículo: ' . $e->getMessage());
        }
    }

    public function checkPlaca(Request $request)
    {
        $request->validate(['placa' => 'required|string']);
        
        $exists = Veiculo::where('placa', $request->placa)
            ->when($request->id, fn($q) => $q->where('id', '!=', $request->id))
            ->exists();
            
        return response()->json(['exists' => $exists]);
    }
}