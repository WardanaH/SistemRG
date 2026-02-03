<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GudangCabangController;

/*
|--------------------------------------------------------------------------
| GUDANG CABANG (SISTEM BARU – STANDALONE)
|--------------------------------------------------------------------------
| Role : inventory cabang
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:inventory cabang'])
    ->prefix('gudang-cabang')
    ->name('gudangcabang.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', function () {
            return view('inventaris.gudangcabang.dashboard');
        })->name('dashboard');

        // barang
        Route::get('/barang', [GudangCabangController::class, 'barang'])
            ->name('barang');

        Route::post('/barang/store', [GudangCabangController::class, 'barangStore'])
            ->name('barang.store');

        Route::put('/barang/update/{id}', [GudangCabangController::class, 'barangUpdate'])
            ->name('barang.update');

        Route::delete('/barang/delete/{id}', [GudangCabangController::class, 'barangDestroy'])
            ->name('barang.destroy');

        //penerimaan barang
        Route::get('/penerimaan', [GudangCabangController::class, 'penerimaan'])
            ->name('penerimaan');
        Route::post('/penerimaan/terima/{id}', [GudangCabangController::class, 'terimaPengiriman'])
            ->name('penerimaan.terima');

        //laporan
        Route::get('/laporan', [GudangCabangController::class, 'laporanIndex'])
            ->name('laporan.index');

        Route::get('/laporan/{bulan}/{tahun}', [GudangCabangController::class, 'laporanDetail'])
            ->name('laporan.detail');

        Route::get('/laporan/{bulan}/{tahun}/download', [GudangCabangController::class, 'laporanDownload'])
            ->name('laporan.download');
Route::get('/laporan/{bulan}/{tahun}/excel',
    [GudangCabangController::class, 'laporanExcel']
)->name('laporan.excel');

// ==============================
// PERMINTAAN PENGIRIMAN
// ==============================

Route::get('/permintaan-pengiriman', [GudangCabangController::class, 'permintaan'])
    ->name('permintaan.index');

Route::post('/permintaan-pengiriman/store', [GudangCabangController::class, 'permintaanStore'])
    ->name('permintaan.store');

    });
