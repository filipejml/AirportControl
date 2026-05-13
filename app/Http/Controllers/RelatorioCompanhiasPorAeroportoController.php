<?php
// app/Http/Controllers/RelatorioCompanhiasPorAeroportoController.php

namespace App\Http\Controllers;

use App\Models\Aeroporto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class RelatorioCompanhiasPorAeroportoController extends Controller
{
    public function getData()
    {
        $dados = Aeroporto::with('companhias')
            ->get()
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
            'timestamp' => now()->toIso8601String()
        ]);
    }

    public function adminIndex()
    {
        // Adiciona asset específico para admin
        View::share('adminRelatorio', true);
        return view('admin.relatorios.companhias-por-aeroporto');
    }

    public function userIndex()
    {
        return view('relatorios.companhias-por-aeroporto');
    }
}