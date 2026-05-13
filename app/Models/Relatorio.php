<?php
// app/Models/Relatorio.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Relatorio extends Model
{
    protected $fillable = [
        'nome',
        'descricao',
        'visivel_usuario'
    ];
    
    /**
     * Relacionamento com o tipo de relatório
     * Você pode adicionar um campo 'tipo' ou 'rota' na migration
     */
    protected $casts = [
        'visivel_usuario' => 'boolean',
    ];
    
    /**
     * Get the route for this report
     */
    public function getRouteAttribute()
    {
        // Mapeamento dos tipos de relatório para suas rotas
        $rotas = [
            'companhias_por_aeroporto' => 'relatorios.companhias-por-aeroporto',
            'voos_por_periodo' => 'relatorios.voos-por-periodo',
            // Adicione outros relatórios aqui
        ];
        
        return $rotas[$this->tipo] ?? '#';
    }
}