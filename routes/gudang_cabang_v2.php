<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GudangCabangControllerV2;

Route::middleware(['auth', 'role:inventory cabang'])->prefix('gudang-cabangs')->group(function () {
    Route::get('/dashboard', [GudangCabangControllerV2::class, 'dashboard'])
        ->name('gudang-cabang.dashboard');

    // Rute untuk membuat permintaan barang
    Route::get('/permintaan', [GudangCabangControllerV2::class, 'permintaan'])
        ->name('gudang-cabang.permintaan');
    Route::post('/permintaan', [GudangCabangControllerV2::class, 'store'])
        ->name('gudang-cabang.permintaan.store');

    // Rute untuk riwayat permintaan
    Route::get('/permintaan/riwayat', [GudangCabangControllerV2::class, 'riwayat_permintaan'])
        ->name('gudang-cabang.permintaan.riwayat');

    // Rute untuk penerimaan barang
    Route::get('/gudang-cabang/permintaan/penerimaan', [GudangCabangControllerV2::class, 'penerimaan'])
        ->name('gudang-cabang.permintaan.penerimaan');
    Route::post('/gudang-cabang/permintaan/{id}/terima', [GudangCabangControllerV2::class, 'terimaBarang'])
        ->name('gudang-cabang.permintaan.terima');
});
