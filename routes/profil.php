<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Profil\Public\PBerandaController as PublicPBerandaController;
use App\Http\Controllers\Profil\Public\PTentangController as PublicPTentangController;
use App\Http\Controllers\Profil\Public\PLayananController as PublicPLayananController;
use App\Http\Controllers\Profil\Public\PBeritaController as PublicPBeritaController;
use App\Http\Controllers\Profil\Public\PKontakController as PublicPKontakController;

use App\Http\Controllers\Profil\Admin\PBerandaController as AdminPBerandaController;
use App\Http\Controllers\Profil\Admin\PTentangController as AdminPTentangController;
use App\Http\Controllers\Profil\Admin\PLayananController as AdminPLayananController;
use App\Http\Controllers\Profil\Admin\PBeritaController as AdminPBeritaController;
use App\Http\Controllers\Profil\Admin\PKontakController as AdminPKontakController;
use App\Http\Controllers\Profil\Admin\PSiteLayoutController as AdminPSiteLayoutController;




// =======================
// PUBLIC PROFIL
// =======================
Route::prefix('profil')->name('profil.')->group(function () {
    Route::get('/beranda', [PublicPBerandaController::class, 'index'])->name('beranda');
    Route::get('/layanan', [PublicPLayananController::class, 'index'])->name('layanan');
    Route::get('/tentang', [PublicPTentangController::class, 'index'])->name('tentang');
    Route::get('/berita', [PublicPBeritaController::class, 'index'])->name('berita');
    Route::get('/berita/{slug}', [PublicPBeritaController::class, 'show'])->name('berita.show');
    Route::get('/kontak', [PublicPKontakController::class, 'index'])->name('kontak');
});

// =======================
// ADMIN PROFIL
// =======================
Route::prefix('admin')->name('profil.admin.')->group(function () {
    Route::view('/dashboard', 'profil.admin.pages.dashboard')->name('dashboard');
    Route::get('/beranda/edit', [AdminPBerandaController::class, 'edit'])->name('beranda.edit');
    Route::put('/beranda', [AdminPBerandaController::class, 'update'])->name('beranda.update');
    Route::get('/tentang/edit', [AdminPTentangController::class, 'edit'])->name('tentang.edit');
    Route::put('/tentang', [AdminPTentangController::class, 'update'])->name('tentang.update');
    Route::put('/tentang/clients/{index}', [AdminPTentangController::class, 'deleteClient'])
            ->whereNumber('index')->name('tentang.clients.delete');
    Route::get('/layanan/edit', [AdminPLayananController::class, 'edit'])->name('layanan.edit');
    Route::put('/layanan', [AdminPLayananController::class, 'update'])->name('layanan.update');
    Route::get('/berita', [AdminPBeritaController::class, 'index'])->name('berita.index');
    Route::get('/berita/create', [AdminPBeritaController::class, 'create'])->name('berita.create');
    Route::post('/berita', [AdminPBeritaController::class, 'store'])->name('berita.store');
    Route::get('/berita/{beritum}/edit', [AdminPBeritaController::class, 'edit'])->name('berita.edit');
    Route::put('/berita/{beritum}', [AdminPBeritaController::class, 'update'])->name('berita.update');
    Route::delete('/berita/{beritum}', [AdminPBeritaController::class, 'destroy'])->name('berita.destroy');
    Route::get('/berita-halaman/edit', [AdminPBeritaController::class, 'editHalaman'])->name('berita.halaman.edit');
    Route::put('/berita-halaman', [AdminPBeritaController::class, 'updateHalaman'])->name('berita.halaman.update');
    Route::get('/kontak/edit', [AdminPKontakController::class, 'edit'])->name('kontak.edit');
    Route::put('/kontak', [AdminPKontakController::class, 'update'])->name('kontak.update');
    Route::get('/tampilan/navbar/edit', [AdminPSiteLayoutController::class, 'editNavbar'])->name('tampilan.navbar.edit');
    Route::put('/tampilan/navbar',      [AdminPSiteLayoutController::class, 'updateNavbar'])->name('tampilan.navbar.update');
    Route::get('/tampilan/footer/edit', [AdminPSiteLayoutController::class, 'editFooter'])->name('tampilan.footer.edit');
    Route::put('/tampilan/footer',      [AdminPSiteLayoutController::class, 'updateFooter'])->name('tampilan.footer.update');
    Route::post('/logout', function () {
        return redirect()->route('profil.admin.dashboard')
            ->with('success', 'Logout dummy berhasil (sementara).');
    })->name('logout');
});
