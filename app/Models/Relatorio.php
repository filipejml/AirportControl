<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Relatorio extends Model
{
    public const TIPO_COMPANHIAS_POR_AEROPORTO = 'companhias_por_aeroporto';
    public const TIPO_VOOS_POR_AEROPORTO = 'voos_por_aeroporto';
    public const TIPO_DESEMPENHO_COMPANHIAS = 'desempenho_companhias';
    public const TIPO_MOVIMENTACAO_POR_PERIODO = 'movimentacao_por_periodo';
    public const TIPO_RANKING_AEROPORTOS = 'ranking_aeroportos';

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
            self::TIPO_DESEMPENHO_COMPANHIAS => 'relatorios.desempenho-companhias',
            self::TIPO_MOVIMENTACAO_POR_PERIODO => 'relatorios.movimentacao-por-periodo',
            self::TIPO_RANKING_AEROPORTOS => 'relatorios.ranking-aeroportos',
        ][$this->tipo] ?? '#';
    }

    public function getAdminRouteAttribute(): ?string
    {
        return [
            self::TIPO_COMPANHIAS_POR_AEROPORTO => 'admin.relatorios.companhias-por-aeroporto',
            self::TIPO_VOOS_POR_AEROPORTO => 'admin.relatorios.voos-por-aeroporto',
            self::TIPO_DESEMPENHO_COMPANHIAS => 'admin.relatorios.desempenho-companhias',
            self::TIPO_MOVIMENTACAO_POR_PERIODO => 'admin.relatorios.movimentacao-por-periodo',
            self::TIPO_RANKING_AEROPORTOS => 'admin.relatorios.ranking-aeroportos',
        ][$this->tipo] ?? null;
    }
}
