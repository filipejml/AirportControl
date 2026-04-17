<?php
// app/Http/Controllers/AeroportoController.php

namespace App\Http\Controllers;

use App\Models\Aeroporto;
use App\Models\CompanhiaAerea;
use App\Models\Deposito;
use App\Models\Veiculo;
use App\Services\AeroportoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AeroportoController extends Controller
{
    protected $aeroportoService;
    
    public function __construct(AeroportoService $aeroportoService)
    {
        $this->aeroportoService = $aeroportoService;
    }

    /**
     * STEP 1: Informações básicas do aeroporto
     */
    public function createStep1()
    {
        // Limpar sessão anterior se existir
        Session::forget('aeroporto_em_criacao');
        
        // Carregar companhias para o formulário (igual ao create antigo)
        $companhias = CompanhiaAerea::withCount('aeronaves')->get();
        
        return view('admin.aeroportos.wizard.step1', compact('companhias'));
    }

    /**
     * Store Step 1 - Salva informações básicas
     */
    public function storeStep1(Request $request)
    {
        $request->validate([
            'nome_aeroporto' => 'required|string|max:255|unique:aeroportos,nome_aeroporto',
            'companhias' => 'nullable|array'
        ]);

        // Criar aeroporto
        $aeroporto = Aeroporto::create([
            'nome_aeroporto' => $request->nome_aeroporto
        ]);

        // Associar companhias selecionadas
        if ($request->has('companhias')) {
            $aeroporto->companhias()->sync($request->companhias);
        }

        // Armazenar ID na sessão para uso nos próximos passos
        Session::put('aeroporto_em_criacao', $aeroporto->id);

        return redirect()->route('aeroportos.create.step2', $aeroporto)
            ->with('success', 'Aeroporto criado com sucesso! Agora adicione os depósitos.');
    }

    /**
     * STEP 2: Cadastro de Depósitos
     */
    public function createStep2(Aeroporto $aeroporto)
    {
        // Verificar se o aeroporto pertence à criação atual
        if (Session::get('aeroporto_em_criacao') != $aeroporto->id) {
            return redirect()->route('aeroportos.create.step1')
                ->with('error', 'Sessão expirada. Comece novamente.');
        }

        $depositos = $aeroporto->depositos()->get();
        
        return view('admin.aeroportos.wizard.step2', compact('aeroporto', 'depositos'));
    }

    /**
     * Store Step 2 - Salva depósitos
     */
    public function storeStep2(Request $request, Aeroporto $aeroporto)
    {
        // Validar se é para pular ou salvar
        if ($request->has('skip')) {
            return redirect()->route('aeroportos.create.step3', $aeroporto);
        }

        // Se não tem depósitos para salvar, também pula
        if (empty($request->depositos)) {
            return redirect()->route('aeroportos.create.step3', $aeroporto);
        }

        // Validar os dados dos depósitos
        $request->validate([
            'depositos.*.nome' => 'required|string|max:255',
            'depositos.*.capacidade_maxima' => 'nullable|integer|min:0',
            'depositos.*.observacoes' => 'nullable|string'
        ]);

        // Salvar cada depósito
        foreach ($request->depositos as $depositoData) {
            $depositoData['status'] = 'ativo';
            $aeroporto->depositos()->create($depositoData);
        }

        // Redirecionar para o próximo passo
        return redirect()->route('aeroportos.create.step3', $aeroporto)
            ->with('success', 'Depósitos adicionados com sucesso!');
    }

    /**
     * STEP 3: Cadastro de Veículos por depósito
     */
    public function createStep3(Aeroporto $aeroporto)
    {
        // Verificar se o aeroporto pertence à criação atual
        if (Session::get('aeroporto_em_criacao') != $aeroporto->id) {
            return redirect()->route('aeroportos.create.step1')
                ->with('error', 'Sessão expirada. Comece novamente.');
        }

        $depositos = $aeroporto->depositos()->with('veiculos')->get();
        
        return view('admin.aeroportos.wizard.step3', compact('aeroporto', 'depositos'));
    }

    /**
     * Store Step 3 - Salva veículos
     */
    public function storeStep3(Request $request, Aeroporto $aeroporto)
    {
        $request->validate([
            'veiculos' => 'array',
            'veiculos.*.deposito_id' => 'required|exists:depositos,id',
            'veiculos.*.tipo_veiculo' => 'required|in:' . implode(',', array_keys(Veiculo::TIPOS_VEICULOS)),
            'veiculos.*.codigo' => 'required|string|max:50|unique:veiculos,codigo',
            'veiculos.*.quantidade' => 'nullable|integer|min:1'
        ]);

        foreach ($request->veiculos as $veiculoData) {
            $deposito = Deposito::find($veiculoData['deposito_id']);
            
            $veiculoData['quantidade'] = $veiculoData['quantidade'] ?? 1;
            $veiculoData['status'] = 'disponivel';
            
            $deposito->veiculos()->create($veiculoData);
        }

        Session::forget('aeroporto_em_criacao');

        return redirect()->route('aeroportos.show', $aeroporto)
            ->with('success', 'Aeroporto cadastrado com todos os veículos!');
    }

    /**
     * Get vehicle template for AJAX requests
     */
    public function getVeiculoTemplate(Request $request)
    {
        $depositoId = $request->deposito_id;
        $index = $request->index;
        
        return view('admin.aeroportos.wizard.partials.veiculo-form', compact('depositoId', 'index'))->render();
    }

    /**
     * Check if vehicle code already exists (AJAX)
     */
    public function checkVeiculoCodigo(Request $request)
    {
        $request->validate(['codigo' => 'required|string']);
        
        $exists = Veiculo::where('codigo', $request->codigo)->exists();
        
        return response()->json(['exists' => $exists]);
    }

    /**
     * Display a listing of the resource (ADMIN)
     */
    public function index()
    {
        $aeroportos = Aeroporto::with('companhias')->get();
        return view('admin.aeroportos.index', compact('aeroportos'));
    }

    /**
     * Store a newly created resource in storage (ADMIN)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome_aeroporto' => 'required|string|max:255|unique:aeroportos,nome_aeroporto',
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
     * Display the specified resource (ADMIN)
     */
    public function show(Aeroporto $aeroporto)
    {
        // Carregar os relacionamentos necessários para o ADMIN
        $aeroporto->load([
            'companhias', 
            'depositos.veiculos'
        ]);
        
        return view('admin.aeroportos.show', compact('aeroporto'));
    }

    /**
     * Show the form for editing the specified resource (ADMIN)
     */
    public function edit(Aeroporto $aeroporto)
    {
        $companhias = CompanhiaAerea::withCount('aeronaves')->get();
        $aeroporto->load('companhias');
        return view('admin.aeroportos.edit', compact('aeroporto', 'companhias'));
    }

    /**
     * Update the specified resource in storage (ADMIN)
     */
    public function update(Request $request, Aeroporto $aeroporto)
    {
        $request->validate([
            'nome_aeroporto' => 'required|string|max:255|unique:aeroportos,nome_aeroporto,' . $aeroporto->id,
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
     * Remove the specified resource from storage (ADMIN)
     */
    public function destroy(Aeroporto $aeroporto)
    {
        try {
            // Verificar se tem voos
            if ($aeroporto->voos()->exists()) {
                return redirect()->route('aeroportos.index')
                    ->with('error', 'Não é possível excluir um aeroporto que possui voos cadastrados.');
            }
            
            $aeroporto->companhias()->detach();
            $aeroporto->depositos()->delete(); // Remove depósitos e veículos (cascade)
            $aeroporto->delete();
            
            return redirect()->route('aeroportos.index')
                ->with('success', 'Aeroporto excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('aeroportos.index')
                ->with('error', 'Erro ao excluir aeroporto: ' . $e->getMessage());
        }
    }

    /**
     * Display dashboard for common user (USUÁRIO COMUM)
     */
    public function dashboard(Aeroporto $aeroporto)
    {
        $data = $this->aeroportoService->getDashboardData($aeroporto);
        
        // Extrair notas para variáveis individuais
        $notaObj = $data['mediasNotas']['nota_obj'];
        $notaPontualidade = $data['mediasNotas']['nota_pontualidade'];
        $notaServicos = $data['mediasNotas']['nota_servicos'];
        $notaPatio = $data['mediasNotas']['nota_patio'];
        
        return view('aeroportos.dashboard', array_merge($data, compact(
            'notaObj', 'notaPontualidade', 'notaServicos', 'notaPatio'
        )));
    }

    /**
     * Display general information about airports (USUÁRIO COMUM)
     */
    public function informacoes(Request $request)
    {
        $anoSelecionado = $request->get('ano', date('Y'));
        
        // Buscar todos os dados através do Service
        $data = $this->aeroportoService->getInformacoesData($anoSelecionado);
        
        // Retornar a view de USUÁRIO COMUM (informacoes)
        return view('aeroportos.informacoes', $data);
    }

    /**
     * Check if airport name already exists (AJAX - ADMIN)
     */
    public function checkName(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255'
        ]);

        $nome = $request->nome;
        $airportId = $request->id;

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
}