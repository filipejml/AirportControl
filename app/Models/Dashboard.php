<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model
{
    private const CATEGORIAS_NOTA = [
        'objetivo' => 'nota_obj',
        'pontualidade' => 'nota_pontualidade',
        'servicos' => 'nota_servicos',
        'patio' => 'nota_patio',
    ];

    /**
     * Get general statistics
     */
    public function getEstatisticasGerais()
    {
        $stats = [];
        $voosStats = DB::table('voos')
            ->selectRaw('COALESCE(SUM(qtd_voos), 0) as total_voos')
            ->selectRaw('COALESCE(SUM(total_passageiros), 0) as total_passageiros')
            ->first();
        
        $stats['companhias'] = DB::table('companhias_aereas')->count();
        $stats['modelos'] = DB::table('aeronaves')->distinct('modelo')->count('modelo');
        $stats['aeroportos'] = DB::table('aeroportos')->count();
        $stats['voos'] = (int) ($voosStats->total_voos ?? 0);
        $stats['passageiros_total'] = (int) ($voosStats->total_passageiros ?? 0);
        
        return $stats;
    }
    
    /**
     * Get average ratings
     */
    public function getMediasNotas()
    {
        $medias = DB::table('voos')
            ->selectRaw('COALESCE(SUM(qtd_voos * nota_obj) / NULLIF(SUM(CASE WHEN nota_obj IS NOT NULL THEN qtd_voos ELSE 0 END), 0), 0) as objetivo')
            ->selectRaw('COALESCE(SUM(qtd_voos * nota_pontualidade) / NULLIF(SUM(CASE WHEN nota_pontualidade IS NOT NULL THEN qtd_voos ELSE 0 END), 0), 0) as pontualidade')
            ->selectRaw('COALESCE(SUM(qtd_voos * nota_servicos) / NULLIF(SUM(CASE WHEN nota_servicos IS NOT NULL THEN qtd_voos ELSE 0 END), 0), 0) as servicos')
            ->selectRaw('COALESCE(SUM(qtd_voos * nota_patio) / NULLIF(SUM(CASE WHEN nota_patio IS NOT NULL THEN qtd_voos ELSE 0 END), 0), 0) as patio')
            ->first();

        return [
            'objetivo' => (float) ($medias->objetivo ?? 0),
            'pontualidade' => (float) ($medias->pontualidade ?? 0),
            'servicos' => (float) ($medias->servicos ?? 0),
            'patio' => (float) ($medias->patio ?? 0),
        ];
    }
    
    /**
     * Get best companies by category
     */
    public function getMelhoresCompanhias()
    {
        $melhores = [];

        foreach (self::CATEGORIAS_NOTA as $categoria => $campoNota) {
            $melhores[$categoria] = $this->buscarMelhorAgrupado(
                'companhias_aereas',
                'voos.companhia_aerea_id',
                'companhias_aereas.id',
                'companhias_aereas.nome',
                $campoNota
            );
        }

        return $melhores;
        
        // Melhor companhia por objetivo
        $melhorObjetivo = DB::table('voos')
            ->join('companhias_aereas', 'voos.companhia_aerea_id', '=', 'companhias_aereas.id')
            ->select('companhias_aereas.nome', DB::raw('SUM(voos.qtd_voos * voos.nota_obj) / SUM(voos.qtd_voos) as media'))
            ->whereNotNull('voos.nota_obj')
            ->groupBy('companhias_aereas.id', 'companhias_aereas.nome')
            ->havingRaw('SUM(voos.qtd_voos) >= 3')
            ->orderBy('media', 'DESC')
            ->first();
            
        $melhores['objetivo'] = $melhorObjetivo ? $melhorObjetivo->nome : 'N/A';
        
        // Melhor companhia por pontualidade
        $melhorPontualidade = DB::table('voos')
            ->join('companhias_aereas', 'voos.companhia_aerea_id', '=', 'companhias_aereas.id')
            ->select('companhias_aereas.nome', DB::raw('SUM(voos.qtd_voos * voos.nota_pontualidade) / SUM(voos.qtd_voos) as media'))
            ->whereNotNull('voos.nota_pontualidade')
            ->groupBy('companhias_aereas.id', 'companhias_aereas.nome')
            ->havingRaw('SUM(voos.qtd_voos) >= 3')
            ->orderBy('media', 'DESC')
            ->first();
            
        $melhores['pontualidade'] = $melhorPontualidade ? $melhorPontualidade->nome : 'N/A';
        
        // Melhor companhia por serviços
        $melhorServicos = DB::table('voos')
            ->join('companhias_aereas', 'voos.companhia_aerea_id', '=', 'companhias_aereas.id')
            ->select('companhias_aereas.nome', DB::raw('SUM(voos.qtd_voos * voos.nota_servicos) / SUM(voos.qtd_voos) as media'))
            ->whereNotNull('voos.nota_servicos')
            ->groupBy('companhias_aereas.id', 'companhias_aereas.nome')
            ->havingRaw('SUM(voos.qtd_voos) >= 3')
            ->orderBy('media', 'DESC')
            ->first();
            
        $melhores['servicos'] = $melhorServicos ? $melhorServicos->nome : 'N/A';
        
        // Melhor companhia por patio
        $melhorPatio = DB::table('voos')
            ->join('companhias_aereas', 'voos.companhia_aerea_id', '=', 'companhias_aereas.id')
            ->select('companhias_aereas.nome', DB::raw('SUM(voos.qtd_voos * voos.nota_patio) / SUM(voos.qtd_voos) as media'))
            ->whereNotNull('voos.nota_patio')
            ->groupBy('companhias_aereas.id', 'companhias_aereas.nome')
            ->havingRaw('SUM(voos.qtd_voos) >= 3')
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

        foreach (self::CATEGORIAS_NOTA as $categoria => $campoNota) {
            $melhores[$categoria] = $this->buscarMelhorAgrupado(
                'aeronaves',
                'voos.aeronave_id',
                'aeronaves.id',
                'aeronaves.modelo',
                $campoNota
            );
        }

        return $melhores;
        
        // Melhor modelo por objetivo
        $melhorObjetivo = DB::table('voos')
            ->join('aeronaves', 'voos.aeronave_id', '=', 'aeronaves.id')
            ->select('aeronaves.modelo', DB::raw('SUM(voos.qtd_voos * voos.nota_obj) / SUM(voos.qtd_voos) as media'))
            ->whereNotNull('voos.nota_obj')
            ->groupBy('aeronaves.modelo')
            ->havingRaw('SUM(voos.qtd_voos) >= 3')
            ->orderBy('media', 'DESC')
            ->first();
            
        $melhores['objetivo'] = $melhorObjetivo ? $melhorObjetivo->modelo : 'N/A';
        
        // Melhor modelo por pontualidade
        $melhorPontualidade = DB::table('voos')
            ->join('aeronaves', 'voos.aeronave_id', '=', 'aeronaves.id')
            ->select('aeronaves.modelo', DB::raw('SUM(voos.qtd_voos * voos.nota_pontualidade) / SUM(voos.qtd_voos) as media'))
            ->whereNotNull('voos.nota_pontualidade')
            ->groupBy('aeronaves.modelo')
            ->havingRaw('SUM(voos.qtd_voos) >= 3')
            ->orderBy('media', 'DESC')
            ->first();
            
        $melhores['pontualidade'] = $melhorPontualidade ? $melhorPontualidade->modelo : 'N/A';
        
        // Melhor modelo por serviços
        $melhorServicos = DB::table('voos')
            ->join('aeronaves', 'voos.aeronave_id', '=', 'aeronaves.id')
            ->select('aeronaves.modelo', DB::raw('SUM(voos.qtd_voos * voos.nota_servicos) / SUM(voos.qtd_voos) as media'))
            ->whereNotNull('voos.nota_servicos')
            ->groupBy('aeronaves.modelo')
            ->havingRaw('SUM(voos.qtd_voos) >= 3')
            ->orderBy('media', 'DESC')
            ->first();
            
        $melhores['servicos'] = $melhorServicos ? $melhorServicos->modelo : 'N/A';
        
        // Melhor modelo por patio
        $melhorPatio = DB::table('voos')
            ->join('aeronaves', 'voos.aeronave_id', '=', 'aeronaves.id')
            ->select('aeronaves.modelo', DB::raw('SUM(voos.qtd_voos * voos.nota_patio) / SUM(voos.qtd_voos) as media'))
            ->whereNotNull('voos.nota_patio')
            ->groupBy('aeronaves.modelo')
            ->havingRaw('SUM(voos.qtd_voos) >= 3')
            ->orderBy('media', 'DESC')
            ->first();
            
        $melhores['patio'] = $melhorPatio ? $melhorPatio->modelo : 'N/A';
        
        return $melhores;
    }

    private function buscarMelhorAgrupado(
        string $tabela,
        string $vooJoinColumn,
        string $tabelaJoinColumn,
        string $nomeColumn,
        string $campoNota
    ): string {
        $melhor = DB::table('voos')
            ->join($tabela, $vooJoinColumn, '=', $tabelaJoinColumn)
            ->selectRaw("{$nomeColumn} as nome")
            ->selectRaw("SUM(voos.qtd_voos * voos.{$campoNota}) / NULLIF(SUM(voos.qtd_voos), 0) as media")
            ->whereNotNull("voos.{$campoNota}")
            ->groupBy($tabelaJoinColumn, $nomeColumn)
            ->havingRaw('SUM(voos.qtd_voos) >= 3')
            ->orderByDesc('media')
            ->first();

        return $melhor ? $melhor->nome : 'N/A';
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
