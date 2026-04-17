<?php
// app/Http/Controllers/AeronaveController.php

namespace App\Http\Controllers;

use App\Models\Aeronave;
use App\Models\Fabricante;
use App\Repositories\AeronaveRepository;
use App\Services\RankingService;
use Illuminate\Http\Request;

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
    
    // ... outros métodos (index, create, store, edit, update, destroy)
    
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