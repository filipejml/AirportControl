<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AeronaveController;
use App\Http\Controllers\CompanhiaAereaController;
use App\Http\Controllers\AeroportoController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/', [AuthController::class, 'showLogin'])->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');

Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::get('/home', [AuthController::class, 'home'])->name('home');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::resource('aeronaves', AeronaveController::class);
Route::resource('companhias', CompanhiaAereaController::class);
Route::resource('aeroportos', AeroportoController::class);