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
        'codigo',
        'localizacao',
        'area_total',
        'capacidade_maxima',
        'status',
        'observacoes'
    ];

    protected $casts = [
        'area_total' => 'decimal:2',
        'capacidade_maxima' => 'integer',
        'data_aquisicao' => 'date',
        'ultima_manutencao' => 'date',
        'proxima_manutencao' => 'date'
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

    // Veículos em manutenção
    public function getVeiculosManutencaoAttribute()
    {
        return $this->veiculos()->where('status', 'manutencao')->count();
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

    // Scope para depósitos com capacidade disponível
    public function scopeComCapacidade($query)
    {
        return $query->whereRaw('capacidade_maxima IS NULL OR (SELECT COUNT(*) FROM veiculos WHERE veiculos.deposito_id = depositos.id) < capacidade_maxima');
    }
}