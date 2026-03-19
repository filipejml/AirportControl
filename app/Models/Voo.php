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
        'nota_patio'
    ];

    protected $casts = [
        'qtd_voos' => 'integer',
        'qtd_passageiros' => 'integer',
        'total_passageiros' => 'integer',
        'nota_obj' => 'integer',
        'nota_pontualidade' => 'integer',
        'nota_servicos' => 'integer',
        'nota_patio' => 'integer'
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

    // Accessors para converter número para letra
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

    // Método auxiliar para converter número para letra
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

    // Mutator para converter letra para número
    public function setNotaObjAttribute($value)
    {
        $this->attributes['nota_obj'] = $this->letraParaNumero($value);
    }

    public function setNotaPontualidadeAttribute($value)
    {
        $this->attributes['nota_pontualidade'] = $this->letraParaNumero($value);
    }

    public function setNotaServicosAttribute($value)
    {
        $this->attributes['nota_servicos'] = $this->letraParaNumero($value);
    }

    public function setNotaPatioAttribute($value)
    {
        $this->attributes['nota_patio'] = $this->letraParaNumero($value);
    }

    // Método auxiliar para converter letra para número
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