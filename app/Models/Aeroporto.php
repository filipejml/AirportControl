<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aeroporto extends Model
{
    protected $fillable = ['nome_aeroporto'];

    public function companhias()
    {
        return $this->belongsToMany(CompanhiaAerea::class);
    }
}