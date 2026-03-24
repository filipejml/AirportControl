<?php
// app/Http/Controllers/HomeController.php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Display the home dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // A view home.blade.php já utiliza auth()->user() diretamente
        // Portanto, apenas retornamos a view sem necessidade de passar dados extras
        // para manter compatibilidade total com o código existente
        
        return view('dashboard.home');
    }
}