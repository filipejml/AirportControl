<?php
// app/Http/Controllers/VooController.php

namespace App\Http\Controllers;

use App\Models\Voo;
use App\Models\Aeroporto;
use App\Models\CompanhiaAerea;
use App\Models\Aeronave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VooController extends Controller
{
    public function index()
    {
        $voos = Voo::with(['aeroporto', 'companhiaAerea', 'aeronave.fabricante'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Recalcular médias para garantir (opcional)
        foreach ($voos as $voo) {
            if ($voo->isDirty()) {
                $voo->save();
            }
        }
        
        // Estatísticas para os cards
        $estatisticas = [
            'total_voos' => $voos->count(),
            'total_passageiros' => $voos->sum('total_passageiros'),
            'media_pax_voo' => $voos->count() > 0 ? round($voos->sum('total_passageiros') / $voos->count(), 0) : 0,
            'voos_com_notas' => $voos->filter(function($voo) { return $voo->media_notas !== null; })->count(),
            'media_geral_notas' => $voos->filter(function($voo) { return $voo->media_notas !== null; })->avg('media_notas')
        ];
        
        return view('voos.index', compact('voos', 'estatisticas'));
    }

    public function create()
    {
        $aeroportos = Aeroporto::orderBy('nome_aeroporto')->get();
        $companhias = CompanhiaAerea::orderBy('nome')->get();
        $aeronaves = Aeronave::with('fabricante', 'companhias')->get();
        
        return view('voos.create', compact('aeroportos', 'companhias', 'aeronaves'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_voo' => 'required|string|max:10|unique:voos,id_voo',
            'aeroporto_id' => 'required|exists:aeroportos,id',
            'companhia_aerea_id' => 'required|exists:companhias_aereas,id',
            'aeronave_id' => 'required|exists:aeronaves,id',
            'tipo_voo' => 'required|in:Regular,Charter',
            'qtd_voos' => 'required|integer|min:1',
            'horario_voo' => 'required|in:EAM,AM,AN,PM,ALL',
            'nota_obj' => 'nullable|in:A,B,C,D,E,F',
            'nota_pontualidade' => 'nullable|in:A,B,C,D,E,F',
            'nota_servicos' => 'nullable|in:A,B,C,D,E,F',
            'nota_patio' => 'nullable|in:A,B,C,D,E,F'
        ]);

        try {
            DB::beginTransaction();

            $aeronave = Aeronave::findOrFail($request->aeronave_id);
            
            $voo = Voo::create([
                'id_voo' => $request->id_voo,
                'aeroporto_id' => $request->aeroporto_id,
                'companhia_aerea_id' => $request->companhia_aerea_id,
                'aeronave_id' => $request->aeronave_id,
                'tipo_voo' => $request->tipo_voo,
                'qtd_voos' => $request->qtd_voos,
                'horario_voo' => $request->horario_voo,
                'qtd_passageiros' => $aeronave->capacidade,
                'nota_obj' => $request->nota_obj,
                'nota_pontualidade' => $request->nota_pontualidade,
                'nota_servicos' => $request->nota_servicos,
                'nota_patio' => $request->nota_patio
            ]);

            DB::commit();

            return redirect()->route('voos.index')
                ->with('success', "Voo {$voo->id_voo} cadastrado com sucesso! " . 
                    ($voo->media_notas ? "Média das notas: {$voo->media_notas}" : ""));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erro ao cadastrar voo: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Voo $voo)
    {
        $voo->load(['aeroporto', 'companhiaAerea', 'aeronave.fabricante']);
        return view('voos.show', compact('voo'));
    }

    public function edit(Voo $voo)
    {
        $aeroportos = Aeroporto::orderBy('nome_aeroporto')->get();
        $companhias = CompanhiaAerea::orderBy('nome')->get();
        $aeronaves = Aeronave::with('fabricante', 'companhias')->get();
        
        return view('voos.edit', compact('voo', 'aeroportos', 'companhias', 'aeronaves'));
    }

    public function update(Request $request, Voo $voo)
    {
        $request->validate([
            'id_voo' => 'required|string|max:10|unique:voos,id_voo,' . $voo->id,
            'aeroporto_id' => 'required|exists:aeroportos,id',
            'companhia_aerea_id' => 'required|exists:companhias_aereas,id',
            'aeronave_id' => 'required|exists:aeronaves,id',
            'tipo_voo' => 'required|in:Regular,Charter',
            'qtd_voos' => 'required|integer|min:1',
            'horario_voo' => 'required|in:EAM,AM,AN,PM,ALL',
            'nota_obj' => 'nullable|in:A,B,C,D,E,F',
            'nota_pontualidade' => 'nullable|in:A,B,C,D,E,F',
            'nota_servicos' => 'nullable|in:A,B,C,D,E,F',
            'nota_patio' => 'nullable|in:A,B,C,D,E,F'
        ]);

        try {
            DB::beginTransaction();

            $aeronave = Aeronave::findOrFail($request->aeronave_id);
            
            $voo->update([
                'id_voo' => $request->id_voo,
                'aeroporto_id' => $request->aeroporto_id,
                'companhia_aerea_id' => $request->companhia_aerea_id,
                'aeronave_id' => $request->aeronave_id,
                'tipo_voo' => $request->tipo_voo,
                'qtd_voos' => $request->qtd_voos,
                'horario_voo' => $request->horario_voo,
                'qtd_passageiros' => $aeronave->capacidade,
                'nota_obj' => $request->nota_obj,
                'nota_pontualidade' => $request->nota_pontualidade,
                'nota_servicos' => $request->nota_servicos,
                'nota_patio' => $request->nota_patio
            ]);

            DB::commit();

            return redirect()->route('voos.index')
                ->with('success', "Voo {$voo->id_voo} atualizado com sucesso! " .
                    ($voo->media_notas ? "Média das notas: {$voo->media_notas}" : ""));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erro ao atualizar voo: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Voo $voo)
    {
        try {
            $idVoo = $voo->id_voo;
            $voo->delete();
            return redirect()->route('voos.index')
                ->with('success', "Voo {$idVoo} excluído com sucesso!");
        } catch (\Exception $e) {
            return redirect()->route('voos.index')
                ->with('error', 'Erro ao excluir voo: ' . $e->getMessage());
        }
    }

    public function getAeronavesByCompanhia($companhiaId)
    {
        $companhia = CompanhiaAerea::with('aeronaves.fabricante')->findOrFail($companhiaId);
        return response()->json($companhia->aeronaves);
    }
}