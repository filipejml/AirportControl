<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    // NOVO: Relacionamento com Voos
    public function voos()
    {
        return $this->hasMany(Voo::class);
    }

    // NOVO: Total de voos do aeroporto
    public function getTotalVoosAttribute()
    {
        return $this->voos()->count();
    }

    // NOVO: Total de passageiros que passaram pelo aeroporto
    public function getTotalPassageirosAttribute()
    {
        return $this->voos()->sum('total_passageiros');
    }

    // NOVO: Média de passageiros por voo
    public function getMediaPassageirosPorVooAttribute()
    {
        $totalVoos = $this->total_voos;
        
        if ($totalVoos === 0) {
            return 0;
        }

        return round($this->total_passageiros / $totalVoos, 0);
    }

    // NOVO: Lista de companhias que operam no aeroporto com contagem de voos
    public function getCompanhiasComVoosAttribute()
    {
        return $this->companhias->map(function($companhia) {
            $companhia->voos_no_aeroporto = $this->voos()
                ->where('companhia_aerea_id', $companhia->id)
                ->count();
            return $companhia;
        });
    }

    // NOVO: Estatísticas por período
    public function getEstatisticasPorPeriodo($dataInicial, $dataFinal)
    {
        $voosPeriodo = $this->voos()
            ->whereBetween('created_at', [$dataInicial, $dataFinal])
            ->get();

        return [
            'total_voos' => $voosPeriodo->count(),
            'total_passageiros' => $voosPeriodo->sum('total_passageiros'),
            'por_tipo' => [
                'regular' => $voosPeriodo->where('tipo_voo', 'Regular')->count(),
                'charter' => $voosPeriodo->where('tipo_voo', 'Charter')->count(),
            ],
            'por_horario' => [
                'EAM' => $voosPeriodo->where('horario_voo', 'EAM')->count(),
                'AM' => $voosPeriodo->where('horario_voo', 'AM')->count(),
                'AN' => $voosPeriodo->where('horario_voo', 'AN')->count(),
                'PM' => $voosPeriodo->where('horario_voo', 'PM')->count(),
                'ALL' => $voosPeriodo->where('horario_voo', 'ALL')->count(),
            ]
        ];
    }

    // NOVO: Scopes para consultas comuns
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

    public function scopeMaisMovimentados($query, $limite = 10)
    {
        return $query->withCount('voos')
            ->orderBy('voos_count', 'desc')
            ->limit($limite);
    }

    // NOVO: Verificar se determinada companhia opera neste aeroporto
    public function companhiaOperaAqui($companhiaId)
    {
        return $this->companhias()
            ->where('companhia_aerea_id', $companhiaId)
            ->exists();
    }

    // NOVO: Dias com maior movimento
    public function getDiasMaisMovimentados($limite = 5)
    {
        return $this->voos()
            ->selectRaw('DATE(created_at) as data, COUNT(*) as total_voos, SUM(total_passageiros) as total_pax')
            ->groupBy('data')
            ->orderBy('total_voos', 'desc')
            ->limit($limite)
            ->get();
    }

    // NOVO: Média de notas dos voos do aeroporto
    public function getMediaNotasAttribute()
    {
        $voos = $this->voos;
        
        if ($voos->isEmpty()) {
            return null;
        }

        $somaNotas = 0;
        $totalNotas = 0;

        foreach ($voos as $voo) {
            if ($voo->nota_obj) {
                $somaNotas += $voo->nota_obj;
                $totalNotas++;
            }
            if ($voo->nota_pontualidade) {
                $somaNotas += $voo->nota_pontualidade;
                $totalNotas++;
            }
            if ($voo->nota_servicos) {
                $somaNotas += $voo->nota_servicos;
                $totalNotas++;
            }
            if ($voo->nota_patio) {
                $somaNotas += $voo->nota_patio;
                $totalNotas++;
            }
        }

        return $totalNotas > 0 ? round($somaNotas / $totalNotas, 1) : null;
    }

    // NOVO: Top companhias que mais operam no aeroporto
    public function getTopCompanhiasAttribute($limite = 5)
    {
        return $this->companhias()
            ->withCount(['voos' => function($query) {
                $query->where('aeroporto_id', $this->id);
            }])
            ->orderBy('voos_count', 'desc')
            ->limit($limite)
            ->get();
    }

    // NOVO: Estatísticas resumidas
    public function getResumoAttribute()
    {
        $ultimos30Dias = $this->voos()
            ->where('created_at', '>=', now()->subDays(30))
            ->get();

        return [
            'total_voos_geral' => $this->total_voos,
            'total_passageiros_geral' => $this->total_passageiros,
            'voos_ultimos_30_dias' => $ultimos30Dias->count(),
            'passageiros_ultimos_30_dias' => $ultimos30Dias->sum('total_passageiros'),
            'total_companhias' => $this->companhias()->count(),
            'media_notas' => $this->media_notas,
            'media_passageiros_por_voo' => $this->media_passageiros_por_voo,
        ];
    }

    // NOVO: Boot do modelo para eventos
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