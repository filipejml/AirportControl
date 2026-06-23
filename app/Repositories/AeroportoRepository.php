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
        return Aeroporto::with(['companhias'])
            ->withCount('companhias')
            ->withSum(['voos as total_voos' => function ($query) use ($anoSelecionado) {
                $query->whereYear('created_at', $anoSelecionado);
            }], 'qtd_voos')
            ->withSum(['voos as total_passageiros' => function ($query) use ($anoSelecionado) {
                $query->whereYear('created_at', $anoSelecionado);
            }], 'total_passageiros')
            ->get();
    }
    
    /**
     * Busca médias das notas de um aeroporto (ponderadas por quantidade de voos)
     */
    public function getMediasNotas($aeroportoId, $ano = null)
    {
        $query = DB::table('voos')
            ->where('aeroporto_id', $aeroportoId);
            
        if ($ano) {
            $query->whereYear('created_at', $ano);
        }
        
        $notaObj = $query->clone()
            ->whereNotNull('nota_obj')
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

    public function getMediasNotasPorAeroporto($ano, array $aeroportoIds = []): array
    {
        return DB::table('voos')
            ->whereYear('created_at', $ano)
            ->when($aeroportoIds, fn ($query) => $query->whereIn('aeroporto_id', $aeroportoIds))
            ->select(
                'aeroporto_id',
                DB::raw('COALESCE(SUM(qtd_voos * nota_obj) / NULLIF(SUM(CASE WHEN nota_obj IS NOT NULL THEN qtd_voos ELSE 0 END), 0), 0) as nota_obj'),
                DB::raw('COALESCE(SUM(qtd_voos * nota_pontualidade) / NULLIF(SUM(CASE WHEN nota_pontualidade IS NOT NULL THEN qtd_voos ELSE 0 END), 0), 0) as nota_pontualidade'),
                DB::raw('COALESCE(SUM(qtd_voos * nota_servicos) / NULLIF(SUM(CASE WHEN nota_servicos IS NOT NULL THEN qtd_voos ELSE 0 END), 0), 0) as nota_servicos'),
                DB::raw('COALESCE(SUM(qtd_voos * nota_patio) / NULLIF(SUM(CASE WHEN nota_patio IS NOT NULL THEN qtd_voos ELSE 0 END), 0), 0) as nota_patio')
            )
            ->groupBy('aeroporto_id')
            ->get()
            ->mapWithKeys(function ($item) {
                $notaObj = (float) $item->nota_obj;
                $notaPontualidade = (float) $item->nota_pontualidade;
                $notaServicos = (float) $item->nota_servicos;
                $notaPatio = (float) $item->nota_patio;

                return [
                    $item->aeroporto_id => [
                        'nota_obj' => $notaObj,
                        'nota_pontualidade' => $notaPontualidade,
                        'nota_servicos' => $notaServicos,
                        'nota_patio' => $notaPatio,
                        'media_geral' => $this->calcularMediaGeral(
                            $notaObj,
                            $notaPontualidade,
                            $notaServicos,
                            $notaPatio
                        ),
                    ],
                ];
            })
            ->all();
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
                DB::raw('SUM(voos.qtd_voos * voos.nota_obj) / NULLIF(SUM(CASE WHEN voos.nota_obj IS NOT NULL THEN voos.qtd_voos ELSE 0 END), 0) as nota_obj'),
                DB::raw('SUM(voos.qtd_voos * voos.nota_pontualidade) / NULLIF(SUM(CASE WHEN voos.nota_pontualidade IS NOT NULL THEN voos.qtd_voos ELSE 0 END), 0) as nota_pontualidade'),
                DB::raw('SUM(voos.qtd_voos * voos.nota_servicos) / NULLIF(SUM(CASE WHEN voos.nota_servicos IS NOT NULL THEN voos.qtd_voos ELSE 0 END), 0) as nota_servicos'),
                DB::raw('SUM(voos.qtd_voos * voos.nota_patio) / NULLIF(SUM(CASE WHEN voos.nota_patio IS NOT NULL THEN voos.qtd_voos ELSE 0 END), 0) as nota_patio'),
                DB::raw('(
                    SUM(voos.qtd_voos * voos.nota_obj) / NULLIF(SUM(CASE WHEN voos.nota_obj IS NOT NULL THEN voos.qtd_voos ELSE 0 END), 0) +
                    SUM(voos.qtd_voos * voos.nota_pontualidade) / NULLIF(SUM(CASE WHEN voos.nota_pontualidade IS NOT NULL THEN voos.qtd_voos ELSE 0 END), 0) +
                    SUM(voos.qtd_voos * voos.nota_servicos) / NULLIF(SUM(CASE WHEN voos.nota_servicos IS NOT NULL THEN voos.qtd_voos ELSE 0 END), 0) +
                    SUM(voos.qtd_voos * voos.nota_patio) / NULLIF(SUM(CASE WHEN voos.nota_patio IS NOT NULL THEN voos.qtd_voos ELSE 0 END), 0)
                ) / 4 as media_geral')
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

    public function getMelhoresCompanhiasPorCategoriaPorAeroporto($ano, array $aeroportoIds = []): array
    {
        $categorias = [
            'Objetivo' => 'nota_obj',
            'Pontualidade' => 'nota_pontualidade',
            'Servicos' => 'nota_servicos',
            'Patio' => 'nota_patio',
        ];

        $melhores = [];

        foreach ($categorias as $categoria => $campoNota) {
            $linhas = DB::table('voos')
                ->join('companhias_aereas', 'voos.companhia_aerea_id', '=', 'companhias_aereas.id')
                ->select(
                    'voos.aeroporto_id',
                    'companhias_aereas.id',
                    'companhias_aereas.nome',
                    DB::raw("SUM(voos.qtd_voos * voos.{$campoNota}) / NULLIF(SUM(voos.qtd_voos), 0) as media_nota")
                )
                ->whereYear('voos.created_at', $ano)
                ->when($aeroportoIds, fn ($query) => $query->whereIn('voos.aeroporto_id', $aeroportoIds))
                ->whereNotNull("voos.{$campoNota}")
                ->groupBy('voos.aeroporto_id', 'companhias_aereas.id', 'companhias_aereas.nome')
                ->orderBy('voos.aeroporto_id')
                ->orderByDesc('media_nota')
                ->get();

            foreach ($linhas->groupBy('aeroporto_id') as $aeroportoId => $items) {
                $melhor = $items->first();
                $melhores[$aeroportoId][$categoria] = $melhor ? [
                    'id' => $melhor->id,
                    'nome' => $melhor->nome,
                    'media' => $melhor->media_nota ?? 0,
                ] : null;
            }
        }

        return $melhores;
    }

    public function getVoosCompanhiasPorAeroporto($ano, array $aeroportoIds = []): array
    {
        return DB::table('voos')
            ->whereYear('created_at', $ano)
            ->when($aeroportoIds, fn ($query) => $query->whereIn('aeroporto_id', $aeroportoIds))
            ->select('aeroporto_id', 'companhia_aerea_id', DB::raw('SUM(qtd_voos) as total_voos'))
            ->groupBy('aeroporto_id', 'companhia_aerea_id')
            ->get()
            ->groupBy('aeroporto_id')
            ->map(fn ($items) => $items->keyBy('companhia_aerea_id'))
            ->all();
    }
    
    /**
     * Busca dados semanais de passageiros para múltiplos aeroportos
     */
    public function getDadosPassageirosPorSemana($aeroportos, $ano)
    {
        $semanasComDados = [];
        $dadosPorAeroporto = [];
        $aeroportoIds = $aeroportos->pluck('id')->all();
        
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
        
        $passageirosPorSemana = DB::table('voos')
            ->whereYear('created_at', $ano)
            ->when($aeroportoIds, fn ($query) => $query->whereIn('aeroporto_id', $aeroportoIds))
            ->select(
                'aeroporto_id',
                DB::raw('YEARWEEK(created_at, 1) as semana_ano'),
                DB::raw('SUM(total_passageiros) as total_passageiros')
            )
            ->groupBy('aeroporto_id', 'semana_ano')
            ->get()
            ->groupBy('aeroporto_id')
            ->map(fn ($items) => $items->keyBy('semana_ano'));

        foreach ($aeroportos as $aeroporto) {
            $dadosSemanais = [];
            $dadosAeroporto = $passageirosPorSemana->get($aeroporto->id, collect());

            foreach ($semanasComDados as $semana) {
                $dadosSemanais[] = (int) ($dadosAeroporto->get($semana['semana_ano'])->total_passageiros ?? 0);
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
