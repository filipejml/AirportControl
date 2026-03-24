<?php
// app/Http/Controllers/HomeController.php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\CompanhiaAerea;
use App\Models\Aeronave;
use App\Models\Aeroporto;
use App\Models\Voo;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // Total de passageiros (soma da coluna qtd_passageiros de todos os voos)
        $totalPassageiros = Voo::sum('qtd_passageiros');
        
        // Passageiros por aeroporto (agrupando por aeroporto usando JOIN)
        $passageirosPorAeroporto = Voo::join('aeroportos', 'voos.aeroporto_id', '=', 'aeroportos.id')
            ->select('aeroportos.nome_aeroporto as aeroporto', DB::raw('SUM(voos.qtd_passageiros) as total_passageiros'))
            ->groupBy('aeroportos.id', 'aeroportos.nome_aeroporto')
            ->orderByDesc('total_passageiros')
            ->pluck('total_passageiros', 'aeroporto')
            ->toArray();
        
        // Passageiros por horário
        $passageirosPorHorario = Voo::select('horario_voo', DB::raw('SUM(qtd_passageiros) as total_passageiros'))
            ->groupBy('horario_voo')
            ->orderByRaw("FIELD(horario_voo, 'EAM', 'AM', 'AN', 'PM', 'ALL')")
            ->pluck('total_passageiros', 'horario_voo')
            ->toArray();
        
        // Voos por aeroporto (quantidade de voos realizados)
        $voosPorAeroporto = Voo::join('aeroportos', 'voos.aeroporto_id', '=', 'aeroportos.id')
            ->select('aeroportos.nome_aeroporto as aeroporto', DB::raw('SUM(voos.qtd_voos) as total_voos'))
            ->groupBy('aeroportos.id', 'aeroportos.nome_aeroporto')
            ->orderByDesc('total_voos')
            ->pluck('total_voos', 'aeroporto')
            ->toArray();
        
        // Médias das notas por categoria
        $mediasNotas = [
            'objetivo' => Voo::whereNotNull('nota_obj')->avg('nota_obj') ?? 0,
            'pontualidade' => Voo::whereNotNull('nota_pontualidade')->avg('nota_pontualidade') ?? 0,
            'servicos' => Voo::whereNotNull('nota_servicos')->avg('nota_servicos') ?? 0,
            'patio' => Voo::whereNotNull('nota_patio')->avg('nota_patio') ?? 0,
        ];
        
        // Melhores companhias por categoria
        $melhoresCompanhias = [
            'objetivo' => Voo::join('companhias_aereas', 'voos.companhia_aerea_id', '=', 'companhias_aereas.id')
                ->select('companhias_aereas.nome as companhia', DB::raw('AVG(nota_obj) as media'))
                ->whereNotNull('nota_obj')
                ->groupBy('companhias_aereas.id', 'companhias_aereas.nome')
                ->orderByDesc('media')
                ->first()
                ->companhia ?? null,
            
            'pontualidade' => Voo::join('companhias_aereas', 'voos.companhia_aerea_id', '=', 'companhias_aereas.id')
                ->select('companhias_aereas.nome as companhia', DB::raw('AVG(nota_pontualidade) as media'))
                ->whereNotNull('nota_pontualidade')
                ->groupBy('companhias_aereas.id', 'companhias_aereas.nome')
                ->orderByDesc('media')
                ->first()
                ->companhia ?? null,
            
            'servicos' => Voo::join('companhias_aereas', 'voos.companhia_aerea_id', '=', 'companhias_aereas.id')
                ->select('companhias_aereas.nome as companhia', DB::raw('AVG(nota_servicos) as media'))
                ->whereNotNull('nota_servicos')
                ->groupBy('companhias_aereas.id', 'companhias_aereas.nome')
                ->orderByDesc('media')
                ->first()
                ->companhia ?? null,
            
            'patio' => Voo::join('companhias_aereas', 'voos.companhia_aerea_id', '=', 'companhias_aereas.id')
                ->select('companhias_aereas.nome as companhia', DB::raw('AVG(nota_patio) as media'))
                ->whereNotNull('nota_patio')
                ->groupBy('companhias_aereas.id', 'companhias_aereas.nome')
                ->orderByDesc('media')
                ->first()
                ->companhia ?? null,
        ];
        
        // Melhores modelos por categoria
        $melhoresModelos = [
            'objetivo' => Voo::join('aeronaves', 'voos.aeronave_id', '=', 'aeronaves.id')
                ->select('aeronaves.modelo as modelo_aviao', DB::raw('AVG(nota_obj) as media'))
                ->whereNotNull('nota_obj')
                ->groupBy('aeronaves.id', 'aeronaves.modelo')
                ->orderByDesc('media')
                ->first()
                ->modelo_aviao ?? null,
            
            'pontualidade' => Voo::join('aeronaves', 'voos.aeronave_id', '=', 'aeronaves.id')
                ->select('aeronaves.modelo as modelo_aviao', DB::raw('AVG(nota_pontualidade) as media'))
                ->whereNotNull('nota_pontualidade')
                ->groupBy('aeronaves.id', 'aeronaves.modelo')
                ->orderByDesc('media')
                ->first()
                ->modelo_aviao ?? null,
            
            'servicos' => Voo::join('aeronaves', 'voos.aeronave_id', '=', 'aeronaves.id')
                ->select('aeronaves.modelo as modelo_aviao', DB::raw('AVG(nota_servicos) as media'))
                ->whereNotNull('nota_servicos')
                ->groupBy('aeronaves.id', 'aeronaves.modelo')
                ->orderByDesc('media')
                ->first()
                ->modelo_aviao ?? null,
            
            'patio' => Voo::join('aeronaves', 'voos.aeronave_id', '=', 'aeronaves.id')
                ->select('aeronaves.modelo as modelo_aviao', DB::raw('AVG(nota_patio) as media'))
                ->whereNotNull('nota_patio')
                ->groupBy('aeronaves.id', 'aeronaves.modelo')
                ->orderByDesc('media')
                ->first()
                ->modelo_aviao ?? null,
        ];
        
        $stats = [
            'companhias' => CompanhiaAerea::count(),
            'modelos' => Aeronave::distinct('modelo')->count('modelo'),
            'aeroportos' => Aeroporto::count(),
            'voos' => Voo::count(),
            'passageiros_total' => $totalPassageiros,
        ];
        
        return view('dashboard.home', compact('stats', 'passageirosPorAeroporto', 'passageirosPorHorario', 'voosPorAeroporto', 'mediasNotas', 'melhoresCompanhias', 'melhoresModelos'));
    }
}