<?php

namespace App\Http\Controllers;

use App\Models\Dashboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dashboard panel page.
     */
    public function index()
    {
        // Instanciar o model Dashboard
        $dashboard = new Dashboard();
        
        // Obter dados estatísticos
        $stats = $dashboard->getEstatisticasGerais();
        
        // Obter médias das notas
        $mediasNotas = $dashboard->getMediasNotas();
        
        // Obter voos por horário
        $voosPorHorario = $dashboard->getVoosPorHorario();
        
        // Obter passageiros por horário
        $passageirosPorHorario = $dashboard->getPassageirosPorHorario();
        
        // Obter voos por tipo
        $voosPorTipo = $dashboard->getVoosPorTipo();
        
        // Obter passageiros por tipo
        $passageirosPorTipo = $dashboard->getPassageirosPorTipo();
        
        // Obter voos por tipo de aeronave
        $voosPorTipoAeronave = $dashboard->getVoosPorTipoAeronave();
        
        // Obter passageiros por tipo de aeronave
        $passageirosPorTipoAeronave = $dashboard->getPassageirosPorTipoAeronave();
        
        return view('dashboard.index', compact(
            'stats',
            'mediasNotas',
            'voosPorHorario',
            'passageirosPorHorario',
            'voosPorTipo',
            'passageirosPorTipo',
            'voosPorTipoAeronave',
            'passageirosPorTipoAeronave'
        ));
    }
    
    /**
     * Display the dashboard graphics page.
     */
    public function graficos()
    {
        return view('dashboard.graficos');
    }
}