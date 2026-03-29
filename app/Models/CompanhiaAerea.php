<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class CompanhiaAerea extends Model
{
    protected $table = 'companhias_aereas';

    protected $fillable = ['nome'];

    // Relacionamento com Aeronaves (muitos-para-muitos)
    public function aeronaves()
    {
        return $this->belongsToMany(
            Aeronave::class, 
            'companhia_aeronave', 
            'companhia_aerea_id', 
            'aeronave_id'
        )->withTimestamps();
    }

    // Relacionamento com Aeroportos (muitos-para-muitos)
    public function aeroportos()
    {
        return $this->belongsToMany(
            Aeroporto::class, 
            'aeroporto_companhia',
            'companhia_aerea_id',
            'aeroporto_id'
        )->withTimestamps();
    }

    // NOVO: Relacionamento com Voos
    public function voos()
    {
        return $this->hasMany(Voo::class);
    }

    // NOVO: Contagem de aeronaves (já usado nos controllers)
    public function getAeronavesCountAttribute()
    {
        return $this->aeronaves()->count();
    }

    // NOVO: Contagem de voos
    public function getVoosCountAttribute()
    {
        return $this->voos()->count();
    }

    // NOVO: Total de passageiros transportados
    public function getTotalPassageirosAttribute()
    {
        return $this->voos()->sum('total_passageiros');
    }

    // NOVO: Média de notas dos voos da companhia
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

    // NOVO: Scope para filtrar companhias com mais de X aeronaves
    public function scopeComMaisDeXAeronaves($query, $quantidade)
    {
        return $query->has('aeronaves', '>=', $quantidade);
    }

    // NOVO: Scope para filtrar companhias ativas (com voos nos últimos 30 dias)
    public function scopeAtivas($query)
    {
        return $query->whereHas('voos', function($q) {
            $q->whereDate('created_at', '>=', now()->subDays(30));
        });
    }

    // NOVO: Verificar se a companhia opera em determinado aeroporto
    public function operaEmAeroporto($aeroportoId)
    {
        return $this->aeroportos()->where('aeroporto_id', $aeroportoId)->exists();
    }

    // NOVO: Verificar se a companhia possui determinada aeronave
    public function possuiAeronave($aeronaveId)
    {
        return $this->aeronaves()->where('aeronave_id', $aeronaveId)->exists();
    }

    // NOVO: Estatísticas resumidas da companhia
    public function getEstatisticasAttribute()
    {
        return [
            'total_aeronaves' => $this->aeronaves_count,
            'total_voos' => $this->voos_count,
            'total_passageiros' => $this->total_passageiros,
            'media_notas' => $this->media_notas,
            'primeiro_voo' => $this->voos()->orderBy('created_at')->first()?->created_at->format('d/m/Y'),
            'ultimo_voo' => $this->voos()->orderBy('created_at', 'desc')->first()?->created_at->format('d/m/Y'),
        ];
    }

    // NOVO: Boot do modelo para eventos
    protected static function booted()
    {
        static::deleting(function ($companhia) {
            // Antes de deletar a companhia, remove os relacionamentos
            $companhia->aeronaves()->detach();
            $companhia->aeroportos()->detach();
        });
    }
}