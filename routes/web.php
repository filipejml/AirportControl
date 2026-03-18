<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/', [AuthController::class, 'showLogin'])->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');

Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::get('/home', [AuthController::class, 'home'])->name('home');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');