<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Aeroporto extends Model
{
    protected $table = 'aeroportos';
    
    protected $fillable = ['nome_aeroporto'];

    // Relacionamento com Companhias Aéreas (muitos-para-muitos)
    public function companhias()
    {
        return $this->belongsToMany(
            CompanhiaAerea::class, 
            'aeroporto_companhia',
            'aeroporto_id',
            'companhia_aerea_id'
        )->withTimestamps();
    }

    // Relacionamento com Voos
    public function voos()
    {
        return $this->hasMany(Voo::class);
    }

    // Relacionamento com Depósitos
    public function depositos()
    {
        return $this->hasMany(Deposito::class);
    }

    // Relacionamento com Veículos através de depósitos
    public function veiculos()
    {
        return $this->hasManyThrough(Veiculo::class, Deposito::class, 'aeroporto_id', 'deposito_id', 'id', 'id');
    }

    // Total de veículos no aeroporto
    public function getTotalVeiculosAttribute()
    {
        return $this->veiculos()->count();
    }

    // Total de depósitos no aeroporto
    public function getTotalDepositosAttribute()
    {
        return $this->depositos()->count();
    }

    // Total de veículos disponíveis
    public function getVeiculosDisponiveisAttribute()
    {
        return $this->veiculos()->where('status', 'disponivel')->count();
    }

    // Total de voos do aeroporto (soma qtd_voos)
    public function getTotalVoosAttribute()
    {
        return $this->voos()->sum('qtd_voos');
    }

    // Total de passageiros que passaram pelo aeroporto
    public function getTotalPassageirosAttribute()
    {
        return $this->voos()->sum('total_passageiros');
    }

    // Média de passageiros por voo
    public function getMediaPassageirosPorVooAttribute()
    {
        $totalVoos = $this->total_voos;
        
        if ($totalVoos === 0) {
            return 0;
        }

        return round($this->total_passageiros / $totalVoos, 0);
    }

    // Média ponderada da nota Objetivo
    public function getMediaNotaObjAttribute()
    {
        $result = DB::table('voos')
            ->where('aeroporto_id', $this->id)
            ->whereNotNull('nota_obj')
            ->select(DB::raw('SUM(qtd_voos * nota_obj) / SUM(qtd_voos) as media'))
            ->value('media');
        
        return $result ?? 0;
    }

    // Média ponderada da nota Pontualidade
    public function getMediaNotaPontualidadeAttribute()
    {
        $result = DB::table('voos')
            ->where('aeroporto_id', $this->id)
            ->whereNotNull('nota_pontualidade')
            ->select(DB::raw('SUM(qtd_voos * nota_pontualidade) / SUM(qtd_voos) as media'))
            ->value('media');
        
        return $result ?? 0;
    }

    // Média ponderada da nota Serviços
    public function getMediaNotaServicosAttribute()
    {
        $result = DB::table('voos')
            ->where('aeroporto_id', $this->id)
            ->whereNotNull('nota_servicos')
            ->select(DB::raw('SUM(qtd_voos * nota_servicos) / SUM(qtd_voos) as media'))
            ->value('media');
        
        return $result ?? 0;
    }

    // Média ponderada da nota Pátio
    public function getMediaNotaPatioAttribute()
    {
        $result = DB::table('voos')
            ->where('aeroporto_id', $this->id)
            ->whereNotNull('nota_patio')
            ->select(DB::raw('SUM(qtd_voos * nota_patio) / SUM(qtd_voos) as media'))
            ->value('media');
        
        return $result ?? 0;
    }

    // Média geral das notas
    public function getMediaNotasAttribute()
    {
        $notaObj = $this->media_nota_obj;
        $notaPontualidade = $this->media_nota_pontualidade;
        $notaServicos = $this->media_nota_servicos;
        $notaPatio = $this->media_nota_patio;
        
        $soma = 0;
        $count = 0;
        
        if ($notaObj > 0) { $soma += $notaObj; $count++; }
        if ($notaPontualidade > 0) { $soma += $notaPontualidade; $count++; }
        if ($notaServicos > 0) { $soma += $notaServicos; $count++; }
        if ($notaPatio > 0) { $soma += $notaPatio; $count++; }
        
        return $count > 0 ? round($soma / $count, 1) : 0;
    }

    // Lista de companhias que operam no aeroporto com contagem de voos
    public function getCompanhiasComVoosAttribute()
    {
        return $this->companhias->map(function($companhia) {
            $companhia->voos_no_aeroporto = $this->voos()
                ->where('companhia_aerea_id', $companhia->id)
                ->sum('qtd_voos');
            return $companhia;
        });
    }

    // Estatísticas por período
    public function getEstatisticasPorPeriodo($dataInicial, $dataFinal)
    {
        $voosPeriodo = $this->voos()
            ->whereBetween('created_at', [$dataInicial, $dataFinal])
            ->get();
        
        $totalVoos = $voosPeriodo->sum('qtd_voos');
        $totalPassageiros = $voosPeriodo->sum('total_passageiros');

        return [
            'total_voos' => $totalVoos,
            'total_passageiros' => $totalPassageiros,
            'por_tipo' => [
                'regular' => $voosPeriodo->where('tipo_voo', 'Regular')->sum('qtd_voos'),
                'charter' => $voosPeriodo->where('tipo_voo', 'Charter')->sum('qtd_voos'),
            ],
            'por_horario' => [
                'EAM' => $voosPeriodo->where('horario_voo', 'EAM')->sum('qtd_voos'),
                'AM' => $voosPeriodo->where('horario_voo', 'AM')->sum('qtd_voos'),
                'AN' => $voosPeriodo->where('horario_voo', 'AN')->sum('qtd_voos'),
                'PM' => $voosPeriodo->where('horario_voo', 'PM')->sum('qtd_voos'),
                'ALL' => $voosPeriodo->where('horario_voo', 'ALL')->sum('qtd_voos'),
            ]
        ];
    }

    // Scopes para consultas comuns
    public function scopeComVoosNoPeriodo($query, $dataInicial, $dataFinal)
    {
        return $query->whereHas('voos', function($q) use ($dataInicial, $dataFinal) {
            $q->whereBetween('created_at', [$dataInicial, $dataFinal]);
        });
    }

    public function scopeComCompanhia($query, $companhiaId)
    {
        return $query->whereHas('companhias', function($q) use ($companhiaId) {
            $q->where('companhia_aerea_id', $companhiaId);
        });
    }

    // CORRIGIDO: Aeroportos mais movimentados (por soma de qtd_voos)
    public function scopeMaisMovimentados($query, $limite = 10)
    {
        return $query->withCount(['voos as total_voos_sum' => function($q) {
                $q->select(DB::raw('SUM(qtd_voos)'));
            }])
            ->orderBy('total_voos_sum', 'desc')
            ->limit($limite);
    }

    // Verificar se determinada companhia opera neste aeroporto
    public function companhiaOperaAqui($companhiaId)
    {
        return $this->companhias()
            ->where('companhia_aerea_id', $companhiaId)
            ->exists();
    }

    // Dias com maior movimento
    public function getDiasMaisMovimentados($limite = 5)
    {
        return $this->voos()
            ->selectRaw('DATE(created_at) as data, SUM(qtd_voos) as total_voos, SUM(total_passageiros) as total_pax')
            ->groupBy('data')
            ->orderBy('total_voos', 'desc')
            ->limit($limite)
            ->get();
    }

    // Top companhias que mais operam no aeroporto
    public function getTopCompanhiasAttribute($limite = 5)
    {
        return $this->companhias()
            ->withCount(['voos as total_voos_sum' => function($query) {
                $query->where('aeroporto_id', $this->id)
                      ->select(DB::raw('SUM(qtd_voos)'));
            }])
            ->orderBy('total_voos_sum', 'desc')
            ->limit($limite)
            ->get();
    }

    // Estatísticas resumidas
    public function getResumoAttribute()
    {
        $ultimos30Dias = $this->voos()
            ->where('created_at', '>=', now()->subDays(30))
            ->get();
        
        $totalVoosUltimos30Dias = $ultimos30Dias->sum('qtd_voos');
        $totalPassageirosUltimos30Dias = $ultimos30Dias->sum('total_passageiros');

        return [
            'total_voos_geral' => $this->total_voos,
            'total_passageiros_geral' => $this->total_passageiros,
            'voos_ultimos_30_dias' => $totalVoosUltimos30Dias,
            'passageiros_ultimos_30_dias' => $totalPassageirosUltimos30Dias,
            'total_companhias' => $this->companhias()->count(),
            'media_notas' => $this->media_notas,
            'media_passageiros_por_voo' => $this->media_passageiros_por_voo,
        ];
    }

    // Boot do modelo para eventos
    protected static function booted()
    {
        static::deleting(function ($aeroporto) {
            // Antes de deletar o aeroporto, verifica se existem voos associados
            if ($aeroporto->voos()->exists()) {
                throw new \Exception('Não é possível excluir um aeroporto que possui voos cadastrados.');
            }
            
            // Remove os relacionamentos com companhias
            $aeroporto->companhias()->detach();
        });
    }
}