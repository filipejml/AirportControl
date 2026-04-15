<?php
// app/Http/Controllers/DepositoController.php

namespace App\Http\Controllers;

use App\Models\Aeroporto;
use App\Models\Deposito;
use Illuminate\Http\Request;

class DepositoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Aeroporto $aeroporto)
    {
        $depositos = $aeroporto->depositos()->withCount('veiculos')->get();
        
        return view('admin.aeroportos.depositos.index', compact('aeroporto', 'depositos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Aeroporto $aeroporto)
    {
        // Calcular o próximo número de depósito
        $proximoNumero = $aeroporto->depositos()->count() + 1;
        $nomeSugerido = "Depósito {$proximoNumero}";
        
        return view('admin.aeroportos.depositos.create', compact('aeroporto', 'nomeSugerido', 'proximoNumero'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Aeroporto $aeroporto)
    {
        $request->validate([
            'nome' => 'nullable|string|max:255',
            'capacidade_maxima' => 'nullable|integer|min:0',
            'status' => 'required|in:ativo,inativo,manutencao'
        ]);

        // Se o nome não foi enviado ou está vazio, gerar automaticamente
        $nome = $request->nome;
        if (empty($nome)) {
            $count = $aeroporto->depositos()->count() + 1;
            $nome = "Depósito {$count}";
        }

        $deposito = $aeroporto->depositos()->create([
            'nome' => $nome,
            'capacidade_maxima' => $request->capacidade_maxima,
            'status' => $request->status
        ]);

        return redirect()->route('aeroportos.depositos.show', [$aeroporto, $deposito])
            ->with('success', 'Depósito criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Aeroporto $aeroporto, Deposito $deposito)
    {
        $deposito->load(['veiculos' => function($query) {
            $query->orderBy('status')->orderBy('codigo');
        }]);
        
        $estatisticas = [
            'total_veiculos' => $deposito->veiculos->count(),
            'disponiveis' => $deposito->veiculos->where('status', 'disponivel')->count(),
            'indisponiveis' => $deposito->veiculos->where('status', 'indisponivel')->count(),
            'por_tipo' => $deposito->veiculos->groupBy('tipo_veiculo')->map->count(),
        ];
        
        return view('admin.aeroportos.depositos.show', compact('aeroporto', 'deposito', 'estatisticas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Aeroporto $aeroporto, Deposito $deposito)
    {
        return view('admin.aeroportos.depositos.edit', compact('aeroporto', 'deposito'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Aeroporto $aeroporto, Deposito $deposito)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'capacidade_maxima' => 'nullable|integer|min:0',
            'status' => 'required|in:ativo,inativo,manutencao'
        ]);

        $deposito->update($request->only(['nome', 'capacidade_maxima', 'status']));

        return redirect()->route('aeroportos.depositos.show', [$aeroporto, $deposito])
            ->with('success', 'Depósito atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aeroporto $aeroporto, Deposito $deposito)
    {
        // Verificar se há veículos no depósito
        if ($deposito->veiculos()->count() > 0) {
            return redirect()->route('aeroportos.depositos.index', $aeroporto)
                ->with('error', 'Não é possível excluir um depósito que possui veículos cadastrados.');
        }
        
        $deposito->delete();
        
        return redirect()->route('aeroportos.depositos.index', $aeroporto)
            ->with('success', 'Depósito excluído com sucesso!');
    }

    /**
     * Check if deposit code already exists (AJAX) - Removido pois não usamos mais código
     * Mantido apenas para compatibilidade, mas sempre retorna disponível
     */
    public function checkCodigo(Request $request, Aeroporto $aeroporto)
    {
        return response()->json([
            'exists' => false,
            'message' => 'Código disponível'
        ]);
    }
}