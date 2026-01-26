<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::get('/operator/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
