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
        
        // Preparar dados para cada aeroporto
        $aeroportosData = [];
        foreach ($aeroportos as $aeroporto) {
            $mediasNotas = $this->aeroportoRepository->getMediasNotas($aeroporto->id, $anoSelecionado);
            $melhoresCompanhias = $this->aeroportoRepository->getMelhoresCompanhiasPorCategoria($aeroporto->id, $anoSelecionado);
            
            $aeroportosData[] = [
                'id' => $aeroporto->id,
                'nome' => $aeroporto->nome_aeroporto,
                'companhias_count' => $aeroporto->companhias_count ?? 0,
                'total_voos' => $aeroporto->total_voos ?? 0,
                'total_passageiros' => $aeroporto->total_passageiros ?? 0,
                'media_passageiros_por_voo' => $aeroporto->media_passageiros_por_voo ?? 0,
                'media_notas' => $mediasNotas['media_geral'],
                'nota_obj' => $mediasNotas['nota_obj'],
                'nota_pontualidade' => $mediasNotas['nota_pontualidade'],
                'nota_servicos' => $mediasNotas['nota_servicos'],
                'nota_patio' => $mediasNotas['nota_patio'],
                'melhores_companhias' => $melhoresCompanhias,
                'companhias' => $aeroporto->companhias->map(function($c) use ($aeroporto, $anoSelecionado) {
                    $totalVoosCompanhia = DB::table('voos')
                        ->where('aeroporto_id', $aeroporto->id)
                        ->where('companhia_aerea_id', $c->id)
                        ->whereYear('created_at', $anoSelecionado)
                        ->sum('qtd_voos');
                    
                    return [
                        'id' => $c->id,
                        'nome' => $c->nome,
                        'voos_count' => $totalVoosCompanhia
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