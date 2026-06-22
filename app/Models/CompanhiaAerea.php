<?php

namespace App\Models;

use App\Services\VooMetricasService;
use Illuminate\Database\Eloquent\Model;


class CompanhiaAerea extends Model
{
    protected $table = 'companhias_aereas';

    protected $fillable = ['nome','codigo'];

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
    public function getVoosCountAttribute($value = null)
    {
        if (array_key_exists('voos_sum_qtd_voos', $this->attributes)) {
            return (int) $this->attributes['voos_sum_qtd_voos'];
        }

        return $this->voos()->sum('qtd_voos');
    }

    // NOVO: Total de passageiros transportados
    public function getTotalPassageirosAttribute($value = null)
    {
        if (array_key_exists('voos_sum_total_passageiros', $this->attributes)) {
            return (int) $this->attributes['voos_sum_total_passageiros'];
        }

        return $this->voos()->sum('total_passageiros');
    }

    // NOVO: Média de notas dos voos da companhia
    public function getMediaNotasAttribute()
    {
        $voos = $this->voos;
        
        if ($voos->isEmpty()) {
            return null;
        }

        $media = VooMetricasService::mediaGeral($voos);

        return $media > 0 ? round($media, 1) : null;
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

    // NOVO: Atualizar disponibilidade da aeronave para esta companhia
    public function atualizarDisponibilidadeAeronave($aeronaveId, $disponivel)
    {
        return $this->aeronaves()->updateExistingPivot($aeronaveId, [
            'disponivel' => $disponivel
        ]);
    }

    // NOVO: Obter aeronaves disponíveis para esta companhia
    public function aeronavesDisponiveis()
    {
        return $this->belongsToMany(Aeronave::class, 'companhia_aeronave', 'companhia_aerea_id', 'aeronave_id')
            ->withPivot('disponivel')
            ->wherePivot('disponivel', true)
            ->withTimestamps();
    }

    // NOVO: Obter todas as aeronaves com status de disponibilidade
    public function aeronavesComDisponibilidade()
    {
        return $this->belongsToMany(Aeronave::class, 'companhia_aeronave', 'companhia_aerea_id', 'aeronave_id')
            ->withPivot('disponivel')
            ->withTimestamps();
    }
}
