<?php
// app/Models/Fabricante.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fabricante extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'pais_origem'];

    public function aeronaves()
    {
        return $this->hasMany(Aeronave::class);
    }
}