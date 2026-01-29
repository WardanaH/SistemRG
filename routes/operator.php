<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MSpkController;
use App\Http\Controllers\OperatorController;

Route::middleware('auth')->group(function () {
    Route::get('/operator/dashboard', [OperatorController::class, 'index'])
        ->middleware('role:operator indoor|operator outdoor|operator multi')
        ->name('operator.dashboard');

    Route::get('/produksi', [MSpkController::class, 'operatorIndex'])
        ->middleware('role:manajemen|operator indoor|operator outdoor|operator multi')
        ->name('spk.produksi');
    Route::put('/produksi/{id}/selesai', [MSpkController::class, 'selesaiProduksi'])
        ->middleware('role:manajemen|operator indoor|operator outdoor|operator multi')
        ->name('spk.selesai');
    Route::get('/produksi/riwayat', [MSpkController::class, 'riwayat'])
        ->middleware('role:manajemen|admin|operator indoor|operator outdoor|operator multi')
        ->name('spk.riwayat');
});
