<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\GudangCabangController;

Route::get('/dashboard', function () {
    return view('dashboard.index');
})->name('dashboard');

Route::get('/table', function () {
    return view('table.tables');
})->name('table');

// Route::get('/login', function () {
//     return view('auth.login');
// })->name('login');



// require route
require __DIR__.'/auth.php';
require __DIR__.'/manajemen.php';
require __DIR__.'/admin.php';
require __DIR__.'/operator.php';
require __DIR__.'/designer.php';
require __DIR__.'/gudang_pusat.php';
require __DIR__.'/gudang_cabang.php';

Route::get('/', function () {
    $user = auth()->user();

    if ($user->hasRole('manajemen')) {
        return redirect()->route('manajemen.dashboard');
    } elseif ($user->hasRole('operator indoor') || $user->hasRole('operator outdoor') || $user->hasRole('operator multi') || $user->hasRole('operator dtf')) {
        return redirect()->route('operator.dashboard');
    } elseif ($user->hasRole('designer')) {
        return redirect()->route('designer.dashboard');
    } elseif ($user->hasrole('admin')) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('auth.index');
})->middleware('auth')->name('home');


// inventaris qr tanpalogin
Route::get('/inventaris/qr/{kode}',[GudangCabangController::class, 'inventarisQr'])->name('inventaris.qr.public');

Route::middleware(['auth'])->group(function () {
    // Ganti middleware role agar bisa diakses semua role yang berkepentingan
    Route::get('/laporan-kinerja', [LaporanController::class, 'index'])
        ->middleware('role:admin|manajemen|designer|operator indoor|operator outdoor|operator multi|operator dtf')
        ->name('laporan.index');
    Route::post('/laporan-kinerja/set-target', [LaporanController::class, 'storeTarget'])
        ->middleware('role:manajemen|admin') // Hanya manajemen/admin yg boleh set target
        ->name('laporan.storeTarget');
    Route::post('/laporan-kinerja/set-target-role', [LaporanController::class, 'storeTargetByRole'])
        ->name('laporan.storeTargetByRole');
});
