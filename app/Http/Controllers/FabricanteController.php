<?php
// app/Http/Controllers/FabricanteController.php

namespace App\Http\Controllers;

use App\Models\Fabricante;
use App\Services\VooMetricasService;
use Illuminate\Http\Request;

class FabricanteController extends Controller
{
    public function informacoes()
    {
        $fabricantes = Fabricante::query()
            ->with(['aeronaves.voos', 'aeronaves.companhias'])
            ->orderBy('nome')
            ->get()
            ->map(function (Fabricante $fabricante) {
                $voos = $fabricante->aeronaves->flatMap->voos;
                $totalVoos = (int) $voos->sum('qtd_voos');
                $mediasPorTipo = [
                    'objetivo' => VooMetricasService::mediaPonderada($voos, 'nota_obj'),
                    'pontualidade' => VooMetricasService::mediaPonderada($voos, 'nota_pontualidade'),
                    'servicos' => VooMetricasService::mediaPonderada($voos, 'nota_servicos'),
                    'patio' => VooMetricasService::mediaPonderada($voos, 'nota_patio'),
                ];
                $mediasValidas = collect($mediasPorTipo)->filter(fn ($media) => $media > 0);

                return [
                    'id' => $fabricante->id,
                    'nome' => $fabricante->nome,
                    'pais_origem' => $fabricante->pais_origem ?: 'Não informado',
                    'total_modelos' => $fabricante->aeronaves->count(),
                    'total_companhias' => $fabricante->aeronaves
                        ->flatMap->companhias
                        ->unique('id')
                        ->count(),
                    'total_voos' => $totalVoos,
                    'total_passageiros' => (int) $voos->sum('total_passageiros'),
                    'nota_geral' => $mediasValidas->isEmpty() ? 0 : round($mediasValidas->avg(), 1),
                    'medias_por_tipo' => collect($mediasPorTipo)
                        ->map(fn ($media) => round($media, 1))
                        ->all(),
                ];
            });

        $estatisticas = [
            'total_fabricantes' => $fabricantes->count(),
            'total_paises' => $fabricantes
                ->pluck('pais_origem')
                ->reject(fn ($pais) => $pais === 'Não informado')
                ->unique()
                ->count(),
            'total_modelos' => $fabricantes->sum('total_modelos'),
            'total_voos' => $fabricantes->sum('total_voos'),
            'total_passageiros' => $fabricantes->sum('total_passageiros'),
        ];

        return view('fabricantes.informacoes', compact('fabricantes', 'estatisticas'));
    }

    public function index()
    {
        $fabricantes = Fabricante::withCount('aeronaves')->orderBy('nome')->get();
        return view('admin.fabricantes.index', compact('fabricantes'));
    }

    public function create()
    {
        return view('admin.fabricantes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:fabricantes',
            'pais_origem' => 'nullable|string|max:255'
        ]);

        $fabricante = Fabricante::create($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'fabricante' => $fabricante
            ]);
        }

        return redirect()->route('fabricantes.index')
            ->with('success', 'Fabricante cadastrado com sucesso!');
    }

    public function show(Fabricante $fabricante)
    {
        // Carregar as aeronaves, companhias e movimentação operacional
        $fabricante->load(['aeronaves.companhias', 'aeronaves.voos']);
        $fabricante->loadCount('aeronaves');

        $voos = $fabricante->aeronaves->flatMap->voos;
        $totalVoos = (int) $voos->sum('qtd_voos');
        $medias = [
            'objetivo' => round(VooMetricasService::mediaPonderada($voos, 'nota_obj'), 1),
            'pontualidade' => round(VooMetricasService::mediaPonderada($voos, 'nota_pontualidade'), 1),
            'servicos' => round(VooMetricasService::mediaPonderada($voos, 'nota_servicos'), 1),
            'patio' => round(VooMetricasService::mediaPonderada($voos, 'nota_patio'), 1),
        ];
        $mediasValidas = collect($medias)->filter(fn ($media) => $media > 0);

        $estatisticasOperacionais = [
            'total_voos' => $totalVoos,
            'total_passageiros' => (int) $voos->sum('total_passageiros'),
            'passageiros_por_voo' => $totalVoos > 0
                ? round($voos->sum('total_passageiros') / $totalVoos)
                : 0,
            'total_aeroportos' => $voos->pluck('aeroporto_id')->filter()->unique()->count(),
            'nota_geral' => $mediasValidas->isEmpty() ? 0 : round($mediasValidas->avg(), 1),
            'medias' => $medias,
        ];
        
        return view('admin.fabricantes.show', compact('fabricante', 'estatisticasOperacionais'));
    }

    public function edit(Fabricante $fabricante)
    {
        return view('admin.fabricantes.edit', compact('fabricante'));
    }

    public function update(Request $request, Fabricante $fabricante)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:fabricantes,nome,' . $fabricante->id,
            'pais_origem' => 'nullable|string|max:255'
        ]);

        $fabricante->update($request->all());

        return redirect()->route('fabricantes.index')
            ->with('success', 'Fabricante atualizado com sucesso!');
    }

    public function destroy(Fabricante $fabricante)
    {
        // Verificar se existem aeronaves vinculadas
        if ($fabricante->aeronaves()->count() > 0) {
            return redirect()->route('fabricantes.index')
                ->with('error', 'Não é possível excluir este fabricante pois existem aeronaves vinculadas a ele.');
        }

        $fabricante->delete();

        return redirect()->route('fabricantes.index')
            ->with('success', 'Fabricante excluído com sucesso!');
    }
}
