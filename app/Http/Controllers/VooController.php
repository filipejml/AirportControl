<?php
// app/Http/Controllers/VooController.php

namespace App\Http\Controllers;

use App\Models\Voo;
use App\Models\Aeroporto;
use App\Models\CompanhiaAerea;
use App\Models\Aeronave;
use App\Helpers\CompanhiaHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

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

    /**
     * Exporta a lista de voos para CSV com suporte a filtros
     */
    public function exportCSV(Request $request)
    {
        // Buscar voos com relacionamentos, aplicando os mesmos filtros da listagem
        $query = Voo::with(['aeroporto', 'companhiaAerea', 'aeronave'])
            ->orderBy('created_at', 'desc');
        
        // Aplicar filtros se existirem
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id_voo', 'like', "%{$search}%")
                ->orWhereHas('aeroporto', function($sq) use ($search) {
                    $sq->where('nome_aeroporto', 'like', "%{$search}%");
                })
                ->orWhereHas('companhiaAerea', function($sq) use ($search) {
                    $sq->where('nome', 'like', "%{$search}%");
                })
                ->orWhereHas('aeronave', function($sq) use ($search) {
                    $sq->where('modelo', 'like', "%{$search}%");
                });
            });
        }
        
        if ($request->has('tipo') && $request->tipo) {
            $query->where('tipo_voo', $request->tipo);
        }
        
        if ($request->has('horario') && $request->horario) {
            $query->where('horario_voo', $request->horario);
        }
        
        if ($request->has('dias') && $request->dias) {
            $dataLimite = now()->subDays((int)$request->dias);
            $query->where('created_at', '>=', $dataLimite);
        }
        
        $voos = $query->get();
        
        // Se não houver dados, retornar com mensagem de erro
        if ($voos->isEmpty()) {
            return redirect()->back()->with('error', 'Não há voos para exportar com os filtros selecionados.');
        }
        
        // Nome do arquivo com quantidade de registros
        $filename = 'voos_' . date('Y-m-d_His') . '_' . $voos->count() . '_registros.csv';
        
        // Headers para download
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
            'Pragma' => 'public'
        ];
        
        // Callback para gerar o CSV
        $callback = function() use ($voos) {
            $file = fopen('php://output', 'w');
            
            // Adicionar BOM para UTF-8 (resolve problemas de acentuação no Excel)
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Cabeçalho do CSV (corrigido para corresponder aos dados)
            fputcsv($file, [
                'ID do Voo',
                'Data Cadastro',
                'Companhia Aérea',
                'Aeroporto',
                'Aeronave',
                'Tipo de Voo',
                'Tipo Aeronave',
                'Quantidade Voos',
                'Horário',
                'Capacidade (pax/voo)',
                'Total Passageiros',
                'Nota Objetivo',
                'Nota Pontualidade',
                'Nota Serviços',
                'Nota Pátio',
                'Média das Notas',
                'Classificação'
            ], ';');
            
            // Dados
            foreach ($voos as $voo) {
                // Mapeamento de notas
                $notaObj = $voo->nota_obj ? $voo->nota_obj . ' (' . $this->getNotaLetra($voo->nota_obj) . ')' : '';
                $notaPont = $voo->nota_pontualidade ? $voo->nota_pontualidade . ' (' . $this->getNotaLetra($voo->nota_pontualidade) . ')' : '';
                $notaServ = $voo->nota_servicos ? $voo->nota_servicos . ' (' . $this->getNotaLetra($voo->nota_servicos) . ')' : '';
                $notaPatio = $voo->nota_patio ? $voo->nota_patio . ' (' . $this->getNotaLetra($voo->nota_patio) . ')' : '';
                
                // Converter tipo de aeronave para texto
                $tipoAeronaveTexto = match($voo->tipo_aeronave) {
                    'PC' => 'Pequeno Porte',
                    'MC' => 'Médio Porte',
                    'LC' => 'Grande Porte',
                    default => $voo->tipo_aeronave ?? ''
                };
                
                // Converter horário para texto
                $horarioTexto = match($voo->horario_voo) {
                    'EAM' => 'Early Morning (00h-06h)',
                    'AM' => 'Morning (06h-12h)',
                    'AN' => 'Afternoon (12h-18h)',
                    'PM' => 'Evening (18h-00h)',
                    'ALL' => 'Diário',
                    default => $voo->horario_voo ?? ''
                };
                
                fputcsv($file, [
                    $voo->id_voo,
                    $voo->created_at ? $voo->created_at->format('d/m/Y H:i:s') : '',
                    $voo->companhiaAerea->nome ?? '',
                    $voo->aeroporto->nome_aeroporto ?? '',
                    $voo->aeronave->modelo ?? '',
                    $voo->tipo_voo ?? '',
                    $tipoAeronaveTexto,
                    $voo->qtd_voos,
                    $horarioTexto,
                    $voo->qtd_passageiros ?? 0,
                    number_format($voo->total_passageiros, 0, ',', '.'),
                    $notaObj,
                    $notaPont,
                    $notaServ,
                    $notaPatio,
                    $voo->media_notas ? number_format($voo->media_notas, 1) : '',
                    $this->getClassificacaoMedia($voo->media_notas)
                ], ';');
            }
            
            fclose($file);
        };
        
        return new StreamedResponse($callback, 200, $headers);
    }

    /**
     * Converte nota numérica para letra
     */
    private function getNotaLetra($nota)
    {
        return match($nota) {
            10 => 'A',
            9 => 'B',
            8 => 'C',
            6 => 'D',
            4 => 'E',
            2 => 'F',
            default => ''
        };
    }

    /**
     * Retorna classificação baseada na média
     */
    private function getClassificacaoMedia($media)
    {
        if (!$media) return '';
        
        return match(true) {
            $media >= 9 => 'Excelente',
            $media >= 7 => 'Bom',
            $media >= 5 => 'Regular',
            default => 'Ruim'
        };
    }

    /**
     * Exporta a lista de voos para PDF
     */
    public function exportPDF(Request $request)
    {
        try {
            // Buscar voos com relacionamentos, aplicando os mesmos filtros da listagem
            $query = Voo::with(['aeroporto', 'companhiaAerea', 'aeronave'])
                ->orderBy('created_at', 'desc');
            
            // Aplicar filtros
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('id_voo', 'like', "%{$search}%")
                    ->orWhereHas('aeroporto', function($sq) use ($search) {
                        $sq->where('nome_aeroporto', 'like', "%{$search}%");
                    })
                    ->orWhereHas('companhiaAerea', function($sq) use ($search) {
                        $sq->where('nome', 'like', "%{$search}%");
                    })
                    ->orWhereHas('aeronave', function($sq) use ($search) {
                        $sq->where('modelo', 'like', "%{$search}%");
                    });
                });
            }
            
            if ($request->filled('tipo')) {
                $query->where('tipo_voo', $request->tipo);
            }
            
            if ($request->filled('horario')) {
                $query->where('horario_voo', $request->horario);
            }
            
            if ($request->filled('dias')) {
                $dataLimite = now()->subDays((int)$request->dias);
                $query->where('created_at', '>=', $dataLimite);
            }
            
            $voos = $query->get();
            
            // Se não houver dados
            if ($voos->isEmpty()) {
                if ($request->ajax()) {
                    return response()->json(['error' => 'Não há voos para exportar com os filtros selecionados.'], 404);
                }
                return redirect()->back()->with('error', 'Não há voos para exportar com os filtros selecionados.');
            }
            
            // Calcular estatísticas para o relatório
            $estatisticas = [
                'total_voos' => $voos->count(),
                'total_passageiros' => $voos->sum('total_passageiros'),
                'media_pax_voo' => $voos->count() > 0 ? round($voos->sum('total_passageiros') / $voos->count(), 0) : 0,
                'voos_com_notas' => $voos->filter(function($voo) { return $voo->media_notas !== null; })->count(),
                'media_geral_notas' => $voos->filter(function($voo) { return $voo->media_notas !== null; })->avg('media_notas'),
                'data_exportacao' => now()->format('d/m/Y H:i:s'),
                'filtros_aplicados' => $this->getFiltrosTexto($request)
            ];
            
            // Preparar dados para a view
            $data = [
                'voos' => $voos,
                'estatisticas' => $estatisticas,
                'titulo' => 'Relatório de Voos',
                'empresa' => 'Airport Manager',
                'data_geracao' => now()->format('d/m/Y H:i:s')
            ];
            
            // Gerar PDF
            $pdf = Pdf::loadView('pdf.voos-relatorio', $data);
            
            // Configurar opções do PDF
            $pdf->setPaper('A4', 'landscape');
            $pdf->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true
            ]);
            
            // Nome do arquivo
            $filename = 'relatorio_voos_' . date('Y-m-d_His') . '.pdf';
            
            // Download do PDF
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao exportar PDF: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['error' => 'Erro ao exportar PDF: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Erro ao exportar PDF: ' . $e->getMessage());
        }
    }

    /**
     * Retorna texto dos filtros aplicados
     */
    private function getFiltrosTexto($request)
    {
        $filtros = [];
        
        if ($request->filled('search')) {
            $filtros[] = "Busca: {$request->search}";
        }
        if ($request->filled('tipo')) {
            $filtros[] = "Tipo: {$request->tipo}";
        }
        if ($request->filled('horario')) {
            $filtros[] = "Horário: {$request->horario}";
        }
        if ($request->filled('dias')) {
            $filtros[] = "Últimos {$request->dias} dias";
        }
        
        return empty($filtros) ? 'Todos os registros' : implode(' | ', $filtros);
    }
}