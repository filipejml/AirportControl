<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aeronave extends Model
{
    protected $fillable = ['modelo', 'capacidade'];

    public function companhias()
    {
        return $this->belongsToMany(CompanhiaAerea::class);
    }
}