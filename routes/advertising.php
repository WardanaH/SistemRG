<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdvertisingController;

// Pastikan middleware role sesuai dengan setup permission Anda (misal: spatie/permission)
Route::group(['middleware' => ['auth']], function () {

    // Dashboard & List SPK
    Route::get('/advertising', [AdvertisingController::class, 'index'])
        ->middleware('role:advertising')
        ->name('advertising.dashboard');

    // manajemen
    Route::get('/advertising/create', [AdvertisingController::class, 'create'])
        ->middleware('role:advertising')
        ->name('advertising.create');
    Route::post('/advertising/store', [AdvertisingController::class, 'store'])
        ->middleware('role:advertising')
        ->name('advertising.store');
    Route::get('/advertising/{id}/show', [AdvertisingController::class, 'show'])
        ->middleware('role:advertising')
        ->name('advertising.show');
    Route::get('/advertising/{id}/print', [AdvertisingController::class, 'print'])
        ->middleware('role:advertising')
        ->name('advertising.print');
    Route::delete('/advertising/{id}/destroy', [AdvertisingController::class, 'destroy'])
        ->middleware('role:advertising')
        ->name('advertising.destroy');
    Route::get('/advertising/riwayat-produksi', [AdvertisingController::class, 'riwayatProduksi'])
        ->middleware('role:advertising|operator indoor|operator outdoor|operator multi|operator dtf')
        ->name('advertising.riwayat');


});
