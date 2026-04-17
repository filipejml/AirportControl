<?php
// app/Repositories/AeronaveRepository.php

namespace App\Repositories;

use App\Models\Aeronave;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class AeronaveRepository
{
    /**
     * Get all aircrafts with their relationships and counts
     */
    public function getAllWithRelations(): Collection
    {
        return Aeronave::with(['fabricante', 'companhias', 'voos.companhiaAerea'])
            ->withCount(['companhias', 'voos'])
            ->get();
    }
    
    /**
     * Get aircraft statistics for general info page
     */
    public function getStatisticsForInformacoes(): array
    {
        $aeronaves = $this->getAllWithRelations();
        $modelosComDados = [];
        
        foreach ($aeronaves as $aeronave) {
            $modelosComDados[$aeronave->modelo] = $this->getAircraftAggregatedData($aeronave);
        }
        
        return $modelosComDados;
    }
    
    /**
     * Get aggregated data for a single aircraft (voos, passageiros, medias)
     */
    public function getAircraftAggregatedData(Aeronave $aeronave): array
    {
        $totalVoos = $aeronave->voos()->sum('qtd_voos');
        $totalPassageiros = $aeronave->voos()->sum('total_passageiros');
        
        return [
            'id' => $aeronave->id,
            'fabricante' => $aeronave->fabricante->nome ?? 'N/A',
            'capacidade' => $aeronave->capacidade,
            'porte' => $aeronave->porte_descricao,
            'total_voos' => $totalVoos,
            'total_passageiros' => $totalPassageiros,
            'media_objetivo' => $this->getAverageRating($aeronave->id, 'nota_obj'),
            'media_pontualidade' => $this->getAverageRating($aeronave->id, 'nota_pontualidade'),
            'media_servicos' => $this->getAverageRating($aeronave->id, 'nota_servicos'),
            'media_patio' => $this->getAverageRating($aeronave->id, 'nota_patio'),
            'tem_dados' => $totalVoos > 0
        ];
    }
    
    /**
     * Get weighted average rating for a specific field
     */
    public function getAverageRating(int $aeronaveId, string $campoNota, int $minRecords = 3): float
    {
        $registros = DB::table('voos')
            ->where('aeronave_id', $aeronaveId)
            ->whereNotNull($campoNota);
        
        // Verificar se tem registros suficientes
        if ($registros->count() < $minRecords) {
            return 0;
        }
        
        $media = $registros->select(DB::raw("AVG($campoNota) as media"))
            ->value('media');
            
        return round($media ?? 0, 1);
    }
    
    /**
     * Get aircrafts with sufficient data for ranking
     */
    public function getAircraftsWithSufficientData(int $minRecords = 3): Collection
    {
        return Aeronave::with('fabricante')
            ->whereHas('voos', function($query) use ($minRecords) {
                $query->select('aeronave_id')
                    ->groupBy('aeronave_id')
                    ->havingRaw('COUNT(*) >= ?', [$minRecords]);
            })
            ->withCount('voos')
            ->get();
    }
    
    /**
     * Get dashboard statistics for an aircraft with filters
     */
    public function getDashboardStats(Aeronave $aeronave, array $filters = []): array
    {
        $queryVoos = $aeronave->voos()->with(['companhiaAerea', 'aeroporto']);
        
        // Apply filters
        $this->applyFilters($queryVoos, $filters);
        
        $voosFiltrados = $queryVoos->get();
        
        return [
            'total_voos' => $voosFiltrados->count(),
            'total_passageiros' => $voosFiltrados->sum('total_passageiros'),
            'total_companhias' => $aeronave->companhias()->count(),
            'total_aeroportos' => $voosFiltrados->pluck('aeroporto_id')->unique()->count(),
            'nota_obj' => $voosFiltrados->avg('nota_obj') ?? 0,
            'nota_pontualidade' => $voosFiltrados->avg('nota_pontualidade') ?? 0,
            'nota_servicos' => $voosFiltrados->avg('nota_servicos') ?? 0,
            'nota_patio' => $voosFiltrados->avg('nota_patio') ?? 0,
            'ultimos_voos' => $queryVoos->clone()->orderBy('created_at', 'desc')->limit(5)->get(),
            'voos_por_companhia' => $this->getVoosPorCompanhia($aeronave, $filters),
            'passageiros_por_companhia' => $this->getPassageirosPorCompanhia($aeronave, $filters)
        ];
    }
    
    /**
     * Get available filter options for aircraft dashboard
     */
    public function getFilterOptions(Aeronave $aeronave): array
    {
        return [
            'companhias_disponiveis' => $aeronave->companhias,
            'semanas_disponiveis' => $this->getAvailableWeeks($aeronave),
            'anos_disponiveis' => $aeronave->voos()
                ->select(DB::raw('DISTINCT YEAR(created_at) as ano'))
                ->orderBy('ano', 'desc')
                ->pluck('ano')
                ->toArray()
        ];
    }
    
    /**
     * Apply filters to voos query
     */
    private function applyFilters($query, array $filters): void
    {
        if (!empty($filters['companhia_id']) && $filters['companhia_id'] !== 'geral') {
            $query->where('companhia_aerea_id', $filters['companhia_id']);
        }
        
        if (!empty($filters['periodo'])) {
            $this->applyPeriodFilter($query, $filters);
        }
    }
    
    /**
     * Apply period filter to query
     */
    private function applyPeriodFilter($query, array $filters): void
    {
        switch ($filters['periodo']) {
            case 'semanal':
                if (!empty($filters['semana'])) {
                    list($ano, $semana) = explode('-W', $filters['semana']);
                    $dataInicio = (new \DateTime())->setISODate($ano, $semana)->format('Y-m-d 00:00:00');
                    $dataFim = (new \DateTime())->setISODate($ano, $semana)->modify('+6 days')->format('Y-m-d 23:59:59');
                    $query->whereBetween('created_at', [$dataInicio, $dataFim]);
                }
                break;
            case 'mensal':
                if (!empty($filters['ano']) && !empty($filters['mes'])) {
                    $dataInicio = "{$filters['ano']}-{$filters['mes']}-01 00:00:00";
                    $dataFim = date('Y-m-t 23:59:59', strtotime($dataInicio));
                    $query->whereBetween('created_at', [$dataInicio, $dataFim]);
                }
                break;
            case 'anual':
                if (!empty($filters['ano_selecionado'])) {
                    $dataInicio = "{$filters['ano_selecionado']}-01-01 00:00:00";
                    $dataFim = "{$filters['ano_selecionado']}-12-31 23:59:59";
                    $query->whereBetween('created_at', [$dataInicio, $dataFim]);
                }
                break;
        }
    }
    
    /**
     * Get voos por companhia
     */
    private function getVoosPorCompanhia(Aeronave $aeronave, array $filters): \Illuminate\Support\Collection
    {
        $result = collect();
        
        foreach ($aeronave->companhias as $companhia) {
            $query = $aeronave->voos()->where('companhia_aerea_id', $companhia->id);
            $this->applyFilters($query, $filters);
            $quantidade = $query->count();
            
            if ($quantidade > 0) {
                $result->put($companhia->nome, $quantidade);
            }
        }
        
        return $result->sortDesc();
    }
    
    /**
     * Get passageiros por companhia
     */
    private function getPassageirosPorCompanhia(Aeronave $aeronave, array $filters): \Illuminate\Support\Collection
    {
        $result = collect();
        
        foreach ($aeronave->companhias as $companhia) {
            $query = $aeronave->voos()->where('companhia_aerea_id', $companhia->id);
            $this->applyFilters($query, $filters);
            $quantidade = $query->sum('total_passageiros');
            
            if ($quantidade > 0) {
                $result->put($companhia->nome, $quantidade);
            }
        }
        
        return $result->sortDesc();
    }
    
    /**
     * Get available weeks for filtering
     */
    private function getAvailableWeeks(Aeronave $aeronave): \Illuminate\Support\Collection
    {
        $semanas = collect();
        
        for ($i = 0; $i < 52; $i++) {
            $data = now()->subWeeks($i);
            $semanas->push((object)[
                'semana' => $data->format('Y-\WW'),
                'numero_semana' => $data->weekOfYear,
                'ano' => $data->year
            ]);
        }
        
        return $semanas->unique('semana');
    }
}