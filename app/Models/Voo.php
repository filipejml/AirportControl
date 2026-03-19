<?php
// app/Models/Voo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voo extends Model
{
    protected $table = 'voos';
    
    protected $fillable = [
        'id_voo',
        'aeroporto_id',
        'companhia_aerea_id',
        'aeronave_id',
        'tipo_voo',
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
        'qtd_voos' => 'integer',
        'qtd_passageiros' => 'integer',
        'total_passageiros' => 'integer',
        'nota_obj' => 'integer',
        'nota_pontualidade' => 'integer',
        'nota_servicos' => 'integer',
        'nota_patio' => 'integer',
        'media_notas' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

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

    // Calcula a média das notas
    public function calcularMediaNotas(): ?float
    {
        $notas = [];
        
        if ($this->nota_obj !== null) {
            $notas[] = $this->nota_obj;
        }
        if ($this->nota_pontualidade !== null) {
            $notas[] = $this->nota_pontualidade;
        }
        if ($this->nota_servicos !== null) {
            $notas[] = $this->nota_servicos;
        }
        if ($this->nota_patio !== null) {
            $notas[] = $this->nota_patio;
        }

        if (empty($notas)) {
            return null;
        }

        return round(array_sum($notas) / count($notas), 2);
    }

    // Boot para calcular média automaticamente
    protected static function booted()
    {
        static::saving(function ($voo) {
            $voo->media_notas = $voo->calcularMediaNotas();
        });

        static::updating(function ($voo) {
            if ($voo->isDirty(['nota_obj', 'nota_pontualidade', 'nota_servicos', 'nota_patio'])) {
                $voo->media_notas = $voo->calcularMediaNotas();
            }
        });
    }

    // Accessors para converter número para letra nas notas
    public function getNotaObjLetraAttribute(): ?string
    {
        return $this->numeroParaLetra($this->nota_obj);
    }

    public function getNotaPontualidadeLetraAttribute(): ?string
    {
        return $this->numeroParaLetra($this->nota_pontualidade);
    }

    public function getNotaServicosLetraAttribute(): ?string
    {
        return $this->numeroParaLetra($this->nota_servicos);
    }

    public function getNotaPatioLetraAttribute(): ?string
    {
        return $this->numeroParaLetra($this->nota_patio);
    }

    // Accessor para média em letra
    public function getMediaNotasLetraAttribute(): ?string
    {
        if ($this->media_notas === null) {
            return null;
        }

        // Arredonda para o valor mais próximo na escala
        $valor = round($this->media_notas);
        
        $mapa = [
            10 => 'A',
            9 => 'B',
            8 => 'C',
            6 => 'D',
            4 => 'E',
            2 => 'F'
        ];

        return $mapa[$valor] ?? $this->media_notas;
    }

    // Accessor para cor da média
    public function getMediaNotasCorAttribute(): string
    {
        if ($this->media_notas === null) {
            return 'secondary';
        }

        return match(true) {
            $this->media_notas >= 9 => 'success',
            $this->media_notas >= 7 => 'info',
            $this->media_notas >= 5 => 'warning',
            default => 'danger'
        };
    }

    // Mutators para converter letra para número
    public function setNotaObjAttribute($value): void
    {
        $this->attributes['nota_obj'] = $this->letraParaNumero($value);
    }

    public function setNotaPontualidadeAttribute($value): void
    {
        $this->attributes['nota_pontualidade'] = $this->letraParaNumero($value);
    }

    public function setNotaServicosAttribute($value): void
    {
        $this->attributes['nota_servicos'] = $this->letraParaNumero($value);
    }

    public function setNotaPatioAttribute($value): void
    {
        $this->attributes['nota_patio'] = $this->letraParaNumero($value);
    }

    // Métodos auxiliares
    private function numeroParaLetra(?int $numero): ?string
    {
        $mapa = [
            10 => 'A',
            9 => 'B',
            8 => 'C',
            6 => 'D',
            4 => 'E',
            2 => 'F'
        ];

        return $numero !== null ? ($mapa[$numero] ?? null) : null;
    }

    private function letraParaNumero($letra): ?int
    {
        if (empty($letra)) {
            return null;
        }

        $mapa = [
            'A' => 10,
            'B' => 9,
            'C' => 8,
            'D' => 6,
            'E' => 4,
            'F' => 2
        ];

        return $mapa[strtoupper($letra)] ?? null;
    }
}