<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OperatorController;

Route::middleware('auth')->group(function () {
    Route::get('/operator/dashboard', [OperatorController::class, 'index'])->name('operator.dashboard');
});
