<?php

use App\Http\Controllers\ManajemenController;
use App\Http\Controllers\MBahanBakuController;
use App\Http\Controllers\MCabangController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/manajemen/dashboard', [ManajemenController::class, 'index'])->middleware('role:manajemen')->name('manajemen.dashboard');
    Route::get('/manajemen/user', [UserController::class, 'index'])->middleware('role:manajemen')->name('manajemen.user');
    Route::post('/manajemen/user', [UserController::class, 'store'])->middleware('role:manajemen')->name('manajemen.user.store');
    Route::get('/manajemen/user/{user}/edit', [UserController::class, 'edit'])->middleware('role:manajemen')->name('manajemen.user.edit');
    Route::post('/manajemen/user/{user}', [UserController::class, 'update'])->middleware('role:manajemen')->name('manajemen.user.update');
    Route::delete('/manajemen/user/{user}/delete', [UserController::class, 'destroy'])->middleware('role:manajemen')->name('manajemen.user.destroy');
    Route::post('/manajemen/user/import', [UserController::class, 'importCsv'])->middleware('role:manajemen')->name('manajemen.user.import');

    Route::get('/manajemen/cabang', [MCabangController::class, 'index'])->middleware('role:manajemen')->name('manajemen.cabang');
    Route::post('/manajemen/cabang', [MCabangController::class, 'store'])->middleware('role:manajemen')->name('manajemen.cabang.store');
    Route::get('/manajemen/cabang/{cabang}/edit', [MCabangController::class, 'edit'])->middleware('role:manajemen')->name('manajemen.cabang.edit');
    Route::post('/manajemen/cabang/{cabang}', [MCabangController::class, 'update'])->middleware('role:manajemen')->name('manajemen.cabang.update');
    Route::delete('/manajemen/cabang/{cabang}/delete', [MCabangController::class, 'destroy'])->middleware('role:manajemen')->name('manajemen.cabang.destroy');

    Route::get('/manajemen/bahanbaku', [MBahanBakuController::class, 'index'])->middleware('role:manajemen')->name('manajemen.bahanbaku');
    Route::post('/manajemen/bahanbaku', [MBahanBakuController::class, 'store'])->middleware('role:manajemen')->name('manajemen.bahanbaku.store');
    Route::get('/manajemen/bahanbaku/{bahanbaku}/edit', [MBahanBakuController::class, 'edit'])->middleware('role:manajemen')->name('manajemen.bahanbaku.edit');
    Route::post('/manajemen/bahanbaku/{bahanbaku}', [MBahanBakuController::class, 'update'])->middleware('role:manajemen')->name('manajemen.bahanbaku.update');
    Route::delete('/manajemen/bahanbaku/{bahanbaku}/delete', [MBahanBakuController::class, 'destroy'])->middleware('role:manajemen')->name('manajemen.bahanbaku.destroy');
});
