<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

// Redirect root to login for now (frontend only)
Route::get('/', function () { return redirect()->route('login'); });

// Auth (frontend-only)
Route::get('/login', function () { return view('auth.login'); })->name('login');

// Dashboard
Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');

// Inventory
Route::get('/products', [App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
Route::get('/products/create', [App\Http\Controllers\ProductController::class, 'create'])->name('products.create');

// Export routes (avant les routes avec paramètres) - Structure organisée
Route::prefix('export')->name('export.')->group(function () {
    // Exports produits
    Route::get('/products/excel', [App\Http\Controllers\Export\ExportController::class, 'productsExcel'])->name('products.excel');
    Route::get('/products/pdf', [App\Http\Controllers\Export\ExportController::class, 'productsPdf'])->name('products.pdf');
    Route::get('/products/preview', [App\Http\Controllers\Export\ExportController::class, 'productsPreview'])->name('products.preview');
    Route::get('/products/stats', [App\Http\Controllers\Export\ExportController::class, 'productsStats'])->name('products.stats');
    
    // Exports stocks (à implémenter)
    Route::get('/stocks/excel', [App\Http\Controllers\Export\ExportController::class, 'stocksExcel'])->name('stocks.excel');
    Route::get('/stocks/pdf', [App\Http\Controllers\Export\ExportController::class, 'stocksPdf'])->name('stocks.pdf');
    
    // Exports mouvements (à implémenter)
    Route::get('/movements/excel', [App\Http\Controllers\Export\ExportController::class, 'movementsExcel'])->name('movements.excel');
    Route::get('/movements/pdf', [App\Http\Controllers\Export\ExportController::class, 'movementsPdf'])->name('movements.pdf');
});

// Routes de compatibilité (anciennes URLs)
Route::get('/products/export/excel', [App\Http\Controllers\Export\ExportController::class, 'productsExcel'])->name('products.export.excel');
Route::get('/products/export/pdf', [App\Http\Controllers\Export\ExportController::class, 'productsPdf'])->name('products.export.pdf');

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

//Categories
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::post('/', [CategoryController::class, 'store'])->name('store');
});
