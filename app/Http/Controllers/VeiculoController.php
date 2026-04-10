<?php
// app/Http/Controllers/VeiculoController.php

namespace App\Http\Controllers;

use App\Models\Aeroporto;
use App\Models\Deposito;
use App\Models\Veiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class VeiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Aeroporto $aeroporto, Deposito $deposito)
    {
        $veiculos = $deposito->veiculos()->paginate(15);
        
        return view('admin.aeroportos.depositos.veiculos.index', compact('aeroporto', 'deposito', 'veiculos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Aeroporto $aeroporto, Deposito $deposito)
    {
        $tiposVeiculos = Veiculo::TIPOS_VEICULOS;
        
        // Limpar carrinho da sessão ao iniciar novo cadastro
        Session::forget('veiculos_carrinho_' . $deposito->id);
        
        return view('admin.aeroportos.depositos.veiculos.create', compact('aeroporto', 'deposito', 'tiposVeiculos'));
    }

    /**
     * Store a newly created resource in storage (via AJAX para o carrinho).
     */
    public function store(Request $request, Aeroporto $aeroporto, Deposito $deposito)
    {
        $request->validate([
            'codigo' => 'required|string|max:50',
            'tipo_veiculo' => 'required|in:' . implode(',', array_keys(Veiculo::TIPOS_VEICULOS)),
            'status' => 'nullable|in:disponivel,indisponivel'
        ]);

        // Verificar se o código já existe neste depósito
        $exists = Veiculo::where('deposito_id', $deposito->id)
                        ->where('codigo', $request->codigo)
                        ->exists();

        if ($exists) {
            if ($request->ajax() || $request->has('ajax')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este código já está em uso neste depósito.'
                ], 422);
            }
            
            return back()->with('error', 'Este código já está em uso neste depósito.')
                        ->withInput();
        }

        // Se for AJAX, adicionar ao carrinho da sessão
        if ($request->ajax() || $request->has('ajax')) {
            $carrinhoKey = 'veiculos_carrinho_' . $deposito->id;
            $carrinho = Session::get($carrinhoKey, []);
            
            // Verificar se o código já existe no carrinho
            $codigoExists = collect($carrinho)->contains('codigo', $request->codigo);
            if ($codigoExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este código já foi adicionado na lista atual.'
                ], 422);
            }
            
            // Adicionar ao carrinho
            $carrinho[] = [
                'codigo' => $request->codigo,
                'tipo_veiculo' => $request->tipo_veiculo,
                'tipo_nome' => Veiculo::TIPOS_VEICULOS[$request->tipo_veiculo]['nome'],
                'tipo_icone' => Veiculo::TIPOS_VEICULOS[$request->tipo_veiculo]['icone'],
                'status' => $request->status ?? 'disponivel'
            ];
            
            Session::put($carrinhoKey, $carrinho);
            
            return response()->json([
                'success' => true,
                'message' => 'Veículo adicionado à lista!',
                'carrinho' => $carrinho,
                'total' => count($carrinho)
            ]);
        }
        
        // Se não for AJAX, salvar diretamente (fallback)
        $veiculo = $deposito->veiculos()->create([
            'codigo' => $request->codigo,
            'tipo_veiculo' => $request->tipo_veiculo,
            'status' => $request->status ?? 'disponivel'
        ]);

        return redirect()->route('aeroportos.depositos.veiculos.index', [$aeroporto, $deposito])
            ->with('success', 'Veículo cadastrado com sucesso!');
    }
    
    /**
     * Finalizar e salvar todos os veículos do carrinho.
     */
    public function finalizar(Aeroporto $aeroporto, Deposito $deposito)
    {
        $carrinhoKey = 'veiculos_carrinho_' . $deposito->id;
        $carrinho = Session::get($carrinhoKey, []);
        
        if (empty($carrinho)) {
            return redirect()->route('aeroportos.depositos.veiculos.index', [$aeroporto, $deposito])
                ->with('warning', 'Nenhum veículo para salvar.');
        }
        
        $errors = [];
        $successCount = 0;
        
        foreach ($carrinho as $item) {
            try {
                // Verificar se o código já existe neste depósito
                if (Veiculo::where('deposito_id', $deposito->id)
                        ->where('codigo', $item['codigo'])
                        ->exists()) {
                    $errors[] = "Código {$item['codigo']} já existe neste depósito.";
                    continue;
                }
                
                $deposito->veiculos()->create([
                    'codigo' => $item['codigo'],
                    'tipo_veiculo' => $item['tipo_veiculo'],
                    'status' => $item['status']
                ]);
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Erro ao salvar {$item['codigo']}: " . $e->getMessage();
            }
        }
        
        // Limpar carrinho
        Session::forget($carrinhoKey);
        
        $message = "{$successCount} veículo(s) cadastrado(s) com sucesso!";
        
        if ($successCount > 0) {
            return redirect()->route('aeroportos.depositos.veiculos.index', [$aeroporto, $deposito])
                ->with('success', $message);
        } else {
            return redirect()->route('aeroportos.depositos.veiculos.create', [$aeroporto, $deposito])
                ->with('error', 'Nenhum veículo foi cadastrado. ' . implode(', ', $errors));
        }
    }
    
    /**
     * Remover um veículo do carrinho.
     */
    public function removerDoCarrinho(Request $request, Aeroporto $aeroporto, Deposito $deposito)
    {
        $request->validate([
            'codigo' => 'required|string'
        ]);
        
        $carrinhoKey = 'veiculos_carrinho_' . $deposito->id;
        $carrinho = Session::get($carrinhoKey, []);
        
        $carrinho = array_filter($carrinho, function($item) use ($request) {
            return $item['codigo'] !== $request->codigo;
        });
        
        $carrinho = array_values($carrinho); // Reindexar array
        
        Session::put($carrinhoKey, $carrinho);
        
        return response()->json([
            'success' => true,
            'message' => 'Veículo removido da lista!',
            'carrinho' => $carrinho,
            'total' => count($carrinho)
        ]);
    }
    
    /**
     * Limpar todo o carrinho.
     */
    public function limparCarrinho(Request $request, Aeroporto $aeroporto, Deposito $deposito)
    {
        $carrinhoKey = 'veiculos_carrinho_' . $deposito->id;
        Session::forget($carrinhoKey);
        
        return response()->json([
            'success' => true,
            'message' => 'Lista limpa com sucesso!',
            'total' => 0
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Aeroporto $aeroporto, Deposito $deposito, Veiculo $veiculo)
    {
        return view('admin.aeroportos.depositos.veiculos.show', compact('aeroporto', 'deposito', 'veiculo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Aeroporto $aeroporto, Deposito $deposito, Veiculo $veiculo)
    {
        $tiposVeiculos = Veiculo::TIPOS_VEICULOS;
        
        return view('admin.aeroportos.depositos.veiculos.edit', compact('aeroporto', 'deposito', 'veiculo', 'tiposVeiculos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Aeroporto $aeroporto, Deposito $deposito, Veiculo $veiculo)
    {
        $request->validate([
            'codigo' => 'required|string|max:50|unique:veiculos,codigo,' . $veiculo->id,
            'tipo_veiculo' => 'required|in:' . implode(',', array_keys(Veiculo::TIPOS_VEICULOS)),
            'status' => 'required|in:disponivel,indisponivel'
        ]);

        $veiculo->update($request->only(['codigo', 'tipo_veiculo', 'status']));

        return redirect()->route('aeroportos.depositos.veiculos.show', [$aeroporto, $deposito, $veiculo])
            ->with('success', 'Veículo atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aeroporto $aeroporto, Deposito $deposito, Veiculo $veiculo)
    {
        $veiculo->delete();
        
        return redirect()->route('aeroportos.depositos.veiculos.index', [$aeroporto, $deposito])
            ->with('success', 'Veículo excluído com sucesso!');
    }

    /**
     * Check if vehicle code already exists (AJAX)
     */
    public function checkCodigo(Request $request, Aeroporto $aeroporto, Deposito $deposito)
    {
        $request->validate([
            'codigo' => 'required|string|max:50'
        ]);

        $codigo = $request->codigo;
        $veiculoId = $request->id;

        // Verificar se existe no banco para este depósito específico
        $query = Veiculo::where('deposito_id', $deposito->id)
                        ->where('codigo', $codigo);
        
        if ($veiculoId) {
            $query->where('id', '!=', $veiculoId);
        }
        $exists = $query->exists();
        
        // Verificar se existe no carrinho atual (também para este depósito)
        $carrinhoKey = 'veiculos_carrinho_' . $deposito->id;
        $carrinho = Session::get($carrinhoKey, []);
        $carrinhoExists = collect($carrinho)->contains('codigo', $codigo);

        return response()->json([
            'exists' => $exists || $carrinhoExists,
            'message' => ($exists || $carrinhoExists) ? 'Este código já está em uso neste depósito.' : 'Código disponível'
        ]);
    }
}