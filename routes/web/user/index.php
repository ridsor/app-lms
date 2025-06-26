<?php

use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\RoomController;
use App\Http\Controllers\User\PeriodController;
use App\Http\Controllers\User\ClassController;
use App\Http\Controllers\User\StudentController;
use App\Http\Controllers\User\MajorController;
use Illuminate\Support\Facades\Route;

Route::middleware(["auth", "role:vice-principal|teacher|student|parent"])->group(function () {
    Route::get('/beranda', [HomeController::class, 'index'])->name('user.home');

    Route::resource('/ruangan', RoomController::class)->except(['create'])->names('user.room');
    Route::delete('ruangan/hapus', [RoomController::class, 'bulkDestroy'])->name('user.room.bulkDestroy');

    Route::resource('/periode', PeriodController::class)->except(['create'])->names('user.period');
    Route::post('/periode/active/{id}', [PeriodController::class, 'active'])->name('user.period.active');

    Route::delete('kelas/hapus', [ClassController::class, 'bulkDestroy'])->name('user.class.bulkDestroy');
    Route::resource('/kelas', ClassController::class)->except(['create'])->names('user.class');
    Route::get('/kelas/by-major/{major_id}', [ClassController::class, 'getByMajor'])->name('user.class.byMajor');

    Route::delete('siswa/hapus', [StudentController::class, 'bulkDestroy'])->name('user.student.bulkDestroy');
    Route::resource('/siswa', StudentController::class)->except(['create'])->names('user.student');

    Route::delete('jurusan/hapus', [MajorController::class, 'bulkDestroy'])->name('user.major.bulkDestroy');
    Route::resource('/jurusan', MajorController::class)->except(['create'])->names('user.major');
});
