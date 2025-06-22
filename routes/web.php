<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('wakasek')->group(function () {
    require __DIR__ . '/web/vice-principal/index.php';
});
Route::prefix('orang-tua')->group(function () {
    require __DIR__ . '/web/parent/index.php';
});
Route::prefix('siswa')->group(function () {
    require __DIR__ . '/web/student/index.php';
});
Route::prefix('guru')->group(function () {
    require __DIR__ . '/web/teacher/index.php';
});
Route::prefix('admin')->group(function () {
    require __DIR__ . '/web/admin/index.php';
});
require __DIR__ . '/auth.php';
require __DIR__ . '/web/user/index.php';
