<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\Auth\LoginController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('student.login');
Route::post('/login', [LoginController::class, 'login']);
