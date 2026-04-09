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
        'placa',
        'status',
        'capacidade_operacional',
        'unidade_capacidade',
        'horimetro',
        'ultima_manutencao',
        'proxima_manutencao',
        'manutencao_prevista_horas',
        'certificado_operacao',
        'validade_certificado',
        'operadores_autorizados',
        'observacoes',
        'historico_manutencoes'
    ];

    protected $casts = [
        'ano_fabricacao' => 'integer',
        'capacidade_operacional' => 'integer',
        'horimetro' => 'integer',
        'manutencao_prevista_horas' => 'integer',
        'ultima_manutencao' => 'date',
        'proxima_manutencao' => 'date',
        'validade_certificado' => 'date',
        'historico_manutencoes' => 'array'
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

    // Verificar se precisa de manutenção
    public function getPrecisaManutencaoAttribute()
    {
        if ($this->manutencao_prevista_horas && $this->horimetro) {
            $horasDesdeUltima = $this->horimetro - ($this->ultima_manutencao_horas ?? 0);
            return $horasDesdeUltima >= $this->manutencao_prevista_horas;
        }
        
        if ($this->proxima_manutencao) {
            return $this->proxima_manutencao->isPast();
        }
        
        return false;
    }

    // Certificado válido?
    public function getCertificadoValidoAttribute()
    {
        if (!$this->validade_certificado) {
            return null;
        }
        return !$this->validade_certificado->isPast();
    }

    // Dias para vencimento do certificado
    public function getDiasParaVencimentoCertificadoAttribute()
    {
        if (!$this->validade_certificado) {
            return null;
        }
        
        $dias = now()->diffInDays($this->validade_certificado, false);
        return $dias >= 0 ? $dias : -$dias;
    }

    // Adicionar manutenção ao histórico
    public function adicionarManutencao($descricao, $horasAtual = null)
    {
        $historico = $this->historico_manutencoes ?? [];
        
        $horasNaManutencao = $horasAtual ?? $this->horimetro;
        $horasUltimaManutencao = $this->ultima_manutencao_horas ?? 0;
        $horasRodadas = $horasNaManutencao - $horasUltimaManutencao;
        
        $historico[] = [
            'data' => now()->toDateTimeString(),
            'descricao' => $descricao,
            'horimetro_antes' => $horasUltimaManutencao,
            'horimetro_depois' => $horasNaManutencao,
            'horas_rodadas' => $horasRodadas,
            'operador' => auth()->user()->name ?? 'Sistema'
        ];
        
        $this->historico_manutencoes = $historico;
        $this->ultima_manutencao = now();
        $this->ultima_manutencao_horas = $horasNaManutencao;
        
        if ($this->manutencao_prevista_horas) {
            $this->proxima_manutencao = null; // Será calculada na próxima atualização do horímetro
        }
        
        $this->save();
    }

    // Atualizar horímetro e verificar manutenção
    public function atualizarHorimetro($novoHorimetro)
    {
        $this->horimetro = $novoHorimetro;
        
        if ($this->manutencao_prevista_horas && $this->ultima_manutencao_horas) {
            $horasRodadas = $novoHorimetro - $this->ultima_manutencao_horas;
            if ($horasRodadas >= $this->manutencao_prevista_horas) {
                $this->status = 'manutencao';
            }
        }
        
        $this->save();
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

    public function scopePrecisamManutencao($query)
    {
        return $query->where(function($q) {
            $q->whereNotNull('proxima_manutencao')
              ->where('proxima_manutencao', '<=', now()->addDays(7))
              ->orWhere(function($q2) {
                  $q2->whereNotNull('manutencao_prevista_horas')
                     ->whereRaw('horimetro - COALESCE(ultima_manutencao_horas, 0) >= manutencao_prevista_horas');
              });
        });
    }

    public function scopeCertificadosVencendo($query, $dias = 30)
    {
        return $query->whereNotNull('validade_certificado')
                     ->where('validade_certificado', '<=', now()->addDays($dias));
    }
}