<?php

use App\Http\Controllers\profil2\BerandaController;
use App\Http\Controllers\profil2\ProfilController;
use Illuminate\Support\Facades\Route;

// public profil
Route::get('/beranda', [BerandaController::class, 'index'])->name('profil2.beranda');
Route::get('/layanan', [BerandaController::class, 'layanan'])->name('profil2.layanan');
Route::get('/produk', [BerandaController::class, 'produk'])->name('profil2.produk');
Route::get('/produk/detail/{id}', [BerandaController::class, 'detailProduk'])->name('profil2.produk.detail');
Route::get('/event/{tema}', [BerandaController::class, 'event'])->name('profil2.event');
Route::get('/tentang', [BerandaController::class, 'tentang'])->name('profil2.tentang');
Route::get('/kontak', [BerandaController::class, 'kontak'])->name('profil2.kontak');
Route::get('/legal/{jenis}', [BerandaController::class, 'legal'])->name('profil2.legal.public');

// Dashboard Admin Profil (Hanya gasan nang sudah login & role: profil2)
Route::middleware(['auth', 'role:profil2'])->group(function () {

    // Nampaiakan Dashboard Edit
    Route::get('/admin-profil', [App\Http\Controllers\profil2\ProfilController::class, 'dashboard'])->name('profil2.dashboard');

    // Route Info Perusahaan
    Route::get('/admin-profil/perusahaan', [App\Http\Controllers\profil2\ProfilController::class, 'editPerusahaan'])->name('profil2.perusahaan.edit');
    Route::put('/admin-profil/perusahaan', [App\Http\Controllers\profil2\ProfilController::class, 'updatePerusahaan'])->name('profil2.perusahaan.update');

    // Route Atur Teks Hero
    Route::get('/admin-profil/hero', [App\Http\Controllers\profil2\ProfilController::class, 'editHero'])->name('profil2.hero.edit');
    Route::put('/admin-profil/hero', [App\Http\Controllers\profil2\ProfilController::class, 'updateHero'])->name('profil2.hero.update');

    // Route Kelola Produk
    Route::get('/admin-profil/produk', [App\Http\Controllers\profil2\ProfilController::class, 'indexProduk'])->name('profil2.produk.index');
    Route::post('/admin-profil/produk', [App\Http\Controllers\profil2\ProfilController::class, 'storeProduk'])->name('profil2.produk.store');
    Route::put('/admin-profil/produk/{id}', [App\Http\Controllers\profil2\ProfilController::class, 'updateProduk'])->name('profil2.produk.update');
    Route::delete('/admin-profil/produk/{id}', [App\Http\Controllers\profil2\ProfilController::class, 'destroyProduk'])->name('profil2.produk.destroy');

    // Route Legal & Privasi
    Route::get('/admin-profil/legal', [App\Http\Controllers\profil2\ProfilController::class, 'editLegal'])->name('profil2.legal.edit');
    Route::put('/admin-profil/legal', [App\Http\Controllers\profil2\ProfilController::class, 'updateLegal'])->name('profil2.legal.update');

    // Route Kelola Event Spesial
    Route::get('/admin-profil/event', [App\Http\Controllers\profil2\ProfilController::class, 'indexEvent'])->name('profil2.event.index');
    Route::post('/admin-profil/event', [App\Http\Controllers\profil2\ProfilController::class, 'storeEvent'])->name('profil2.event.store');
    Route::put('/admin-profil/event/{id}', [App\Http\Controllers\profil2\ProfilController::class, 'updateEvent'])->name('profil2.event.update');
    Route::delete('/admin-profil/event/{id}', [App\Http\Controllers\profil2\ProfilController::class, 'destroyEvent'])->name('profil2.event.destroy');

    // Route Kelola Mesin
    Route::get('/admin-profil/mesin', [App\Http\Controllers\profil2\ProfilController::class, 'indexMesin'])->name('profil2.mesin.index');
    Route::post('/admin-profil/mesin', [App\Http\Controllers\profil2\ProfilController::class, 'storeMesin'])->name('profil2.mesin.store');
    Route::put('/admin-profil/mesin/{id}', [App\Http\Controllers\profil2\ProfilController::class, 'updateMesin'])->name('profil2.mesin.update');
    Route::delete('/admin-profil/mesin/{id}', [App\Http\Controllers\profil2\ProfilController::class, 'destroyMesin'])->name('profil2.mesin.destroy');

    // Route Kelola Klien
    Route::get('/admin-profil/klien', [App\Http\Controllers\profil2\ProfilController::class, 'indexKlien'])->name('profil2.klien.index');
    Route::post('/admin-profil/klien', [App\Http\Controllers\profil2\ProfilController::class, 'storeKlien'])->name('profil2.klien.store');
    Route::put('/admin-profil/klien/{id}', [App\Http\Controllers\profil2\ProfilController::class, 'updateKlien'])->name('profil2.klien.update');
    Route::delete('/admin-profil/klien/{id}', [App\Http\Controllers\profil2\ProfilController::class, 'destroyKlien'])->name('profil2.klien.destroy');
});
