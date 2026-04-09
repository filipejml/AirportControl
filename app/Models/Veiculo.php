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
        'modelo',
        'fabricante',
        'ano_fabricacao',
        'capacidade_operacional',
        'unidade_capacidade',
        'status',
        'observacoes'
    ];

    protected $casts = [
        'ano_fabricacao' => 'integer',
        'capacidade_operacional' => 'decimal:2'
    ];

    // Mapeamento dos tipos de veículos
    const TIPOS_VEICULOS = [
        'esteira_bagagem' => [
            'nome' => 'Esteira de Bagagem',
            'icone' => 'bi-box-seam',
            'cor' => 'primary',
            'unidade_padrao' => 'kg',
            'descricao' => 'Transporte de bagagens entre terminais e aeronaves'
        ],
        'caminhao_combustivel' => [
            'nome' => 'Caminhão de Combustível',
            'icone' => 'bi-fuel-pump',
            'cor' => 'danger',
            'unidade_padrao' => 'litros',
            'descricao' => 'Abastecimento de aeronaves'
        ],
        'carro_inspecao' => [
            'nome' => 'Carro de Inspeção',
            'icone' => 'bi-search',
            'cor' => 'info',
            'unidade_padrao' => null,
            'descricao' => 'Inspeção de segurança e manutenção de pistas'
        ],
        'carrinho_bagagem' => [
            'nome' => 'Carrinho de Bagagem',
            'icone' => 'bi-cart',
            'cor' => 'secondary',
            'unidade_padrao' => 'unidades',
            'descricao' => 'Transporte de bagagens no pátio'
        ],
        'caminhao_pushback' => [
            'nome' => 'Caminhão de Pushback',
            'icone' => 'bi-arrow-return-left',
            'cor' => 'warning',
            'unidade_padrao' => 'toneladas',
            'descricao' => 'Manobra de aeronaves para posicionamento'
        ],
        'caminhao_escada' => [
            'nome' => 'Caminhão Escada',
            'icone' => 'bi-stairs',
            'cor' => 'success',
            'unidade_padrao' => 'metros',
            'descricao' => 'Embarque e desembarque de passageiros'
        ],
        'caminhao_limpeza' => [
            'nome' => 'Caminhão de Limpeza',
            'icone' => 'bi-brush',
            'cor' => 'dark',
            'unidade_padrao' => 'litros',
            'descricao' => 'Limpeza de aeronaves e áreas operacionais'
        ],
        'outro' => [
            'nome' => 'Outro',
            'icone' => 'bi-question-circle',
            'cor' => 'secondary',
            'unidade_padrao' => null,
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

    // Acessor para ícone
    public function getTipoIconeAttribute()
    {
        return $this->tipo_info['icone'];
    }

    // Acessor para cor
    public function getTipoCorAttribute()
    {
        return $this->tipo_info['cor'];
    }

    // Acessor para descrição
    public function getTipoDescricaoAttribute()
    {
        return $this->tipo_info['descricao'];
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