<?php

use App\Http\Controllers\DesignerController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/operator/dashboard', [DesignerController::class, 'index'])->name('designer.dashboard');
    Route::get('/operator/spk', [DesignerController::class, 'spk'])->name('designer.spk');
});
