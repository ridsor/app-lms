<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Teacher\Auth\LoginController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('teacher.login');
Route::post('/login', [LoginController::class, 'login']);
