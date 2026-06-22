<?php
// app/Http/Controllers/AeronaveController.php

namespace App\Http\Controllers;

use App\Models\Aeronave;
use App\Models\Fabricante;
use App\Repositories\AeronaveRepository;
use App\Services\RankingService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AeronaveController extends Controller
{
    protected AeronaveRepository $aeronaveRepository;
    protected RankingService $rankingService;
    
    public function __construct(
        AeronaveRepository $aeronaveRepository,
        RankingService $rankingService
    ) {
        $this->aeronaveRepository = $aeronaveRepository;
        $this->rankingService = $rankingService;
    }
    
    public function index()
    {
        $aeronaves = Aeronave::with(['fabricante', 'companhias'])
            ->orderBy('modelo')
            ->get();

        return view('admin.aeronaves.index', compact('aeronaves'));
    }

    public function create()
    {
        $fabricantes = Fabricante::orderBy('nome')->get();

        return view('admin.aeronaves.create', compact('fabricantes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'modelo' => ['required', 'string', 'max:255', 'unique:aeronaves,modelo'],
            'capacidade' => ['required', 'integer', 'min:1'],
            'fabricante_id' => ['required', 'integer', 'exists:fabricantes,id'],
        ], [
            'modelo.unique' => 'Este modelo de aeronave já está cadastrado.',
        ]);

        $aeronave = Aeronave::create($data);

        return redirect()
            ->route('aeronaves.show', $aeronave)
            ->with('success', 'Aeronave cadastrada com sucesso!');
    }

    public function show(Aeronave $aeronave)
    {
        $aeronave->load(['fabricante', 'companhias']);

        return view('admin.aeronaves.show', compact('aeronave'));
    }

    public function edit(Aeronave $aeronave)
    {
        $fabricantes = Fabricante::orderBy('nome')->get();

        return view('admin.aeronaves.edit', compact('aeronave', 'fabricantes'));
    }

    public function update(Request $request, Aeronave $aeronave)
    {
        $data = $request->validate([
            'modelo' => [
                'required',
                'string',
                'max:255',
                Rule::unique('aeronaves', 'modelo')->ignore($aeronave->id),
            ],
            'capacidade' => ['required', 'integer', 'min:1'],
            'fabricante_id' => ['required', 'integer', 'exists:fabricantes,id'],
        ], [
            'modelo.unique' => 'Este modelo de aeronave já está cadastrado.',
        ]);

        $aeronave->update($data);

        return redirect()
            ->route('aeronaves.show', $aeronave)
            ->with('success', 'Aeronave atualizada com sucesso!');
    }

    public function destroy(Aeronave $aeronave)
    {
        if ($aeronave->voos()->exists()) {
            return redirect()
                ->route('aeronaves.index')
                ->with('error', 'Não é possível excluir uma aeronave que possui voos vinculados.');
        }

        $aeronave->companhias()->detach();
        $aeronave->delete();

        return redirect()
            ->route('aeronaves.index')
            ->with('success', 'Aeronave excluída com sucesso!');
    }

    public function verificarModelo(Request $request)
    {
        $data = $request->validate([
            'modelo' => ['required', 'string', 'max:255'],
            'id' => ['nullable', 'integer', 'exists:aeronaves,id'],
        ]);

        $query = Aeronave::where('modelo', $data['modelo']);

        if (!empty($data['id'])) {
            $query->whereKeyNot($data['id']);
        }

        $existe = $query->exists();

        return response()->json([
            'existe' => $existe,
            'message' => $existe
                ? 'Este modelo de aeronave já está cadastrado.'
                : 'Modelo disponível.',
        ]);
    }
    
    /**
     * Display general information about aircrafts
     */
    public function informacoes()
    {
        $modelosComDados = $this->aeronaveRepository->getStatisticsForInformacoes();
        
        return view('aeronaves.informacoes', compact('modelosComDados'));
    }
    
    /**
     * Display dashboard for a specific aircraft
     */
    public function dashboard(Request $request, Aeronave $aeronave)
    {
        $aeronave->load(['fabricante', 'companhias']);
        
        // Prepare filters
        $filters = [
            'companhia_id' => $request->get('companhia', 'geral'),
            'periodo' => $request->get('periodo', 'geral'),
            'semana' => $request->get('semana'),
            'ano' => $request->get('ano'),
            'mes' => $request->get('mes'),
            'ano_selecionado' => $request->get('ano_selecionado')
        ];
        
        // Get stats using repository
        $stats = $this->aeronaveRepository->getDashboardStats($aeronave, $filters);
        
        // Get filter options
        $filterOptions = $this->aeronaveRepository->getFilterOptions($aeronave);
        
        return view('aeronaves.dashboard', array_merge(
            compact('aeronave'),
            $stats,
            $filterOptions,
            $filters
        ));
    }
    
    /**
     * Display ranking and general data about aircrafts
     */
    public function ranking()
    {
        $rankings = $this->rankingService->generateRankings();
        
        return view('aeronaves.ranking', $rankings);
    }
}
