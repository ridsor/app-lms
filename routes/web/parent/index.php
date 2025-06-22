<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Parent\Auth\LoginController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('parent.login');
Route::post('/login', [LoginController::class, 'login']);
