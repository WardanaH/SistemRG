<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    $user = auth()->user();

    if ($user->hasRole('manajemen')) {
        return redirect()->route('manajemen.dashboard');
    } elseif ($user->hasRole('operator indoor') || $user->hasRole('operator outdoor') || $user->hasRole('operator multi')) {
        return redirect()->route('operator.dashboard');
    } elseif ($user->hasRole('designer')) {
        return redirect()->route('designer.dashboard');
    } elseif ($user->hasrole('admin')) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('auth.index');
})->middleware('auth')->name('home');
