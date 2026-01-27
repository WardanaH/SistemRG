<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GudangPusatController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| GUDANG PUSAT (SISTEM BARU – STANDALONE)
|--------------------------------------------------------------------------
| Role : inventory utama
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:inventory utama'])
    ->prefix('gudang-pusat')
    ->group(function () {

        //dashboard
        Route::get('/dashboard', function () {
        return view('inventaris.gudangpusat.dashboard');
        })->name('gudangpusat.dashboard');

        // barang
        Route::get('/barang', [GudangPusatController::class, 'index'])
            ->name('barang.pusat');

        Route::post('/barang/store', [GudangPusatController::class, 'store'])
            ->name('barang.pusat.store');

        Route::get('/barang/edit/{id}', [GudangPusatController::class, 'edit'])
            ->name('barang.pusat.edit');

        Route::put('/barang/update/{id}', [GudangPusatController::class, 'update'])
            ->name('barang.pusat.update');

        Route::delete('/barang/delete/{id}', [GudangPusatController::class, 'destroy'])
            ->name('barang.pusat.destroy');

        //pengiriman
        Route::get('/pengiriman-pusat', [GudangPusatController::class, 'pengirimanIndex'])
            ->name('pengiriman.pusat.index');

        Route::post('/pengiriman-pusat/store', [GudangPusatController::class, 'pengirimanStore'])
            ->name('pengiriman.pusat.store');

        Route::put('/pengiriman-pusat/status/{id}', [GudangPusatController::class, 'pengirimanUpdateStatus'])
            ->name('pengiriman.pusat.status');

        Route::delete('/pengiriman-pusat/delete/{id}', [GudangPusatController::class, 'pengirimanDestroy'])
            ->name('pengiriman.pusat.destroy');

        // laporan
        Route::get('/laporan-pengiriman', [GudangPusatController::class, 'laporanIndex'])
            ->name('laporan.pengiriman.index');

        Route::get('/laporan-pengiriman/{bulan}/{tahun}', [GudangPusatController::class, 'laporanDetail'])
            ->name('laporan.pengiriman.detail');

        Route::get('/laporan-pengiriman/{bulan}/{tahun}/download', [GudangPusatController::class, 'laporanDownload'])
            ->name('laporan.pengiriman.download');


    });

//logout
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');
