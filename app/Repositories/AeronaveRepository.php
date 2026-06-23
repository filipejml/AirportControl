<?php
// app/Repositories/AeronaveRepository.php

namespace App\Repositories;

use App\Models\Aeronave;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use App\Services\VooMetricasService;

class AeronaveRepository
{
    /**
     * Get all aircrafts with their relationships and counts
     */
    public function getAllWithRelations(array $filters = []): Collection
    {
        return Aeronave::with([
            'fabricante',
            'companhias',
            'voos' => function ($query) use ($filters) {
                $query->with(['companhiaAerea', 'aeroporto']);
                $this->applyFilters($query, $filters);
            },
        ])
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
        $voos = $aeronave->relationLoaded('voos') ? $aeronave->voos : $aeronave->voos()->get();
        $totalVoos = $voos->sum('qtd_voos');
        $totalPassageiros = $voos->sum('total_passageiros');
        $hasSufficientData = $totalVoos >= 3;
        
        return [
            'id' => $aeronave->id,
            'fabricante' => $aeronave->fabricante->nome ?? 'N/A',
            'capacidade' => $aeronave->capacidade,
            'porte' => $aeronave->porte_descricao,
            'total_voos' => $totalVoos,
            'total_passageiros' => $totalPassageiros,
            'media_objetivo' => $hasSufficientData ? round(VooMetricasService::mediaPonderada($voos, 'nota_obj'), 1) : 0,
            'media_pontualidade' => $hasSufficientData ? round(VooMetricasService::mediaPonderada($voos, 'nota_pontualidade'), 1) : 0,
            'media_servicos' => $hasSufficientData ? round(VooMetricasService::mediaPonderada($voos, 'nota_servicos'), 1) : 0,
            'media_patio' => $hasSufficientData ? round(VooMetricasService::mediaPonderada($voos, 'nota_patio'), 1) : 0,
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
        
        // Cada registro pode representar vários voos.
        if ((clone $registros)->sum('qtd_voos') < $minRecords) {
            return 0;
        }

        return round(VooMetricasService::mediaPonderadaQuery($registros, $campoNota), 1);
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
                    ->havingRaw('SUM(qtd_voos) >= ?', [$minRecords]);
            })
            ->withSum('voos', 'qtd_voos')
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
            'totalVoos' => $voosFiltrados->sum('qtd_voos'),
            'totalPassageiros' => $voosFiltrados->sum('total_passageiros'),
            'totalCompanhias' => $aeronave->companhias()->count(),
            'totalAeroportos' => $voosFiltrados->pluck('aeroporto_id')->unique()->count(),
            'notaObj' => VooMetricasService::mediaPonderada($voosFiltrados, 'nota_obj'),
            'notaPontualidade' => VooMetricasService::mediaPonderada($voosFiltrados, 'nota_pontualidade'),
            'notaServicos' => VooMetricasService::mediaPonderada($voosFiltrados, 'nota_servicos'),
            'notaPatio' => VooMetricasService::mediaPonderada($voosFiltrados, 'nota_patio'),
            'ultimosVoos' => $voosFiltrados->sortByDesc('created_at')->take(5)->values(),
            'voosPorCompanhia' => $this->groupVoosPorCompanhia($voosFiltrados, 'qtd_voos'),
            'passageirosPorCompanhia' => $this->groupVoosPorCompanhia($voosFiltrados, 'total_passageiros')
        ];
    }
    
    /**
     * Get available filter options for aircraft dashboard
     */
    public function getFilterOptions(Aeronave $aeronave): array
    {
        return [
            'companhiasDisponiveis' => $aeronave->companhias,
            'semanasDisponiveis' => $this->getAvailableWeeks($aeronave),
            'anosDisponiveis' => $this->getAvailableYears($aeronave)
        ];
    }
    
    /**
     * Apply filters to voos query
     */
    public function applyFilters($query, array $filters): void
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
    
    private function groupVoosPorCompanhia(\Illuminate\Support\Collection $voos, string $campo): \Illuminate\Support\Collection
    {
        return $voos
            ->groupBy('companhia_aerea_id')
            ->mapWithKeys(function ($items) use ($campo) {
                $companhia = $items->first()->companhiaAerea;

                if (!$companhia) {
                    return [];
                }

                return [$companhia->nome => $items->sum($campo)];
            })
            ->filter(fn ($quantidade) => $quantidade > 0)
            ->sortDesc();
    }
    
    /**
     * Get available weeks for filtering
     */
    public function getAvailableWeeks(?Aeronave $aeronave = null): \Illuminate\Support\Collection
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

    public function getAvailableYears(?Aeronave $aeronave = null): array
    {
        $query = $aeronave ? $aeronave->voos() : DB::table('voos');

        return $query
            ->select(DB::raw('DISTINCT YEAR(created_at) as ano'))
            ->orderBy('ano', 'desc')
            ->pluck('ano')
            ->filter()
            ->toArray();
    }
}
