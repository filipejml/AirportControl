<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Seu projeto não usa API, então mantenha apenas esta rota básica
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});