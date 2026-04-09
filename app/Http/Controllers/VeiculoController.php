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
        $query = $deposito->veiculos();
        
        if ($tipo = request('tipo')) {
            $query->where('tipo_veiculo', $tipo);
        }
        
        if ($status = request('status')) {
            $query->where('status', $status);
        }
        
        $veiculos = $query->orderBy('codigo')->paginate(15);
        
        return view('admin.aeroportos.depositos.veiculos.index', compact('aeroporto', 'deposito', 'veiculos'));
    }

    public function create(Aeroporto $aeroporto, Deposito $deposito)
    {
        return view('admin.aeroportos.depositos.veiculos.create', compact('aeroporto', 'deposito'));
    }

    public function store(Request $request, Aeroporto $aeroporto, Deposito $deposito)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:veiculos,codigo',
            'tipo_veiculo' => 'required|in:' . implode(',', array_keys(Veiculo::TIPOS_VEICULOS)),
            'modelo' => 'nullable|string|max:100',
            'fabricante' => 'nullable|string|max:100',
            'ano_fabricacao' => 'nullable|integer|min:1900|max:' . date('Y'),
            'capacidade_operacional' => 'nullable|numeric|min:0',
            'status' => 'required|in:disponivel,indisponivel',
            'observacoes' => 'nullable|string'
        ]);

        // Definir unidade de capacidade baseada no tipo
        $unidadeMap = [
            'esteira_bagagem' => 'kg',
            'caminhao_combustivel' => 'litros',
            'carrinho_bagagem' => 'unidades',
            'caminhao_pushback' => 'toneladas',
            'caminhao_escada' => 'metros',
            'caminhao_limpeza' => 'litros'
        ];
        
        $validated['unidade_capacidade'] = $unidadeMap[$validated['tipo_veiculo']] ?? null;

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
            'codigo' => 'required|string|max:50|unique:veiculos,codigo,' . $veiculo->id,
            'tipo_veiculo' => 'required|in:' . implode(',', array_keys(Veiculo::TIPOS_VEICULOS)),
            'modelo' => 'nullable|string|max:100',
            'fabricante' => 'nullable|string|max:100',
            'ano_fabricacao' => 'nullable|integer|min:1900|max:' . date('Y'),
            'capacidade_operacional' => 'nullable|numeric|min:0',
            'status' => 'required|in:disponivel,indisponivel',
            'observacoes' => 'nullable|string'
        ]);

        // Atualizar unidade de capacidade baseada no tipo
        $unidadeMap = [
            'esteira_bagagem' => 'kg',
            'caminhao_combustivel' => 'litros',
            'carrinho_bagagem' => 'unidades',
            'caminhao_pushback' => 'toneladas',
            'caminhao_escada' => 'metros',
            'caminhao_limpeza' => 'litros'
        ];
        
        $validated['unidade_capacidade'] = $unidadeMap[$validated['tipo_veiculo']] ?? null;

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

    public function checkCodigo(Request $request, Aeroporto $aeroporto, Deposito $deposito)
    {
        $request->validate(['codigo' => 'required|string']);
        
        $exists = Veiculo::where('codigo', $request->codigo)
            ->when($request->id, fn($q) => $q->where('id', '!=', $request->id))
            ->exists();
            
        return response()->json(['exists' => $exists]);
    }
}