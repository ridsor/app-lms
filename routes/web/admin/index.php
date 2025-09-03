<?php

use App\Http\Controllers\Admin\ActivityLogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\HomeController;

Route::middleware(['guest'])->group(function () {
  Route::get('/', [LoginController::class, 'showLoginForm'])->name('admin.index');
  Route::post('/login', [LoginController::class, 'login'])->name('admin.login');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
  Route::get('/beranda', [HomeController::class, 'index'])->name('admin.home');
  Route::get('/catatan-aktivitas', [ActivityLogController::class, 'index'])->name('admin.activity-log.index');
});
