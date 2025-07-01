<?php

use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\RoomController;
use App\Http\Controllers\User\PeriodController;
use App\Http\Controllers\User\ClassController;
use App\Http\Controllers\User\StudentController;
use App\Http\Controllers\User\MajorController;
use App\Http\Controllers\User\TeacherController;
use App\Http\Controllers\User\CurriculumController;
use App\Http\Controllers\User\SubjectController;
use Illuminate\Support\Facades\Route;

Route::middleware(["auth", "role:vice-principal|teacher|student|parent"])->group(function () {
    Route::get('/beranda', [HomeController::class, 'index'])->name('user.home');

    Route::delete('ruangan/hapus', [RoomController::class, 'bulkDestroy'])->name('user.room.bulkDestroy');
    Route::resource('/ruangan', RoomController::class)->except(['create', 'show'])->names('user.room');

    Route::post('/periode/active/{id}', [PeriodController::class, 'active'])->name('user.period.active');
    Route::resource('/periode', PeriodController::class)->except(['create', 'show'])->names('user.period');

    Route::delete('kelas/hapus', [ClassController::class, 'bulkDestroy'])->name('user.class.bulkDestroy');
    Route::resource('/kelas', ClassController::class)->except(['create', 'show'])->names('user.class');

    Route::delete('siswa/hapus', [StudentController::class, 'bulkDestroy'])->name('user.student.bulkDestroy');
    Route::patch('siswa/edit', [StudentController::class, 'bulkEdit'])->name('user.student.bulkEdit');
    Route::get('siswa/akun/export', [StudentController::class, 'exportStudentAccount'])->name('user.student.account.export');
    Route::get('siswa/orang-tua/akun/export', [StudentController::class, 'exportParentAccount'])->name('user.student.parent.account.export');
    Route::resource('/siswa', StudentController::class)->except(['create'])->except(['create'])->names('user.student');

    Route::delete('jurusan/hapus', [MajorController::class, 'bulkDestroy'])->name('user.major.bulkDestroy');
    Route::resource('/jurusan', MajorController::class)->except(['create', 'show'])->names('user.major');

    Route::delete('guru/hapus', [TeacherController::class, 'bulkDestroy'])->name('user.teacher.bulkDestroy');
    Route::patch('guru/edit', [TeacherController::class, 'bulkEdit'])->name('user.teacher.bulkEdit');
    Route::get('guru/akun/export', [TeacherController::class, 'exportAccount'])->name('user.teacher.account.export');
    Route::resource('/guru', TeacherController::class)->except(['create'])->names('user.teacher');

    Route::post('/kurikulum/active/{id}', [CurriculumController::class, 'active'])->name('user.curriculum.active');
    Route::delete('/kurikulum/hapus', [CurriculumController::class, 'bulkDestroy'])->name('user.curriculum.bulk-destroy');
    Route::resource('/kurikulum', CurriculumController::class)->except(['create', 'show'])->names('user.curriculum');

    Route::resource('/kurikulum/{curriculum_id}/mata-pelajaran', SubjectController::class)
        ->except(['create', 'show'])
        ->names('user.subject');
});
