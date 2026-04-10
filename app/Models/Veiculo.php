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
        'tipo_veiculo',
        'codigo',
        'quantidade',
        'status',
        'observacoes'
    ];

    protected $casts = [
        'quantidade' => 'integer'
    ];

    // Mapeamento dos tipos de veículos
    const TIPOS_VEICULOS = [
        'esteira_bagagem' => [
            'nome' => 'Esteira de Bagagem',
            'icone' => 'bi-box-seam',
            'cor' => 'primary',
            'descricao' => 'Transporte de bagagens entre terminais e aeronaves'
        ],
        'caminhao_combustivel' => [
            'nome' => 'Caminhão de Combustível',
            'icone' => 'bi-fuel-pump',
            'cor' => 'danger',
            'descricao' => 'Abastecimento de aeronaves'
        ],
        'carro_inspecao' => [
            'nome' => 'Carro de Inspeção',
            'icone' => 'bi-search',
            'cor' => 'info',
            'descricao' => 'Inspeção de segurança e manutenção de pistas'
        ],
        'carrinho_bagagem' => [
            'nome' => 'Carrinho de Bagagem',
            'icone' => 'bi-cart',
            'cor' => 'secondary',
            'descricao' => 'Transporte de bagagens no pátio'
        ],
        'caminhao_pushback' => [
            'nome' => 'Caminhão de Pushback',
            'icone' => 'bi-arrow-return-left',
            'cor' => 'warning',
            'descricao' => 'Manobra de aeronaves para posicionamento'
        ],
        'caminhao_escada' => [
            'nome' => 'Caminhão Escada',
            'icone' => 'bi-stairs',
            'cor' => 'success',
            'descricao' => 'Embarque e desembarque de passageiros'
        ],
        'caminhao_limpeza' => [
            'nome' => 'Caminhão de Limpeza',
            'icone' => 'bi-brush',
            'cor' => 'dark',
            'descricao' => 'Limpeza de aeronaves e áreas operacionais'
        ],
        'outro' => [
            'nome' => 'Outro',
            'icone' => 'bi-question-circle',
            'cor' => 'secondary',
            'descricao' => 'Outros tipos de veículos'
        ]
    ];

    // Relacionamento com Depósito
    public function deposito(): BelongsTo
    {
        return $this->belongsTo(Deposito::class);
    }

    // Acessor para o aeroporto
    public function getAeroportoAttribute()
    {
        return $this->deposito->aeroporto;
    }

    // Acessor para informações do tipo de veículo
    public function getTipoInfoAttribute()
    {
        return self::TIPOS_VEICULOS[$this->tipo_veiculo] ?? self::TIPOS_VEICULOS['outro'];
    }

    // Acessor para nome do tipo formatado
    public function getTipoNomeAttribute()
    {
        return $this->tipo_info['nome'];
    }

    // Verificar se está disponível
    public function isDisponivel()
    {
        return $this->status === 'disponivel';
    }

    // Scopes
    public function scopeDisponiveis($query)
    {
        return $query->where('status', 'disponivel');
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo_veiculo', $tipo);
    }
}