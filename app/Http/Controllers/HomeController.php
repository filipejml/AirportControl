<?php
// app/Http/Controllers/HomeController.php

namespace App\Http\Controllers;

use App\Models\CompanhiaAerea;
use App\Models\Aeronave;
use App\Models\Aeroporto;
use App\Models\Voo;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // Estatísticas Gerais
        $stats = [
            'companhias' => CompanhiaAerea::count(),
            'modelos' => Aeronave::distinct('modelo')->count('modelo'),
            'aeroportos' => Aeroporto::count(),
            'voos' => Voo::sum('qtd_voos') ?? 0,
            'passageiros_total' => Voo::sum('total_passageiros') ?? 0,
        ];
        
        // Passageiros por Aeroporto
        $passageirosPorAeroporto = Voo::select('aeroportos.nome_aeroporto', DB::raw('SUM(voos.total_passageiros) as total'))
            ->join('aeroportos', 'voos.aeroporto_id', '=', 'aeroportos.id')
            ->groupBy('aeroportos.id', 'aeroportos.nome_aeroporto')
            ->orderByDesc('total')
            ->pluck('total', 'aeroportos.nome_aeroporto')
            ->toArray();
        
        // Passageiros por Horário
        $horarios = ['EAM', 'AM', 'AN', 'PM', 'ALL'];
        $passageirosPorHorario = [];
        foreach ($horarios as $horario) {
            $passageirosPorHorario[$horario] = Voo::where('horario_voo', $horario)->sum('total_passageiros');
        }
        
        // Voos por Aeroporto
        $voosPorAeroporto = Voo::select('aeroportos.nome_aeroporto', DB::raw('SUM(voos.qtd_voos) as total'))
            ->join('aeroportos', 'voos.aeroporto_id', '=', 'aeroportos.id')
            ->groupBy('aeroportos.id', 'aeroportos.nome_aeroporto')
            ->orderByDesc('total')
            ->pluck('total', 'aeroportos.nome_aeroporto')
            ->toArray();
        
        // Médias das Notas (usando os nomes corretos dos campos)
        $mediasNotas = [
            'objetivo' => round(Voo::whereNotNull('nota_obj')->avg('nota_obj') ?? 0, 1),
            'pontualidade' => round(Voo::whereNotNull('nota_pontualidade')->avg('nota_pontualidade') ?? 0, 1),
            'servicos' => round(Voo::whereNotNull('nota_servicos')->avg('nota_servicos') ?? 0, 1),
            'patio' => round(Voo::whereNotNull('nota_patio')->avg('nota_patio') ?? 0, 1),
        ];
        
        // Melhores Companhias (usando os nomes corretos dos campos)
        $melhoresCompanhias = $this->getMelhoresCompanhias();
        
        // Melhores Modelos (usando os nomes corretos dos campos)
        $melhoresModelos = $this->getMelhoresModelos();
        
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
    
    /**
     * Get best companies by category (mínimo de 3 voos para considerar)
     */
    private function getMelhoresCompanhias()
    {
        // CORREÇÃO: Mapear os nomes corretos dos campos do banco
        $categorias = [
            'objetivo' => 'nota_obj',
            'pontualidade' => 'nota_pontualidade',
            'servicos' => 'nota_servicos',
            'patio' => 'nota_patio'
        ];
        
        $melhores = [];
        
        foreach ($categorias as $displayName => $dbField) {
            $melhor = CompanhiaAerea::select('companhias_aereas.nome')
                ->join('voos', 'companhias_aereas.id', '=', 'voos.companhia_aerea_id')
                ->whereNotNull("voos.{$dbField}")
                ->groupBy('companhias_aereas.id', 'companhias_aereas.nome')
                ->havingRaw('COUNT(*) >= 3')  // Mínimo de 3 voos para considerar
                ->orderByRaw('AVG(voos.' . $dbField . ') DESC')
                ->first();
            
            $melhores[$displayName] = $melhor ? $melhor->nome : 'N/A';
        }
        
        return $melhores;
    }
    
    /**
     * Get best aircraft models by category (mínimo de 3 voos para considerar)
     */
    private function getMelhoresModelos()
    {
        // CORREÇÃO: Mapear os nomes corretos dos campos do banco
        $categorias = [
            'objetivo' => 'nota_obj',
            'pontualidade' => 'nota_pontualidade',
            'servicos' => 'nota_servicos',
            'patio' => 'nota_patio'
        ];
        
        $melhores = [];
        
        foreach ($categorias as $displayName => $dbField) {
            $melhor = Aeronave::select('aeronaves.modelo')
                ->join('voos', 'aeronaves.id', '=', 'voos.aeronave_id')
                ->whereNotNull("voos.{$dbField}")
                ->groupBy('aeronaves.id', 'aeronaves.modelo')
                ->havingRaw('COUNT(*) >= 3')  // Mínimo de 3 voos para considerar
                ->orderByRaw('AVG(voos.' . $dbField . ') DESC')
                ->first();
            
            $melhores[$displayName] = $melhor ? $melhor->modelo : 'N/A';
        }
        
        return $melhores;
    }
}