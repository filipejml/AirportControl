<?php
// app/Http/Controllers/HomeController.php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Home;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // Instanciar o model Home
        $home = new Home();
        
        // Buscar todos os dados necessários
        $stats = $home->getEstatisticasGerais();
        $passageirosPorAeroporto = $home->getPassageirosPorAeroporto();
        $passageirosPorHorario = $home->getPassageirosPorHorario();
        $voosPorAeroporto = $home->getVoosPorAeroporto();
        $mediasNotas = $home->getMediasNotas();
        $melhoresCompanhias = $home->getMelhoresCompanhias();
        $melhoresModelos = $home->getMelhoresModelos();
        
        return view('dashboard.home', compact(
            'stats', 
            'passageirosPorAeroporto', 
            'passageirosPorHorario', 
            'voosPorAeroporto', 
            'mediasNotas', 
            'melhoresCompanhias', 
            'melhoresModelos'
        ));
    }
}