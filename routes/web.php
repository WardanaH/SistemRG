<?php

use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('dashboard.index');
})->name('dashboard');

Route::get('/table', function () {
    return view('table.tables');
})->name('table');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');


