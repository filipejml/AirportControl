<?php
// app/Repositories/AeroportoRepository.php

namespace App\Repositories;

use App\Models\Aeroporto;
use App\Models\CompanhiaAerea;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AeroportoRepository
{
    /**
     * Busca aeroportos com estatísticas básicas para a view de informações
     */
    public function getAeroportosComEstatisticas($anoSelecionado)
    {
        $aeroportos = Aeroporto::with(['companhias', 'voos.companhiaAerea'])
            ->withCount('companhias')
            ->get();
        
        foreach ($aeroportos as $aeroporto) {
            $aeroporto->total_voos = $aeroporto->voos()
                ->whereYear('created_at', $anoSelecionado)
                ->sum('qtd_voos');
                
            $aeroporto->total_passageiros = $aeroporto->voos()
                ->whereYear('created_at', $anoSelecionado)
                ->sum('total_passageiros');
                
            $aeroporto->media_passageiros_por_voo = $aeroporto->total_voos > 0 
                ? $aeroporto->total_passageiros / $aeroporto->total_voos 
                : 0;
        }
        
        return $aeroportos;
    }
    
    /**
     * Busca médias das notas de um aeroporto (ponderadas por quantidade de voos)
     */
    public function getMediasNotas($aeroportoId, $ano = null)
    {
        $query = DB::table('voos')
            ->where('aeroporto_id', $aeroportoId)
            ->whereNotNull('nota_obj');
            
        if ($ano) {
            $query->whereYear('created_at', $ano);
        }
        
        $notaObj = $query->clone()
            ->select(DB::raw('SUM(qtd_voos * nota_obj) / SUM(qtd_voos) as media'))
            ->value('media') ?? 0;
            
        $notaPontualidade = $query->clone()
            ->whereNotNull('nota_pontualidade')
            ->select(DB::raw('SUM(qtd_voos * nota_pontualidade) / SUM(qtd_voos) as media'))
            ->value('media') ?? 0;
            
        $notaServicos = $query->clone()
            ->whereNotNull('nota_servicos')
            ->select(DB::raw('SUM(qtd_voos * nota_servicos) / SUM(qtd_voos) as media'))
            ->value('media') ?? 0;
            
        $notaPatio = $query->clone()
            ->whereNotNull('nota_patio')
            ->select(DB::raw('SUM(qtd_voos * nota_patio) / SUM(qtd_voos) as media'))
            ->value('media') ?? 0;
        
        return [
            'nota_obj' => $notaObj,
            'nota_pontualidade' => $notaPontualidade,
            'nota_servicos' => $notaServicos,
            'nota_patio' => $notaPatio,
            'media_geral' => $this->calcularMediaGeral($notaObj, $notaPontualidade, $notaServicos, $notaPatio)
        ];
    }
    
    /**
     * Busca voos por companhia para um aeroporto
     */
    public function getVoosPorCompanhia($aeroportoId)
    {
        return DB::table('voos')
            ->join('companhias_aereas', 'voos.companhia_aerea_id', '=', 'companhias_aereas.id')
            ->where('voos.aeroporto_id', $aeroportoId)
            ->select('companhias_aereas.nome as companhia', DB::raw('SUM(voos.qtd_voos) as total_voos'))
            ->groupBy('companhias_aereas.id', 'companhias_aereas.nome')
            ->orderByDesc('total_voos')
            ->get();
    }
    
    /**
     * Busca passageiros por companhia para um aeroporto
     */
    public function getPassageirosPorCompanhia($aeroportoId)
    {
        return DB::table('voos')
            ->join('companhias_aereas', 'voos.companhia_aerea_id', '=', 'companhias_aereas.id')
            ->where('voos.aeroporto_id', $aeroportoId)
            ->select('companhias_aereas.nome as companhia', DB::raw('SUM(voos.total_passageiros) as total_passageiros'))
            ->groupBy('companhias_aereas.id', 'companhias_aereas.nome')
            ->orderByDesc('total_passageiros')
            ->get();
    }
    
    /**
     * Busca voos agrupados por horário
     */
    public function getVoosPorHorario($aeroportoId)
    {
        $horariosMap = [
            'EAM' => 'Early Morning',
            'AM' => 'Morning', 
            'AN' => 'Afternoon',
            'PM' => 'Evening',
            'ALL' => 'All Day'
        ];
        
        $voosPorHorario = DB::table('voos')
            ->where('aeroporto_id', $aeroportoId)
            ->select('horario_voo', DB::raw('SUM(qtd_voos) as total_voos'))
            ->whereNotNull('horario_voo')
            ->groupBy('horario_voo')
            ->get();
        
        $horariosData = [];
        foreach ($horariosMap as $key => $label) {
            $found = $voosPorHorario->firstWhere('horario_voo', $key);
            $horariosData[$label] = $found ? $found->total_voos : 0;
        }
        
        return $horariosData;
    }
    
    /**
     * Busca voos agrupados por tipo
     */
    public function getVoosPorTipo($aeroportoId)
    {
        $voosPorTipo = DB::table('voos')
            ->where('aeroporto_id', $aeroportoId)
            ->select('tipo_voo', DB::raw('SUM(qtd_voos) as total_voos'))
            ->whereNotNull('tipo_voo')
            ->groupBy('tipo_voo')
            ->get();
        
        $tiposData = ['Regular' => 0, 'Charter' => 0];
        
        foreach ($voosPorTipo as $item) {
            if (isset($tiposData[$item->tipo_voo])) {
                $tiposData[$item->tipo_voo] = $item->total_voos;
            }
        }
        
        return $tiposData;
    }
    
    /**
     * Busca top companhias melhor avaliadas
     */
    public function getTopCompanhiasAvaliadas($aeroportoId, $limite = 5)
    {
        return DB::table('voos')
            ->join('companhias_aereas', 'voos.companhia_aerea_id', '=', 'companhias_aereas.id')
            ->where('voos.aeroporto_id', $aeroportoId)
            ->select(
                'companhias_aereas.nome as companhia',
                DB::raw('AVG(voos.nota_obj) as nota_obj'),
                DB::raw('AVG(voos.nota_pontualidade) as nota_pontualidade'),
                DB::raw('AVG(voos.nota_servicos) as nota_servicos'),
                DB::raw('AVG(voos.nota_patio) as nota_patio'),
                DB::raw('(AVG(voos.nota_obj) + AVG(voos.nota_pontualidade) + AVG(voos.nota_servicos) + AVG(voos.nota_patio)) / 4 as media_geral')
            )
            ->groupBy('companhias_aereas.id', 'companhias_aereas.nome')
            ->havingRaw('media_geral > 0')
            ->orderByDesc('media_geral')
            ->limit($limite)
            ->get();
    }
    
    /**
     * Busca evolução mensal dos últimos X meses
     */
    public function getEvolucaoMensal($aeroportoId, $meses = 12)
    {
        return DB::table('voos')
            ->where('aeroporto_id', $aeroportoId)
            ->where('created_at', '>=', now()->subMonths($meses))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mes'),
                DB::raw('SUM(qtd_voos) as total_voos'),
                DB::raw('SUM(total_passageiros) as total_passageiros')
            )
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();
    }
    
    /**
     * Busca melhores companhias por categoria para um aeroporto em um ano
     */
    public function getMelhoresCompanhiasPorCategoria($aeroportoId, $ano)
    {
        $categorias = [
            'Objetivo' => 'nota_obj',
            'Pontualidade' => 'nota_pontualidade',
            'Servicos' => 'nota_servicos',
            'Patio' => 'nota_patio'
        ];
        
        $melhores = [];
        
        foreach ($categorias as $categoria => $campoNota) {
            $melhorCompanhia = DB::table('voos')
                ->join('companhias_aereas', 'voos.companhia_aerea_id', '=', 'companhias_aereas.id')
                ->select(
                    'companhias_aereas.id',
                    'companhias_aereas.nome',
                    DB::raw('SUM(voos.qtd_voos * voos.' . $campoNota . ') / SUM(voos.qtd_voos) as media_nota')
                )
                ->where('voos.aeroporto_id', $aeroportoId)
                ->whereYear('voos.created_at', $ano)
                ->whereNotNull('voos.' . $campoNota)
                ->groupBy('companhias_aereas.id', 'companhias_aereas.nome')
                ->orderByDesc('media_nota')
                ->first();
            
            $melhores[$categoria] = $melhorCompanhia ? [
                'id' => $melhorCompanhia->id,
                'nome' => $melhorCompanhia->nome,
                'media' => $melhorCompanhia->media_nota ?? 0
            ] : null;
        }
        
        return $melhores;
    }
    
    /**
     * Busca dados semanais de passageiros para múltiplos aeroportos
     */
    public function getDadosPassageirosPorSemana($aeroportos, $ano)
    {
        $semanasComDados = [];
        $dadosPorAeroporto = [];
        
        $semanasExistentes = DB::table('voos')
            ->whereYear('created_at', $ano)
            ->select(DB::raw('YEARWEEK(created_at, 1) as semana_ano'), DB::raw('MIN(created_at) as data_referencia'))
            ->groupBy('semana_ano')
            ->orderBy('semana_ano', 'asc')
            ->get();
        
        if ($semanasExistentes->isEmpty()) {
            return ['semanas' => [], 'aeroportos' => []];
        }
        
        foreach ($semanasExistentes as $semana) {
            $data = \Carbon\Carbon::parse($semana->data_referencia);
            $numeroSemana = $data->weekOfYear;
            $dataInicioSemana = $data->copy()->startOfWeek();
            $dataFimSemana = $data->copy()->endOfWeek();
            
            $semanaLabel = 'Sem ' . $numeroSemana . ' (' . $dataInicioSemana->format('d/m') . '-' . $dataFimSemana->format('d/m') . ')';
            $semanasComDados[] = [
                'label' => $semanaLabel,
                'semana_ano' => $semana->semana_ano,
                'data_inicio' => $dataInicioSemana,
                'data_fim' => $dataFimSemana
            ];
        }
        
        foreach ($aeroportos as $aeroporto) {
            $dadosSemanais = [];
            
            foreach ($semanasComDados as $semana) {
                $totalPassageiros = DB::table('voos')
                    ->where('aeroporto_id', $aeroporto->id)
                    ->whereYear('created_at', $ano)
                    ->whereBetween('created_at', [$semana['data_inicio'], $semana['data_fim']])
                    ->sum('total_passageiros');
                
                $dadosSemanais[] = $totalPassageiros;
            }
            
            if (array_sum($dadosSemanais) > 0) {
                $dadosPorAeroporto[] = [
                    'nome' => $aeroporto->nome_aeroporto,
                    'dados' => $dadosSemanais,
                    'cor' => $this->gerarCorAleatoria(count($dadosPorAeroporto))
                ];
            }
        }
        
        return [
            'semanas' => array_column($semanasComDados, 'label'),
            'aeroportos' => $dadosPorAeroporto
        ];
    }
    
    /**
     * Busca anos disponíveis nos voos
     */
    public function getAnosDisponiveis()
    {
        $anos = DB::table('voos')
            ->select(DB::raw('DISTINCT YEAR(created_at) as ano'))
            ->orderBy('ano', 'desc')
            ->pluck('ano')
            ->toArray();
        
        return empty($anos) ? [date('Y')] : $anos;
    }
    
    /**
     * Calcula média geral a partir das 4 notas
     */
    private function calcularMediaGeral($notaObj, $notaPontualidade, $notaServicos, $notaPatio)
    {
        $soma = 0;
        $count = 0;
        
        if ($notaObj > 0) { $soma += $notaObj; $count++; }
        if ($notaPontualidade > 0) { $soma += $notaPontualidade; $count++; }
        if ($notaServicos > 0) { $soma += $notaServicos; $count++; }
        if ($notaPatio > 0) { $soma += $notaPatio; $count++; }
        
        return $count > 0 ? round($soma / $count, 1) : 0;
    }
    
    /**
     * Gera cor aleatória para gráficos
     */
    private function gerarCorAleatoria($index)
    {
        $cores = [
            '#0d6efd', '#198754', '#dc3545', '#ffc107', '#6f42c1',
            '#fd7e14', '#20c997', '#e83e8c', '#6610f2', '#d63384'
        ];
        
        return $cores[$index % count($cores)];
    }
}