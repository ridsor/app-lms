<?php

use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\RoomController;
use App\Http\Controllers\User\PeriodController;
use App\Http\Controllers\User\ClassController;
use Illuminate\Support\Facades\Route;

Route::middleware(["auth", "role:vice-principal|teacher|student|parent"])->group(function () {
    Route::get('/beranda', [HomeController::class, 'index'])->name('user.home');

    Route::resource('/ruangan', RoomController::class)->except(['create'])->names('user.room');

    Route::resource('/periode', PeriodController::class)->except(['create'])->names('user.period');
    Route::post('/periode/active/{id}', [PeriodController::class, 'active'])->name('user.period.active');

    Route::delete('kelas/hapus', [ClassController::class, 'bulkDestroy'])->name('user.class.bulkDestroy');
    Route::resource('/kelas', ClassController::class)->except(['create'])->names('user.class');
});
