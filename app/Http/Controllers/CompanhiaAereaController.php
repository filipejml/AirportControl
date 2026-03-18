<?php

namespace App\Http\Controllers;

use App\Models\CompanhiaAerea;
use Illuminate\Http\Request;

class CompanhiaAereaController extends Controller
{
    public function index()
    {
        return CompanhiaAerea::with('aeronaves')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required',
            'aeronaves' => 'array'
        ]);

        $companhia = CompanhiaAerea::create([
            'nome' => $request->nome
        ]);

        if ($request->aeronaves) {
            $companhia->aeronaves()->sync($request->aeronaves);
        }

        return $companhia;
    }
}
