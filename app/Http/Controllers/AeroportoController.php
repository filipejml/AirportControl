<?php

namespace App\Http\Controllers;

use App\Models\Aeroporto;
use Illuminate\Http\Request;

class AeroportoController extends Controller
{
    public function index()
    {
        return Aeroporto::with('companhias')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome_aeroporto' => 'required',
            'companhias' => 'array'
        ]);

        $aeroporto = Aeroporto::create([
            'nome_aeroporto' => $request->nome_aeroporto
        ]);

        if ($request->companhias) {
            $aeroporto->companhias()->sync($request->companhias);
        }

        return $aeroporto;
    }
}