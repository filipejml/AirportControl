<?php
// app/Http/Controllers/RelatorioController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Relatorio;
use App\Models\Aeroporto;
use App\Models\CompanhiaAerea;

class RelatorioController extends Controller
{
    /**
     * LISTAGEM (ADMIN + USUÁRIO)
     */
    public function index()
    {
        if (auth()->user()->tipo == 0) {
            $relatorios = Relatorio::all();
        } else {
            $relatorios = Relatorio::where('visivel_usuario', true)->get();
        }

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
     * FORM CREATE (ADMIN)
     */
    public function create()
    {
        return view('admin.relatorios.create');
    }

    /**
     * SALVAR (ADMIN)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'tipo' => 'nullable|string|max:100', // Adicionar campo tipo
        ]);

        // TRATAMENTO DO CHECKBOX
        $data['visivel_usuario'] = $request->has('visivel_usuario');

        Relatorio::create($data);

        return redirect()->route('admin.relatorios.index')
            ->with('success', 'Relatório criado com sucesso!');
    }

    /**
     * FORM EDIT
     */
    public function edit(Relatorio $relatorio)
    {
        return view('admin.relatorios.edit', compact('relatorio'));
    }

    /**
     * UPDATE
     */
    public function update(Request $request, Relatorio $relatorio)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'tipo' => 'nullable|string|max:100',
        ]);

        $data['visivel_usuario'] = $request->has('visivel_usuario');

        $relatorio->update($data);

        return redirect()->route('admin.relatorios.index')
            ->with('success', 'Relatório atualizado!');
    }

    /**
     * DELETE
     */
    public function destroy(Relatorio $relatorio)
    {
        $relatorio->delete();

        return redirect()->route('admin.relatorios.index')
            ->with('success', 'Relatório removido!');
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
        $relatorio = Relatorio::firstOrCreate(
            ['tipo' => 'companhias_por_aeroporto'],
            [
                'nome' => 'Companhias por Aeroporto',
                'descricao' => 'Lista de todas as companhias aéreas organizadas por aeroporto',
                'visivel_usuario' => true
            ]
        );
        
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
        $relatorio = Relatorio::where('tipo', 'companhias_por_aeroporto')
            ->where('visivel_usuario', true)
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
        try {
            $novoStatus = $request->input('visivel_usuario', false);
            $relatorio->visivel_usuario = $novoStatus;
            $relatorio->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Status atualizado com sucesso!',
                'visivel_usuario' => $relatorio->visivel_usuario
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para dados do relatório de Voos por Aeroporto
     */
    public function apiVoosPorAeroporto(Request $request)
    {
        $query = Aeroporto::with(['voos' => function($q) {
            $q->with('companhia');
        }]);
        
        // Filtro por período
        if ($request->filled('periodo')) {
            switch ($request->periodo) {
                case 'hoje':
                    $query->whereHas('voos', function($q) {
                        $q->whereDate('created_at', today());
                    });
                    break;
                case 'semana':
                    $query->whereHas('voos', function($q) {
                        $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    });
                    break;
                case 'mes':
                    $query->whereHas('voos', function($q) {
                        $q->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    });
                    break;
                case 'ano':
                    $query->whereHas('voos', function($q) {
                        $q->whereYear('created_at', now()->year);
                    });
                    break;
            }
        }
        
        $dados = $query->get()->map(function ($aeroporto) {
            $voos = $aeroporto->voos;
            $totalVoos = $voos->count();
            $totalPassageiros = $voos->sum('total_passageiros');
            
            // Médias das notas
            $notaObj = $voos->avg('nota_obj') ?? 0;
            $notaPontualidade = $voos->avg('nota_pontualidade') ?? 0;
            $notaServicos = $voos->avg('nota_servicos') ?? 0;
            $notaPatio = $voos->avg('nota_patio') ?? 0;
            $mediaGeral = ($notaObj + $notaPontualidade + $notaServicos + $notaPatio) / 4;
            
            // Voos por tipo (Regular/Charter)
            $voosRegulares = $voos->where('tipo_voo', 'Regular')->count();
            $voosCharter = $voos->where('tipo_voo', 'Charter')->count();
            
            // Voos por horário
            $voosPorHorario = [
                'EAM' => $voos->where('horario_voo', 'EAM')->count(),
                'AM' => $voos->where('horario_voo', 'AM')->count(),
                'AN' => $voos->where('horario_voo', 'AN')->count(),
                'PM' => $voos->where('horario_voo', 'PM')->count(),
                'ALL' => $voos->where('horario_voo', 'ALL')->count(),
            ];
            
            // Companhias que operam neste aeroporto
            $companhias = $voos->groupBy('companhia.id')->map(function($items) {
                $companhia = $items->first()->companhia;
                return [
                    'id' => $companhia->id,
                    'nome' => $companhia->nome,
                    'codigo' => $companhia->codigo,
                    'total_voos' => $items->count(),
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
            'media_geral_geral' => round($dados->avg('media_geral'), 2),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $dados,
            'totais' => $totais,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
    
    /**
     * View do relatório para ADMIN (tabela)
     */
    public function adminVoosPorAeroporto()
    {
        // Verificar se o relatório existe no banco, se não, criar
        $relatorio = Relatorio::firstOrCreate(
            ['tipo' => 'voos_por_aeroporto'],
            [
                'nome' => 'Voos por Aeroporto',
                'descricao' => 'Estatísticas de voos, passageiros e notas organizadas por aeroporto',
                'visivel_usuario' => true
            ]
        );
        
        return view('admin.relatorios.voos-por-aeroporto', compact('relatorio'));
    }
    
    /**
     * View do relatório para USUÁRIO COMUM (cards)
     */
    public function userVoosPorAeroporto()
    {
        $relatorio = Relatorio::where('tipo', 'voos_por_aeroporto')
            ->where('visivel_usuario', true)
            ->firstOrFail();
            
        return view('relatorios.voos-por-aeroporto', compact('relatorio'));
    }
}