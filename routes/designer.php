<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MSpkController;
use App\Http\Controllers\DesignerController;
use App\Http\Controllers\SpkBantuanController;

Route::middleware('auth')->group(function () {
    Route::get('/designer/dashboard', [DesignerController::class, 'index'])
        ->name('designer.dashboard');

    Route::get('/spk/buat', [MSpkController::class, 'buat'])
        ->middleware('role:manajemen|designer')
        ->name('spk');
    Route::get('/spk', [MSpkController::class, 'index'])
        ->middleware('role:manajemen|designer|admin')
        ->name('spk.index');
    Route::get('/spk/{spk}/detail', [MSpkController::class, 'show'])
        ->middleware('role:manajemen|designer|admin')
        ->name('spk.show');
    Route::post('/spk', [MSpkController::class, 'store'])
        ->middleware('role:manajemen|designer')
        ->name('spk.store');
    Route::get('/spk/{spk}/edit', [MSpkController::class, 'edit'])
        ->middleware('role:manajemen|admin')
        ->name('spk.edit');
    Route::put('/spk/{spk}', [MSpkController::class, 'update'])
        ->middleware('role:manajemen|admin')
        ->name('spk.update');
    Route::delete('/spk/{spk}/delete', [MSpkController::class, 'destroy'])
        ->middleware('role:manajemen|designer|admin')
        ->name('spk.destroy');

    Route::put('/manajemen/spk/update-status/{id}', [MSpkController::class, 'updateStatus'])
        ->middleware('role:manajemen|admin')
        ->name('manajemen.spk.update-status');
    Route::get('/manajemen/spk/cetak-spk/{id}', [MSpkController::class, 'cetakSpk'])
        ->middleware('role:manajemen|designer|admin|operator indoor|operator outdoor|operator multi')
        ->name('manajemen.spk.cetak-spk');

    Route::get('/spk-bantuan', [SpkBantuanController::class, 'index'])->name('spk-bantuan.index');
    Route::get('/spk-bantuan/{id}/detail', [SpkBantuanController::class, 'show'])->name('spk-bantuan.show');
    Route::get('/spk-bantuan/buat', [SpkBantuanController::class, 'create'])->name('spk-bantuan');
    Route::post('/spk-bantuan', [SpkBantuanController::class, 'store'])->name('spk-bantuan.store');
    Route::get('/spk-bantuan/cetak/{id}', [SpkBantuanController::class, 'cetakSpkBantuan'])->name('spk-bantuan.cetak-spk-bantuan');
});
