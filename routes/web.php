<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\GudangCabangController;
use App\Http\Controllers\UserController;

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
require __DIR__.'/profil.php';
require __DIR__.'/advertising.php';

Route::get('/', function () {
    // 1. Cek apakah user sudah login
    if (auth()->check()) {
        $user = auth()->user();

        if ($user->hasRole('manajemen')) {
            return redirect()->route('manajemen.dashboard');
        } elseif ($user->hasAnyRole(['operator indoor', 'operator outdoor', 'operator multi', 'operator dtf'])) {
            return redirect()->route('operator.dashboard');
        } elseif ($user->hasRole('designer')) {
            return redirect()->route('designer.dashboard');
        } elseif ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('advertising')) {
            return redirect()->route('advertising.dashboard');
        }
    }


    // 2. Jika tidak login ATAU tidak punya role di atas, arahkan ke beranda profil
    return redirect()->route('profil.beranda');
})->name('home');


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

    // laporan charge
    Route::get('/laporan-charge', [LaporanController::class, 'laporanCharge'])
        ->name('laporan.charge');
    Route::get('/laporan-charge/pdf', [LaporanController::class, 'exportChargePdf'])
        ->name('laporan.charge.pdf');
    Route::get('/laporan-charge/excel', [LaporanController::class, 'exportChargeExcel'])
        ->name('laporan.charge.excel');
});

Route::middleware('auth')->group(function (){
    Route::get('/user-setting', [UserController::class, 'indexSetting'])
    ->name('user.setting');
    Route::put('/user-setting', [UserController::class, 'updateUser'])
    ->name('user.update');
});




