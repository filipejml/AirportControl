<?php
// app/Models/Home.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Home extends Model
{
    /**
     * Get total passengers
     */
    public function getTotalPassageiros()
    {
        // Usar a coluna virtual que já calcula qtd_voos * qtd_passageiros
        return DB::table('voos')->sum('total_passageiros') ?? 0;
    }
    
    /**
     * Get passengers by airport
     */
    public function getPassageirosPorAeroporto()
    {
        $passageiros = DB::table('voos')
            ->join('aeroportos', 'voos.aeroporto_id', '=', 'aeroportos.id')
            ->select('aeroportos.nome_aeroporto as aeroporto', DB::raw('SUM(voos.total_passageiros) as total_passageiros'))
            ->groupBy('aeroportos.id', 'aeroportos.nome_aeroporto')
            ->orderByDesc('total_passageiros')
            ->pluck('total_passageiros', 'aeroporto')
            ->toArray();
            
        return $passageiros;
    }
    
    /**
     * Get passengers by time period
     */
    public function getPassageirosPorHorario()
    {
        $horarios = ['EAM', 'AM', 'AN', 'PM', 'ALL'];
        
        $dados = DB::table('voos')
            ->select('horario_voo', DB::raw('SUM(total_passageiros) as total_passageiros'))
            ->whereNotNull('horario_voo')
            ->groupBy('horario_voo')
            ->get();
        
        $passageirosPorHorario = [];
        foreach ($horarios as $horario) {
            $passageirosPorHorario[$horario] = 0;
        }
        
        foreach ($dados as $item) {
            if (isset($passageirosPorHorario[$item->horario_voo])) {
                $passageirosPorHorario[$item->horario_voo] = (int) $item->total_passageiros;
            }
        }
        
        return $passageirosPorHorario;
    }
    
    /**
     * Get flights by airport
     */
    public function getVoosPorAeroporto()
    {
        $voos = DB::table('voos')
            ->join('aeroportos', 'voos.aeroporto_id', '=', 'aeroportos.id')
            ->select('aeroportos.nome_aeroporto as aeroporto', DB::raw('SUM(voos.qtd_voos) as total_voos'))
            ->groupBy('aeroportos.id', 'aeroportos.nome_aeroporto')
            ->orderByDesc('total_voos')
            ->pluck('total_voos', 'aeroporto')
            ->toArray();
            
        return $voos;
    }
    
    /**
     * Get average ratings
     */
    public function getMediasNotas()
    {
        $medias = [];
        
        $medias['objetivo'] = DB::table('voos')->whereNotNull('nota_obj')->avg('nota_obj') ?? 0;
        $medias['pontualidade'] = DB::table('voos')->whereNotNull('nota_pontualidade')->avg('nota_pontualidade') ?? 0;
        $medias['servicos'] = DB::table('voos')->whereNotNull('nota_servicos')->avg('nota_servicos') ?? 0;
        $medias['patio'] = DB::table('voos')->whereNotNull('nota_patio')->avg('nota_patio') ?? 0;
        
        return $medias;
    }
    
    /**
     * Get best companies by category
     */
    public function getMelhoresCompanhias()
    {
        $melhores = [];
        
        // Objetivo
        $melhorObjetivo = DB::table('voos')
            ->join('companhias_aereas', 'voos.companhia_aerea_id', '=', 'companhias_aereas.id')
            ->select('companhias_aereas.nome as companhia', DB::raw('AVG(voos.nota_obj) as media'))
            ->whereNotNull('voos.nota_obj')
            ->groupBy('companhias_aereas.id', 'companhias_aereas.nome')
            ->orderByDesc('media')
            ->first();
        $melhores['objetivo'] = $melhorObjetivo ? $melhorObjetivo->companhia : null;
        
        // Pontualidade
        $melhorPontualidade = DB::table('voos')
            ->join('companhias_aereas', 'voos.companhia_aerea_id', '=', 'companhias_aereas.id')
            ->select('companhias_aereas.nome as companhia', DB::raw('AVG(voos.nota_pontualidade) as media'))
            ->whereNotNull('voos.nota_pontualidade')
            ->groupBy('companhias_aereas.id', 'companhias_aereas.nome')
            ->orderByDesc('media')
            ->first();
        $melhores['pontualidade'] = $melhorPontualidade ? $melhorPontualidade->companhia : null;
        
        // Serviços
        $melhorServicos = DB::table('voos')
            ->join('companhias_aereas', 'voos.companhia_aerea_id', '=', 'companhias_aereas.id')
            ->select('companhias_aereas.nome as companhia', DB::raw('AVG(voos.nota_servicos) as media'))
            ->whereNotNull('voos.nota_servicos')
            ->groupBy('companhias_aereas.id', 'companhias_aereas.nome')
            ->orderByDesc('media')
            ->first();
        $melhores['servicos'] = $melhorServicos ? $melhorServicos->companhia : null;
        
        // Pátio
        $melhorPatio = DB::table('voos')
            ->join('companhias_aereas', 'voos.companhia_aerea_id', '=', 'companhias_aereas.id')
            ->select('companhias_aereas.nome as companhia', DB::raw('AVG(voos.nota_patio) as media'))
            ->whereNotNull('voos.nota_patio')
            ->groupBy('companhias_aereas.id', 'companhias_aereas.nome')
            ->orderByDesc('media')
            ->first();
        $melhores['patio'] = $melhorPatio ? $melhorPatio->companhia : null;
        
        return $melhores;
    }
    
    /**
     * Get best aircraft models by category
     */
    public function getMelhoresModelos()
    {
        $melhores = [];
        
        // Objetivo
        $melhorObjetivo = DB::table('voos')
            ->join('aeronaves', 'voos.aeronave_id', '=', 'aeronaves.id')
            ->select('aeronaves.modelo as modelo', DB::raw('AVG(voos.nota_obj) as media'))
            ->whereNotNull('voos.nota_obj')
            ->groupBy('aeronaves.id', 'aeronaves.modelo')
            ->orderByDesc('media')
            ->first();
        $melhores['objetivo'] = $melhorObjetivo ? $melhorObjetivo->modelo : null;
        
        // Pontualidade
        $melhorPontualidade = DB::table('voos')
            ->join('aeronaves', 'voos.aeronave_id', '=', 'aeronaves.id')
            ->select('aeronaves.modelo as modelo', DB::raw('AVG(voos.nota_pontualidade) as media'))
            ->whereNotNull('voos.nota_pontualidade')
            ->groupBy('aeronaves.id', 'aeronaves.modelo')
            ->orderByDesc('media')
            ->first();
        $melhores['pontualidade'] = $melhorPontualidade ? $melhorPontualidade->modelo : null;
        
        // Serviços
        $melhorServicos = DB::table('voos')
            ->join('aeronaves', 'voos.aeronave_id', '=', 'aeronaves.id')
            ->select('aeronaves.modelo as modelo', DB::raw('AVG(voos.nota_servicos) as media'))
            ->whereNotNull('voos.nota_servicos')
            ->groupBy('aeronaves.id', 'aeronaves.modelo')
            ->orderByDesc('media')
            ->first();
        $melhores['servicos'] = $melhorServicos ? $melhorServicos->modelo : null;
        
        // Pátio
        $melhorPatio = DB::table('voos')
            ->join('aeronaves', 'voos.aeronave_id', '=', 'aeronaves.id')
            ->select('aeronaves.modelo as modelo', DB::raw('AVG(voos.nota_patio) as media'))
            ->whereNotNull('voos.nota_patio')
            ->groupBy('aeronaves.id', 'aeronaves.modelo')
            ->orderByDesc('media')
            ->first();
        $melhores['patio'] = $melhorPatio ? $melhorPatio->modelo : null;
        
        return $melhores;
    }
    
    /**
     * Get general statistics
     */
    public function getEstatisticasGerais()
    {
        $stats = [];
        
        $stats['companhias'] = DB::table('companhias_aereas')->count();
        $stats['modelos'] = DB::table('aeronaves')->distinct('modelo')->count('modelo');
        $stats['aeroportos'] = DB::table('aeroportos')->count();
        
        // ALTERAR: somar qtd_voos em vez de contar registros
        $stats['voos'] = DB::table('voos')->sum('qtd_voos') ?? 0;
        
        $stats['passageiros_total'] = DB::table('voos')->sum('total_passageiros') ?? 0;
        
        return $stats;
    }
}