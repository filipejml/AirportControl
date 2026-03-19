<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aeroporto extends Model
{
    protected $table = 'aeroportos';
    
    protected $fillable = ['nome_aeroporto'];

    // Relacionamento com Companhias Aéreas (muitos-para-muitos)
    public function companhias()
    {
        return $this->belongsToMany(
            CompanhiaAerea::class, 
            'aeroporto_companhia', // Nome correto da tabela pivô
            'aeroporto_id',        // Foreign key desta tabela na pivô
            'companhia_aerea_id'    // Foreign key da outra tabela na pivô
        );
    }
}