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
        Route::get('/dashboard', [GudangCabangController::class, 'dashboard'])
            ->name('dashboard');

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

        Route::get('/laporan/detail', [GudangCabangController::class, 'laporanDetail'])
            ->name('laporan.detail');

        Route::get('/laporan/download', [GudangCabangController::class, 'laporanDownload'])
            ->name('laporan.download');

        Route::get('/laporan/excel',[GudangCabangController::class, 'laporanExcel'])
            ->name('laporan.excel');


        //notif
        Route::post('/notif/read/{id}', [GudangCabangController::class, 'markNotifRead'])
            ->name('notif.read');

            //inventaris
        Route::get('/inventaris',[GudangCabangController::class, 'inventarisIndex'])
            ->name('inventaris.index');

        Route::get('/inventaris/create',[GudangCabangController::class, 'inventarisCreate'])
            ->name('inventaris.create');

        Route::post('/inventaris',[GudangCabangController::class, 'inventarisStore'])
            ->name('inventaris.store');

        Route::get('/inventaris/{id}/edit',[GudangCabangController::class, 'inventarisEdit']);

        Route::put('/inventaris/{id}',[GudangCabangController::class, 'inventarisUpdate'])
            ->name('inventaris.update');

        // permintaan oengiriman
        Route::get('/permintaan-pengiriman', [GudangCabangController::class, 'permintaan'])
            ->name('permintaan.index');

        Route::post('/permintaan-pengiriman/store', [GudangCabangController::class, 'permintaanStore'])
            ->name('permintaan.store');

        Route::delete('/permintaan-pengiriman/{id}',[GudangCabangController::class, 'permintaanDestroy'])
            ->name('permintaan.destroy');

        // AMBIL
        Route::get('/ambil', [GudangCabangController::class, 'ambilIndex'])
            ->name('ambil.index');

        Route::post('/ambil/store', [GudangCabangController::class, 'ambilStore'])
            ->name('ambil.store');

        Route::get('/ambil/{id}', [GudangCabangController::class, 'ambilDetail'])
            ->name('ambil.detail');

        Route::get('/ambil/{id}/edit', [GudangCabangController::class, 'ambilEdit'])
            ->name('ambil.edit');

        Route::put('/ambil/{id}', [GudangCabangController::class, 'ambilUpdate'])
            ->name('ambil.update');

        Route::delete('/ambil/{id}', [GudangCabangController::class, 'ambilDestroy'])
            ->name('ambil.destroy');

        Route::post('/ambil/terima/{id}', [GudangCabangController::class, 'ambilTerima'])
            ->name('ambil.terima');

        // ANTAR
        Route::get('/antar', [GudangCabangController::class, 'antarIndex'])
            ->name('antar.index');

        Route::post('/antar/kirim/{id}', [GudangCabangController::class, 'antarKirim'])
            ->name('antar.kirim');

        Route::get('/antar/{id}', [GudangCabangController::class, 'antarDetail'])
            ->name('antar.detail');

        Route::post('/antar/terima/{id}', [GudangCabangController::class, 'antarTerima'])
            ->name('antar.terima');

        //pengambilan
        Route::post('pengambilan/store', [GudangCabangController::class, 'pengambilanStore'])
            ->name('pengambilan.store');

        Route::get('pengambilan', [GudangCabangController::class, 'pengambilanIndex'])
            ->name('pengambilan.index');

        Route::get('pengambilan/edit/{id}', [GudangCabangController::class, 'pengambilanEdit'])
            ->name('pengambilan.edit');

        Route::post('pengambilan/update/{id}', [GudangCabangController::class, 'pengambilanUpdate'])
            ->name('pengambilan.update');

        Route::delete('pengambilan/{id}', [GudangCabangController::class, 'pengambilanDestroy'])
            ->name('pengambilan.destroy');

    });
