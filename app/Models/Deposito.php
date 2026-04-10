<?php
// app/Models/Deposito.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deposito extends Model
{
    protected $table = 'depositos';
    
    protected $fillable = [
        'aeroporto_id',
        'nome',
        'capacidade_maxima',
        'status'
    ];
    
    protected $casts = [
        'capacidade_maxima' => 'integer',
        'status' => 'string'
    ];
    
    // Relacionamento com Aeroporto
    public function aeroporto()
    {
        return $this->belongsTo(Aeroporto::class);
    }
    
    // Relacionamento com Veículos
    public function veiculos(): HasMany
    {
        return $this->hasMany(Veiculo::class);
    }
    
    // Accessor para capacidade formatada
    public function getCapacidadeFormatadaAttribute()
    {
        return $this->capacidade_maxima ? $this->capacidade_maxima . ' veículos' : 'Ilimitada';
    }
    
    // Accessor para status com cor
    public function getStatusCorAttribute()
    {
        return match($this->status) {
            'ativo' => 'success',
            'inativo' => 'danger',
            'manutencao' => 'warning',
            default => 'secondary'
        };
    }
    
    // Accessor para status ícone
    public function getStatusIconeAttribute()
    {
        return match($this->status) {
            'ativo' => 'check-circle',
            'inativo' => 'x-circle',
            'manutencao' => 'tools',
            default => 'question-circle'
        };
    }
    
    // Boot do modelo
    protected static function booted()
    {
        static::creating(function ($deposito) {
            // Se não tiver nome definido, gerar automaticamente
            if (empty($deposito->nome)) {
                $count = self::where('aeroporto_id', $deposito->aeroporto_id)->count() + 1;
                $deposito->nome = "Depósito {$count}";
            }
        });
    }
}