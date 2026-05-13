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
}