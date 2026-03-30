<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model
{
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
        
        // ALTERAR: usar total_passageiros
        $stats['passageiros_total'] = DB::table('voos')->sum('total_passageiros') ?? 0;
        
        return $stats;
    }
    
    /**
     * Get average ratings
     */
    public function getMediasNotas()
    {
        $medias = [];
        
        // Média de objetivo (nota_obj)
        $medias['objetivo'] = DB::table('voos')->avg('nota_obj') ?? 0;
        
        // Média de pontualidade (nota_pontualidade)
        $medias['pontualidade'] = DB::table('voos')->avg('nota_pontualidade') ?? 0;
        
        // Média de serviços (nota_servicos)
        $medias['servicos'] = DB::table('voos')->avg('nota_servicos') ?? 0;
        
        // Média de patio (nota_patio)
        $medias['patio'] = DB::table('voos')->avg('nota_patio') ?? 0;
        
        return $medias;
    }
    
    /**
     * Get best companies by category
     */
    public function getMelhoresCompanhias()
    {
        $melhores = [];
        
        // Melhor companhia por objetivo
        $melhorObjetivo = DB::table('voos')
            ->join('companhias_aereas', 'voos.companhia_aerea_id', '=', 'companhias_aereas.id')
            ->select('companhias_aereas.nome', DB::raw('AVG(voos.nota_obj) as media'))
            ->whereNotNull('voos.nota_obj')
            ->groupBy('companhias_aereas.id', 'companhias_aereas.nome')
            ->orderBy('media', 'DESC')
            ->first();
            
        $melhores['objetivo'] = $melhorObjetivo ? $melhorObjetivo->nome : 'N/A';
        
        // Melhor companhia por pontualidade
        $melhorPontualidade = DB::table('voos')
            ->join('companhias_aereas', 'voos.companhia_aerea_id', '=', 'companhias_aereas.id')
            ->select('companhias_aereas.nome', DB::raw('AVG(voos.nota_pontualidade) as media'))
            ->whereNotNull('voos.nota_pontualidade')
            ->groupBy('companhias_aereas.id', 'companhias_aereas.nome')
            ->orderBy('media', 'DESC')
            ->first();
            
        $melhores['pontualidade'] = $melhorPontualidade ? $melhorPontualidade->nome : 'N/A';
        
        // Melhor companhia por serviços
        $melhorServicos = DB::table('voos')
            ->join('companhias_aereas', 'voos.companhia_aerea_id', '=', 'companhias_aereas.id')
            ->select('companhias_aereas.nome', DB::raw('AVG(voos.nota_servicos) as media'))
            ->whereNotNull('voos.nota_servicos')
            ->groupBy('companhias_aereas.id', 'companhias_aereas.nome')
            ->orderBy('media', 'DESC')
            ->first();
            
        $melhores['servicos'] = $melhorServicos ? $melhorServicos->nome : 'N/A';
        
        // Melhor companhia por patio
        $melhorPatio = DB::table('voos')
            ->join('companhias_aereas', 'voos.companhia_aerea_id', '=', 'companhias_aereas.id')
            ->select('companhias_aereas.nome', DB::raw('AVG(voos.nota_patio) as media'))
            ->whereNotNull('voos.nota_patio')
            ->groupBy('companhias_aereas.id', 'companhias_aereas.nome')
            ->orderBy('media', 'DESC')
            ->first();
            
        $melhores['patio'] = $melhorPatio ? $melhorPatio->nome : 'N/A';
        
        return $melhores;
    }
    
    /**
     * Get best models by rating
     */
    public function getMelhoresModelos()
    {
        $melhores = [];
        
        // Melhor modelo por objetivo
        $melhorObjetivo = DB::table('voos')
            ->join('aeronaves', 'voos.aeronave_id', '=', 'aeronaves.id')
            ->select('aeronaves.modelo', DB::raw('AVG(voos.nota_obj) as media'))
            ->whereNotNull('voos.nota_obj')
            ->groupBy('aeronaves.modelo')
            ->orderBy('media', 'DESC')
            ->first();
            
        $melhores['objetivo'] = $melhorObjetivo ? $melhorObjetivo->modelo : 'N/A';
        
        // Melhor modelo por pontualidade
        $melhorPontualidade = DB::table('voos')
            ->join('aeronaves', 'voos.aeronave_id', '=', 'aeronaves.id')
            ->select('aeronaves.modelo', DB::raw('AVG(voos.nota_pontualidade) as media'))
            ->whereNotNull('voos.nota_pontualidade')
            ->groupBy('aeronaves.modelo')
            ->orderBy('media', 'DESC')
            ->first();
            
        $melhores['pontualidade'] = $melhorPontualidade ? $melhorPontualidade->modelo : 'N/A';
        
        // Melhor modelo por serviços
        $melhorServicos = DB::table('voos')
            ->join('aeronaves', 'voos.aeronave_id', '=', 'aeronaves.id')
            ->select('aeronaves.modelo', DB::raw('AVG(voos.nota_servicos) as media'))
            ->whereNotNull('voos.nota_servicos')
            ->groupBy('aeronaves.modelo')
            ->orderBy('media', 'DESC')
            ->first();
            
        $melhores['servicos'] = $melhorServicos ? $melhorServicos->modelo : 'N/A';
        
        // Melhor modelo por patio
        $melhorPatio = DB::table('voos')
            ->join('aeronaves', 'voos.aeronave_id', '=', 'aeronaves.id')
            ->select('aeronaves.modelo', DB::raw('AVG(voos.nota_patio) as media'))
            ->whereNotNull('voos.nota_patio')
            ->groupBy('aeronaves.modelo')
            ->orderBy('media', 'DESC')
            ->first();
            
        $melhores['patio'] = $melhorPatio ? $melhorPatio->modelo : 'N/A';
        
        return $melhores;
    }

    /**
     * Get flights by time period
     */
    public function getVoosPorHorario()
    {
        $voos = DB::table('voos')
            ->select('horario_voo', DB::raw('SUM(qtd_voos) as total_voos'))  // ALTERADO: COUNT para SUM
            ->groupBy('horario_voo')
            ->pluck('total_voos', 'horario_voo')
            ->toArray();
            
        // Garantir que todos os períodos existam no array
        $periodos = ['EAM', 'AM', 'AN', 'PM', 'ALL'];
        foreach ($periodos as $periodo) {
            if (!isset($voos[$periodo])) {
                $voos[$periodo] = 0;
            }
        }
        
        return $voos;
    }

    /**
     * Get passengers by time period
     */
    public function getPassageirosPorHorario()
    {
        $passageiros = DB::table('voos')
            ->select('horario_voo', DB::raw('SUM(total_passageiros) as total_passageiros'))  // ALTERADO: qtd_passageiros para total_passageiros
            ->groupBy('horario_voo')
            ->pluck('total_passageiros', 'horario_voo')
            ->toArray();
            
        // Garantir que todos os períodos existam no array
        $periodos = ['EAM', 'AM', 'AN', 'PM', 'ALL'];
        foreach ($periodos as $periodo) {
            if (!isset($passageiros[$periodo])) {
                $passageiros[$periodo] = 0;
            }
        }
        
        return $passageiros;
    }

    /**
     * Get flights by type
     */
    public function getVoosPorTipo()
    {
        $voos = DB::table('voos')
            ->select('tipo_voo', DB::raw('SUM(qtd_voos) as total_voos'))  
            ->groupBy('tipo_voo')
            ->pluck('total_voos', 'tipo_voo')
            ->toArray();
            
        // Garantir que os tipos existam no array
        $tipos = ['Regular', 'Charter'];
        foreach ($tipos as $tipo) {
            if (!isset($voos[$tipo])) {
                $voos[$tipo] = 0;
            }
        }
        
        return $voos;
    }

    /**
     * Get passengers by type
     */
    public function getPassageirosPorTipo()
    {
        $passageiros = DB::table('voos')
            ->select('tipo_voo', DB::raw('SUM(total_passageiros) as total_passageiros'))  // ALTERADO: qtd_passageiros para total_passageiros
            ->groupBy('tipo_voo')
            ->pluck('total_passageiros', 'tipo_voo')
            ->toArray();
            
        // Garantir que os tipos existam no array
        $tipos = ['Regular', 'Charter'];
        foreach ($tipos as $tipo) {
            if (!isset($passageiros[$tipo])) {
                $passageiros[$tipo] = 0;
            }
        }
        
        return $passageiros;
    }

    /**
     * Get flights by aircraft type
     */
    public function getVoosPorTipoAeronave()
    {
        $voos = DB::table('voos')
            ->select('tipo_aeronave', DB::raw('SUM(qtd_voos) as total_voos'))  // ALTERADO: COUNT para SUM
            ->whereNotNull('tipo_aeronave')
            ->groupBy('tipo_aeronave')
            ->pluck('total_voos', 'tipo_aeronave')
            ->toArray();
            
        // Garantir que os tipos existam no array
        $tipos = ['PC', 'MC', 'LC'];
        foreach ($tipos as $tipo) {
            if (!isset($voos[$tipo])) {
                $voos[$tipo] = 0;
            }
        }
        
        return $voos;
    }

    /**
     * Get passengers by aircraft type
     */
    public function getPassageirosPorTipoAeronave()
    {
        $passageiros = DB::table('voos')
            ->select('tipo_aeronave', DB::raw('SUM(total_passageiros) as total_passageiros'))  // ALTERADO: qtd_passageiros para total_passageiros
            ->whereNotNull('tipo_aeronave')  // Mantenha ou remova conforme necessidade
            ->groupBy('tipo_aeronave')
            ->pluck('total_passageiros', 'tipo_aeronave')
            ->toArray();
            
        // Garantir que os tipos existam no array
        $tipos = ['PC', 'MC', 'LC'];
        foreach ($tipos as $tipo) {
            if (!isset($passageiros[$tipo])) {
                $passageiros[$tipo] = 0;
            }
        }
        
        return $passageiros;
    }
}