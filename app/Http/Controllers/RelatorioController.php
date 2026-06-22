<?php
// app/Http/Controllers/RelatorioController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Relatorio;
use App\Models\Aeroporto;
use App\Models\CompanhiaAerea;
use App\Services\VooMetricasService;

class RelatorioController extends Controller
{
    /**
     * LISTAGEM (ADMIN + USUÁRIO)
     */
    public function index()
    {
        $relatorios = Relatorio::visiveis()->get();

        return view('relatorios.index', compact('relatorios'));
    }

    /**
     * LISTAGEM PARA ADMIN (CONTROLE)
     */
    public function adminIndex()
    {
        $relatorios = Relatorio::all();
        return view('admin.relatorios.index', compact('relatorios'));
    }

    /**
     * API para dados do relatório de Companhias por Aeroporto
     */
    public function apiCompanhiasPorAeroporto(Request $request)
    {
        $query = Aeroporto::with('companhias');
        
        // Filtro por aeroporto específico
        if ($request->has('aeroporto_id') && $request->aeroporto_id) {
            $query->where('id', $request->aeroporto_id);
        }
        
        // Filtro por companhia específica
        if ($request->has('companhia_id') && $request->companhia_id) {
            $query->whereHas('companhias', function($q) use ($request) {
                $q->where('companhias_aereas.id', $request->companhia_id);
            });
        }
        
        $dados = $query->get()
            ->map(function ($aeroporto) {
                return [
                    'aeroporto' => $aeroporto->nome_aeroporto,
                    'id_aeroporto' => $aeroporto->id,
                    'quantidade_companhias' => $aeroporto->companhias->count(),
                    'companhias' => $aeroporto->companhias->map(function ($companhia) {
                        return [
                            'id' => $companhia->id,
                            'nome' => $companhia->nome,
                            'codigo' => $companhia->codigo,
                        ];
                    }),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $dados,
            'timestamp' => now()->toIso8601String(),
            'filters' => [
                'aeroporto_id' => $request->aeroporto_id,
                'companhia_id' => $request->companhia_id,
            ]
        ]);
    }
    
    /**
     * View do relatório para ADMIN (tabela)
     */
    public function adminCompanhiasPorAeroporto()
    {
        $relatorio = Relatorio::where('tipo', Relatorio::TIPO_COMPANHIAS_POR_AEROPORTO)
            ->firstOrFail();
        
        // Buscar todos os aeroportos e companhias para os filtros
        $aeroportos = Aeroporto::orderBy('nome_aeroporto')->get();
        $companhias = CompanhiaAerea::orderBy('nome')->get();
        
        return view('admin.relatorios.companhias-por-aeroporto', compact('relatorio', 'aeroportos', 'companhias'));
    }
    
    /**
     * View do relatório para USUÁRIO COMUM (cards)
     */
    public function userCompanhiasPorAeroporto()
    {
        $relatorio = Relatorio::visiveis()
            ->where('tipo', Relatorio::TIPO_COMPANHIAS_POR_AEROPORTO)
            ->firstOrFail();
        
        // Buscar todos os aeroportos e companhias para os filtros
        $aeroportos = Aeroporto::orderBy('nome_aeroporto')->get();
        $companhias = CompanhiaAerea::orderBy('nome')->get();
        
        return view('relatorios.companhias-por-aeroporto', compact('relatorio', 'aeroportos', 'companhias'));
    }

    /**
     * Toggle a visibilidade do relatório (AJAX)
     */
    public function toggleVisibilidade(Request $request, Relatorio $relatorio)
    {
        $data = $request->validate([
            'visivel_usuario' => ['required', 'boolean'],
        ]);

        try {
            $relatorio->update($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Status atualizado com sucesso!',
                'visivel_usuario' => $relatorio->visivel_usuario
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível atualizar a visibilidade do relatório.'
            ], 500);
        }
    }

    /**
     * API para dados do relatório de Voos por Aeroporto
     */
    public function apiVoosPorAeroporto(Request $request)
    {
        $periodo = $request->validate([
            'periodo' => ['nullable', 'in:hoje,semana,mes,ano'],
        ])['periodo'] ?? null;

        $carregarVoos = function ($q) use ($periodo) {
            $q->with('companhiaAerea');

            switch ($periodo) {
                case 'hoje':
                    $q->whereDate('created_at', today());
                    break;
                case 'semana':
                    $q->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek(),
                    ]);
                    break;
                case 'mes':
                    $q->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year);
                    break;
                case 'ano':
                    $q->whereYear('created_at', now()->year);
                    break;
            }
        };

        $query = Aeroporto::with(['voos' => $carregarVoos]);
        
        $aeroportos = $query->get();
        $todosVoos = $aeroportos->flatMap->voos;

        $dados = $aeroportos->map(function ($aeroporto) {
            $voos = $aeroporto->voos;
            $totalVoos = $voos->sum('qtd_voos');
            $totalPassageiros = $voos->sum('total_passageiros');

            $notaObj = VooMetricasService::mediaPonderada($voos, 'nota_obj');
            $notaPontualidade = VooMetricasService::mediaPonderada($voos, 'nota_pontualidade');
            $notaServicos = VooMetricasService::mediaPonderada($voos, 'nota_servicos');
            $notaPatio = VooMetricasService::mediaPonderada($voos, 'nota_patio');
            $mediaGeral = VooMetricasService::mediaGeral($voos);
            
            // Voos por tipo (Regular/Charter)
            $voosRegulares = $voos->where('tipo_voo', 'Regular')->sum('qtd_voos');
            $voosCharter = $voos->where('tipo_voo', 'Charter')->sum('qtd_voos');
            
            // Voos por horário
            $voosPorHorario = [
                'EAM' => $voos->where('horario_voo', 'EAM')->sum('qtd_voos'),
                'AM' => $voos->where('horario_voo', 'AM')->sum('qtd_voos'),
                'AN' => $voos->where('horario_voo', 'AN')->sum('qtd_voos'),
                'PM' => $voos->where('horario_voo', 'PM')->sum('qtd_voos'),
                'ALL' => $voos->where('horario_voo', 'ALL')->sum('qtd_voos'),
            ];
            
            // Companhias que operam neste aeroporto
            $companhias = $voos->groupBy('companhia_aerea_id')->map(function($items) {
                $companhia = $items->first()->companhiaAerea;
                return [
                    'id' => $companhia->id,
                    'nome' => $companhia->nome,
                    'codigo' => $companhia->codigo,
                    'total_voos' => $items->sum('qtd_voos'),
                    'total_passageiros' => $items->sum('total_passageiros'),
                ];
            })->values();
            
            return [
                'id' => $aeroporto->id,
                'aeroporto' => $aeroporto->nome_aeroporto,
                'total_voos' => $totalVoos,
                'total_passageiros' => $totalPassageiros,
                'media_passageiros_por_voo' => $totalVoos > 0 ? round($totalPassageiros / $totalVoos, 0) : 0,
                'nota_obj' => round($notaObj, 2),
                'nota_pontualidade' => round($notaPontualidade, 2),
                'nota_servicos' => round($notaServicos, 2),
                'nota_patio' => round($notaPatio, 2),
                'media_geral' => round($mediaGeral, 2),
                'voos_regulares' => $voosRegulares,
                'voos_charter' => $voosCharter,
                'voos_por_horario' => $voosPorHorario,
                'companhias' => $companhias,
            ];
        })->filter(function($item) {
            // Filtrar aeroportos sem voos
            return $item['total_voos'] > 0;
        })->sortByDesc('total_voos')->values();
        
        // Totais gerais
        $totais = [
            'total_aeroportos' => $dados->count(),
            'total_voos' => $dados->sum('total_voos'),
            'total_passageiros' => $dados->sum('total_passageiros'),
            'media_geral_geral' => round(VooMetricasService::mediaGeral($todosVoos), 2),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $dados,
            'totais' => $totais,
            'periodo' => $periodo,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
    
    /**
     * View do relatório para ADMIN (tabela)
     */
    public function adminVoosPorAeroporto()
    {
        $relatorio = Relatorio::where('tipo', Relatorio::TIPO_VOOS_POR_AEROPORTO)
            ->firstOrFail();
        
        return view('admin.relatorios.voos-por-aeroporto', compact('relatorio'));
    }
    
    /**
     * View do relatório para USUÁRIO COMUM (cards)
     */
    public function userVoosPorAeroporto()
    {
        $relatorio = Relatorio::visiveis()
            ->where('tipo', Relatorio::TIPO_VOOS_POR_AEROPORTO)
            ->firstOrFail();
            
        return view('relatorios.voos-por-aeroporto', compact('relatorio'));
    }
}
