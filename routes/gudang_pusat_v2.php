<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GudangPusatControllerV2;

Route::middleware(['auth', 'role:inventory utama'])
    ->prefix('gudang-pusats')
    ->group(function () {
        Route::get('/dashboard', [GudangPusatControllerV2::class, 'dashboard'])
            ->name('gudang-pusat.dashboard');

        // Rute untuk membuat permintaan barang
        Route::get('/permintaan', [GudangPusatControllerV2::class, 'permintaan'])
            ->name('gudang-pusat.permintaan');

        // Rute untuk permintaan masuk dari cabang
        Route::get('/permintaan-masuk', [GudangPusatControllerV2::class, 'permintaanMasuk'])
            ->name('gudang-pusat.permintaan.masuk');
        Route::post('/permintaan/{id}/proses', [GudangPusatControllerV2::class, 'prosesPermintaan'])
            ->name('gudang-pusat.permintaan.proses');

        // Rute untuk riwayat permintaan
        Route::get('/permintaan/riwayat', [GudangPusatControllerV2::class, 'riwayat'])
            ->name('gudang-pusat.permintaan.riwayat');

        // Rute untuk laporan distribusi barang
        Route::get('/laporan', [GudangPusatControllerV2::class, 'laporan'])
            ->name('gudang-pusat.laporan');

        // Rute untuk laporan distribusi barang
        Route::get('/laporan-barang', [GudangPusatControllerV2::class, 'laporanBarang'])
            ->name('gudang-pusat.laporan.barang');
    });
