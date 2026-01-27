<?php

use App\Http\Controllers\DesignerController;
use App\Http\Controllers\MSpkController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/operator/dashboard', [DesignerController::class, 'index'])->name('designer.dashboard');

    Route::get('/operator/spk/buat', [MSpkController::class, 'buat'])->name('designer.spk');
    Route::get('/operator/spk', [MSpkController::class, 'index'])->name('designer.spk.index');
    Route::post('/operator/spk', [MSpkController::class, 'store'])->name('designer.spk.store');
    Route::get('/operator/spk/{spk}/edit', [MSpkController::class, 'edit'])->name('designer.spk.edit');
    Route::delete('/operator/spk/{spk}', [MSpkController::class, 'update'])->name('designer.spk.update');
    Route::delete('/operator/spk/{spk}/delete', [MSpkController::class, 'destroy'])->name('designer.spk.destroy');
});
