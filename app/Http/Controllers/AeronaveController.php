<?php

namespace App\Http\Controllers;

use App\Models\Aeronave;
use Illuminate\Http\Request;

class AeronaveController extends Controller
{
    public function index()
    {
        return Aeronave::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'modelo' => 'required',
            'capacidade' => 'required|integer'
        ]);

        return Aeronave::create($request->all());
    }
}