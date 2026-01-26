<?php

use App\Http\Controllers\ManajemenController;
use App\Http\Controllers\MCabangController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/manajemen/dashboard', [ManajemenController::class, 'index'])->name('manajemen.dashboard');
    Route::get('/manajemen/user', [UserController::class, 'index'])->name('manajemen.user');
    Route::post('/manajemen/user', [UserController::class, 'store'])->name('manajemen.user.store');
    Route::post('/manajemen/user/import', [UserController::class, 'importCsv'])->middleware('role:manajemen')->name('manajemen.user.import');
    Route::get('/manajemen/user/{user}/edit', [UserController::class, 'edit'])->middleware('role:manajemen')->name('manajemen.user.edit');
    Route::post('/manajemen/user/{user}', [UserController::class, 'update'])->middleware('role:manajemen')->name('manajemen.user.update');
    Route::delete('/manajemen/user/{user}/delete', [UserController::class, 'destroy'])->middleware('role:manajemen')->name('manajemen.user.destroy');

    Route::get('/manajemen/cabang', [MCabangController::class, 'index'])->name('manajemen.cabang');
    Route::post('/manajemen/cabang', [MCabangController::class, 'store'])->name('manajemen.cabang.store');
    Route::get('/manajemen/cabang/{cabang}/edit', [MCabangController::class, 'edit'])->name('manajemen.cabang.edit');
    Route::post('/manajemen/cabang/{cabang}', [MCabangController::class, 'update'])->name('manajemen.cabang.update');
    Route::delete('/manajemen/cabang/{cabang}/delete', [MCabangController::class, 'destroy'])->name('manajemen.cabang.destroy');
});
