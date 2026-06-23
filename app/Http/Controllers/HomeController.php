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
        $voosStats = Voo::query()
            ->selectRaw('COALESCE(SUM(qtd_voos), 0) as total_voos')
            ->selectRaw('COALESCE(SUM(total_passageiros), 0) as total_passageiros')
            ->first();

        // Estatísticas Gerais
        $stats = [
            'companhias' => CompanhiaAerea::count(),
            'modelos' => Aeronave::distinct('modelo')->count('modelo'),
            'aeroportos' => Aeroporto::count(),
            'voos' => (int) ($voosStats->total_voos ?? 0),
            'passageiros_total' => (int) ($voosStats->total_passageiros ?? 0),
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
        $passageirosPorHorario = Voo::select('horario_voo', DB::raw('SUM(total_passageiros) as total_passageiros'))
            ->whereIn('horario_voo', $horarios)
            ->groupBy('horario_voo')
            ->pluck('total_passageiros', 'horario_voo')
            ->toArray();
        $passageirosPorHorario = array_replace(array_fill_keys($horarios, 0), $passageirosPorHorario);
        
        // Voos por Aeroporto
        $voosPorAeroporto = Voo::select('aeroportos.nome_aeroporto', DB::raw('SUM(voos.qtd_voos) as total'))
            ->join('aeroportos', 'voos.aeroporto_id', '=', 'aeroportos.id')
            ->groupBy('aeroportos.id', 'aeroportos.nome_aeroporto')
            ->orderByDesc('total')
            ->pluck('total', 'aeroportos.nome_aeroporto')
            ->toArray();
        
        // Médias das Notas (usando os nomes corretos dos campos)
        $medias = Voo::query()
            ->selectRaw('COALESCE(SUM(qtd_voos * nota_obj) / NULLIF(SUM(CASE WHEN nota_obj IS NOT NULL THEN qtd_voos ELSE 0 END), 0), 0) as objetivo')
            ->selectRaw('COALESCE(SUM(qtd_voos * nota_pontualidade) / NULLIF(SUM(CASE WHEN nota_pontualidade IS NOT NULL THEN qtd_voos ELSE 0 END), 0), 0) as pontualidade')
            ->selectRaw('COALESCE(SUM(qtd_voos * nota_servicos) / NULLIF(SUM(CASE WHEN nota_servicos IS NOT NULL THEN qtd_voos ELSE 0 END), 0), 0) as servicos')
            ->selectRaw('COALESCE(SUM(qtd_voos * nota_patio) / NULLIF(SUM(CASE WHEN nota_patio IS NOT NULL THEN qtd_voos ELSE 0 END), 0), 0) as patio')
            ->first();
        $mediasNotas = [
            'objetivo' => round((float) ($medias->objetivo ?? 0), 1),
            'pontualidade' => round((float) ($medias->pontualidade ?? 0), 1),
            'servicos' => round((float) ($medias->servicos ?? 0), 1),
            'patio' => round((float) ($medias->patio ?? 0), 1),
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
                ->havingRaw('SUM(voos.qtd_voos) >= 3')
                ->orderByRaw(
                    'SUM(voos.qtd_voos * voos.' . $dbField . ') / SUM(voos.qtd_voos) DESC'
                )
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
                ->havingRaw('SUM(voos.qtd_voos) >= 3')
                ->orderByRaw(
                    'SUM(voos.qtd_voos * voos.' . $dbField . ') / SUM(voos.qtd_voos) DESC'
                )
                ->first();
            
            $melhores[$displayName] = $melhor ? $melhor->modelo : 'N/A';
        }
        
        return $melhores;
    }
}
