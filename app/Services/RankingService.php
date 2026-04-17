<?php
// app/Services/RankingService.php

namespace App\Services;

use App\Repositories\AeronaveRepository;
use Illuminate\Support\Collection;

class RankingService
{
    protected AeronaveRepository $aeronaveRepository;
    
    public function __construct(AeronaveRepository $aeronaveRepository)
    {
        $this->aeronaveRepository = $aeronaveRepository;
    }
    
    /**
     * Generate complete ranking data
     */
    public function generateRankings(): array
    {
        $aeronaves = $this->aeronaveRepository->getAllWithRelations();
        $rankings = $this->buildRankingData($aeronaves);
        $aeronavesComDadosSuficientes = $this->filterAircraftsWithSufficientData($rankings);
        
        return [
            'rankings_por_nota' => $this->sortByNotaGeral($aeronavesComDadosSuficientes),
            'rankings_objetivo' => $this->sortByCategory($aeronavesComDadosSuficientes, 'media_objetivo'),
            'rankings_pontualidade' => $this->sortByCategory($aeronavesComDadosSuficientes, 'media_pontualidade'),
            'rankings_servicos' => $this->sortByCategory($aeronavesComDadosSuficientes, 'media_servicos'),
            'rankings_patio' => $this->sortByCategory($aeronavesComDadosSuficientes, 'media_patio'),
            'rankings_por_voos' => $this->sortByTotal($rankings, 'total_voos'),
            'rankings_por_passageiros' => $this->sortByTotal($rankings, 'total_passageiros'),
            'rankings_por_capacidade' => $this->sortByTotal($rankings, 'capacidade'),
            'estatisticas' => $this->calculateStatistics($rankings, $aeronavesComDadosSuficientes),
            'destaques' => $this->getHighlights($rankings, $aeronavesComDadosSuficientes)
        ];
    }
    
    /**
     * Build ranking data for all aircrafts
     */
    private function buildRankingData(Collection $aeronaves): array
    {
        $rankings = [];
        
        foreach ($aeronaves as $aeronave) {
            $totalVoos = $aeronave->voos()->sum('qtd_voos');
            $totalPassageiros = $aeronave->voos()->sum('total_passageiros');
            $numeroRegistrosVoos = $aeronave->voos()->count();
            $minRecords = 3;
            
            $hasSufficientData = $numeroRegistrosVoos >= $minRecords;
            
            $rankings[] = [
                'id' => $aeronave->id,
                'modelo' => $aeronave->modelo,
                'fabricante' => $aeronave->fabricante->nome ?? 'N/A',
                'capacidade' => $aeronave->capacidade,
                'porte' => $aeronave->porte_descricao,
                'total_voos' => $totalVoos,
                'total_passageiros' => $totalPassageiros,
                'media_objetivo' => $hasSufficientData ? $this->aeronaveRepository->getAverageRating($aeronave->id, 'nota_obj') : 0,
                'media_pontualidade' => $hasSufficientData ? $this->aeronaveRepository->getAverageRating($aeronave->id, 'nota_pontualidade') : 0,
                'media_servicos' => $hasSufficientData ? $this->aeronaveRepository->getAverageRating($aeronave->id, 'nota_servicos') : 0,
                'media_patio' => $hasSufficientData ? $this->aeronaveRepository->getAverageRating($aeronave->id, 'nota_patio') : 0,
                'nota_geral' => $hasSufficientData ? $this->calculateNotaGeral($aeronave->id) : 0,
                'tem_dados' => $totalVoos > 0,
                'dados_suficientes' => $hasSufficientData
            ];
        }
        
        return $rankings;
    }
    
    /**
     * Calculate overall rating for an aircraft
     */
    private function calculateNotaGeral(int $aeronaveId): float
    {
        $mediaObjetivo = $this->aeronaveRepository->getAverageRating($aeronaveId, 'nota_obj');
        $mediaPontualidade = $this->aeronaveRepository->getAverageRating($aeronaveId, 'nota_pontualidade');
        $mediaServicos = $this->aeronaveRepository->getAverageRating($aeronaveId, 'nota_servicos');
        $mediaPatio = $this->aeronaveRepository->getAverageRating($aeronaveId, 'nota_patio');
        
        return round(($mediaObjetivo + $mediaPontualidade + $mediaServicos + $mediaPatio) / 4, 1);
    }
    
    /**
     * Filter aircrafts with sufficient data
     */
    private function filterAircraftsWithSufficientData(array $rankings): Collection
    {
        return collect($rankings)->filter(function($item) {
            return $item['dados_suficientes'] === true && $item['nota_geral'] > 0;
        })->values();
    }
    
    /**
     * Sort by overall rating
     */
    private function sortByNotaGeral(Collection $aeronaves): Collection
    {
        return $aeronaves->sortByDesc(function($item) {
            return [$item['nota_geral'], $item['total_voos']];
        })->values();
    }
    
    /**
     * Sort by specific category
     */
    private function sortByCategory(Collection $aeronaves, string $category): Collection
    {
        return $aeronaves->sortByDesc(function($item) use ($category) {
            return [$item[$category], $item['total_voos']];
        })->values();
    }
    
    /**
     * Sort by total value
     */
    private function sortByTotal(array $rankings, string $field): Collection
    {
        return collect($rankings)->sortByDesc($field)->values();
    }
    
    /**
     * Calculate statistics
     */
    private function calculateStatistics(array $rankings, Collection $aeronavesComDados): array
    {
        $collection = collect($rankings);
        
        return [
            'total_aeronaves' => count($rankings),
            'total_fabricantes' => $collection->pluck('fabricante')->unique()->count(),
            'total_voos_geral' => $collection->sum('total_voos'),
            'total_passageiros_geral' => $collection->sum('total_passageiros'),
            'media_nota_geral' => $aeronavesComDados->avg('nota_geral'),
            'aeronaves_com_dados' => $collection->where('tem_dados', true)->count(),
            'aeronaves_com_dados_suficientes' => $aeronavesComDados->count(),
            'porte_pequeno' => $collection->where('porte', 'Pequeno Porte (≤100)')->count(),
            'porte_medio' => $collection->where('porte', 'Médio Porte (101-299)')->count(),
            'porte_grande' => $collection->where('porte', 'Grande Porte (≥300)')->count(),
            'aviso_sem_dados' => $aeronavesComDados->isEmpty()
        ];
    }
    
    /**
     * Get ranking highlights
     */
    private function getHighlights(array $rankings, Collection $aeronavesComDados): array
    {
        $rankingsPorNota = $this->sortByNotaGeral($aeronavesComDados);
        $rankingsPorVoos = $this->sortByTotal($rankings, 'total_voos');
        $rankingsPorPassageiros = $this->sortByTotal($rankings, 'total_passageiros');
        $rankingsPorCapacidade = $this->sortByTotal($rankings, 'capacidade');
        
        return [
            'melhor_nota_geral' => $rankingsPorNota->first(),
            'pior_nota_geral' => $rankingsPorNota->last(),
            'mais_voos' => $rankingsPorVoos->first(),
            'menos_voos' => $rankingsPorVoos->last(),
            'mais_passageiros' => $rankingsPorPassageiros->first(),
            'menos_passageiros' => $rankingsPorPassageiros->last(),
            'maior_capacidade' => $rankingsPorCapacidade->first(),
            'menor_capacidade' => $rankingsPorCapacidade->last()
        ];
    }
}