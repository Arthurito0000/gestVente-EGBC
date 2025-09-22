<?php

use Illuminate\Support\Facades\Route;

// Redirect root to login for now (frontend only)
Route::get('/', function () { return redirect()->route('login'); });

// Auth (frontend-only)
Route::get('/login', function () { return view('auth.login'); })->name('login');

// Dashboard
Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');

// Inventory
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', fn() => view('products.index'))->name('index');
});

Route::prefix('stock')->name('stock.')->group(function () {
    Route::get('/', fn() => view('stock.index'))->name('index');
});

Route::prefix('movements')->name('movements.')->group(function () {
    Route::get('/', fn() => view('movements.index'))->name('index');
});

// Sales
Route::prefix('invoices')->name('invoices.')->group(function () {
    Route::get('/', fn() => view('invoices.index'))->name('index');
});

// Administration
Route::prefix('users')->name('users.')->group(function () {
    Route::get('/', fn() => view('users.index'))->name('index');
});

Route::prefix('roles')->name('roles.')->group(function () {
    Route::get('/', fn() => view('roles.index'))->name('index');
});

Route::prefix('permissions')->name('permissions.')->group(function () {
    Route::get('/', fn() => view('permissions.index'))->name('index');
});
