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
    public function index(Request $request)
    {
        $query = Voo::with(['aeroporto', 'companhiaAerea', 'aeronave.fabricante'])
            ->orderBy('created_at', 'desc');
        
        // Aplicar filtros se existirem
        if ($request->has('search') && $request->search) {
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
        
        // Configurar tamanho da página
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
        
        $voos = $query->paginate($perPage)->withQueryString();
        
        $estatisticas = [
            'total_voos' => Voo::count(),
            'total_passageiros' => Voo::sum('total_passageiros'),
            'media_pax_voo' => Voo::count() > 0 ? round(Voo::sum('total_passageiros') / Voo::count(), 0) : 0,
            'voos_com_notas' => Voo::whereNotNull('media_notas')->count(),
            'media_geral_notas' => Voo::whereNotNull('media_notas')->avg('media_notas')
        ];
        
        return view('voos.index', compact('voos', 'estatisticas', 'perPage'));
    }

    public function create()
    {
        $aeroportos = Aeroporto::orderBy('nome_aeroporto')->get();
        $companhias = CompanhiaAerea::orderBy('nome')->get();

        $companhiaCodigos = $companhias->mapWithKeys(function ($companhia) {
            return [$companhia->id => CompanhiaHelper::buscarCodigoPorNome($companhia->nome) ?: ''];
        })->toArray();
        
        $ultimoVoo = Voo::with([
            'aeroporto' => function($query) {
                $query->select('id', 'nome_aeroporto');
            },
            'companhiaAerea' => function($query) {
                $query->select('id', 'nome');
            },
            'aeronave' => function($query) {
                $query->select('id', 'modelo', 'capacidade', 'porte');
            }
        ])
        ->select([
            'id', 'id_voo', 'aeroporto_id', 'companhia_aerea_id', 
            'aeronave_id', 'tipo_aeronave', 'qtd_voos',
            'horario_voo', 'nota_obj', 'nota_pontualidade', 'nota_servicos',
            'nota_patio', 'media_notas', 'qtd_passageiros', 'created_at', 
            'tipo_voo'
        ])
        ->orderBy('created_at', 'desc')
        ->first();
        
        return view('voos.create', compact('aeroportos', 'companhias', 'ultimoVoo', 'companhiaCodigos'));
    }

    public function store(Request $request)
    {
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
            'nota_patio' => 'nullable|in:A,B,C,D,E,F',
            'created_at' => 'nullable|date'
        ]);

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
            
            $updateData = [
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
            ];
            
            if ($request->has('created_at') && $request->created_at) {
                $updateData['created_at'] = $request->created_at;
            }
            
            $voo->update($updateData);

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

    public function getAeronavesByCompanhia($companhiaId)
    {
        $aeronaves = Aeronave::whereHas('companhias', function($query) use ($companhiaId) {
            $query->where('companhias_aereas.id', $companhiaId);
        })
        ->with('fabricante')
        ->get();
        
        return response()->json($aeronaves->map(function($aeronave) {
            return [
                'id' => $aeronave->id,
                'modelo' => $aeronave->modelo,
                'capacidade' => $aeronave->capacidade,
                'porte' => $aeronave->porte,
                'fabricante' => $aeronave->fabricante ? ['nome' => $aeronave->fabricante->nome] : null
            ];
        }));
    }

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
        
        $companhia = CompanhiaAerea::where('codigo', $codigo)
            ->orWhere('nome', 'like', '%' . CompanhiaHelper::getNomeCompanhia($codigo) . '%')
            ->first();
        
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
            'codigo' => $codigo,
            'companhia_nome' => CompanhiaHelper::getNomeCompanhia($codigo),
            'companhia_encontrada' => $companhia ? true : false,
            'companhia_id' => $companhia ? $companhia->id : null,
            'companhia_nome_completo' => $companhia ? $companhia->nome : null
        ]);
    }

    public function exportCSV(Request $request)
    {
        $query = Voo::with(['aeroporto', 'companhiaAerea', 'aeronave'])
            ->orderBy('created_at', 'desc');
        
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
        
        if ($voos->isEmpty()) {
            return redirect()->back()->with('error', 'Não há voos para exportar com os filtros selecionados.');
        }
        
        $filename = 'voos_' . date('Y-m-d_His') . '_' . $voos->count() . '_registros.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
            'Pragma' => 'public'
        ];
        
        $callback = function() use ($voos) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
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
            
            foreach ($voos as $voo) {
                $notaObj = $voo->nota_obj ? $voo->nota_obj . ' (' . $this->getNotaLetra($voo->nota_obj) . ')' : '';
                $notaPont = $voo->nota_pontualidade ? $voo->nota_pontualidade . ' (' . $this->getNotaLetra($voo->nota_pontualidade) . ')' : '';
                $notaServ = $voo->nota_servicos ? $voo->nota_servicos . ' (' . $this->getNotaLetra($voo->nota_servicos) . ')' : '';
                $notaPatio = $voo->nota_patio ? $voo->nota_patio . ' (' . $this->getNotaLetra($voo->nota_patio) . ')' : '';
                
                $tipoAeronaveTexto = match($voo->tipo_aeronave) {
                    'PC' => 'Pequeno Porte',
                    'MC' => 'Médio Porte',
                    'LC' => 'Grande Porte',
                    default => $voo->tipo_aeronave ?? ''
                };
                
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

    public function exportPDF(Request $request)
    {
        try {
            $query = Voo::with(['aeroporto', 'companhiaAerea', 'aeronave'])
                ->orderBy('created_at', 'desc');
            
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
            
            if ($voos->isEmpty()) {
                if ($request->ajax()) {
                    return response()->json(['error' => 'Não há voos para exportar com os filtros selecionados.'], 404);
                }
                return redirect()->back()->with('error', 'Não há voos para exportar com os filtros selecionados.');
            }
            
            $estatisticas = [
                'total_voos' => $voos->count(),
                'total_passageiros' => $voos->sum('total_passageiros'),
                'media_pax_voo' => $voos->count() > 0 ? round($voos->sum('total_passageiros') / $voos->count(), 0) : 0,
                'voos_com_notas' => $voos->filter(function($voo) { return $voo->media_notas !== null; })->count(),
                'media_geral_notas' => $voos->filter(function($voo) { return $voo->media_notas !== null; })->avg('media_notas'),
                'data_exportacao' => now()->format('d/m/Y H:i:s'),
                'filtros_aplicados' => $this->getFiltrosTexto($request)
            ];
            
            $data = [
                'voos' => $voos,
                'estatisticas' => $estatisticas,
                'titulo' => 'Relatório de Voos',
                'empresa' => 'Airport Manager',
                'data_geracao' => now()->format('d/m/Y H:i:s')
            ];
            
            $pdf = Pdf::loadView('pdf.voos-relatorio', $data);
            $pdf->setPaper('A4', 'landscape');
            $pdf->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true
            ]);
            
            $filename = 'relatorio_voos_' . date('Y-m-d_His') . '.pdf';
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao exportar PDF: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['error' => 'Erro ao exportar PDF: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Erro ao exportar PDF: ' . $e->getMessage());
        }
    }

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

    public function buscarCompanhiaPorCodigo($codigo)
    {
        if (!CompanhiaHelper::isCodigoValido($codigo)) {
            return response()->json([
                'valid' => false,
                'message' => 'Código de companhia inválido!'
            ]);
        }
        
        $companhia = CompanhiaAerea::where('codigo', $codigo)
            ->orWhere('nome', 'like', '%' . CompanhiaHelper::getNomeCompanhia($codigo) . '%')
            ->first();
        
        if ($companhia) {
            return response()->json([
                'valid' => true,
                'companhia_id' => $companhia->id,
                'companhia_nome' => $companhia->nome,
                'message' => 'Companhia identificada: ' . $companhia->nome
            ]);
        }
        
        return response()->json([
            'valid' => true,
            'companhia_id' => null,
            'companhia_nome' => CompanhiaHelper::getNomeCompanhia($codigo),
            'message' => 'Código válido: ' . CompanhiaHelper::getNomeCompanhia($codigo) . ' (Companhia não cadastrada no sistema)'
        ]);
    }
}