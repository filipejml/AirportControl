<?php
// app/Models/Voo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voo extends Model
{
    protected $fillable = [
        'id_voo',
        'aeroporto_id',
        'companhia_aerea_id',
        'aeronave_id',
        'tipo_voo',
        'tipo_aeronave',
        'qtd_voos',
        'horario_voo',
        'qtd_passageiros',
        'nota_obj',
        'nota_pontualidade',
        'nota_servicos',
        'nota_patio',
        'media_notas'
    ];

    protected $casts = [
        'nota_obj' => 'integer',
        'nota_pontualidade' => 'integer',
        'nota_servicos' => 'integer',
        'nota_patio' => 'integer',
        'media_notas' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($voo) {
            $voo->calcularMediaNotas();
        });

        static::updating(function ($voo) {
            $voo->calcularMediaNotas();
        });
    }

    public function calcularMediaNotas()
    {
        $notas = array_filter([
            $this->nota_obj,
            $this->nota_pontualidade,
            $this->nota_servicos,
            $this->nota_patio
        ]);

        if (count($notas) > 0) {
            $this->media_notas = array_sum($notas) / count($notas);
        } else {
            $this->media_notas = null;
        }
    }

    // Relacionamentos
    public function aeroporto(): BelongsTo
    {
        return $this->belongsTo(Aeroporto::class);
    }

    public function companhiaAerea(): BelongsTo
    {
        return $this->belongsTo(CompanhiaAerea::class);
    }

    public function aeronave(): BelongsTo
    {
        return $this->belongsTo(Aeronave::class);
    }

    // Accessors para converter notas de número para letra
    public function getNotaObjLetraAttribute()
    {
        return $this->convertNumberToLetter($this->nota_obj);
    }

    public function getNotaPontualidadeLetraAttribute()
    {
        return $this->convertNumberToLetter($this->nota_pontualidade);
    }

    public function getNotaServicosLetraAttribute()
    {
        return $this->convertNumberToLetter($this->nota_servicos);
    }

    public function getNotaPatioLetraAttribute()
    {
        return $this->convertNumberToLetter($this->nota_patio);
    }

    public function getMediaNotasLetraAttribute()
    {
        if (!$this->media_notas) {
            return null;
        }

        // Usa a mesma lógica de classificação do controller
        return match(true) {
            $this->media_notas >= 9 => 'A',
            $this->media_notas >= 8 => 'B',
            $this->media_notas >= 7 => 'C',
            $this->media_notas >= 5 => 'D',
            $this->media_notas >= 3 => 'E',
            default => 'F'
        };
    }

    private function convertNumberToLetter($nota)
    {
        if ($nota === null) {
            return null;
        }

        $mapa = [
            10 => 'A',
            9 => 'B',
            8 => 'C',
            6 => 'D',
            4 => 'E',
            2 => 'F'
        ];

        return $mapa[$nota] ?? null;
    }

    /**
     * Retorna o total de passageiros (capacidade * quantidade de voos)
     */
    public function getTotalPassageirosAttribute()
    {
        return $this->qtd_passageiros * $this->qtd_voos;
    }

    /**
     * Retorna a classificação textual da média
     */
    public function getClassificacaoMediaAttribute()
    {
        if (!$this->media_notas) return '';
        
        return match(true) {
            $this->media_notas >= 9 => 'Excelente',
            $this->media_notas >= 7 => 'Bom',
            $this->media_notas >= 5 => 'Regular',
            default => 'Ruim'
        };
    }

    /**
     * Retorna o texto do horário
     */
    public function getHorarioTextoAttribute()
    {
        return match($this->horario_voo) {
            'EAM' => 'Early Morning (00h-06h)',
            'AM' => 'Morning (06h-12h)',
            'AN' => 'Afternoon (12h-18h)',
            'PM' => 'Evening (18h-00h)',
            'ALL' => 'Diário',
            default => $this->horario_voo ?? ''
        };
    }

    /**
     * Retorna o texto do tipo de aeronave
     */
    public function getTipoAeronaveTextoAttribute()
    {
        return match($this->tipo_aeronave) {
            'PC' => 'Pequeno Porte',
            'MC' => 'Médio Porte',
            'LC' => 'Grande Porte',
            default => $this->tipo_aeronave ?? ''
        };
    }
}