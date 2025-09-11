<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Operator\Auth\LoginController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('operator.login');
Route::post('/login', [LoginController::class, 'login']);
