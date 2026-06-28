<?php

namespace App\Http\Controllers;

use App\Models\Aeronave;
use App\Models\Aeroporto;
use App\Models\CompanhiaAerea;
use App\Models\Voo;
use App\Services\CompanhiaRankingService;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(CompanhiaRankingService $rankingService)
    {
        $voosStats = Voo::query()
            ->selectRaw('COALESCE(SUM(qtd_voos), 0) as total_voos')
            ->selectRaw('COALESCE(SUM(qtd_voos * qtd_passageiros), 0) as passageiros_total')
            ->first();

        $stats = [
            'companhias' => CompanhiaAerea::count(),
            'modelos' => Aeronave::distinct('modelo')->count('modelo'),
            'aeroportos' => Aeroporto::count(),
            'voos' => (int) ($voosStats->total_voos ?? 0),
            'passageiros_total' => (int) ($voosStats->passageiros_total ?? 0),
        ];

        $passageirosPorAeroporto = Voo::select(
                'aeroportos.nome_aeroporto',
                DB::raw('SUM(voos.total_passageiros) as total')
            )
            ->join('aeroportos', 'voos.aeroporto_id', '=', 'aeroportos.id')
            ->groupBy('aeroportos.id', 'aeroportos.nome_aeroporto')
            ->orderByDesc('total')
            ->pluck('total', 'aeroportos.nome_aeroporto')
            ->toArray();

        $horarios = ['EAM', 'AM', 'AN', 'PM', 'ALL'];
        $passageirosPorHorario = Voo::select(
                'horario_voo',
                DB::raw('SUM(qtd_voos * qtd_passageiros) as passageiros_total')
            )
            ->whereIn('horario_voo', $horarios)
            ->groupBy('horario_voo')
            ->pluck('passageiros_total', 'horario_voo')
            ->toArray();
        $passageirosPorHorario = array_replace(array_fill_keys($horarios, 0), $passageirosPorHorario);

        $voosPorAeroporto = Voo::select(
                'aeroportos.nome_aeroporto',
                DB::raw('SUM(voos.qtd_voos) as total')
            )
            ->join('aeroportos', 'voos.aeroporto_id', '=', 'aeroportos.id')
            ->groupBy('aeroportos.id', 'aeroportos.nome_aeroporto')
            ->orderByDesc('total')
            ->pluck('total', 'aeroportos.nome_aeroporto')
            ->toArray();

        $medias = Voo::query()
            ->selectRaw('COALESCE(SUM(qtd_voos * nota_obj) / NULLIF(SUM(CASE WHEN nota_obj IS NOT NULL THEN qtd_voos ELSE 0 END), 0), 0) as objetivo')
            ->selectRaw('COALESCE(SUM(qtd_voos * nota_pontualidade) / NULLIF(SUM(CASE WHEN nota_pontualidade IS NOT NULL THEN qtd_voos ELSE 0 END), 0), 0) as pontualidade')
            ->selectRaw('COALESCE(SUM(qtd_voos * nota_servicos) / NULLIF(SUM(CASE WHEN nota_servicos IS NOT NULL THEN qtd_voos ELSE 0 END), 0), 0) as servicos')
            ->selectRaw('COALESCE(SUM(qtd_voos * nota_patio) / NULLIF(SUM(CASE WHEN nota_patio IS NOT NULL THEN qtd_voos ELSE 0 END), 0), 0) as patio')
            ->first();

        $mediasNotas = [
            'objetivo' => round((float) ($medias->objetivo ?? 0), 1),
            'pontualidade' => round((float) ($medias->pontualidade ?? 0), 1),
            'servicos' => round((float) ($medias->servicos ?? 0), 1),
            'patio' => round((float) ($medias->patio ?? 0), 1),
        ];

        $rankingsCompanhias = $rankingService->generateRankings(['periodo' => 'geral']);
        $melhoresCompanhias = [
            'objetivo' => $rankingsCompanhias['rankings_objetivo']->first(),
            'pontualidade' => $rankingsCompanhias['rankings_pontualidade']->first(),
            'servicos' => $rankingsCompanhias['rankings_servicos']->first(),
            'patio' => $rankingsCompanhias['rankings_patio']->first(),
        ];

        return view('dashboard.home', compact(
            'stats',
            'passageirosPorAeroporto',
            'passageirosPorHorario',
            'voosPorAeroporto',
            'mediasNotas',
            'melhoresCompanhias'
        ));
    }

}
