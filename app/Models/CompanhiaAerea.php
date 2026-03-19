<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanhiaAerea extends Model
{
    protected $table = 'companhias_aereas';

    protected $fillable = ['nome'];

    // Relacionamento com Aeronaves (muitos-para-muitos)
    public function aeronaves()
    {
        return $this->belongsToMany(
            Aeronave::class, 
            'companhia_aeronave', 
            'companhia_aerea_id', 
            'aeronave_id'
        );
    }

    // Relacionamento com Aeroportos (muitos-para-muitos) - CORRIGIDO
    public function aeroportos()
    {
        return $this->belongsToMany(
            Aeroporto::class, 
            'aeroporto_companhia',      // Nome da tabela pivô
            'companhia_aerea_id',        // Foreign key desta tabela na pivô
            'aeroporto_id'               // Foreign key da outra tabela na pivô
        );
    }
}