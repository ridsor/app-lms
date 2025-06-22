<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VicePrincipal\Auth\LoginController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('vice-principal.login');
Route::post('/login', [LoginController::class, 'login']);

