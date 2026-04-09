<?php
// app/Models/Deposito.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deposito extends Model
{
    protected $table = 'depositos';
    
    protected $fillable = [
        'aeroporto_id',
        'nome',
        'capacidade_maxima',
        'status',
        'observacoes'
    ];

    protected $casts = [
        'capacidade_maxima' => 'integer'
    ];

    // Relacionamento com Aeroporto
    public function aeroporto(): BelongsTo
    {
        return $this->belongsTo(Aeroporto::class);
    }

    // Relacionamento com Veículos
    public function veiculos(): HasMany
    {
        return $this->hasMany(Veiculo::class);
    }

    // Total de veículos no depósito
    public function getTotalVeiculosAttribute()
    {
        return $this->veiculos()->count();
    }

    // Percentual de ocupação
    public function getPercentualOcupacaoAttribute()
    {
        if (!$this->capacidade_maxima || $this->capacidade_maxima == 0) {
            return 0;
        }
        return round(($this->total_veiculos / $this->capacidade_maxima) * 100, 1);
    }

    // Veículos disponíveis
    public function getVeiculosDisponiveisAttribute()
    {
        return $this->veiculos()->where('status', 'disponivel')->count();
    }

    // Veículos indisponíveis
    public function getVeiculosIndisponiveisAttribute()
    {
        return $this->veiculos()->where('status', 'indisponivel')->count();
    }

    // Verificar se há espaço disponível
    public function hasEspacoDisponivel($quantidade = 1)
    {
        if (!$this->capacidade_maxima) {
            return true;
        }
        return ($this->total_veiculos + $quantidade) <= $this->capacidade_maxima;
    }

    // Scope para depósitos ativos
    public function scopeAtivos($query)
    {
        return $query->where('status', 'ativo');
    }
}