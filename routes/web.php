<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
// use App\Http\Controllers\InvoiceController; // duplicate removed
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;

// Redirect root to login for now (frontend only)
Route::get('/', function () { return redirect()->route('login'); });

// Auth (frontend-only)
Route::get('/login', function () { return view('auth.login'); })->name('login');

// Dashboard
Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');

// Inventory
Route::get('/products', [App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
Route::get('/products/create', [App\Http\Controllers\ProductController::class, 'create'])->name('products.create');
Route::post('/products', [App\Http\Controllers\ProductController::class, 'store'])->name('products.store');
Route::get('/products/{product}', [App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
Route::get('/products/{product}/edit', [App\Http\Controllers\ProductController::class, 'edit'])->name('products.edit');
Route::put('/products/{product}', [App\Http\Controllers\ProductController::class, 'update'])->name('products.update');
Route::delete('/products/{product}', [App\Http\Controllers\ProductController::class, 'destroy'])->name('products.destroy');

Route::prefix('stock')->name('stock.')->group(function () {
    Route::get('/', [App\Http\Controllers\StockController::class, 'index'])->name('index');
    Route::get('/{product}', [App\Http\Controllers\StockController::class, 'show'])->name('stock.show');
});

Route::prefix('movements')->name('movements.')->group(function () {
    Route::get('/', [App\Http\Controllers\MovementController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\MovementController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\MovementController::class, 'store'])->name('store');
});

// Sales

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

//Categories
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::post('/', [CategoryController::class, 'store'])->name('store');
});

// Invoices
Route::resource('invoices', InvoiceController::class);
Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');

// Auth routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');