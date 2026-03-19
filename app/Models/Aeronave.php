<?php
// app/Models/Aeronave.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aeronave extends Model
{
    use HasFactory;

    protected $fillable = ['modelo', 'capacidade', 'fabricante_id', 'porte'];
    protected $table = 'aeronaves';
    protected $casts = [
        'capacidade' => 'integer'
    ];

    protected static function booted()
    {
        static::creating(function ($aeronave) {
            $aeronave->porte = $aeronave->classificarPorte();
        });

        static::updating(function ($aeronave) {
            if ($aeronave->isDirty('capacidade')) {
                $aeronave->porte = $aeronave->classificarPorte();
            }
        });
    }

    public function classificarPorte()
    {
        if ($this->capacidade <= 100) {
            return 'PC'; // Pequeno Porte
        } elseif ($this->capacidade <= 299) {
            return 'MC'; // Médio Porte
        } else {
            return 'LC'; // Grande Porte
        }
    }

    public function getPorteDescricaoAttribute()
    {
        return match($this->porte) {
            'PC' => 'Pequeno Porte (≤100)',
            'MC' => 'Médio Porte (101-299)',
            'LC' => 'Grande Porte (≥300)',
            default => 'Não classificado'
        };
    }

    // Relacionamento com Fabricante (CORRIGIDO - removido os dois pontos)
    public function fabricante()
    {
        return $this->belongsTo(Fabricante::class);
    }

    // Relacionamento com Companhias Aéreas (muitos-para-muitos)
    public function companhias()
    {
        return $this->belongsToMany(CompanhiaAerea::class, 'companhia_aeronave', 'aeronave_id', 'companhia_aerea_id')
                    ->withTimestamps();
    }

    // NOVO: Relacionamento com Voos
    public function voos()
    {
        return $this->hasMany(Voo::class);
    }

    // Método auxiliar para verificar se a aeronave pertence a uma companhia
    public function pertenceACompanhia($companhiaId)
    {
        return $this->companhias()->where('companhia_aerea_id', $companhiaId)->exists();
    }

    // Scope para filtrar por porte
    public function scopePorPorte($query, $porte)
    {
        return $query->where('porte', $porte);
    }

    // Scope para filtrar por capacidade mínima
    public function scopeCapacidadeMinima($query, $capacidade)
    {
        return $query->where('capacidade', '>=', $capacidade);
    }

    // Scope para filtrar aeronaves disponíveis (que não estão em uso em voos ativos)
    public function scopeDisponiveis($query)
    {
        return $query->whereDoesntHave('voos', function($q) {
            $q->whereDate('created_at', today());
        });
    }
}