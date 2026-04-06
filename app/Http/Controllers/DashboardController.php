<?php

namespace App\Http\Controllers;

use App\Models\Dashboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard panel page.
     */
    public function index()
    {
        // Instanciar o model Dashboard
        $dashboard = new Dashboard();
        
        // Obter dados estatísticos
        $stats = $dashboard->getEstatisticasGerais();
        
        // Obter médias das notas
        $mediasNotas = $dashboard->getMediasNotas();
        
        // Obter voos por horário
        $voosPorHorario = $dashboard->getVoosPorHorario();
        
        // Obter passageiros por horário
        $passageirosPorHorario = $dashboard->getPassageirosPorHorario();
        
        // Obter voos por tipo
        $voosPorTipo = $dashboard->getVoosPorTipo();
        
        // Obter passageiros por tipo
        $passageirosPorTipo = $dashboard->getPassageirosPorTipo();
        
        // Obter voos por tipo de aeronave
        $voosPorTipoAeronave = $dashboard->getVoosPorTipoAeronave();
        
        // Obter passageiros por tipo de aeronave
        $passageirosPorTipoAeronave = $dashboard->getPassageirosPorTipoAeronave();

        // Obter melhores companhias
        $melhoresCompanhias = $dashboard->getMelhoresCompanhias();

        // Obter melhores modelos de aeronave
        $melhoresModelos = $dashboard->getMelhoresModelos();
        
        return view('dashboard.index', compact(
            'stats',
            'mediasNotas',
            'voosPorHorario',
            'passageirosPorHorario',
            'voosPorTipo',
            'passageirosPorTipo',
            'voosPorTipoAeronave',
            'passageirosPorTipoAeronave',
            'melhoresCompanhias',
            'melhoresModelos'
        ));
    }
    
    /**
     * Calculate median of an array
     */
    private function calcularMediana($array)
    {
        $count = count($array);
        if ($count == 0) return 0;
        
        sort($array);
        $middle = floor(($count - 1) / 2);
        
        if ($count % 2 == 0) {
            return ($array[$middle] + $array[$middle + 1]) / 2;
        } else {
            return $array[$middle];
        }
    }
    
    /**
     * Display the dashboard graphics page.
     */
    public function graficos()
    {
        // Buscar apenas companhias que têm voos registrados
        $companhiasComDados = DB::table('voos')
            ->join('companhias_aereas', 'voos.companhia_aerea_id', '=', 'companhias_aereas.id')
            ->select('companhias_aereas.id', 'companhias_aereas.nome')
            ->distinct()
            ->get();
        
        // Array para armazenar os dados antes da ordenação
        $dadosVoos = [];
        $dadosPassageiros = [];
        
        // Cores pré-definidas para as companhias
        $colorPalette = [
            'rgba(54, 162, 235, 0.8)',
            'rgba(255, 99, 132, 0.8)',
            'rgba(75, 192, 192, 0.8)',
            'rgba(255, 206, 86, 0.8)',
            'rgba(153, 102, 255, 0.8)',
            'rgba(255, 159, 64, 0.8)',
            'rgba(199, 199, 199, 0.8)',
            'rgba(83, 102, 255, 0.8)',
            'rgba(255, 99, 255, 0.8)',
            'rgba(99, 255, 132, 0.8)',
        ];
        
        // Coletar dados de voos por companhia
        foreach ($companhiasComDados as $companhia) {
            $totalVoos = DB::table('voos')
                ->where('companhia_aerea_id', $companhia->id)
                ->sum('qtd_voos') ?? 0;
            
            $totalPassageiros = DB::table('voos')
                ->where('companhia_aerea_id', $companhia->id)
                ->sum('total_passageiros') ?? 0;
            
            $dadosVoos[] = [
                'id' => $companhia->id,
                'nome' => $companhia->nome,
                'total' => $totalVoos
            ];
            
            $dadosPassageiros[] = [
                'id' => $companhia->id,
                'nome' => $companhia->nome,
                'total' => $totalPassageiros
            ];
        }
        
        // ALTERADO: Ordenar os dados de voos de forma decrescente (maior para o menor)
        usort($dadosVoos, function($a, $b) {
            return $b['total'] - $a['total'];
        });
        
        // ALTERADO: Ordenar os dados de passageiros de forma decrescente (maior para o menor)
        usort($dadosPassageiros, function($a, $b) {
            return $b['total'] - $a['total'];
        });
        
        // Preparar arrays para o gráfico de voos
        $labelsCompanhia = [];
        $dataCompanhia = [];
        $coresCompanhia = [];
        $totalGeralVoos = 0;
        $valoresVoos = [];
        
        foreach ($dadosVoos as $index => $dado) {
            $labelsCompanhia[] = $dado['nome'];
            $dataCompanhia[] = $dado['total'];
            $coresCompanhia[] = $colorPalette[$index % count($colorPalette)];
            $totalGeralVoos += $dado['total'];
            $valoresVoos[] = $dado['total'];
        }
        
        $medianaGeralVoos = $this->calcularMediana($valoresVoos);
        $medianasGerais = array_fill(0, count($dataCompanhia), $medianaGeralVoos);
        
        // Preparar arrays para o gráfico de passageiros
        $labelsPassageirosCompanhia = [];
        $dataPassageirosCompanhia = [];
        $coresPassageirosCompanhia = [];
        $totalPassageiros = 0;
        $valoresPassageiros = [];
        
        foreach ($dadosPassageiros as $index => $dado) {
            $labelsPassageirosCompanhia[] = $dado['nome'];
            $dataPassageirosCompanhia[] = $dado['total'];
            $coresPassageirosCompanhia[] = $colorPalette[$index % count($colorPalette)];
            $totalPassageiros += $dado['total'];
            $valoresPassageiros[] = $dado['total'];
        }
        
        $medianaGeralPassageiros = $this->calcularMediana($valoresPassageiros);
        
        // Dados para Voos por Horário
        $dashboard = new Dashboard();
        $voosPorHorario = $dashboard->getVoosPorHorario();
        $horarios = ['EAM', 'AM', 'AN', 'PM', 'ALL'];
        $dataHorario = [];
        $valoresHorario = [];
        
        foreach ($horarios as $horario) {
            $valor = $voosPorHorario[$horario] ?? 0;
            $dataHorario[] = $valor;
            $valoresHorario[] = $valor;
        }
        $medianaHorario = $this->calcularMediana($valoresHorario);
        $medianaHorarioDataset = array_fill(0, count($dataHorario), $medianaHorario);
        
        // Dados para Passageiros por Horário
        $passageirosPorHorario = $dashboard->getPassageirosPorHorario();
        $dataPassageirosHorario = [];
        $valoresPassageirosHorario = [];
        
        foreach ($horarios as $horario) {
            $valor = $passageirosPorHorario[$horario] ?? 0;
            $dataPassageirosHorario[] = $valor;
            $valoresPassageirosHorario[] = $valor;
        }
        $medianaPassageirosHorario = $this->calcularMediana($valoresPassageirosHorario);
        $medianaPassageirosHorarioDataset = array_fill(0, count($dataPassageirosHorario), $medianaPassageirosHorario);
        
        // Dados para Voos por Tipo
        $voosPorTipo = $dashboard->getVoosPorTipo();
        $labelsTipoVoo = ['Regular', 'Charter'];
        $dataTipoVoo = [];
        $totalTipoVoo = 0;
        $valoresTipoVoo = [];
        
        foreach ($labelsTipoVoo as $tipo) {
            $valor = $voosPorTipo[$tipo] ?? 0;
            $dataTipoVoo[] = $valor;
            $totalTipoVoo += $valor;
            $valoresTipoVoo[] = $valor;
        }
        $medianaTipoVoo = $this->calcularMediana($valoresTipoVoo);
        $medianaTipoVooDataset = array_fill(0, count($dataTipoVoo), $medianaTipoVoo);
        $coresTipoVoo = ['rgba(13, 202, 240, 0.8)', 'rgba(25, 135, 84, 0.8)'];
        
        // Dados para Passageiros por Tipo de Voo
        $passageirosPorTipo = $dashboard->getPassageirosPorTipo();
        $labelsPassageirosTipoVoo = ['Regular', 'Charter'];
        $dataPassageirosTipoVoo = [];
        $totalPassageirosTipoVoo = 0;
        $valoresPassageirosTipoVoo = [];
        
        foreach ($labelsPassageirosTipoVoo as $tipo) {
            $valor = $passageirosPorTipo[$tipo] ?? 0;
            $dataPassageirosTipoVoo[] = $valor;
            $totalPassageirosTipoVoo += $valor;
            $valoresPassageirosTipoVoo[] = $valor;
        }
        $medianaPassageirosTipoVoo = $this->calcularMediana($valoresPassageirosTipoVoo);
        $medianaPassageirosTipoVooDataset = array_fill(0, count($dataPassageirosTipoVoo), $medianaPassageirosTipoVoo);
        $coresPassageirosTipoVoo = ['rgba(255, 193, 7, 0.8)', 'rgba(25, 135, 84, 0.8)'];
        
        // Dados para Voos por Tipo de Aeronave
        $voosPorTipoAeronave = $dashboard->getVoosPorTipoAeronave();
        $labelsTipoAeronave = ['PC', 'MC', 'LC'];
        $dataTipoAeronave = [];
        $totalTipoAeronave = 0;
        $valoresTipoAeronave = [];
        
        foreach ($labelsTipoAeronave as $tipo) {
            $valor = $voosPorTipoAeronave[$tipo] ?? 0;
            $dataTipoAeronave[] = $valor;
            $totalTipoAeronave += $valor;
            $valoresTipoAeronave[] = $valor;
        }
        $medianaTipoAeronave = $this->calcularMediana($valoresTipoAeronave);
        $medianaTipoAeronaveDataset = array_fill(0, count($dataTipoAeronave), $medianaTipoAeronave);
        $coresTipoAeronave = ['rgba(111, 66, 193, 0.8)', 'rgba(13, 110, 253, 0.8)', 'rgba(220, 53, 69, 0.8)'];
        
        // Dados para Passageiros por Tipo de Aeronave
        $passageirosPorTipoAeronave = $dashboard->getPassageirosPorTipoAeronave();
        $labelsPassageirosTipoAeronave = ['PC', 'MC', 'LC'];
        $dataPassageirosTipoAeronave = [];
        $totalPassageirosTipoAeronave = 0;
        $valoresPassageirosTipoAeronave = [];
        
        foreach ($labelsPassageirosTipoAeronave as $tipo) {
            $valor = $passageirosPorTipoAeronave[$tipo] ?? 0;
            $dataPassageirosTipoAeronave[] = $valor;
            $totalPassageirosTipoAeronave += $valor;
            $valoresPassageirosTipoAeronave[] = $valor;
        }
        $medianaPassageirosTipoAeronave = $this->calcularMediana($valoresPassageirosTipoAeronave);
        $medianaPassageirosTipoAeronaveDataset = array_fill(0, count($dataPassageirosTipoAeronave), $medianaPassageirosTipoAeronave);
        $coresPassageirosTipoAeronave = ['rgba(232, 62, 140, 0.8)', 'rgba(13, 110, 253, 0.8)', 'rgba(32, 201, 151, 0.8)'];
        
        return view('dashboard.graficos', compact(
            'labelsCompanhia',
            'dataCompanhia',
            'coresCompanhia',
            'medianaGeralVoos',
            'totalGeralVoos',
            'medianasGerais',
            'labelsPassageirosCompanhia',
            'dataPassageirosCompanhia',
            'coresPassageirosCompanhia',
            'medianaGeralPassageiros',
            'totalPassageiros',
            'horarios',
            'dataHorario',
            'medianaHorarioDataset',
            'dataPassageirosHorario',
            'medianaPassageirosHorarioDataset',
            'labelsTipoVoo',
            'dataTipoVoo',
            'coresTipoVoo',
            'totalTipoVoo',
            'medianaTipoVooDataset',
            'labelsPassageirosTipoVoo',
            'dataPassageirosTipoVoo',
            'coresPassageirosTipoVoo',
            'totalPassageirosTipoVoo',
            'medianaPassageirosTipoVooDataset',
            'labelsTipoAeronave',
            'dataTipoAeronave',
            'coresTipoAeronave',
            'totalTipoAeronave',
            'medianaTipoAeronaveDataset',
            'labelsPassageirosTipoAeronave',
            'dataPassageirosTipoAeronave',
            'coresPassageirosTipoAeronave',
            'totalPassageirosTipoAeronave',
            'medianaPassageirosTipoAeronaveDataset'
        ));
    }
}