<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dashboard panel page.
     */
    public function index()
    {
        return view('dashboard.index');
    }
    
    /**
     * Display the dashboard graphics page.
     */
    public function graficos()
    {
        return view('dashboard.graficos');
    }
}