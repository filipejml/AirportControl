<?php
// app/Http/Controllers/VooController.php

namespace App\Http\Controllers;

use App\Models\Voo;
use App\Models\Aeroporto;
use App\Models\CompanhiaAerea;
use App\Models\Aeronave;
use App\Helpers\CompanhiaHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VooController extends Controller
{
    public function index()
    {
        $voos = Voo::with(['aeroporto', 'companhiaAerea', 'aeronave.fabricante'])
            ->orderBy('created_at', 'desc')
            ->get();
        
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
        
        return view('voos.create', compact('aeroportos', 'companhias'));
    }

    public function store(Request $request)
    {
        // Primeiro, validamos o formato básico
        $validated = $request->validate([
            'id_voo' => 'required|string|max:10|unique:voos,id_voo|regex:/^[A-Z]{2,4}-\d{4}$/',
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

        // Validar o código do ID do voo
        $codigo = CompanhiaHelper::extrairCodigo($request->id_voo);
        
        if (!$codigo || !CompanhiaHelper::isCodigoValido($codigo)) {
            $codigosValidos = implode(', ', CompanhiaHelper::getCodigosValidos());
            return redirect()->back()
                ->with('error', "Código de companhia inválido! O ID do voo deve começar com um dos códigos válidos: {$codigosValidos}")
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $aeronave = Aeronave::findOrFail($request->aeronave_id);
            
            // Mapeamento de notas letra para número
            $mapaNotas = [
                'A' => 10,
                'B' => 9,
                'C' => 8,
                'D' => 6,
                'E' => 4,
                'F' => 2
            ];
            
            $voo = Voo::create([
                'id_voo' => strtoupper($request->id_voo),
                'aeroporto_id' => $request->aeroporto_id,
                'companhia_aerea_id' => $request->companhia_aerea_id,
                'aeronave_id' => $request->aeronave_id,
                'tipo_voo' => $request->tipo_voo,
                'tipo_aeronave' => $aeronave->porte,
                'qtd_voos' => $request->qtd_voos,
                'horario_voo' => $request->horario_voo,
                'qtd_passageiros' => $aeronave->capacidade,
                'nota_obj' => $request->nota_obj ? $mapaNotas[$request->nota_obj] : null,
                'nota_pontualidade' => $request->nota_pontualidade ? $mapaNotas[$request->nota_pontualidade] : null,
                'nota_servicos' => $request->nota_servicos ? $mapaNotas[$request->nota_servicos] : null,
                'nota_patio' => $request->nota_patio ? $mapaNotas[$request->nota_patio] : null
            ]);

            DB::commit();

            $mensagem = "Voo {$voo->id_voo} cadastrado com sucesso!";
            if ($voo->media_notas) {
                $mensagem .= " Média das notas: " . number_format($voo->media_notas, 1) . " ({$voo->media_notas_letra})";
            }

            return redirect()->route('voos.index')
                ->with('success', $mensagem);

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
        $validated = $request->validate([
            'id_voo' => 'required|string|max:10|unique:voos,id_voo,' . $voo->id . '|regex:/^[A-Z]{2,4}-\d{4}$/',
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

        // Validar o código do ID do voo
        $codigo = CompanhiaHelper::extrairCodigo($request->id_voo);
        
        if (!$codigo || !CompanhiaHelper::isCodigoValido($codigo)) {
            $codigosValidos = implode(', ', CompanhiaHelper::getCodigosValidos());
            return redirect()->back()
                ->with('error', "Código de companhia inválido! O ID do voo deve começar com um dos códigos válidos: {$codigosValidos}")
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $aeronave = Aeronave::findOrFail($request->aeronave_id);
            
            $mapaNotas = [
                'A' => 10,
                'B' => 9,
                'C' => 8,
                'D' => 6,
                'E' => 4,
                'F' => 2
            ];
            
            $voo->update([
                'id_voo' => strtoupper($request->id_voo),
                'aeroporto_id' => $request->aeroporto_id,
                'companhia_aerea_id' => $request->companhia_aerea_id,
                'aeronave_id' => $request->aeronave_id,
                'tipo_voo' => $request->tipo_voo,
                'tipo_aeronave' => $aeronave->porte,
                'qtd_voos' => $request->qtd_voos,
                'horario_voo' => $request->horario_voo,
                'qtd_passageiros' => $aeronave->capacidade,
                'nota_obj' => $request->nota_obj ? $mapaNotas[$request->nota_obj] : null,
                'nota_pontualidade' => $request->nota_pontualidade ? $mapaNotas[$request->nota_pontualidade] : null,
                'nota_servicos' => $request->nota_servicos ? $mapaNotas[$request->nota_servicos] : null,
                'nota_patio' => $request->nota_patio ? $mapaNotas[$request->nota_patio] : null
            ]);

            DB::commit();

            $mensagem = "Voo {$voo->id_voo} atualizado com sucesso!";
            if ($voo->media_notas) {
                $mensagem .= " Média das notas: " . number_format($voo->media_notas, 1) . " ({$voo->media_notas_letra})";
            }

            return redirect()->route('voos.index')
                ->with('success', $mensagem);

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

    // Rota AJAX para buscar aeronaves por companhia
    public function getAeronavesByCompanhia($companhiaId)
    {
        $aeronaves = Aeronave::whereHas('companhias', function($query) use ($companhiaId) {
            $query->where('companhias_aereas.id', $companhiaId);
        })->with('fabricante')->get();
        
        return response()->json($aeronaves);
    }

    // Rota AJAX para verificar código do ID do voo
    public function verificarIdVoo(Request $request)
    {
        $idVoo = $request->get('id_voo');
        $excludeId = $request->get('exclude_id');
        
        $codigo = CompanhiaHelper::extrairCodigo($idVoo);
        
        if (!$codigo || !CompanhiaHelper::isCodigoValido($codigo)) {
            return response()->json([
                'valid' => false,
                'message' => 'Código de companhia inválido!'
            ]);
        }
        
        $exists = Voo::where('id_voo', $idVoo)
            ->when($excludeId, function($query) use ($excludeId) {
                $query->where('id', '!=', $excludeId);
            })
            ->exists();
        
        if ($exists) {
            return response()->json([
                'valid' => false,
                'message' => 'Este ID de voo já está cadastrado!'
            ]);
        }
        
        return response()->json([
            'valid' => true,
            'message' => 'Código válido!',
            'companhia' => CompanhiaHelper::getNomeCompanhia($codigo)
        ]);
    }
}