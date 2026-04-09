<?php
// app/Models/Veiculo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Veiculo extends Model
{
    protected $table = 'veiculos';
    
    protected $fillable = [
        'deposito_id',
        'placa',
        'modelo',
        'marca',
        'ano',
        'cor',
        'tipo',
        'status',
        'quilometragem',
        'capacidade_passageiros',
        'carga_maxima',
        'data_aquisicao',
        'ultima_manutencao',
        'proxima_manutencao',
        'observacoes'
    ];

    protected $casts = [
        'ano' => 'integer',
        'quilometragem' => 'integer',
        'capacidade_passageiros' => 'integer',
        'carga_maxima' => 'decimal:2',
        'data_aquisicao' => 'date',
        'ultima_manutencao' => 'date',
        'proxima_manutencao' => 'date'
    ];

    // Relacionamento com Depósito
    public function deposito(): BelongsTo
    {
        return $this->belongsTo(Deposito::class);
    }

    // Acessor para obter o aeroporto através do depósito
    public function getAeroportoAttribute()
    {
        return $this->deposito->aeroporto;
    }

    // Verificar se veículo está disponível
    public function isDisponivel()
    {
        return $this->status === 'disponivel';
    }

    // Verificar se veículo está em manutenção
    public function isEmManutencao()
    {
        return $this->status === 'manutencao';
    }

    // Verificar se manutenção está atrasada
    public function getManutencaoAtrasadaAttribute()
    {
        if (!$this->proxima_manutencao) {
            return false;
        }
        return $this->proxima_manutencao->isPast();
    }

    // Idade do veículo em anos
    public function getIdadeAttribute()
    {
        if (!$this->data_aquisicao) {
            return null;
        }
        return $this->data_aquisicao->diffInYears(now());
    }

    // Scope para veículos disponíveis
    public function scopeDisponiveis($query)
    {
        return $query->where('status', 'disponivel');
    }

    // Scope para veículos em manutenção
    public function scopeEmManutencao($query)
    {
        return $query->where('status', 'manutencao');
    }

    // Scope para veículos por tipo
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // Scope para veículos que precisam de manutenção
    public function scopePrecisamManutencao($query)
    {
        return $query->whereNotNull('proxima_manutencao')
                     ->where('proxima_manutencao', '<=', now()->addDays(30));
    }
}