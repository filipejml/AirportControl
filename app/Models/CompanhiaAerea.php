<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanhiaAerea extends Model
{
    protected $table = 'companhias_aereas';

    protected $fillable = ['nome'];

    public function aeronaves()
    {
        return $this->belongsToMany(Aeronave::class);
    }

    public function aeroportos()
    {
        return $this->belongsToMany(Aeroporto::class);
    }
}