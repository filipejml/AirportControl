<?php
// app/Services/AeroportoService.php

namespace App\Services;

use App\Models\Aeroporto;
use App\Repositories\AeroportoRepository;
use Illuminate\Support\Facades\DB;

class AeroportoService
{
    protected $aeroportoRepository;
    
    public function __construct(AeroportoRepository $aeroportoRepository)
    {
        $this->aeroportoRepository = $aeroportoRepository;
    }
    
    /**
     * Obtém dados para o dashboard de um aeroporto específico
     */
    public function getDashboardData(Aeroporto $aeroporto)
    {
        // Carregar relacionamentos
        $aeroporto->load(['depositos.veiculos']);
        
        // Estatísticas gerais
        $totalVoos = $aeroporto->voos()->sum('qtd_voos');
        $totalPassageiros = $aeroporto->voos()->sum('total_passageiros');
        $mediaPassageirosPorVoo = $totalVoos > 0 ? round($totalPassageiros / $totalVoos, 0) : 0;
        $totalCompanhias = $aeroporto->companhias()->count();
        
        // Notas médias
        $mediasNotas = $this->aeroportoRepository->getMediasNotas($aeroporto->id);
        
        // Dados para gráficos
        $voosPorCompanhia = $this->aeroportoRepository->getVoosPorCompanhia($aeroporto->id);
        $passageirosPorCompanhia = $this->aeroportoRepository->getPassageirosPorCompanhia($aeroporto->id);
        $horariosData = $this->aeroportoRepository->getVoosPorHorario($aeroporto->id);
        $tiposData = $this->aeroportoRepository->getVoosPorTipo($aeroporto->id);
        $topCompanhiasNotas = $this->aeroportoRepository->getTopCompanhiasAvaliadas($aeroporto->id);
        $evolucaoMensal = $this->aeroportoRepository->getEvolucaoMensal($aeroporto->id);
        
        return compact(
            'aeroporto',
            'totalVoos',
            'totalPassageiros',
            'mediaPassageirosPorVoo',
            'totalCompanhias',
            'mediasNotas',
            'voosPorCompanhia',
            'passageirosPorCompanhia',
            'horariosData',
            'tiposData',
            'topCompanhiasNotas',
            'evolucaoMensal'
        );
    }
    
    /**
     * Obtém dados para a página de informações gerais dos aeroportos
     */
    public function getInformacoesData($anoSelecionado)
    {
        // Buscar aeroportos com estatísticas
        $aeroportos = $this->aeroportoRepository->getAeroportosComEstatisticas($anoSelecionado);
        $aeroportoIds = $aeroportos->pluck('id')->all();
        $mediasPorAeroporto = $this->aeroportoRepository->getMediasNotasPorAeroporto($anoSelecionado, $aeroportoIds);
        $melhoresPorAeroporto = $this->aeroportoRepository->getMelhoresCompanhiasPorCategoriaPorAeroporto($anoSelecionado, $aeroportoIds);
        $voosCompanhiasPorAeroporto = $this->aeroportoRepository->getVoosCompanhiasPorAeroporto($anoSelecionado, $aeroportoIds);
        
        // Preparar dados para cada aeroporto
        $aeroportosData = [];
        foreach ($aeroportos as $aeroporto) {
            $mediasNotas = $mediasPorAeroporto[$aeroporto->id] ?? [
                'nota_obj' => 0,
                'nota_pontualidade' => 0,
                'nota_servicos' => 0,
                'nota_patio' => 0,
                'media_geral' => 0,
            ];
            $melhoresCompanhias = $melhoresPorAeroporto[$aeroporto->id] ?? [];
            $voosCompanhias = $voosCompanhiasPorAeroporto[$aeroporto->id] ?? collect();
            $totalVoos = (int) ($aeroporto->total_voos ?? 0);
            $totalPassageiros = (int) ($aeroporto->total_passageiros ?? 0);
            
            $aeroportosData[] = [
                'id' => $aeroporto->id,
                'nome' => $aeroporto->nome_aeroporto,
                'companhias_count' => $aeroporto->companhias_count ?? 0,
                'total_voos' => $totalVoos,
                'total_passageiros' => $totalPassageiros,
                'media_passageiros_por_voo' => $totalVoos > 0 ? $totalPassageiros / $totalVoos : 0,
                'media_notas' => $mediasNotas['media_geral'],
                'nota_obj' => $mediasNotas['nota_obj'],
                'nota_pontualidade' => $mediasNotas['nota_pontualidade'],
                'nota_servicos' => $mediasNotas['nota_servicos'],
                'nota_patio' => $mediasNotas['nota_patio'],
                'melhores_companhias' => $melhoresCompanhias,
                'companhias' => $aeroporto->companhias->map(function($c) use ($voosCompanhias) {
                    return [
                        'id' => $c->id,
                        'nome' => $c->nome,
                        'voos_count' => (int) ($voosCompanhias->get($c->id)->total_voos ?? 0)
                    ];
                })
            ];
        }
        
        // Totais gerais
        $totalAeroportos = $aeroportos->count();
        $totalVoos = collect($aeroportosData)->sum('total_voos');
        $totalPassageiros = collect($aeroportosData)->sum('total_passageiros');
        $mediaPassageirosPorVoo = $totalVoos > 0 ? $totalPassageiros / $totalVoos : 0;
        
        // Dados semanais para gráfico
        $dadosSemanais = $this->aeroportoRepository->getDadosPassageirosPorSemana($aeroportos, $anoSelecionado);
        
        // Anos disponíveis
        $anosDisponiveis = $this->aeroportoRepository->getAnosDisponiveis();
        
        // Companhias para filtro
        $companhias = \App\Models\CompanhiaAerea::orderBy('nome')->get();
        
        return compact(
            'aeroportosData',
            'companhias',
            'totalAeroportos',
            'totalVoos',
            'totalPassageiros',
            'mediaPassageirosPorVoo',
            'dadosSemanais',
            'anosDisponiveis',
            'anoSelecionado'
        );
    }
    
    /**
     * Obtém estatísticas resumidas para cards na página de informações
     */
    public function getEstatisticasGerais($anoSelecionado = null)
    {
        $query = Aeroporto::query();
        
        if ($anoSelecionado) {
            // Se tiver ano, filtra os voos por ano
            $totalAeroportos = Aeroporto::count();
            $totalVoos = DB::table('voos')->whereYear('created_at', $anoSelecionado)->sum('qtd_voos');
            $totalPassageiros = DB::table('voos')->whereYear('created_at', $anoSelecionado)->sum('total_passageiros');
        } else {
            $totalAeroportos = Aeroporto::count();
            $totalVoos = DB::table('voos')->sum('qtd_voos');
            $totalPassageiros = DB::table('voos')->sum('total_passageiros');
        }
        
        $mediaPassageirosPorVoo = $totalVoos > 0 ? $totalPassageiros / $totalVoos : 0;
        
        return [
            'total_aeroportos' => $totalAeroportos,
            'total_voos' => $totalVoos,
            'total_passageiros' => $totalPassageiros,
            'media_passageiros_por_voo' => $mediaPassageirosPorVoo
        ];
    }
}
