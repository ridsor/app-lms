<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
  Route::get('/akun/{username}', [AccountController::class, 'index'])->name('account.index');
  Route::get('/profil/{username}/gambar', [ProfileController::class, 'getImage'])->name('profile.image.get');
  Route::patch('/profil/{username}/gambar', [ProfileController::class, 'updateImage'])->name('profile.image.update');
});
