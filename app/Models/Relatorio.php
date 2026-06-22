<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Relatorio extends Model
{
    public const TIPO_COMPANHIAS_POR_AEROPORTO = 'companhias_por_aeroporto';
    public const TIPO_VOOS_POR_AEROPORTO = 'voos_por_aeroporto';

    protected $fillable = [
        'nome',
        'descricao',
        'tipo',
        'visivel_usuario',
    ];

    protected $casts = [
        'visivel_usuario' => 'boolean',
    ];

    public function scopeVisiveis($query)
    {
        return $query->where('visivel_usuario', true);
    }

    public function getRouteAttribute(): string
    {
        return [
            self::TIPO_COMPANHIAS_POR_AEROPORTO => 'relatorios.companhias-por-aeroporto',
            self::TIPO_VOOS_POR_AEROPORTO => 'relatorios.voos-por-aeroporto',
        ][$this->tipo] ?? '#';
    }

    public function getAdminRouteAttribute(): ?string
    {
        return [
            self::TIPO_COMPANHIAS_POR_AEROPORTO => 'admin.relatorios.companhias-por-aeroporto',
            self::TIPO_VOOS_POR_AEROPORTO => 'admin.relatorios.voos-por-aeroporto',
        ][$this->tipo] ?? null;
    }
}
