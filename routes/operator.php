<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MSpkController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\SpkBantuanController;

Route::middleware('auth')->group(function () {
    Route::get('/operator/dashboard', [OperatorController::class, 'index'])
        ->middleware('role:operator indoor|operator outdoor|operator multi|operator dtf')
        ->name('operator.dashboard');

    Route::get('/produksi', [MSpkController::class, 'operatorIndex'])
        ->middleware('role:manajemen|operator indoor|operator outdoor|operator multi|operator dtf')
        ->name('spk.produksi');
    Route::put('/produksi/{id}/update-status', [MSpkController::class, 'updateStatusProduksi'])
        ->middleware('role:manajemen|operator indoor|operator outdoor|operator multi|operator dtf')
        ->name('spk.update-produksi');
    Route::get('/produksi/riwayat', [MSpkController::class, 'riwayat'])
        ->middleware('role:manajemen|admin|operator indoor|operator outdoor|operator multi|operator dtf')
        ->name('spk.riwayat');

    Route::get('/produksi-bantuan', [SpkBantuanController::class, 'operatorIndex'])
        ->middleware('role:manajemen|operator indoor|operator outdoor|operator multi|operator dtf')
        ->name('spk-bantuan.produksi');
    Route::get('/produksi-bantuan/riwayat', [SpkBantuanController::class, 'riwayat'])
        ->middleware('role:manajemen|admin|operator indoor|operator outdoor|operator multi|operator dtf')
        ->name('spk-bantuan.riwayat');

    Route::get('/produksi-lembur', [MSpkController::class, 'operatorIndexLembur'])
        ->middleware('role:manajemen|operator indoor|operator outdoor|operator multi|operator dtf')
        ->name('spk-lembur.produksi');
    Route::get('/produksi-lembur/riwayat', [MSpkController::class, 'riwayatLembur'])
        ->middleware('role:manajemen|admin|operator indoor|operator outdoor|operator multi|operator dtf')
        ->name('spk-lembur.riwayat');
});
