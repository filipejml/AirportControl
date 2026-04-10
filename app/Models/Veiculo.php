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
        'codigo',
        'tipo_veiculo',
        'status'
    ];
    
    protected $casts = [
        'status' => 'string'
    ];
    
    // Tipos de veículos disponíveis
    public const TIPOS_VEICULOS = [
        'esteira_bagagem' => [
            'nome' => 'Esteira de Bagagem',
            'descricao' => 'Transporte de bagagens',
            'icone' => 'bi-box-seam'
        ],
        'caminhao_combustivel' => [
            'nome' => 'Caminhão de Combustível',
            'descricao' => 'Abastecimento de aeronaves',
            'icone' => 'bi-fuel-pump'
        ],
        'carro_inspecao' => [
            'nome' => 'Carro de Inspeção',
            'descricao' => 'Inspeção de pistas e segurança',
            'icone' => 'bi-binoculars'
        ],
        'carrinho_bagagem' => [
            'nome' => 'Carrinho de Bagagem',
            'descricao' => 'Transporte interno de bagagens',
            'icone' => 'bi-cart'
        ],
        'caminhao_pushback' => [
            'nome' => 'Caminhão de Pushback',
            'descricao' => 'Manobra de aeronaves',
            'icone' => 'bi-arrow-left-right'
        ],
        'caminhao_escada' => [
            'nome' => 'Caminhão Escada',
            'descricao' => 'Embarque/desembarque',
            'icone' => 'bi-stairs'
        ],
        'caminhao_limpeza' => [
            'nome' => 'Caminhão de Limpeza',
            'descricao' => 'Limpeza de aeronaves',
            'icone' => 'bi-droplet'
        ],
        'outro' => [
            'nome' => 'Outro',
            'descricao' => 'Outros tipos de veículos',
            'icone' => 'bi-truck'
        ]
    ];
    
    // Relacionamento com Depósito
    public function deposito(): BelongsTo
    {
        return $this->belongsTo(Deposito::class);
    }
    
    // Accessor para nome do tipo
    public function getTipoNomeAttribute()
    {
        return self::TIPOS_VEICULOS[$this->tipo_veiculo]['nome'] ?? ucfirst($this->tipo_veiculo);
    }
    
    // Accessor para ícone do tipo
    public function getTipoIconeAttribute()
    {
        return self::TIPOS_VEICULOS[$this->tipo_veiculo]['icone'] ?? 'bi-truck';
    }
    
    // Accessor para cor do tipo
    public function getTipoCorAttribute()
    {
        $cores = [
            'esteira_bagagem' => 'primary',
            'caminhao_combustivel' => 'danger',
            'carro_inspecao' => 'info',
            'carrinho_bagagem' => 'success',
            'caminhao_pushback' => 'warning',
            'caminhao_escada' => 'secondary',
            'caminhao_limpeza' => 'dark',
            'outro' => 'light'
        ];
        
        return $cores[$this->tipo_veiculo] ?? 'secondary';
    }
    
    // Accessor para status com cor
    public function getStatusCorAttribute()
    {
        return match($this->status) {
            'disponivel' => 'success',
            'indisponivel' => 'danger',
            default => 'secondary'
        };
    }
    
    // Scope para veículos disponíveis
    public function scopeDisponiveis($query)
    {
        return $query->where('status', 'disponivel');
    }
    
    // Boot do modelo
    protected static function booted()
    {
        static::creating(function ($veiculo) {
            $veiculo->status = $veiculo->status ?? 'disponivel';
        });
    }
}